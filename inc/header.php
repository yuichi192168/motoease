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

 </style>
  </head>