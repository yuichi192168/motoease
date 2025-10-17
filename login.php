<?php require_once('./config.php') ?>
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
          background-color:#111;
      }
      /* Constrain login box to avoid stretching on wide screens */
      .login-box{
          width:100%;
          max-width:420px; /* slightly wider for better spacing */
          margin:40px auto; /* center with some breathing room */
      }
      /* Improve card readability atop image backgrounds */
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
<?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
  <!-- /.login-logo -->
  <center><img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img"></center>
  <div class="clear-fix my-2"></div>
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="./" class="h1 text-decoration-none"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="clogin-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" autofocus placeholder="Email">
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
            <button type="submit" class="btn btn-primary btn-sm btn-flat btn-block"><i class="fa fa-sign-in-alt mr-1"></i> Sign In</button>
          </div>
        </div>
        <div class="row mt-1">
          <div class="col-12 mb-2">
            <a href="<?php echo base_url.'register.php' ?>" class="btn btn-outline-primary btn-block"><i class="fa fa-user-plus mr-1"></i> Create an Account</a>
          </div>
          <div class="col-12 text-center">
            <!-- <a href="<?php echo base_url.'login.php' ?>" class="btn btn-link p-0 mr-3">Already have an account? Login here</a> -->
            <a href="<?php echo base_url.'forgot_password.php' ?>" class="btn btn-link p-0">I forgot my password</a>
          </div>
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

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    
    // Handle login form submission
    $('#clogin-frm').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        // Prevent double submissions
        if(_this.data('submitting') === true) return;
        _this.data('submitting', true);

        // Remove any legacy/duplicate alerts to prevent stacking
        _this.find('.err_msg').remove();
        _this.find('.alert.alert-danger, .alert.alert-warning').not('.err-msg').remove();

        // Reuse a single error element to avoid duplicates
        var $err = _this.find('.err-msg');
        if($err.length === 0){
            $err = $('<div>').addClass('alert alert-danger err-msg').hide();
            _this.prepend($err);
        }
        $err.text('').removeClass('alert-warning').addClass('alert-danger').hide();
        
        $.ajax({
            url: _base_url_ + 'classes/Login.php?f=login_client',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function(){
                _this.find('button[type="submit"]').prop('disabled', true).html('Signing in...');
            },
            success: function(resp){
                console.log('Login response:', resp);
                if(resp.status == 'success'){
                    console.log('Login successful, redirecting...');
                    // Add a small delay to ensure session is written
                    setTimeout(function(){
                        location.href = './';
                    }, 100);
                } else if(resp.status == 'locked') {
                    // Show single warning container with optional countdown
                    $err.removeClass('alert-danger').addClass('alert-warning').html('<i class="fa fa-lock"></i> ' + (resp.msg || 'Account is locked.')).show('slow');
                    if(resp.locked_until_ts){
                        try{
                            var $btn = _this.find('button[type="submit"]');
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
                                if(remaining <= 0){
                                    clearInterval(timer);
                                    $btn.prop('disabled', false).text('Sign In');
                                    $err.hide();
                                }
                            }, 1000);
                        }catch(e){ console.log(e); }
                    }
                } else {
                    $err.text(resp.msg || 'Login failed. Please try again.').show('slow');
                }
                _this.find('button[type="submit"]').prop('disabled', false).html('Sign In');
                _this.data('submitting', false);
            },
            error: function(xhr, status, error) {
                console.error('Login error:', error);
                $err.text('An error occurred. Please try again.').show('slow');
                _this.find('button[type="submit"]').prop('disabled', false).html('Sign In');
                _this.data('submitting', false);
            }
        });
    });
  })
</script>
</body>
</html>