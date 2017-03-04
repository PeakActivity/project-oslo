<div class="container-fluid my-2" id="header">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <a href="/"><img src="domains/<?= $_SESSION['domain'] ?>/images/logo.png" border="0" /></a>
      </div>
      <div class="col-6 text-right">
        <span class="align-middle"><a href="#"><?= $_SESSION['username']?> <i class="fa fa-user"></i></a></span>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid bg-primary py-2">
  <div class="container">
    <div class="row">
      <div class="col-6">

        <nav class="navbar navbar-toggleable-md navbar-inverse bg-primary top-nav-bar">
          <button class="navbar-toggler navbar-toggler-left" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <ul class="navbar-nav hidden-md-down">
              <?php if(($_SESSION['type'] & 16) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link active" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
              </li>
              <?php } ?>
              <?php if(($_SESSION['type'] & 16) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link" href="manage-users.php">Manage Users</a>
              </li>
              <?php } ?>
          </ul>
          
        </nav>
        </div>
        <div class="col-6">
        <nav class="navbar navbar-inverse bg-primary top-nav-bar pull-right">
          <ul class="navbar-nav">
            <li class="nav-item">
              <?php if(!isset($_SESSION['username'])){ ?>
                <a class="nav-link" href="login.php">Log in</a>
              <?php } else { ?>
                <a class="nav-link" href="../user/logout.php">Log out</a>
              <?php } ?>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <nav class="navbar navbar-inverse bg-primary top-nav-bar">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
            <?php if(($_SESSION['type'] & 16) > 0) { ?>
            <li class="nav-item">
              <a class="nav-link active" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
            </li>
            <?php } ?>
            <?php if(($_SESSION['type'] & 16) > 0) { ?>
            <li class="nav-item">
              <a class="nav-link" href="manage-users.php">Manage Users</a>
            </li>
            <?php } ?>
        </ul>
      </div>
    </nav>
  </div>
</div>