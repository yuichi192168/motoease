  <?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en">
  <?php require_once('inc/header.php') ?>
  <body>
    <script>start_loader()</script>

    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      body {
        background-image: url('<?= validate_image($_settings->info('cover')) ?>');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
      }

      .overlay {
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 0;
      }

      .register-wrapper {
        z-index: 1;
        position: relative;
        min-height: 100vh;
      }

      #logo-img {
        width: 120px;
        height: 120px;
        object-fit: contain;
      }

      .pass_type {
        cursor: pointer;
      }

      .form-control::placeholder {
        font-size: 0.85rem;
      }

      small {
        font-size: 0.75rem;
      }

      .form-section-title {
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 1rem;
      }

    </style>

    <div class="overlay"></div>
    <div class="container-fluid d-flex align-items-center justify-content-center register-wrapper">
      <div class="row w-100">
        <div class="col-lg-5 text-center text-white mb-4 mb-lg-0 d-flex flex-column justify-content-center align-items-center">
          <img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" class="img-thumbnail rounded-circle mb-3" id="logo-img">
          <h3 class="text-light">Welcome to <?= $_settings->info('name') ?></h3>
        </div>

        <div class="col-lg-7 d-flex align-items-center justify-content-center">
          <div class="card shadow rounded-lg w-100" style="max-width: 650px;">
            <div class="card-header bg-primary text-white text-center">
              <h5 class="mb-0">Create an Account</h5>
            </div>
            <div class="card-body">
              <form id="register-frm" method="post">
                <input type="hidden" name="id">

                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="firstname" class="small">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control form-control-sm" placeholder="Enter First Name" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="middlename" class="small">Middle Name</label>
                    <input type="text" name="middlename" id="middlename" class="form-control form-control-sm" placeholder="Middle Name (Optional)">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="lastname" class="small">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control form-control-sm" placeholder="Enter Last Name" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="gender" class="small">Gender</label>
                    <select name="gender" id="gender" class="custom-select custom-select-sm" required>
                      <option selected disabled>-- Select Gender --</option>
                      <option>Male</option>
                      <option>Female</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="contact" class="small">Contact #</label>
                    <input type="text" name="contact" id="contact" class="form-control form-control-sm" placeholder="09xxxxxxxxx" required>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="address" class="small">Address</label>
                    <textarea name="address" id="address" rows="2" class="form-control form-control-sm" placeholder="Blk 8 Lot 88, Mabuhay Mamatid, Cabuyao City, Laguna, 4025"></textarea>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="email" class="small">Email</label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="jsmith@sample.com" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="password" class="small">Password</label>
                    <div class="input-group">
                      <input type="password" name="password" id="password" class="form-control form-control-sm" required>
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="cpassword" class="small">Confirm Password</label>
                    <div class="input-group">
                      <input type="password" id="cpassword" class="form-control form-control-sm" required>
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                  <a href="<?= base_url ?>" class="text-muted">← Back to Shop</a>
                  <button type="submit" class="btn btn-primary btn-sm px-4">Register</button>
                </div>

                <div class="text-center mt-2">
                  <a href="<?= base_url.'login.php' ?>" class="small">Already have an account? Login here</a>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts remain the same -->
    <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
      $(document).ready(function(){
        end_loader();
        $('.pass_type').click(function(){
          var type = $(this).attr('data-type');
          var input = $(this).closest('.input-group').find('input');
          if(type === 'password'){
            $(this).attr('data-type', 'text');
            input.attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
          } else {
            $(this).attr('data-type', 'password');
            input.attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
          }
        });

        $('#register-frm').submit(function(e){
          e.preventDefault();
          var _this = $(this);
          $('.err-msg').remove();
          var el = $('<div>').hide();

          if($('#password').val() != $('#cpassword').val()){
            el.addClass('alert alert-danger err-msg').text('Password does not match.');
            _this.prepend(el);
            el.show('slow');
            return false;
          }

          start_loader();
          $.ajax({
            url: _base_url_ + "classes/Users.php?f=save_client",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
              console.log(err);
              alert_toast("An error occurred", 'error');
              end_loader();
            },
            success: function(resp){
              if(typeof resp === 'object' && resp.status === 'success'){
                location.href = "./login.php";
              } else if(resp.status === 'failed' && !!resp.msg){
                el.addClass("alert alert-danger err-msg").text(resp.msg);
                _this.prepend(el);
                el.show('slow');
              } else {
                alert_toast("An error occurred", 'error');
                end_loader();
              }
              $('html, body').scrollTop(0);
            }
          });
        });
      });
    </script>
  </body>
</html>
