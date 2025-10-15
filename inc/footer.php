<script>
  $(document).ready(function(){
    $('#p_use').click(function(){
      uni_modal("Privacy Policy","policy.php","mid-large")
    })
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container">
        <div class="row">
            <!-- Company Information -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-white mb-3">
                    <i class="fas fa-motorcycle text-primary"></i> Star Honda Calamba
                </h5>
                <p class="text-white-50 mb-3">
                    Your trusted motorcycle dealer in Calamba City, Laguna. We offer quality Honda motorcycles with flexible payment options.
                </p>
                <div class="text-white-50">
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary"></i> 
                        National Highway Brgy. Parian, Calamba City, Laguna
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone text-primary"></i> 
                        <a href="tel:09482353207" class="text-white-50">0948-235-3207</a>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-envelope text-primary"></i> 
                        <a href="mailto:starhondacalamba55@gmail.com" class="text-white-50">starhondacalamba55@gmail.com</a>
                    </p>
                    <p class="mb-2">
                        <i class="fab fa-facebook text-primary"></i> 
                        <a href="https://www.facebook.com/starhondacalambabranch" target="_blank" class="text-white-50">@starhondacalambabranch</a>
                    </p>
                </div>
            </div>

            <!-- Motorcycle Purchase Requirements -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-white mb-3">
                    <i class="fas fa-clipboard-list text-primary"></i> Purchase Requirements
                </h5>
                
                <div class="mb-3">
                    <h6 class="text-white mb-2">
                        <i class="fas fa-money-bill-wave text-success"></i> For Cash Purchase:
                    </h6>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-check text-success"></i> Valid government ID</li>
                        <li><i class="fas fa-check text-success"></i> Proof of address</li>
                        <li><i class="fas fa-check text-success"></i> Filled-out buyer's information form</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="text-white mb-2">
                        <i class="fas fa-credit-card text-warning"></i> For Installment:
                    </h6>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-check text-success"></i> 2 valid government IDs</li>
                        <li><i class="fas fa-check text-success"></i> Proof of income (Payslip / COE / Bank Statement)</li>
                        <li><i class="fas fa-check text-success"></i> Proof of billing</li>
                        <li><i class="fas fa-check text-success"></i> Application form</li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links & Application -->
            <div class="col-lg-4 col-md-12 mb-4">
                <h5 class="text-white mb-3">
                    <i class="fas fa-link text-primary"></i> Quick Links
                </h5>
                
                <div class="mb-3">
                    <button onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to apply for installment.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } window.open('https://form.jotform.com/242488642552463', '_blank');" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-file-alt"></i> Apply for Installment
                    </button>
                    <a href="./?p=products" class="btn btn-outline-light btn-block mb-2">
                        <i class="fas fa-motorcycle"></i> Browse Motorcycles
                    </a>
                    <a href="./?p=services" class="btn btn-outline-light btn-block mb-2">
                        <i class="fas fa-tools"></i> Our Services
                    </a>
                    <button onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to book an appointment.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } window.location.href='./?p=appointments';" class="btn btn-outline-light btn-block mb-2">
                        <i class="fas fa-calendar"></i> Book Appointment
                    </button>
                </div>

                <div class="text-center">
                    <h6 class="text-white mb-2">Follow Us</h6>
                    <a href="https://www.facebook.com/starhondacalambabranch" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 bg-light">

        <!-- Copyright & Additional Info -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="m-0 text-white-50">
                    Copyright &copy; <?php echo $_settings->info('short_name') ?> 2025. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-right">
                <p class="m-0 text-white-50">
                    <i class="fas fa-shield-alt text-primary"></i> 
                    Your information is treated with confidentiality
                </p>
            </div>
        </div>
    </div>
</footer>

   
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
    <div class="daterangepicker ltr show-ranges opensright">
      <div class="ranges">
        <ul>
          <li data-range-key="Today">Today</li>
          <li data-range-key="Yesterday">Yesterday</li>
          <li data-range-key="Last 7 Days">Last 7 Days</li>
          <li data-range-key="Last 30 Days">Last 30 Days</li>
          <li data-range-key="This Month">This Month</li>
          <li data-range-key="Last Month">Last Month</li>
          <li data-range-key="Custom Range">Custom Range</li>
        </ul>
      </div>
      <div class="drp-calendar left">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-calendar right">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
    <div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;">Idaho</div>
    
    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="btn btn-primary" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: none;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: none;
        background: linear-gradient(135deg, #dc3545, #c82333);
        transition: all 0.3s ease;
    " title="Scroll to Top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <style>
        #scrollToTop:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            background: linear-gradient(135deg, #c82333, #a71e2a);
        }
        
        #scrollToTop.show {
            display: block !important;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Show/hide scroll to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#scrollToTop').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                }
            });
            
            // Smooth scroll to top
            $('#scrollToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });
        });
    </script>