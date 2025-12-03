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
<footer class="bg-dark footer-responsive">
    <div class="container-fluid px-0">
        <!-- Main Footer Content -->
        <div class="row g-0 py-4">
            <!-- Left Column - Star Honda Calamba Info (40%) -->
            <div class="col-12 col-md-4 col-lg-4 px-3 px-md-4 border-right border-light border-opacity-10">
                <h5 class="text-white mb-3 fs-6">
                    <i class="fas fa-motorcycle text-danger"></i>&nbsp;Star Honda Calamba
                </h5>
                <p class="text-white-50 mb-3 small lh-sm">
                    <!-- Your trusted motorcycle dealer in Calamba City, Laguna. We offer quality Honda motorcycles with flexible payment options. -->
                </p>
                <div class="text-white-50 small">
                    <div class="mb-2 d-flex align-items-start">
                        <i class="fas fa-map-marker-alt text-danger mt-1 me-2"></i> 
                        <span>&nbsp;National Highway Brgy. Parian, Calamba City, Laguna</span>
                    </div>
                    <div class="mb-2 d-flex align-items-center">
                        <i class="fas fa-phone text-danger me-2"></i> 
                        <a href="tel:09482353207" class="text-white-50 text-decoration-none">0948-235-3207</a>
                    </div>
                    <div class="mb-2 d-flex align-items-center">
                        <i class="fas fa-envelope text-danger me-2"></i> 
                        <a href="mailto:starhondacalamba55@gmail.com" class="text-white-50 text-decoration-none">starhondacalamba55@gmail.com</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fab fa-facebook text-danger me-2"></i> 
                        <a href="https://www.facebook.com/starhondacalambabranch" target="_blank" class="text-white-50 text-decoration-none">&nbsp;&nbsp;&nbsp;starhondacalambabranch</a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Links & Social (60%) -->
            <div class="col-12 col-md-8 col-lg-8 px-3 px-md-4">
                <div class="row g-0">
                    <!-- Quick Links -->
                    <div class="col-12 col-md-6 col-lg-6 px-2 px-md-3">
                        <h5 class="text-white mb-3 fs-6">
                            <i class="fas fa-link text-danger"></i>&nbsp;Quick Links
                        </h5>
                        
                        <div class="d-flex flex-column gap-2">
                            <a href="./?p=products" class="text-white-50 text-decoration-none footer-link">
                                <i class="fas fa-motorcycle text-danger me-2"></i>&nbsp;&nbsp;Browse Products
                            </a>
                            <a href="./?p=services" class="text-white-50 text-decoration-none footer-link">
                                <i class="fas fa-tools text-danger me-2"></i>&nbsp;&nbsp;&nbsp;Our Services
                            </a>
                            <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                            <a href="javascript:void(0);" onclick="window.open('https://form.jotform.com/242488642552463', '_blank');" class="text-white-50 text-decoration-none footer-link">
                                <i class="fas fa-file-alt text-danger me-2"></i>&nbsp;&nbsp;&nbsp;&nbsp;Apply for Installment
                            </a>
                            <!-- <a href="./?p=appointments" class="text-white-50 text-decoration-none footer-link">
                                <i class="fas fa-calendar text-danger me-2"></i>&nbsp;&nbsp;&nbsp;Book Appointment
                            </a> -->
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Social Media & Support -->
                    <div class="col-12 col-md-6 col-lg-6 px-2 px-md-3">
                        <h5 class="text-white mb-3 fs-6">
                            <i class="fas fa-share-alt text-danger me-2"></i>&nbsp;Connect With Us
                        </h5>
                        
                        <div class="d-flex flex-column gap-2 mb-3">
                            <a href="https://www.facebook.com/starhondacalambabranch" target="_blank" class="text-white-50 text-decoration-none footer-link">
                                <i class="fab fa-facebook-f text-danger me-2"></i>&nbsp;&nbsp;&nbsp;Facebook
                            </a>
                        </div>
                        
                        <div class="pt-2">
                            <h6 class="text-white-50 mb-2 fs-7">Resources</h6>
                            <a href="#" onclick="$('#p_use').click(); return false;" class="text-white-50 text-decoration-none footer-link">
                                <i class="fas fa-file-contract text-danger me-2"></i>&nbsp;&nbsp;&nbsp;Privacy Policy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright - Full Width -->
        <div class="row g-0 border-top border-light border-opacity-10">
            <div class="col-12">
                <div class="px-3 px-md-4 py-3">
                    <p class="m-0 text-white-50 small text-center lh-sm">
                        Copyright &copy; <?php echo $_settings->info('short_name') ?> <?php echo date('Y') ?>. All rights reserved. | 
                        <i class="fas fa-shield-alt text-danger mx-1"></i> Your information is treated with confidentiality
                    </p>
                </div>
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
        .footer-responsive {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
}

