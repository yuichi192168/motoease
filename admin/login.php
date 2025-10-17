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
          background-position:center center;
          background-attachment:fixed;
          background-color:#111; /* fallback */
      }
      .login-box{
          width:100%;
          max-width:420px;
          margin:40px auto;
      }
      .card.card-outline.card-primary{
          backdrop-filter: blur(2px);
          background: rgba(255,255,255,0.92);
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
        <div class="row align-items-center">
          <div class="col-12 col-sm-6 pr-sm-1 mb-2">
            <a href="<?php echo base_url ?>" class="btn btn-outline-secondary btn-block"><i class="fa fa-store mr-1"></i> Back to Shop</a>
          </div>
          <div class="col-12 col-sm-6 pl-sm-1 mb-2">
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in-alt mr-1"></i> Sign In</button>
          </div>
        </div>
      </form>
      <!-- /.social-auth-links -->

      <p class="mb-1 text-center">
        <a href="../forgot_password.php" class="btn btn-link p-0">I forgot my password</a>
      </p>
      
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
      // prevent double submissions
      if(_this.data('submitting') === true) return;
      _this.data('submitting', true);

      // single reusable error container
      var $err = _this.find('.err-msg');
      if($err.length === 0){
        $err = $('<div>').addClass('alert alert-danger err-msg').hide();
        _this.prepend($err);
      }
      $err.text('').removeClass('alert-warning').addClass('alert-danger').hide();

      _this.find('.btn-primary').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing In...');
      
      $.ajax({
        url: '../classes/Login.php?f=login',
        data: $(this).serialize(),
        method: 'POST',
        dataType: 'json',
        error: function(err) {
          console.log(err);
          $err.text('An error occurred. Please try again.').show('slow');
          _this.find('.btn-primary').prop('disabled', false).html('Sign In');
          _this.data('submitting', false);
        },
        success: function(resp) {
          if(resp.status == 'success') {
            location.replace('./');
          } else if(resp.status == 'locked') {
            $err.removeClass('alert-danger').addClass('alert-warning').html('<i class="fa fa-lock"></i> ' + (resp.msg || 'Account is locked.')).show('slow');
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
                    var base = $err.data('base') || $err.text();
                    $err.data('base', base);
                    $err.text(base.replace(/(\s*\(\d{2}:\d{2}\))?$/, '') + ' ('+mm+':'+ss+')');
                    if (remaining <= 0) {
                      clearInterval(timer);
                      $btn.prop('disabled', false).text('Sign In');
                      $err.hide();
                    }
                  }, 1000);
                } catch(e) { console.log(e); }
              })();
            }
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
            _this.data('submitting', false);
          } else if(resp.status == 'incorrect') {
            $err.text('Incorrect username or password.').show('slow');
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
            _this.data('submitting', false);
          } else {
            $err.text(resp.msg || 'An error occurred. Please try again.').show('slow');
            _this.find('.btn-primary').prop('disabled', false).html('Sign In');
            _this.data('submitting', false);
          }
        }
      });
    });
  });
</script>
</body>
</html>