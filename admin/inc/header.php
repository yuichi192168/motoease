<?php
  require_once('sess_auth.php');
  
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<title><?php echo $_settings->info('title') != false ? $_settings->info('title').' | ' : '' ?><?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
      <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/custom.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.css">
     <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <style type="text/css">/* Chart.js */
      @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
    </style>

    <!-- Global Admin Dashboard CSS Fixes -->
    <style>
    /* Fix scrolling issues across all admin pages - Remove multiple scroll bars */
    body {
        overflow-x: hidden !important;
    }

    .wrapper {
        overflow: hidden !important;
    }

    .content-wrapper {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        height: calc(100vh - 60px) !important;
        padding-bottom: 20px;
    }

    .main-content {
        min-height: calc(100vh - 60px);
        overflow: hidden !important;
    }

    .content {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        height: calc(100vh - 60px);
        padding: 0 !important;
    }

    .card-body {
        overflow-x: auto;
        overflow-y: visible !important;
    }

    .table-responsive {
        max-height: 60vh;
        overflow-y: auto;
        overflow-x: auto;
    }

    /* Remove duplicate scroll bars from nested containers */
    .container-fluid {
        overflow: visible !important;
    }

    .row {
        overflow: visible !important;
    }

    /* Ensure proper spacing */
    .info-box {
        margin-bottom: 15px;
    }

    /* Fix modal scrolling */
    .modal-body {
        max-height: 60vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Improve table readability */
    .table th {
        position: sticky;
        top: 0;
        background: #f4f6f9;
        z-index: 10;
    }

    /* Better table styling */
    .table td {
        vertical-align: middle;
    }

    .dropdown-menu {
        min-width: 120px;
    }

    /* Fix sidebar scrolling */
    .main-sidebar {
        overflow-y: auto;
        overflow-x: hidden;
        height: 100vh;
    }

    /* Fix DataTables scrolling */
    .dataTables_wrapper {
        overflow-x: auto;
        overflow-y: visible !important;
    }

    .dataTables_scrollBody {
        overflow-y: auto !important;
        overflow-x: auto !important;
    }

    /* Remove scroll bars from specific problematic elements */
    .card {
        overflow: visible !important;
    }

    .card-header {
        overflow: visible !important;
    }

    /* Better responsive design */
    @media (max-width: 768px) {
        .content-wrapper {
            height: calc(100vh - 120px) !important;
        }
        
        .table-responsive {
            max-height: 50vh;
        }
        
        .content {
            height: calc(100vh - 120px) !important;
        }
    }

    /* Hide scroll bars but keep functionality */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Fix sidebar brand text overflow */
    .brand-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
        display: inline-block;
        font-size: 0.9rem;
        line-height: 1.2;
        font-weight: 500;
    }

    .brand-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
    }

    .brand-image {
        margin-right: 0.5rem;
        flex-shrink: 0;
    }

    /* Responsive sidebar text */
    @media (max-width: 768px) {
        .brand-text {
            max-width: 120px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .brand-text {
            max-width: 100px;
            font-size: 0.75rem;
        }
    }

    /* Ensure sidebar doesn't break layout */
    .main-sidebar {
        width: 250px;
        transition: width 0.3s ease;
    }

    .sidebar-collapse .main-sidebar {
        width: 70px;
    }

    .sidebar-collapse .brand-text {
        display: none;
    }

    /* Fix page title overflow in content area */
    .content-header h1 {
        font-size: 1.5rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* Improve navbar layout */
    .main-header .navbar-nav {
        flex-wrap: nowrap;
    }

    .main-header .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
        white-space: nowrap;
    }

    /* Better mobile handling */
    @media (max-width: 576px) {
        .main-header .navbar-nav .nav-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .main-header .navbar-nav .badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* Mobile sidebar improvements */
        .main-sidebar {
            width: 100% !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar-open .main-sidebar {
            transform: translateX(0);
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
        
        /* Mobile table improvements */
        .table-responsive {
            font-size: 0.8rem;
        }
        
        .table th, .table td {
            padding: 0.3rem;
        }
        
        /* Mobile card improvements */
        .card-body {
            padding: 0.75rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Mobile form improvements */
        .form-control {
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        /* Mobile modal improvements */
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-body {
            padding: 0.75rem;
        }
        
        /* Mobile info boxes */
        .info-box {
            margin-bottom: 0.5rem;
        }
        
        .info-box-content {
            padding: 0.5rem;
        }
        
        .info-box-text {
            font-size: 0.8rem;
        }
        
        .info-box-number {
            font-size: 1.2rem;
        }
    }
    
    /* Tablet improvements */
    @media (max-width: 768px) and (min-width: 577px) {
        .main-sidebar {
            width: 200px;
        }
        
        .content-wrapper {
            margin-left: 200px;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    /* Mobile navigation toggle */
    .navbar-toggler {
        border: none;
        padding: 0.25rem 0.5rem;
    }
    
    .navbar-toggler:focus {
        box-shadow: none;
    }
    
    /* Mobile sidebar overlay */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1030;
        display: none;
    }
    
    .sidebar-open .sidebar-overlay {
        display: block;
    }
    
    /* Admin Notification Styles */
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-item.unread {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    
    .notification-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }
    
    .notification-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 4px;
    }
    
    .notification-time {
        color: #999;
        font-size: 0.8rem;
    }
    
    .notification-icon {
        width: 20px;
        text-align: center;
    }
    
    /* Mobile Admin Navigation Enhancements */
    @media (max-width: 991.98px) {
        .main-header .navbar-nav .nav-item {
            margin-left: 8px;
        }
        
        .main-header .navbar-nav .nav-link {
            padding: 8px 10px;
            font-size: 0.9rem;
        }
        
        .main-header .navbar-nav .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            min-width: 200px;
        }
        
        .main-header .navbar-nav .nav-link img {
            width: 20px;
            height: 20px;
        }
        
        .main-header .navbar-nav .nav-link span {
            display: none;
        }
    }
    
    @media (max-width: 576px) {
        .main-header .navbar-nav .nav-item {
            margin-left: 5px;
        }
        
        .main-header .navbar-nav .nav-link {
            padding: 6px 8px;
            font-size: 0.8rem;
        }
        
        .main-header .navbar-nav .nav-link img {
            width: 18px;
            height: 18px;
        }
        
        .main-header .navbar-nav .dropdown-menu {
            min-width: 180px;
            font-size: 0.9rem;
        }
        
    /* Mobile sidebar width adjustments */
    .main-sidebar {
        width: 80vw !important;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        position: fixed !important;
        z-index: 1040 !important;
    }
    
    .main-sidebar.show {
        transform: translateX(0) !important;
    }
    
    .content-wrapper {
        margin-left: 0 !important;
    }
    
    /* Ensure sidebar is visible on mobile */
    @media (max-width: 991.98px) {
        .main-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            z-index: 1040 !important;
        }
        
        .main-sidebar.show {
            transform: translateX(0) !important;
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
        
        .main-header {
            z-index: 1030 !important;
        }
    }
    
    @media (min-width: 577px) and (max-width: 768px) {
        .main-sidebar {
            width: 60vw !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            position: fixed !important;
            z-index: 1040 !important;
        }
        
        .main-sidebar.show {
            transform: translateX(0) !important;
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
    }
    
    @media (min-width: 769px) and (max-width: 991px) {
        .main-sidebar {
            width: 50vw !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            position: fixed !important;
            z-index: 1040 !important;
        }
        
        .main-sidebar.show {
            transform: translateX(0) !important;
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
    }
    
    /* Mobile Profile Actions */
    .mobile-profile-actions {
        padding: 0 15px 15px 15px;
        border-bottom: 1px solid #4f5962;
    }
    
    .profile-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .profile-action-btn {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #c2c7d0;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    
    .profile-action-btn:hover {
        background: #495057;
        color: white;
        text-decoration: none;
    }
    
    .profile-action-btn.logout-btn:hover {
        background: #dc3545;
        color: white;
    }
    
    .profile-action-btn i {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }
    
    /* Enhanced sidebar overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1030;
        display: none;
    }
    
    .sidebar-open .sidebar-overlay {
        display: block;
    }
    
    /* Body scroll lock when sidebar is open */
    body.sidebar-open {
        overflow: hidden;
    }
    </style>

     <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="<?php echo base_url ?>plugins/toastr/toastr.min.js"></script>
    <!-- DataTables -->
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.min.js"></script>
    <script>
        var _base_url_ = '<?php echo base_url ?>';
        
        // Mobile sidebar functionality
        $(document).ready(function() {
            // Add sidebar overlay
            $('body').append('<div class="sidebar-overlay"></div>');
            
            // Mobile sidebar toggle
            $('#sidebarToggle').on('click', function(e) {
                e.preventDefault();
                if ($(window).width() <= 576) {
                    $('body').toggleClass('sidebar-open');
                }
            });
            
            // Close sidebar when clicking overlay
            $('.sidebar-overlay').on('click', function() {
                $('body').removeClass('sidebar-open');
            });
            
            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($(window).width() <= 576) {
                    if (!$(e.target).closest('.main-sidebar, #sidebarToggle').length) {
                        $('body').removeClass('sidebar-open');
                    }
                }
            });
            
            // Handle window resize
            $(window).on('resize', function() {
                if ($(window).width() > 576) {
                    $('body').removeClass('sidebar-open');
                }
            });
        });
    </script>
    <script src="<?php echo base_url ?>dist/js/script.js"></script>

  </head>