.footer-responsive .footer-link {
    font-size: 0.85rem;
    padding: 3px 0;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.footer-responsive .footer-link:hover {
    color: #ffffff !important;
    text-decoration: none;
    transform: translateX(5px);
}

.footer-responsive h5 {
    font-weight: 600;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dc3545;
    padding-bottom: 8px;
    display: inline-block;
}

.footer-responsive .text-danger {
    color: #dc3545 !important;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .footer-responsive .border-right {
        border-right: none !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .footer-responsive .col-md-8 .row > div {
        margin-bottom: 1.5rem;
    }
    
    .footer-responsive .col-md-8 .row > div:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 767.98px) {
    .footer-responsive .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .footer-responsive .px-3 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .footer-responsive .footer-link {
        font-size: 0.8rem;
    }
    
    .footer-responsive h5 {
        font-size: 0.95rem;
    }
    
    .footer-responsive .small {
        font-size: 0.8rem !important;
    }
}

@media (max-width: 575.98px) {
    .footer-responsive .col-12 {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .footer-responsive .footer-link {
        font-size: 0.75rem;
    }
    
    .footer-responsive .small {
        font-size: 0.75rem !important;
    }
    
    .footer-responsive .text-center {
        font-size: 0.7rem !important;
        line-height: 1.4;
    }
}

/* Remove negative space between sections */
.footer-responsive .row {
    margin-left: 0;
    margin-right: 0;
}

.footer-responsive .container-fluid {
    padding-left: 0;
    padding-right: 0;
}

/* Tighten up spacing */
.footer-responsive .mb-3 {
    margin-bottom: 0.75rem !important;
}

.footer-responsive .mb-2 {
    margin-bottom: 0.5rem !important;
}

.footer-responsive .py-4 {
    padding-top: 1.25rem !important;
    padding-bottom: 1.25rem !important;
}

.footer-responsive .border-right {
    padding-right: 1.5rem !important;
}

.footer-responsive .px-2 {
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
}

        /* Tablets (576px - 767px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .footer-responsive .px-3 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            .footer-responsive .px-md-4 {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }
            
            .footer-responsive .px-lg-5 {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }

            .footer-responsive h5 {
                font-size: 0.98rem;
                margin-bottom: 1.3rem;
            }

            .footer-responsive .py-5 {
                padding-top: 2.5rem !important;
                padding-bottom: 2.5rem !important;
            }

            .footer-responsive .gy-4 {
                row-gap: 2.5rem !important;
            }

            .footer-responsive .d-flex.flex-column.gap-3 {
                gap: 1.2rem !important;
            }

            .footer-link {
                padding: 0.45rem 0;
            }
        }

        /* Large screens (768px+) */
        @media (min-width: 768px) {
            .footer-responsive .px-md-4 {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
            
            .footer-responsive .px-lg-5 {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }

            .footer-responsive h5 {
                font-size: 1rem;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .footer-responsive h5 i {
                font-size: 1rem;
            }

            .footer-responsive .py-5 {
                padding-top: 3rem !important;
                padding-bottom: 3rem !important;
            }

            .footer-responsive .py-4 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            .footer-responsive .gy-4 {
                row-gap: 3rem !important;
            }

            .footer-responsive .d-flex.flex-column.gap-3 {
                gap: 1.5rem !important;
            }

            .footer-link {
                padding: 0.5rem 0;
            }
        }

        /* Ensure columns take equal space and fully utilize width */
        .footer-responsive .col-md-6 {
            min-height: auto;
        }

        .footer-responsive .col-lg-4 {
            min-height: auto;
        }

        /* Footer text positioning and spacing */
        .footer-responsive .text-center {
            text-align: center;
        }

        /* Footer contact info styling */
        .footer-responsive .text-white-50 {
            display: block;
            line-height: 1.6;
        }

        .footer-responsive div.mb-2 {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .footer-responsive div.mb-2 i {
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .footer-responsive div.mb-2 span,
        .footer-responsive div.mb-2 a {
            flex: 1;
            word-break: break-word;
        }

        /* Last section in Resources - proper spacing */
        .footer-responsive .pt-3 {
            padding-top: 1.5rem;
        }

        .footer-responsive .pt-3.border-top {
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Ensure Full Width Utilization - No white space */
        .footer-responsive {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            overflow-x: hidden;
        }

        .footer-responsive .container-fluid {
            width: 100%;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
        }
        
        /* Inner content padding for readability */
        .footer-responsive .px-3 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .footer-responsive .px-md-4 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .footer-responsive .px-lg-5 {
            padding-left: 3rem;
            padding-right: 3rem;
        }

        /* Column Equal Height and Distribution */
        @media (min-width: 992px) {
            .footer-responsive .row.gy-5 > [class*='col-'] {
                display: flex;
                flex-direction: column;
            }

            .footer-responsive .col-lg-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .footer-responsive .row.gy-5 > [class*='col-'] > div:first-child {
                flex: 1;
            }
        }

        /* Balanced footer sections on tablet */
        @media (min-width: 768px) and (max-width: 991px) {
            .footer-responsive .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .footer-responsive .row.gy-5 {
                display: flex;
                flex-wrap: wrap;
            }
        }
    </style>
    
    <style>
        /* Dashboard and Navbar Alignment Styles */
        
        /* Customer Navbar - Icon and Profile Alignment */
        .navbar .nav-item {
            display: flex;
            align-items: center;
        }
        
        .navbar .nav-link {
            display: flex;
            align-items: center;
            height: 56px;
            padding: 0 0.75rem !important;
            white-space: nowrap;
        }
        
        .navbar .nav-link i {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar .dropdown-toggle::after {
            display: none;
        }
        
        /* Ensure consistent spacing for desktop nav */
        @media (min-width: 992px) {
            .navbar .gap-2 {
                gap: 0.5rem !important;
            }
            
            .navbar-nav .nav-item {
                margin: 0 2px;
            }
            
            .navbar-nav .nav-link {
                transition: background-color 0.2s ease;
            }
            
            .navbar-nav .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
            }
        }
        
        /* Mobile navbar alignment */
        @media (max-width: 991.98px) {
            .navbar .nav-link {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.9rem;
            }
            
            .navbar .nav-link i {
                font-size: 1rem;
                margin-right: 8px;
            }
            
            .navbar .dropdown-menu {
                position: absolute;
                right: 0;
                left: auto;
                min-width: 200px;
            }
            
            .navbar .nav-link span {
                display: inline;
            }
        }
        
        /* Dashboard Info Boxes - Consistent Alignment */
        .info-box {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 6px;
            min-height: 100px;
            margin-bottom: 15px;
        }
        
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 100%;
            min-height: 80px;
            border-radius: 6px;
            flex-shrink: 0;
            margin-right: 15px;
        }
        
        .info-box-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .info-box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-grow: 1;
            padding: 0;
        }
        
        .info-box-text {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 6px;
        }
        
        .info-box-number {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        /* Responsive info boxes */
        @media (max-width: 768px) {
            .info-box {
                min-height: 90px;
                padding: 12px;
            }
            
            .info-box-icon {
                width: 70px;
                min-height: 70px;
                margin-right: 10px;
            }
            
            .info-box-icon i {
                font-size: 1.5rem;
            }
            
            .info-box-text {
                font-size: 0.85rem;
            }
            
            .info-box-number {
                font-size: 1.4rem;
            }
        }
        
        @media (max-width: 576px) {
            .info-box {
                min-height: 80px;
                padding: 10px;
                flex-direction: column;
                text-align: center;
            }
            
            .info-box-icon {
                width: 100%;
                height: auto;
                min-height: 50px;
                margin-right: 0;
                margin-bottom: 8px;
                border-radius: 50%;
            }
            
            .info-box-icon i {
                font-size: 1.3rem;
            }
            
            .info-box-text {
                font-size: 0.8rem;
            }
            
            .info-box-number {
                font-size: 1.2rem;
            }
        }
        
        /* Card Alignment and Spacing */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Responsive card spacing */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 15px;
                border-radius: 4px;
            }
            
            .card-header {
                padding: 0.75rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .card-title {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .card {
                margin-bottom: 12px;
            }
            
            .card-header {
                padding: 0.5rem;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .card-title {
                font-size: 0.95rem;
            }
        }
        
        /* Admin Dashboard - Quick Actions Alignment */
        .quick-action-btn {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            color: white;
            text-decoration: none;
            height: 120px;
            background: linear-gradient(135deg, rgba(0,0,0,0.5), rgba(0,0,0,0.7));
            transition: all 0.3s ease;
            border: none;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            color: white;
            text-decoration: none;
        }
        
        .quick-action-btn i {
            font-size: 2rem;
            margin-right: 15px;
            min-width: 50px;
            text-align: center;
        }
        
        .quick-action-btn strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 4px;
        }
        
        .quick-action-btn small {
            display: block;
            opacity: 0.9;
            font-size: 0.85rem;
        }
        
        /* Responsive quick actions */
        @media (max-width: 768px) {
            .quick-action-btn {
                height: 100px;
                padding: 15px;
            }
            
            .quick-action-btn i {
                font-size: 1.5rem;
                margin-right: 12px;
            }
            
            .quick-action-btn strong {
                font-size: 0.95rem;
            }
            
            .quick-action-btn small {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .quick-action-btn {
                height: auto;
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            
            .quick-action-btn i {
                font-size: 1.5rem;
                margin-right: 0;
                margin-bottom: 8px;
                min-width: auto;
            }
        }
        
        /* Row and Column Alignment */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -0.5rem;
            margin-left: -0.5rem;
        }
        
        .col-12, .col-sm-6, .col-md-3, .col-md-4, .col-lg-4 {
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }
        
        /* Ensure proper alignment for all screen sizes */
        @media (max-width: 768px) {
            .row {
                margin-right: -0.375rem;
                margin-left: -0.375rem;
            }
            
            .col-12, .col-sm-6, .col-md-3, .col-md-4, .col-lg-4 {
                padding-right: 0.375rem;
                padding-left: 0.375rem;
            }
        }
        
        /* Content Container Spacing */
        .content, .content-wrapper {
            padding: 0;
        }
        
        .container, .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (max-width: 768px) {
            .container, .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .container, .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
    </style>
    
    <style>
        #scrollToTop:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            background: linear-gradient(135deg, #c82333, #a71e2a);
        }
        
        #scrollToTop.show {
            display: block !important;
        }

        @media (max-width: 575.98px) {
            #scrollToTop {
                width: 45px !important;
                height: 45px !important;
                bottom: 15px !important;
                right: 15px !important;
            }
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