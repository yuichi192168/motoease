<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
  <style>
      body{
          width:calc(100%);
          height:calc(100%);
          background-image:url('<?= validate_image($_settings->info('cover')) ?>');
          background-repeat: no-repeat;
          background-size:cover;
      }
      #logo-img{
          width:15em;
          height:15em;
          object-fit:scale-down;
          object-position:center center;
      }
  </style>
<div class="login-box">
<center><img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img"></center>
  <div class="clear-fix my-2"></div>
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Admin Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" autofocus placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="<?php echo base_url ?>">Back to Shop</a>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

      <!-- <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- Scripts are already included via admin/inc/header.php. Avoid reloading jQuery/AdminLTE to preserve event handlers. -->

<script>
  $(document).ready(function(){
    end_loader();

    $('#login-frm').submit(function(e){
      e.preventDefault();
      var _this = $(this);
      
      _this.find('.btn-primary').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing In...');
      
      $.ajax({
        url: '../classes/Login.php?f=login',
        data: $(this).serialize(),
        method: 'POST',
        dataType: 'json',
        error: function(err) {
          console.log(err);
          alert('An error occurred.');
          _this.find('.btn-primary').prop('disabled', false).html('Sign In');
        },
        success: function(resp) {
          if(resp.status == 'success') {
            location.replace('./');
          } else if(resp.status == 'locked') {
            if ($('.err_msg').length > 0) $('.err_msg').remove();
            var el = $('<div class="alert alert-warning err_msg">');
            el.html('<i class="fa fa-lock"></i> ' + (resp.msg || 'Account is locked.'));
            $('#login-frm').prepend(el);
            // countdown if provided
            if (resp.locked_until_ts) {
              (function startLockCountdown(){
                try {
                  var $form = $('#login-frm');
                  var $btn = $form.find('.btn-primary');
                  var endMs = parseInt(resp.locked_until_ts, 10) * 1000;
                  $btn.prop('disabled', true).data('orig','Sign In').text('Locked');
                  var timer = setInterval(function(){
                    var remaining = Math.max(0, endMs - Date.now());
                    var total = Math.floor(remaining/1000);
                    var mm = String(Math.floor(total/60)).padStart(2,'0');
                    var ss = String(total%60).padStart(2,'0');
                    var base = el.data('base') || el.text();
                    el.data('base', base);
                    el.text(base.replace(/(\s*\(\d{2}:\d{2}\))?$/, '') + ' ('+mm+':'+ss+')');
                    if (remaining <= 0) {
                      clearInterval(timer);
                      $btn.prop('disabled', false).text('Sign In');
                      el.remove();
                    }
                  }, 1000);
                } catch(e) { console.log(e); }
              })();
            }
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
          } else if(resp.status == 'incorrect') {
            alert('Incorrect username or password.');
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
          } else {
            alert('An error occurred.');
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
          }
        }
      });
    });
  });
</script>
</body>
</html>