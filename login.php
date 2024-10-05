<?php
require_once('inc/helpers.php');
require_once('inc/app_settings.php');
$helpers = new Helpers();

if($helpers->checkSession()) {
    header('Location: /');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SLSU-HC Exam</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/css/parsley.css">
    <!-- End layout styles -->    
    <script src="<?php echo BASE_URL ?>/assets/js/jquery-3.7.1.min.js"></script>
    <link rel="shortcut icon" href="<?php echo BASE_URL ?>/assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-left p-5">
                    <!-- <div class="brand-logo">
                        <img src="<?php echo BASE_URL ?>/assets/images/logo.svg">
                    </div> -->
                    <h3>SLSU-HC iTest Hub</h3>
                    <h6 class="font-weight-light">Sign in to continue.</h6>
                    <form id="frmuser" class="pt-3" method="post" data-parsley-validate="">
                        <div class="error-message"></div>
                        <div class="form-group">
                            <input class="form-control form-control-lg" id="username" name="username" placeholder="Username" data-parsley-required="" data-parsley-required-message="Username is required." data-parsley-errors-container="#username-error">
                        <div id="username-error"></div>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" data-parsley-required="" data-parsley-required-message="Password is required." data-parsley-errors-container="#password-error">
                            <div id="password-error"></div>
                        </div>
                        <div class="mt-3 d-grid gap-2">
                            <button type="button" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" id="btn-login">SIGN IN</button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <script>
        $(document).ready(function(){
            $('#btn-login').click(function(){

                if(!$('form#frmuser').parsley().validate()) {
                    return;
                }
                var msg = $('.error-message');
                $.ajax({
                    url : 'api/login.php',
                    type : 'post',
                    data : $('#frmuser').serialize(),
                    success : function(data) {
                        var json = $.parseJSON(data);

                        if(json['code'] == 0) {
                            msg.html('<div class="alert alert-success">'+ json['message'] +'</div>');
                            location.href="/";
                        } else {
                            msg.html('<div class="alert alert-danger">'+ json['message'] +'</div>');
                        }
                    }
                })
                return false;
            })

            $("#password").keyup(function(event){	
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    $('#btn-login').trigger('click');
                }
            })

            $("#username").keyup(function(event){	
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    $('#btn-login').trigger('click');
                }
            })
        })
    </script>
    <!-- plugins:js -->
    <script src="<?php echo BASE_URL ?>/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?php echo BASE_URL ?>/assets/js/off-canvas.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/misc.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/settings.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/todolist.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/jquery.cookie.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/parsley.js"></script>
    <!-- endinject -->
  </body>
</html>