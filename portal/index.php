<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']);
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Welcome to Project Oslo</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <?php if(isset($_SESSION['domain'])) { ?>
        <link rel="stylesheet" href="domains/<?=$_SESSION['domain']?>/css/portal.css" /> 
    <?php } else { ?>
        <link rel="stylesheet" href="domains/<?php echo(ExtractSubdomains($_SERVER['HTTP_HOST'])); ?>/css/portal.css" /> 
    <?php } ?>

</head>

<body>
	<?php include('includes/topbar.php'); ?>
	
    <div class="container py-3 mb-5" id="body-container">
        <?php 
            $aCarouselItems = getCarouselItems(ExtractSubdomains($_SERVER['HTTP_HOST']));
            if(count($aCarouselItems) > 0) { ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success" id="success-alert">
                  <button type="button" class="close" data-dismiss="alert">x</button>
                  <strong>Success! </strong> the item(s) have been added to your Shopping Cart.
                </div>
                <div class="alert alert-danger" id="error-alert">
                  <button type="button" class="close" data-dismiss="alert">x</button>
                  <strong>Error! </strong> There was a problem adding the item(s) to your Shopping Cart.
                </div>
                <div class="card">
                    <div class="card-block">
                        <div id="promotedCarousel" class="carousel slide" data-ride="carousel">                
                            <ol class="carousel-indicators">
                                <?php for($i=0; $i<count($aCarouselItems); $i++) { ?>
                                    <li data-target="#promotedCarousel" data-slide-to="<?= $i ?>" <?php if($i == 0){ ?> class="active"> <?php } ?></li>
                                <?php } ?>
                            </ol>
                            <div class="carousel-inner" role="listbox">
                            <?php for($i=0; $i<count($aCarouselItems); $i++) { ?>
                                <?php if($i ==0) { ?>
                                <div class="carousel-item active">
                                <?php } else { ?>
                                <div class="carousel-item">
                                <?php } ?>
                                    <a href="<?= $aCarouselItems[$i]['link'] ?>"><img class="d-block img-fluid" src="domains/<?php echo(ExtractSubdomains($_SERVER['HTTP_HOST'])); ?>/images/carousel/<?= $aCarouselItems[$i]['file_name'] ?>" border="0" alt="<?= $aCarouselItems[$i]['slide_title'] ?>"></a>
                                
                                </div>
                                <? } ?>
                            </div>
                            <a class="carousel-control-prev" href="#promotedCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#promotedCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-block">
                        <div id="promotedCarousel" class="carousel slide" data-ride="carousel">                
                            <ol class="carousel-indicators">
                                <li data-target="#promotedCarousel" data-slide-to="1" class="active"></li>
                                <li data-target="#promotedCarousel" data-slide-to="2"></li>
                                <li data-target="#promotedCarousel" data-slide-to="0"></li>
                            </ol>
                            <div class="carousel-inner" role="listbox">
                                <div class="carousel-item active">
                                    <a href="/login.php"><img class="d-block img-fluid" src="/assets/images/default_slide1.jpg" border="0" alt="Create Products"></a>
                                </div>
                                <div class="carousel-item">
                                    <a href="/login.php"><img class="d-block img-fluid" src="/assets/images/default_slide2.jpg" border="0" alt="Invite Users"></a>
                                </div>
                                <div class="carousel-item">
                                    <a href="/login.php"><img class="d-block img-fluid" src="/assets/images/default_slide3.jpg" border="0" alt="Create Featured Items"></a>
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#promotedCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#promotedCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php 
            $aFeatured = getFeatured(ExtractSubdomains($_SERVER['HTTP_HOST']));
            if(count($aFeatured) > 0) { ?>
        <div class="row mt-5">
            <div class="col-12"><h3>Featured</h3></div>
            <?php foreach($aFeatured as $aProduct) { ?>
                <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                    <form name="<?= $aProduct['id'] ?>_form" id="<?= $aProduct['id'] ?>_form" role="form" action="assets/ajax/add_to_cart.php" method="post">
                        <div class="card featured-card">
                            <div class="card-block">
                                <a href="/product.php?id=<?= $aProduct['id'] ?>"><img class="featured-image img-fluid" border="0" src="domains/<?php echo(ExtractSubdomains($_SERVER['HTTP_HOST'])); ?>/images/products/<?= $aProduct['file_name'] ?>" alt="<?= $aProduct['name'] ?>"></a>
                                <h4 class="featured-product-name mt-2"><a href="/product.php?id=<?= $aProduct['id'] ?>"><?= $aProduct['name'] ?></a></h4>
                                <p><?php echo(StringConcat($aProduct['description'], 100)); ?></p>
                                <p class="my-2 text-muted">$<?= $aProduct['price'] ?> for <?= $aProduct['per_unit'] ?></p>
                                <p class="small-text">Category: <a class="my-2" href="/category.php?id=<?= $aProduct['category_id'] ?>"><?= $aProduct['category_name'] ?></a></p>
                                <hr />
                                <?php $aOptions = GetProductOptions($aProduct['id']); 
                                if(count($aOptions) > 0) { ?>
                                  <?php for($i=0;$i<count($aOptions);$i++){ ?>
                                  <select name="<?= $aOptions[$i][0]['option_key'] ?>" id="<?= $aOptions[$i][0]['option_key'] ?>" class="form-control mb-3" required="true">
                                    <option value="">-- Select --</option>
                                    <?php foreach ( $aOptions[$i] as $aOption ) : ?>
                                    <option value="<?php echo $aOption['option_value']; ?>"><?php echo $aOption['option_value']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php } ?>
                                <?php } ?>
                                <input type="text" name="product_qty" id="product_qty" class="form-control" placeholder="Enter Quantity" required="true">
                            </div>
                            <div class="card-footer featured-footer text-muted">
                                <input type="hidden" name="product_id" id="product_id" value="<?= $aProduct['id'] ?>">
                                <input type="hidden" name="product_file_name" id="product_file_name" value="<?= $aProduct['file_name'] ?>">
                                <input type="hidden" name="product_name" id="product_name" value="<?= $aProduct['name'] ?>">
                                <input type="hidden" name="product_brand" id="product_brand" value="<?= $aProduct['brand'] ?>">
                                <input type="hidden" name="product_code" id="product_code" value="<?= $aProduct['product_code'] ?>">
                                <input type="hidden" name="product_availability" id="product_availability" value="<?= $aProduct['availability'] ?>">
                                <input type="hidden" name="product_price" id="product_price" value="<?= $aProduct['price'] ?>">
                                <input type="hidden" name="product_per_unit" id="product_per_unit" value="<?= $aProduct['per_unit'] ?>">
                                <input type="hidden" name="product_important_info" id="product_important_info" value="<?= $aProduct['important_info'] ?>">
                                <div class="btn-group featured-footer-group text-muted" role="group">
                                    <button type="submit" class="btn btn-secondary feature-footer-cart-btn"><i class="fa fa-shopping-cart"></i> <span class="hidden-md-down">Add to Cart</span></button>
                                    <button type="button" class="btn btn-secondary " data-toggle="tooltip" title="" onclick="" data-original-title="Add to Wish List"><i class="fa fa-heart"></i></button>
                                    <button type="button" class="btn btn-secondary " data-toggle="tooltip" title="" onclick="" data-original-title="Compare this Product"><i class="fa fa-exchange"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    </div>

    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            // run test on initial page load
            resizeFeatured();
            $("#success-alert").hide();
            $("#error-alert").hide();

            // run test on resize of the window
            $(window).resize(resizeFeatured);
        });


        // Make all of the featured equal height
        function resizeFeatured() {
            var tallest = 0;
            $(".featured-card").each(function(i,e){
                var var_e = jQuery(e);
                tallest = (var_e.height() > tallest) ? var_e.height() : tallest;

            });
            $(".featured-card").height(tallest);
        }

        <?php foreach($aFeatured as $aProduct) { ?>
            $("#<?= $aProduct['id'] ?>_form").on('submit', function (e) {
              e.preventDefault();    
              $("#success-alert").hide();
              $("#error-alert").hide();

              $.ajax({
                type: 'post',
                url: 'assets/ajax/add_to_cart.php',
                data: $("#<?= $aProduct['id'] ?>_form").serialize(),
                success: function (data) {
                  if(data > 0){
                    $("#success-alert").alert();
                    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                      $("#success-alert").slideUp(500);
                    });
                    $("#cart_contents").text('('+data+')');
                  } else {
                    $("#error-alert").alert();
                    $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                      $("#error-alert").slideUp(500);
                    });
                  }
                },
                error: function (data) {
                   alert("Message: " + data);
                }
              });

            });
        
        <?php } ?>

    </script>
</body>
</html>