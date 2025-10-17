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
        <a href="./" class="h1 text-decoration-none"><b>Forgot Password</b></a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Enter your email to receive a password reset link.</p>
        <form id="forgot-frm" method="post">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="lastname" placeholder="Registered Last Name" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-id-card"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="contact_last4" placeholder="Last 4 digits of your contact #" pattern="[0-9]{4}" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-phone"></span>
              </div>
            </div>
          </div>
          <div class="alert alert-info small" id="token-hint" style="display:none"></div>
          <div class="row align-items-center">
            <div class="col-6 pr-1 mb-2">
              <a href="<?= base_url.'login.php' ?>" class="btn btn-outline-secondary btn-block"><i class="fa fa-arrow-left mr-1"></i> Back to Login</a>
            </div>
            <div class="col-6 pl-1 mb-2">
              <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-paper-plane mr-1"></i> Send Link</button>
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
      $('#forgot-frm').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        var $err = _this.find('.err-msg');
        if($err.length === 0){ $err = $('<div>').addClass('alert err-msg').hide(); _this.prepend($err); }
        $err.removeClass('alert-danger alert-success').text('').hide();
        _this.find('button[type="submit"]').prop('disabled', true).text('Sending...');
        $.ajax({
          url: _base_url_ + 'classes/Login.php?f=reset_password',
          method: 'POST',
          data: _this.serialize(),
          dataType: 'json',
          error: function(){
            $err.addClass('alert-danger').text('An error occurred. Please try again.').show('slow');
            _this.find('button[type="submit"]').prop('disabled', false).text('Send Link');
          },
          success: function(resp){
            var link = resp.reset_link || '';
            var token = resp.token || '';
            var msg = resp.msg || 'If the details match our records, a reset option is now available.';
            $err.addClass('alert-success').text(msg).show('slow');
            if(link || token){
              var html = '';
              if(link){ html += 'Reset Link: <a href="'+link+'">'+link+'</a><br>'; }
              if(token){ html += 'Token: <code>'+token+'</code>'; }
              $('#token-hint').html(html).show();
            } else {
              $('#token-hint').hide().empty();
            }
            _this.find('button[type="submit"]').prop('disabled', false).text('Generate Again');
          }
        });
      });
    });
  </script>
</body>
</html>

