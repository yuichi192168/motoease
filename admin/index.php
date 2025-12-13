<?php require_once('../config.php'); ?>
 <!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>
  <body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed sidebar-mini-md sidebar-mini-xs" data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
    <div class="wrapper">
     <?php require_once('inc/topBarNav.php') ?>
     <?php require_once('inc/navigation.php') ?>
              
     <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
     <?php
       // Centralized Role-Based Access Control for admin pages
       $role_type = $_settings->userdata('role_type') ?: 'admin';

      // Define allowed page sets per role
      $always_allowed_pages = ['home'];
      // Service Admin can manage services plus promo & customer images
      $service_admin_allowed_pages = ['service_requests','promo_images','customer_images'];
      // Service Receptionist should only manage services (no image management)
      $service_receptionist_allowed_pages = ['service_requests'];
       $inventory_allowed_pages = ['products','inventory','inventory/abc_analysis'];

      // Helper: check if a page is allowed for a role (with permissions support)
      $is_allowed = function($page, $role) use ($always_allowed_pages, $service_admin_allowed_pages, $service_receptionist_allowed_pages, $inventory_allowed_pages) {
         if (in_array($page, $always_allowed_pages)) return true;
         if ($role === 'admin') return true;

         // Hard block: service receptionist must never access image management, even if permissions are misconfigured
         if ($role === 'service_receptionist' && in_array($page, ['promo_images', 'customer_images'])) {
           return false;
         }
         
        // Map page to permission key
        $page_to_permission = [
          'user' => 'user_management',
          'user/list' => 'user_management',
          'user/manage_user' => 'user_management',
          'clients' => 'customer_management',
          'products' => 'product_management',
          'inventory' => 'inventory_management',
          'inventory/abc_analysis' => 'inventory_management',
          'service_requests' => 'service_requests',
          'promo_images' => 'promo_images',
          'customer_images' => 'customer_images',
          'reviews' => 'reviews_management',
          'announcements' => 'announcements_management',
          'content' => 'content_management',
          'orders' => 'order_management',
          'invoices' => 'invoice_management',
          'customer_account_balances' => 'customer_accounts',
          'orcr_documents' => 'orcr_documents',
          'report' => 'reports',
          'report/orders' => 'reports',
          'report/service_requests' => 'reports',
          'report/invoices' => 'reports',
          'user_log_history' => 'reports',
          'system_info' => 'system_settings',
          'maintenance/category' => 'system_settings',
          'maintenance/services' => 'system_settings',
          'maintenance/manage_category' => 'system_settings',
          'maintenance/manage_service' => 'system_settings',
          'mechanics' => 'mechanics'
        ];
         
         // Check permissions first
         if(isset($page_to_permission[$page])){
           $permission_key = $page_to_permission[$page];
           if(hasPermission($permission_key)){
             return true;
           }
         }
         
        // Fallback to role-based access for backward compatibility
        if ($role === 'system_admin') {
          // System Admin: Full access to user, customer management, and customer accounts
          if (in_array($page, ['user', 'user/list', 'user/manage_user', 'clients', 'customer_account_balances', 'customer_accounts', 'invoices', 'orcr_documents']) ||
              strpos($page, 'user/') === 0 || 
              strpos($page, 'clients') === 0 ||
              strpos($page, 'customer_account') === 0) {
            return true;
          }
          return false;
        }
        if ($role === 'desk_staff') {
          // Desk/Staff: Daily transactions, reports, system settings, management, and OR/CR documents
          $desk_allowed_pages = [
            'products', 'inventory', 'inventory/abc_analysis', 
            'service_requests', 'mechanics', 'orders',
            'report', 'report/orders', 'report/service_requests', 'report/invoices', 'user_log_history',
            'system_info',
            'maintenance/category', 'maintenance/services', 'maintenance/manage_category', 'maintenance/manage_service',
            'orcr_documents'
          ];
          foreach ($desk_allowed_pages as $p) {
            if ($page === $p || strpos($page, $p.'/') === 0) return true;
          }
          return false;
        }
        if ($role === 'data_admin') {
          // Data Admin: Promos, reviews, announcements, content
          $data_allowed_pages = ['promo_images', 'customer_images', 'reviews', 'announcements', 'content'];
          foreach ($data_allowed_pages as $p) {
            if ($page === $p || strpos($page, $p.'/') === 0) return true;
          }
          return false;
        }
        if ($role === 'service_admin') {
          foreach ($service_admin_allowed_pages as $p) {
            if ($page === $p || strpos($page, $p.'/') === 0) return true;
          }
          return false;
        }
        if ($role === 'service_receptionist') {
          foreach ($service_receptionist_allowed_pages as $p) {
            if ($page === $p || strpos($page, $p.'/') === 0) return true;
          }
          return false;
        }
        if ($role === 'inventory') {
          foreach ($inventory_allowed_pages as $p) {
            if ($page === $p || strpos($page, $p.'/') === 0) return true;
          }
          return false;
        }
        // Default deny for other roles/types
        return false;
       };

       if (!$is_allowed($page, $role_type)) {
         echo "<script>alert('Access denied for your role.');location.replace('./');</script>";
         exit;
       }
     ?>
     <?php if($_settings->chk_flashdata('success')): ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
      </script>
    <?php endif;?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper pt-3" style="min-height: 567.854px;">
      
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <?php 
              if(!file_exists($page.".php") && !is_dir($page)){
                  include '404.html';
              }else{
                if(is_dir($page))
                  include $page.'/index.php';
                else
                  include $page.'.php';

              }
            ?>
          </div>
        </section>
        <!-- /.content -->
  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
      </div>
      <!-- /.content-wrapper -->
      <?php require_once('inc/footer.php') ?>
  </body>
</html>
