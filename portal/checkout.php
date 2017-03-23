<?php

// --------------------------------------------------------------------------
// Perform our initialization
// --------------------------------------------------------------------------
require 'assets/libs/cartman/init.php';
require_once ('includes/managesessions.php'); 

ValidateDomain($_SERVER['HTTP_HOST']);

$total = '';
$invoice_action = '';
$allow_submit = true;

if (isset($_GET['invoice_id'])) 
{
    $invoice = Model::factory('Invoice')->where('unique_id', $_GET['invoice_id'])->find_one();

    if ( !$invoice ) 
    {
        $invoice_action = 'not found';
        $error = 'Sorry, but we can\'t seem to find that particular invoice.';
    } 

    elseif ( $invoice->status == 'Paid' ) 
    {
        $invoice_action = 'already paid';
        $allow_submit = false;
    } 

    else 
    {
        $invoice_action = 'valid';
        $total = $invoice->amount;
    }
}

// do some error checking first
if (empty($config['name']) || empty($config['email'])) 
{
    $error = 'You need to enter your name and email in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept payments.';
}

if ($config['enable_paypal'] && empty($config['paypal_email'])) 
{
    $error = 'You need to enter your PayPal email address in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept PayPal payments.';
}

if (empty($config['stripe_secret_key']) || empty($config['stripe_publishable_key'])) 
{
    $error = 'You need to enter your Stripe API details in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept credit card payments.';
}

if (isset($error)) 
{
    $allow_submit = false;
}
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Project-Oslo Checkout</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <?php template('head'); ?>


    <?php if(isset($_SESSION['domain'])) { ?>
        <link rel="stylesheet" href="domains/<?=$_SESSION['domain']?>/css/portal.css" /> 
    <?php } else if($login_domain){ ?>
        <link rel="stylesheet" href="domains/<?= $login_domain ?>/css/portal.css" /> 
    <?php } ?>

</head>

