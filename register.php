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
        font-family: "Segoe UI", sans-serif;
      }

      body {
        width:calc(100%);
        height:calc(100%);
        background-image:url('<?= validate_image($_settings->info('cover')) ?>');
        background-repeat: no-repeat;
        background-size:cover;
        background-position:center center;
        background-attachment:fixed;
        background-color:#111; /* fallback */
      }

      .overlay {
        background: rgba(0,0,0,0.6);
        position: absolute;
        top:0; left:0;
        width:100%; height:100%;
        z-index:0;
      }

      .register-wrapper {
        z-index:1;
        position: relative;
        min-height: 100vh;
      }

      #logo-img {
        width: 120px;
        height: 120px;
        object-fit: contain;
        border: 3px solid #fff;
        padding: 5px;
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

      .card {
        border-radius: 10px;
        border: 2px solid #dc3545;
        backdrop-filter: blur(2px);
        background: rgba(255,255,255,0.92);
      }

      /* Keep scrolling inside the form only */
      .register-wrapper .card-body {
        max-height: 70vh;
        overflow: auto;
      }

      .input-group-text {
        cursor: pointer;
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
      }

      .error-msg {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: 0.25rem;
      }

      /* Red and Black Theme */
      .btn-primary {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        font-weight: 600;
      }

      .btn-primary:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
      }

      .card-header.bg-primary {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border-bottom: 2px solid #a71e2a;
      }

      .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
      }

      .custom-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
      }

      .custom-file-input:focus ~ .custom-file-label {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
      }

      .is-valid {
        border-color: #28a745;
      }

      .is-invalid {
        border-color: #dc3545;
      }

      .text-primary {
        color: #dc3545 !important;
      }

      a.text-primary:hover {
        color: #c82333 !important;
      }

      @media(max-width:767px){
        .register-wrapper .row { flex-direction: column; }
        .card { margin-top: 20px; }
        
        /* Mobile form improvements */
        .container-fluid {
          padding: 10px;
        }
        
        .card {
          margin: 10px;
          max-width: 100%;
        }
        
        .card-body {
          padding: 15px;
        }
        
        .form-group {
          margin-bottom: 15px;
        }
        
        .form-control, .custom-select {
          font-size: 16px; /* Prevents zoom on iOS */
          padding: 8px 12px;
        }
        
        .btn {
          padding: 10px 20px;
          font-size: 16px;
        }
        
        /* Stack form elements vertically on mobile */
        .row .col-md-6 {
          flex: 0 0 100%;
          max-width: 100%;
        }
        
        /* Adjust logo size for mobile */
        #logo-img {
          width: 80px;
          height: 80px;
        }
        
        /* Better spacing for mobile */
        .text-center h3 {
          font-size: 1.5rem;
        }
        
        .text-center p {
          font-size: 0.9rem;
        }
      }
      
      @media(max-width:576px){
        .register-wrapper {
          padding: 10px;
        }
        
        .card {
          margin: 5px;
        }
        
        .card-body {
          padding: 10px;
        }
        
        .form-control, .custom-select {
          font-size: 16px;
          padding: 10px;
        }
        
        .btn {
          width: 100%;
          margin-top: 10px;
        }
        
        /* Hide left panel on very small screens */
        .col-lg-5 {
          display: none;
        }
        
        .col-lg-7 {
          flex: 0 0 100%;
          max-width: 100%;
        }
      }
    </style>

    <div class="overlay"></div>
    <div class="container-fluid d-flex align-items-center justify-content-center register-wrapper">
      <div class="row w-100">
        <!-- Left Info Panel -->
        <div class="col-lg-5 text-center text-white mb-4 mb-lg-0 d-flex flex-column justify-content-center align-items-center">
          <img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" class="img-thumbnail rounded-circle mb-3" id="logo-img">
          <h3 class="text-light">Welcome to <?= $_settings->info('name') ?></h3>
          <p class="text-light small">Register to get started. All fields with * are required.</p>
        </div>

        <!-- Registration Form -->
        <div class="col-lg-7 d-flex align-items-center justify-content-center">
          <div class="card shadow rounded-lg w-100" style="max-width: 650px;">
            <div class="card-header bg-primary text-white text-center">
              <h5 class="mb-0">Create an Account</h5>
            </div>
            <div class="card-body">
              <form id="register-frm" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="id">

                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="firstname" class="small">First Name *</label>
                    <input type="text" name="firstname" id="firstname" class="form-control form-control-sm" placeholder="Enter First Name" required>
                    <div class="error-msg" id="firstname-error"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="middlename" class="small">Middle Name</label>
                    <input type="text" name="middlename" id="middlename" class="form-control form-control-sm" placeholder="Middle Name (Optional)">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="lastname" class="small">Last Name *</label>
                    <input type="text" name="lastname" id="lastname" class="form-control form-control-sm" placeholder="Enter Last Name" required>
                    <div class="error-msg" id="lastname-error"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="gender" class="small">Gender *</label>
                    <select name="gender" id="gender" class="custom-select custom-select-sm" required>
                      <option selected disabled>-- Select Gender --</option>
                      <option>Male</option>
                      <option>Female</option>
                    </select>
                    <div class="error-msg" id="gender-error"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="contact" class="small">Contact # *</label>
                    <input type="text" name="contact" id="contact" class="form-control form-control-sm" placeholder="09xxxxxxxxx" pattern="09[0-9]{9}" required>
                    <div class="error-msg" id="contact-error"></div>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="address" class="small">Address</label>
                    <textarea name="address" id="address" rows="2" class="form-control form-control-sm" placeholder="Blk 8 Lot 88, Mabuhay Mamatid, Cabuyao City, Laguna, 4025"></textarea>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="email" class="small">Email *</label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="jsmith@sample.com" required>
                    <div class="error-msg" id="email-error"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="avatar" class="small">Profile Picture</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="avatar" name="img" onchange="displayImg(this,$(this))" accept="image/*">
                      <label class="custom-file-label" for="avatar">Choose file</label>
                    </div>
                    <small class="text-muted">Optional - Upload your profile picture</small>
                  </div>
                  <div class="form-group col-md-6 d-flex justify-content-center">
                    <img src="assets/img/no-image-available.png" alt="Avatar Preview" id="cimg" class="img-fluid img-thumbnail" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%;">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="password" class="small">Password *</label>
                    <div class="input-group">
                      <input type="password" name="password" id="password" class="form-control form-control-sm" required>
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                      </div>
                    </div>
                    <small>Password must be at least 6 characters</small>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="cpassword" class="small">Confirm Password *</label>
                    <div class="input-group">
                      <input type="password" id="cpassword" class="form-control form-control-sm" required>
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row align-items-center mt-3">
                  <div class="col-6 pr-1 mb-2">
                    <a href="<?= base_url ?>" class="btn btn-outline-secondary btn-block"><i class="fa fa-store mr-1"></i> Back to Shop</a>
                  </div>
                  <div class="col-6 pl-1 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-flat btn-block"><i class="fa fa-user-check mr-1"></i> Register</button>
                  </div>
                </div>

                <div class="row mt-1">
                  <div class="col-12 mb-2">
                    <a href="<?= base_url.'login.php' ?>" class="btn btn-outline-primary btn-block"><i class="fa fa-sign-in-alt mr-1"></i> Already have an account? Login here</a>
                  </div>
                  <!-- <div class="col-12 text-right">
                    <a href="forgot-password.html" class="btn btn-link p-0">I forgot my password</a>
                  </div> -->
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function(){

  // End loader
  end_loader();

  // Password toggle
  $('.pass_type').click(function(){
    var type = $(this).attr('data-type');
    var input = $(this).closest('.input-group').find('input');
    if(type === 'password'){
      $(this).attr('data-type','text');
      input.attr('type','text');
      $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      $(this).attr('data-type','password');
      input.attr('type','password');
      $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  // Real-time validation functions
  function validateField(id, condition, msg){
    var input = $(id);
    if(!condition(input.val())){
      input.addClass('is-invalid').removeClass('is-valid');
      input.next('.error-msg').text(msg);
      return false;
    } else {
      input.addClass('is-valid').removeClass('is-invalid');
      input.next('.error-msg').text('');
      return true;
    }
  }

  $('#firstname').on('input', function(){
    validateField('#firstname', val => val.trim().length > 0, 'First name is required.');
  });

  $('#lastname').on('input', function(){
    validateField('#lastname', val => val.trim().length > 0, 'Last name is required.');
  });

  $('#gender').on('change', function(){
    validateField('#gender', val => val != null && val != '', 'Select gender.');
  });

  $('#contact').on('input', function(){
    validateField('#contact', val => /^09[0-9]{9}$/.test(val), 'Enter a valid 11-digit mobile number starting with 09.');
  });

  $('#email').on('input', function(){
    validateField('#email', val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val), 'Enter a valid email.');
  });

  $('#password').on('input', function(){
    validateField('#password', val => val.length >= 6, 'Password must be at least 6 characters.');
  });

  $('#cpassword').on('input', function(){
    validateField('#cpassword', val => val === $('#password').val(), 'Passwords do not match.');
  });

  // Add click handler for submit button
  $('button[type="submit"]').click(function(e){
    console.log('Submit button clicked!'); // Debug log
    $('#register-frm').submit();
  });

  // Submit form
  $('#register-frm').submit(function(e){
    e.preventDefault();
    console.log('Form submitted!'); // Debug log
    var _this = $(this);
    var el = $('<div>');
    el.addClass("alert alert-danger err-msg");
    el.hide();
    
    $('.err-msg').remove();
    $('.error-msg').text('');

    // Check all fields
    var valid = true;
    valid &= validateField('#firstname', val => val.trim().length > 0, 'First name is required.');
    valid &= validateField('#lastname', val => val.trim().length > 0, 'Last name is required.');
    valid &= validateField('#gender', val => val != null && val != '', 'Select gender.');
    valid &= validateField('#contact', val => /^09[0-9]{9}$/.test(val), 'Enter a valid 11-digit mobile number starting with 09.');
    valid &= validateField('#email', val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val), 'Enter a valid email.');
    valid &= validateField('#password', val => val.length >= 6, 'Password must be at least 6 characters.');
    valid &= validateField('#cpassword', val => val === $('#password').val(), 'Passwords do not match.');

    if(!valid) return false;

    start_loader();
    
    // Prepare form data
    var formData = new FormData($(this)[0]);
    
    $.ajax({
      url: _base_url_ + "classes/Users.php?f=save_client",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      method: 'POST',
      type: 'POST',
      dataType: 'json',
      beforeSend: function(){
        _this.find('button[type="submit"]').prop('disabled', true).html('Registering...');
      },
      error: function(xhr, status, error) {
        console.error('Registration error:', error);
        el.text('An error occurred. Please try again.');
        _this.prepend(el);
        el.show('slow');
        _this.find('button[type="submit"]').prop('disabled', false).html('Register');
        end_loader();
      },
      success: function(resp){
        console.log('Registration Response:', resp);
        if(typeof resp === 'object' && resp.status === 'success'){
          if(resp.auto_login && resp.user_id){
            // Auto-login the user after registration
            alert_toast("Registration successful! Logging you in...", 'success');
            setTimeout(function(){
              // Perform auto-login
              $.ajax({
                url: _base_url_ + 'classes/Login.php?f=login_client',
                method: 'POST',
                data: {
                  email: $('#email').val(),
                  password: $('#password').val()
                },
                dataType: 'json',
                success: function(loginResp){
                  if(loginResp.status === 'success'){
                    location.href = "./";
                  } else {
                    alert_toast("Registration successful! Please login manually.", 'success');
                    location.href = "./login.php";
                  }
                },
                error: function(){
                  alert_toast("Registration successful! Please login manually.", 'success');
                  location.href = "./login.php";
                }
              });
            }, 1000);
          } else {
            alert_toast("Registration successful! Redirecting to login...", 'success');
            setTimeout(function(){
              location.href = "./login.php";
            }, 2000);
          }
        } else if(resp.status == 'failed' && !!resp.msg){
          el.text(resp.msg);
          _this.prepend(el);
          el.show('slow');
        } else {
          console.log('Unexpected response:', resp);
          el.text('An unexpected error occurred. Please try again.');
          _this.prepend(el);
          el.show('slow');
        }
        _this.find('button[type="submit"]').prop('disabled', false).html('Register');
        end_loader();
        $('html, body').scrollTop(0);
      }
    });

  });

});
</script>

  </body>
</html>
