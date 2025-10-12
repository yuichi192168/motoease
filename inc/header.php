<?php
  // require_once('sess_auth.php');
  
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<title><?php echo $_settings->info('title') != false ? $_settings->info('title').' | ' : '' ?><?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> -->
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <link rel="stylesheet" href="<?php echo base_url ?>assets/css/styles.css">
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

     <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="<?php echo base_url ?>plugins/toastr/toastr.min.js"></script>
    <script>
        var _base_url_ = '<?php echo base_url ?>';
    </script>
    <script src="<?php echo base_url ?>dist/js/script.js"></script>
    <script src="<?php echo base_url ?>assets/js/scripts.js"></script>
    <style>
    html,
    body {
        height: 100%;
        width: 100%;
        font-family: Arial, Helvetica, sans-serif;
    }
    #main-header{
        position:relative;
        background: rgb(0,0,0)!important;
        background: radial-gradient(circle, rgba(0,0,0,0.48503151260504207) 22%, rgba(0,0,0,0.39539565826330536) 49%, rgba(0,212,255,0) 100%)!important;
        height:70vh;
    }
    #main-header:before{
        content:"";
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background-image:url(<?php echo base_url.$_settings->info('cover') ?>);
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        filter: drop-shadow(0px 7px 6px black);
        z-index:-1;
    }

    /* Hero Section Styles */
    .hero-section {
        height: 100vh;
        min-height: 600px;
        background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(220,38,38,0.4));
        display: flex;
        align-items: center;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(220,38,38,0.3));
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-buttons .btn {
        transition: all 0.3s ease;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .hero-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .hero-scroll-indicator {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        cursor: pointer;
        color: white;
    }
    
    .scroll-arrow {
        width: 40px;
        height: 40px;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    /* Animation Classes */
    .animate-fade-in {
        opacity: 0;
        animation: fadeIn 1s ease-out forwards;
    }
    
    .animate-slide-up {
        opacity: 0;
        transform: translateY(30px);
        animation: slideUp 1s ease-out 0.3s forwards;
    }
    
    .animate-slide-up.animated {
        opacity: 1;
        transform: translateY(0);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    /* Promo Section Styles */
    .promo-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
    }
    
    .promo-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23dc2626" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        z-index: 0;
    }
    
    .title-underline {
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #dc2626, #991b1b);
        border-radius: 2px;
    }
    
    .promo-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .promo-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    
    .promo-image-container {
        position: relative;
        height: 300px;
        overflow: hidden;
    }
    
    .promo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .promo-card:hover .promo-image {
        transform: scale(1.1);
    }
    
    .promo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(220,38,38,0.9), rgba(153,27,27,0.9));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .promo-card:hover .promo-overlay {
        opacity: 1;
    }
    
    .promo-content {
        text-align: center;
        color: white;
        padding: 20px;
    }
    
    .promo-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .promo-description {
        font-size: 0.9rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .promo-control {
        width: 50px;
        height: 50px;
        background: rgba(220,38,38,0.9) !important;
        border: none !important;
        border-radius: 50%;
        color: white !important;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        opacity: 0.9;
    }
    
    .promo-control:hover {
        background: #dc2626 !important;
        transform: scale(1.1);
        opacity: 1;
    }
    
    .promo-control:focus {
        background: #dc2626 !important;
        box-shadow: 0 0 0 0.2rem rgba(220,38,38,0.25);
    }
    
    .promo-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #dc2626;
        border: none;
        margin: 0 5px;
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }
    
    .promo-indicators button.active {
        opacity: 1;
    }
    
    /* Override Bootstrap carousel control styles */
    .carousel-control-prev.promo-control,
    .carousel-control-next.promo-control {
        background: rgba(220,38,38,0.9) !important;
        border: none !important;
        color: white !important;
    }
    
    .carousel-control-prev.promo-control:hover,
    .carousel-control-next.promo-control:hover {
        background: #dc2626 !important;
        color: white !important;
    }
    
    .carousel-control-prev.promo-control:focus,
    .carousel-control-next.promo-control:focus {
        background: #dc2626 !important;
        color: white !important;
        box-shadow: 0 0 0 0.2rem rgba(220,38,38,0.25);
    }
    
    /* Customer Section Styles */
    .customer-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        position: relative;
        overflow: hidden;
    }
    
    .infinite-carousel-container {
        position: relative;
        overflow: hidden;
        margin: 0 auto;
        width: 100%;
        max-width: 1200px;
    }
    
    .infinite-carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        width: max-content;
    }
    
    .customer-card-wrapper {
        flex: 0 0 300px;
        margin-right: 20px;
    }
    
    .customer-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .customer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .customer-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .customer-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .customer-card:hover .customer-image {
        transform: scale(1.05);
    }
    
    .customer-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(220,38,38,0.8), rgba(153,27,27,0.8));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .customer-card:hover .customer-overlay {
        opacity: 1;
    }
    
    .customer-rating {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .customer-content {
        padding: 20px;
        text-align: center;
    }
    
    .customer-name {
        font-size: 1.1rem;
        font-weight: bold;
        color: #dc2626;
        margin-bottom: 5px;
    }
    
    .customer-model {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .customer-testimonial {
        font-size: 0.85rem;
        color: #6c757d;
        font-style: italic;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    
    .customer-date {
        color: #adb5bd;
        font-size: 0.8rem;
    }
    
    .carousel-controls {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }
    
    .carousel-control-btn {
        width: 50px;
        height: 50px;
        background: #dc2626;
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .carousel-control-btn:hover {
        background: #991b1b;
        transform: scale(1.1);
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .customer-card-wrapper {
            flex: 0 0 280px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-section {
            height: 80vh;
            min-height: 500px;
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            gap: 15px;
        }
        
        .hero-buttons .btn {
            width: 100%;
            max-width: 300px;
        }
        
        .customer-card-wrapper {
            flex: 0 0 250px;
        }
        
        .promo-image-container {
            height: 250px;
        }
        
        .carousel-controls {
            margin-top: 20px;
        }
        
        .carousel-control-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .hero-content h1 {
            font-size: 2rem;
        }
        
        .customer-card-wrapper {
            flex: 0 0 220px;
        }
        
        .promo-image-container {
            height: 200px;
        }
    }

    /* Mobile Navigation Improvements */
    .navbar-toggler {
        border: 1px solid rgba(255,255,255,0.3);
        padding: 0.25rem 0.5rem;
    }
    
    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: rgba(0,0,0,0.1);
            border-radius: 0.375rem;
            margin-top: 0.5rem;
            padding: 1rem;
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .navbar-nav .nav-link:last-child {
            border-bottom: none;
        }
        
        .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none;
            background-color: rgba(0,0,0,0.1);
            box-shadow: none;
        }
        
        .dropdown-item {
            color: rgba(255,255,255,0.8);
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .dropdown-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    }
    
    /* Smooth animations for mobile menu */
    .navbar-collapse {
        transition: all 0.3s ease-in-out;
    }
    
    /* Better mobile cart badge positioning */
    .position-relative .badge {
        font-size: 0.7rem;
        min-width: 1.2rem;
        height: 1.2rem;
        line-height: 1.2rem;
        padding: 0;
    }

    /* Enhanced Promo Carousel Mobile Responsiveness */
    @media (max-width: 768px) {
        .promo-carousel .carousel-inner {
            padding: 0 10px;
        }
        
        .promo-card {
            margin-bottom: 20px;
        }
        
        .promo-image-container {
            height: 200px;
        }
        
        .promo-control {
            width: 40px !important;
            height: 40px !important;
            font-size: 1rem !important;
        }
        
        .promo-indicators {
            margin-top: 20px;
        }
        
        .promo-indicators button {
            width: 10px;
            height: 10px;
            margin: 0 3px;
        }
        
        .carousel-control-prev.promo-control {
            left: 5px;
        }
        
        .carousel-control-next.promo-control {
            right: 5px;
        }
    }
    
    @media (max-width: 576px) {
        .promo-image-container {
            height: 180px;
        }
        
        .promo-content {
            padding: 15px;
        }
        
        .promo-title {
            font-size: 1.2rem;
        }
        
        .promo-description {
            font-size: 0.8rem;
        }
        
        .promo-control {
            width: 35px !important;
            height: 35px !important;
            font-size: 0.9rem !important;
        }
    }
    
    /* Smooth scrolling for promo carousel */
    .carousel-inner {
        transition: transform 0.6s ease-in-out;
    }
    
    .carousel-item {
        transition: transform 0.6s ease-in-out;
    }
    
    /* Better touch scrolling on mobile */
    .carousel {
        touch-action: pan-y;
    }
    
    .carousel-inner {
        overflow: visible;
    }
    
    /* Improved promo card hover effects */
    .promo-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .promo-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    }
    
    /* Better mobile touch targets */
    @media (max-width: 768px) {
        .promo-control {
            touch-action: manipulation;
        }
        
        .promo-indicators button {
            touch-action: manipulation;
        }
    }

    /* Enhanced Notification Styles */
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
    
    .notification-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        flex-shrink: 0;
    }
    
    .notification-title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }
    
    .notification-message {
        font-size: 0.8rem;
        line-height: 1.3;
        margin-bottom: 4px;
    }
    
    .notification-time {
        font-size: 0.75rem;
    }
    
    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: #dc3545;
        border-radius: 50%;
        flex-shrink: 0;
        margin-left: 8px;
        margin-top: 4px;
    }
    
    .dropdown-header {
        padding: 12px 16px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
    }
    
    .dropdown-footer {
        text-align: center;
        padding: 12px 16px;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-weight: 500;
    }
    
    /* Mobile notification improvements */
    @media (max-width: 768px) {
        .notification-item {
            padding: 10px 12px;
        }
        
        .notification-icon {
            width: 28px;
            height: 28px;
        }
        
        .notification-title {
            font-size: 0.85rem;
        }
        
        .notification-message {
            font-size: 0.75rem;
        }
        
        .notification-time {
            font-size: 0.7rem;
        }
        
        .dropdown-header {
            padding: 10px 12px;
        }
        
        .dropdown-footer {
            padding: 10px 12px;
        }
    }

    /* Infinite Carousel Styles */
    .carousel-inner {
        overflow: hidden;
        white-space: nowrap;
    }
    
    .carousel-item {
        display: inline-block;
        vertical-align: top;
        white-space: normal;
        transition: none !important;
    }
    
    #customerInfiniteCarousel {
        overflow: hidden;
        position: relative;
    }
    
    #customerInfiniteCarousel .customer-card-wrapper {
        display: inline-block;
        vertical-align: top;
        margin-right: 20px;
    }

    /* Mobile Sidebar Styles */
    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: -50vw;
        width: 50vw;
        height: 100vh;
        background: #fff;
        z-index: 1050;
        transition: left 0.3s ease;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
    }
    
    .mobile-sidebar.show {
        left: 0;
    }
    
    .mobile-sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .mobile-sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .sidebar-header {
        padding: 15px 20px;
        background: #dc3545;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .sidebar-brand {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .sidebar-brand img {
        margin-right: 10px;
    }
    
    .sidebar-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 5px;
    }
    
    .sidebar-content {
        padding: 0;
    }
    
    .sidebar-user {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }
    
    .user-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 2px;
    }
    
    .user-email {
        font-size: 0.9rem;
        color: #666;
    }
    
    .quick-actions {
        display: flex;
        gap: 10px;
    }
    
    .quick-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        background: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }
    
    .quick-action-btn i {
        margin-right: 8px;
    }
    
    .quick-action-btn .badge {
        margin-left: 8px;
    }
    
    .sidebar-nav {
        padding: 0;
    }
    
    .sidebar-nav .nav-item {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .sidebar-nav .nav-link {
        padding: 15px 20px;
        color: #333;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    
    .sidebar-nav .nav-link:hover {
        background: #f8f9fa;
        color: #dc3545;
    }
    
    .sidebar-nav .nav-link.active {
        background: #dc3545;
        color: white;
    }
    
    .sidebar-nav .nav-link i {
        width: 20px;
        margin-right: 15px;
        text-align: center;
    }
    
    .nav-divider {
        height: 1px;
        background: #e9ecef;
        margin: 10px 0;
    }
    
    .sidebar-nav .badge {
        margin-left: auto;
    }
    
    /* Body scroll lock when sidebar is open */
    body.sidebar-open {
        overflow: hidden;
    }
    
    /* Mobile navbar adjustments */
    @media (max-width: 991.98px) {
        .navbar-nav.ms-auto {
            margin-left: auto !important;
        }
        
        .navbar-nav .nav-item {
            margin-left: 10px;
        }
        
        .navbar-nav .nav-link {
            padding: 8px 12px;
        }
        
        .navbar-nav .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
        }
    }
    
    /* Responsive sidebar width */
    @media (max-width: 576px) {
        .mobile-sidebar {
            width: 80vw;
            left: -80vw;
        }
    }
    
    @media (min-width: 577px) and (max-width: 768px) {
        .mobile-sidebar {
            width: 60vw;
            left: -60vw;
        }
    }
    
    @media (min-width: 769px) and (max-width: 991px) {
        .mobile-sidebar {
            width: 50vw;
            left: -50vw;
        }
    }

 </style>
  </head>