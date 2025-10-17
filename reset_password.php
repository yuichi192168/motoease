<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>start_loader()</script>
  <style>
      body{
          width:calc(100%);
          height:calc(100%);
          background-image:url('<?= validate_image($_settings->info('cover')) ?>');
          background-repeat:no-repeat;
          background-size:cover;
          background-position:center;
          background-attachment:fixed;
          background-color:#111;
      }
      .login-box{ width:100%; max-width:420px; margin:40px auto; }
      .card.card-outline.card-primary{ backdrop-filter: blur(2px); background: rgba(255,255,255,0.92); }
  </style>
  <div class="login-box">
    <center><img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img" style="width:120px;height:120px;object-fit:contain"></center>
    <div class="clear-fix my-2"></div>
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="./" class="h1 text-decoration-none"><b>Reset Password</b></a>
      </div>
      <div class="card-body">
        <?php $token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>
        <p class="login-box-msg">Set your new password.</p>
        <form id="reset-frm" method="post">
          <input type="hidden" name="token" value="<?= $token ?>">
          <div class="form-group mb-3">
            <label class="small">New Password</label>
            <div class="input-group">
              <input type="password" class="form-control" name="password" id="password" minlength="6" required>
              <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-eye-slash pass_toggle" data-target="#password"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label class="small">Confirm Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="cpassword" minlength="6" required>
              <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-eye-slash pass_toggle" data-target="#cpassword"></i></span>
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-6 pr-1 mb-2">
              <a href="<?= base_url.'login.php' ?>" class="btn btn-outline-secondary btn-block"><i class="fa fa-arrow-left mr-1"></i> Back to Login</a>
            </div>
            <div class="col-6 pl-1 mb-2">
              <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save mr-1"></i> Save Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    $(function(){
      end_loader();
      $('.pass_toggle').click(function(){
        var target = $($(this).data('target'));
        var type = target.attr('type') === 'password' ? 'text' : 'password';
        target.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
      });
      $('#reset-frm').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        var $err = _this.find('.err-msg');
        if($err.length === 0){ $err = $('<div>').addClass('alert err-msg').hide(); _this.prepend($err); }
        $err.removeClass('alert-danger alert-success').text('').hide();
        if($('#password').val() !== $('#cpassword').val()){
          $err.addClass('alert-danger').text('Passwords do not match.').show('slow');
          return;
        }
        _this.find('button[type="submit"]').prop('disabled', true).text('Saving...');
        $.ajax({
          url: _base_url_ + 'classes/Login.php?f=apply_password_reset',
          method: 'POST',
          data: _this.serialize(),
          dataType: 'json',
          error: function(){
            $err.addClass('alert-danger').text('An error occurred. Please try again.').show('slow');
            _this.find('button[type="submit"]').prop('disabled', false).text('Save Password');
          },
          success: function(resp){
            if(resp.status === 'success'){
              $err.addClass('alert-success').text(resp.msg || 'Password updated. You can now log in.').show('slow');
              setTimeout(function(){ window.location.href = '<?= base_url.'login.php' ?>'; }, 1500);
            } else {
              $err.addClass('alert-danger').text(resp.msg || 'Invalid or expired token.').show('slow');
              _this.find('button[type="submit"]').prop('disabled', false).text('Save Password');
            }
          }
        });
      });
    });
  </script>
</body>
</html>