<body>
	<?php include('includes/topbar.php'); ?>

    <noscript>
        <div class="alert alert-danger mt20neg">
            <div class="container aligncenter">
                <strong>Oops!</strong> It looks like your browser doesn't have Javascript enabled.  Please enable Javascript to use this website.
            </div>
        </div>
    </noscript>
	
    <div class="container py-5" id="body-container">
        <div class="card">
            <div class="card-block">
        <?php template('message', false); ?>

        <?php if ( isset($error) ) : ?>
            <div class="alert alert-danger">
                <strong><i class="fa fa-exclamation-circle"></i> Oops!</strong><br>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ( $invoice_action == 'already paid' ) : ?>
            <div class="alert alert-success">
                <strong><i class="fa fa-check"></i> This invoice has already been paid!</strong><br>
                Payment for this invoice was received on <?php echo date('F jS, Y', strtotime($invoice->date_paid)); ?>.
            </div>
        <?php endif; ?>


        <form action="<?php echo url('includes/process.php'); ?>" method="post" class="validate form-horizontal <?php echo !$allow_submit ? 'disabled' : ''; ?>" id="order_form">
            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="process_payment">
            <?php if ( $invoice_action == 'valid' ) : ?>
            <input type="hidden" name="invoice_id" value="<?php echo $invoice->id; ?>">
            <?php endif; ?>
            <input type="hidden" class="enable-subscriptions" value="<?php echo $config['enable_subscriptions']; ?>">
            <input type="hidden" class="publishable-key" value="<?php echo trim($config['stripe_publishable_key']); ?>">

            <div class="row">
                <div class="col-md-6">

                    <h3 class="colorgray mb20"><?php echo $invoice_action == 'valid' || $invoice_action == 'already paid' ? 'Invoice' : 'Payment'; ?> Details</h3>

                    <?php if ( $invoice_action == 'valid' || $invoice_action == 'already paid' ) : ?>

                        <div class="form-group row">
                            <label class="col-3 col-form-label">Amount</label>
                            <div class="col-9">
                                <?php echo currency($invoice->amount) ?>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label class="col-3 col-form-label">Description</label>
                            <div class="col-9">
                                <?php echo $invoice->description; ?>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label class="col-3 col-form-label">Due Date</label>
                            <div class="col-9">
                                <?php echo !is_null($invoice->date_due) ? date('F jS, Y', strtotime($invoice->date_due)) : '<em>no due date set</em>'; ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label">Invoice #</label>
                            <div class="col-9">
                                <?php echo $invoice->number ? $invoice->number : $invoice->id; ?>
                            </div>
                        </div>

                    <?php else : ?>

                        <?php if ( $config['payment_type'] == 'item' ) : ?>
                            

                            <div class="form-group row">
                                <label class="col-2 col-form-label"><span class="colordanger">*</span>Item</label>
                                <div class="col-10">
                                    <select name="item_id" class="form-control" required="true">
                                        <option value="">-- Select Item --</option>
                                        <?php foreach ( Model::factory('Item')->find_many() as $item ) : ?>
                                        <option value="<?php echo $item->id; ?>" data-name="<?php echo $item->name; ?>" data-price="<?php echo $item->price; ?>" <?php echo get('item_id') == $item->id ? 'selected' : ''; ?>><?php echo $item->name; ?> (<?php echo currency($item->price); ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>  

                        <?php else : ?>

                            <div class="form-group row">
                                <label class="col-2 col-form-label"><span class="colordanger">*</span>Amount</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-<?php echo currencyCode(); ?>"></i></span>
                                        <input type="text" name="amount" class="form-control" placeholder="0.00" required="true" data-rule-number="true">
                                    </div>
                                </div>
                            </div>  
                            <?php if ( $config['show_description'] ) : ?>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label"><span class="colordanger">*</span>Description</label>
                                    <div class="col-10">
                                        <textarea name="description" class="form-control h55 maxlength" maxlength="120" placeholder="Description" required="true"></textarea>
                                    </div>
                                </div>  
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">Your Information</h3>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"><span class="colordanger">*</span>Email</label>
                        <div class="col-10">
                            <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo isset($invoice) && $invoice ? $invoice->email : ''; ?>" required="true" data-rule-email="true">
                        </div>
                    </div>
                    <?php if ( $config['show_shipping_address'] ) : ?>
                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">Billing Address</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <input type="text" name="billing_name" class="form-control" placeholder="Name" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Address</label>
                            <div class="col-10">
                                <input type="text" name="billing_address" class="form-control" placeholder="Address" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>City</label>
                            <div class="col-10">
                                <input type="text" name="billing_city" class="form-control" placeholder="City" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>State/Zip</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-8 pr5">
                                        <select name="billing_state" class="form-control" required="true">
                                            <option value="">-- Select State --</option>
                                            <?php foreach ( states() as $country_name => $states_arr ) : ?>
                                            <optgroup label="<?php echo $country_name; ?>">
                                                <?php foreach ( $states_arr as $state_code => $state_name ) : ?>
                                                <option value="<?php echo $state_code; ?>"><?php echo $state_name; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php endforeach; ?>
                                            <option value="N/A">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-4 pl5">
                                        <input type="text" name="billing_zip" class="form-control" placeholder="Zip" value="" required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Country</label>
                            <div class="col-10">
                                <select name="billing_country" class="form-control" required="true">
                                    <option value="">-- Select Country --</option>
                                    <?php foreach ( countries() as $country_code => $country_name ) : ?>
                                    <option value="<?php echo $country_code; ?>" <?php echo $country_code == 'US' ? 'selected' : ''; ?>><?php echo $country_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">

                    <?php if ( $config['show_shipping_address'] ) : ?>
                        <hr class="hidden-md-up">
                        <h3 class="colorgray mb20">Shipping Address</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <input type="text" name="shipping_name" class="form-control" placeholder="Name" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Address</label>
                            <div class="col-10">
                                <input type="text" name="shipping_address" class="form-control" placeholder="Address" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>City</label>
                            <div class="col-10">
                                <input type="text" name="shipping_city" class="form-control" placeholder="City" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>State/Zip</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-8 pr5">
                                        <select name="shipping_state" class="form-control" required="true">
                                            <option value="">-- Select State --</option>
                                            <?php foreach ( states() as $country_name => $states_arr ) : ?>
                                            <optgroup label="<?php echo $country_name; ?>">
                                                <?php foreach ( $states_arr as $state_code => $state_name ) : ?>
                                                <option value="<?php echo $state_code; ?>"><?php echo $state_name; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php endforeach; ?>
                                            <option value="N/A">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-4 col-xs-4 pl5">
                                        <input type="text" name="shipping_zip" class="form-control" placeholder="Zip" value="" required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Country</label>
                            <div class="col-10">
                                <select name="shipping_country" class="form-control" required="true">
                                    <option value="">-- Select Country --</option>
                                    <?php foreach ( countries() as $country_code => $country_name ) : ?>
                                    <option value="<?php echo $country_code; ?>" <?php echo $country_code == 'US' ? 'selected' : ''; ?>><?php echo $country_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">
                        Payment Method
                        <div class="floatright">
                            <?php if ( $config['enable_paypal'] && !empty($config['paypal_email']) ) : ?>
                                <label class="radio-inline pt0 mt10neg">
                                    <input type="radio" name="payment_method" value="creditcard" checked> 
                                    <img src="<?php echo url('assets/images/credit-cards.jpg'); ?>" class="">
                                </label>
                                <label class="radio-inline pt0 mt10neg">
                                    <input type="radio" name="payment_method" value="paypal"> 
                                    <img src="<?php echo url('assets/images/paypal.jpg'); ?>" class="w100">
                                </label>
                            <?php else : ?>
                                <img src="<?php echo url('assets/images/credit-cards.jpg'); ?>" class="">
                            <?php endif; ?>
                        </div>
                    </h3>

                    <div class="creditcard-content">

                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" data-stripe="name" name="cardholder_name" class="form-control" placeholder="Name on Card" value="" required="true">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Card #</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" data-stripe="number" class="form-control card-number" placeholder="Card Number" value="" required="true" data-rule-creditcard="true">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                </div> 
                                <div class="card-type-image none"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Expiration/CVC</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-4 pr5">
                                        <select data-stripe="exp-month" class="form-control" required="true">
                                            <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == date('n') ? 'selected="selected"' : ''; ?>><?php echo date('m', strtotime('2000-' . $i . '-01'));; ?></option>
                                            <?php endfor; ?>
                                        </select>   
                                    </div>
                                    <div class="col-4 pl5 pr5">
                                        <select data-stripe="exp-year" class="form-control" required="true">
                                            <?php for ( $i = date('Y'); $i <= date('Y') + 10; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-4 pl5">
                                        <div class="input-group">
                                            <input type="text" data-stripe="cvc" name="cvc" class="form-control" placeholder="CVC" value="" required="true">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt40">
                        
                        <div class="col-12 alignright">
                            <div class="creditcard-content">

                                <button type="submit" class="btn btn-lg btn-primary submit-button mb20" data-loading-text='<i class="fa fa-spinner fa-spin"></i> Submitting...' data-complete-text='<i class="fa fa-check"></i> Payment Complete!' <?php echo $allow_submit ? '' : 'disabled'; ?>>
                                    <span class="total <?php echo !empty($total) ? 'show' : ''; ?>">Total: <?php echo currencySymbol(); ?><span><?php echo $total; ?></span> <small><?php echo currencySuffix(); ?></small></span>
                                    <i class="fa fa-check"></i> Submit Payment
                                </button>

                            </div>
                            <div class="paypal-content displaynone">
                                <a href="#" class="btn btn-lg btn-primary submit-button paypal-button" data-loading-text='<i class="fa fa-spinner fa-spin"></i> Sending to PayPal...' <?php echo $allow_submit ? '' : 'disabled'; ?>>
                                    <span class="total <?php echo !empty($total) ? 'show' : ''; ?>">Total: <?php echo currencySymbol(); ?><span><?php echo $total; ?></span> <small><?php echo currencySuffix(); ?></small></span>
                                    Continue to PayPal <i class="fa fa-angle-double-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </form>

        <?php if ( $config['enable_paypal'] ) : ?>

            <form action="https://www.<?php echo $config['paypal_environment'] == 'sandbox' ? 'sandbox.' : ''; ?>paypal.com/cgi-bin/webscr" method="post" class="paypal-form" target="_top" id="paypal_form_one_time">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="amount" value="<?php echo isset($invoice) && $invoice ? $invoice->amount : ''; ?>">
                <input type="hidden" name="business" value="<?php echo $config['paypal_email']; ?>">
                <input type="hidden" name="item_name" value="<?php echo isset($invoice) && $invoice ? $invoice->description : ''; ?>">
                <input type="hidden" name="currency_code" value="<?php echo $config['currency']; ?>">
                <input type="hidden" name="no_note" value="1">
                <input type="hidden" name="no_shipping" value="1">
                <input type="hidden" name="rm" value="1">
                <input type="hidden" name="custom" value="">
                <input type="hidden" name="return" value="<?php echo url('process.php?action=paypal_success'); ?>">
                <input type="hidden" name="cancel_return" value="<?php echo url('process.php?action=paypal_cancel'); ?>">
                <input type="hidden" name="notify_url" value="<?php echo url('process.php?action=paypal_ipn'); ?>">
            </form>

            <?php if ( $config['enable_subscriptions'] == 'stripe_and_paypal' || $config['enable_subscriptions'] == 'paypal_only' ) : ?>

                <form action="https://www.<?php echo $config['paypal_environment'] == 'sandbox' ? 'sandbox.' : ''; ?>paypal.com/cgi-bin/webscr" method="post" class="paypal-form" target="_top" id="paypal_form_recurring">
                    <input type="hidden" name="cmd" value="_xclick-subscriptions">
                    <input type="hidden" name="business" value="<?php echo $config['paypal_email']; ?>">
                    <input type="hidden" name="lc" value="US">
                    <input type="hidden" name="item_name" value="">
                    <input type="hidden" name="currency_code" value="<?php echo $config['currency']; ?>">
                    <input type="hidden" name="no_note" value="1">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="custom" value="">
                    <input type="hidden" name="src" value="1">

                    <?php if ( $config['subscription_length'] ) : ?>
                    <input type="hidden" name="srt" value="<?php echo $config['subscription_length']; ?>">
                    <?php endif; ?>

                    <?php if ( $config['enable_trial'] ) : ?>
                    <input type="hidden" name="a1" value="0">
                    <input type="hidden" name="p1" value="<?php echo $config['trial_days']; ?>">
                    <input type="hidden" name="t1" value="D">
                    <?php endif; ?>

                    <input type="hidden" name="a3" value=""> <!-- amount gets set dynamically -->
                    <input type="hidden" name="p3" value="<?php echo $config['subscription_interval']; ?>">
                    <input type="hidden" name="t3" value="M">
                    <input type="hidden" name="return" value="<?php echo url('process.php?action=paypal_subscription_success'); ?>">
                    <input type="hidden" name="cancel_return" value="<?php echo url('process.php?action=paypal_cancel'); ?>">
                    <input type="hidden" name="notify_url" value="<?php echo url('process.php?action=paypal_ipn'); ?>">
                </form>

            <?php endif; ?>

        <?php endif; ?>
            </div>
        </div>
	</div>


    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <?php template('javascript-includes'); ?>
</body>
</html>
<?php require 'assets/libs/cartman/close.php'; ?>