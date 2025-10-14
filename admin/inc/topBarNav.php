<style>
  .user-img{
        position: absolute;
        height: 27px;
        width: 27px;
        object-fit: cover;
        left: -7%;
        top: -12%;
  }
  .btn-rounded{
        border-radius: 50px;
  }

  /* Mobile-friendly admin header */
  @media (max-width: 991.98px) {
    .main-header .navbar-nav .nav-item {
        margin-left: 5px;
    }

    .main-header .navbar-nav .nav-link {
        padding: 8px 10px;
        font-size: 0.9rem;
    }

    .main-header .navbar-nav .nav-link img {
        width: 20px;
        height: 20px;
    }

    .main-header .navbar-nav .nav-link span {
        display: none;
    }

    .main-header .navbar-nav .dropdown-menu {
        position: absolute;
        right: 0;
        left: auto;
        min-width: 200px;
    }

    .main-header .navbar-nav .dropdown-menu .dropdown-item {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
  }

  @media (max-width: 576px) {
    .main-header .navbar-nav .nav-item {
        margin-left: 3px;
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

    .main-header .navbar-nav .dropdown-menu .dropdown-item {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .main-header .navbar-brand {
        font-size: 0.9rem;
    }
  }
</style>
<!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-light text-sm shadow" style="font-size:0.95rem; font-weight:600;">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="sidebarToggle"><i class="fas fa-bars"></i></a>                                  
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo base_url ?>" class="nav-link"><?php echo (!isMobileDevice()) ? $_settings->info('name'):$_settings->info('short_name'); ?> - Admin</a>
          </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Notifications Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link position-relative" data-toggle="dropdown" href="#" id="admin-notifications-dropdown">
              <i class="far fa-bell"></i>
              <span class="badge badge-warning navbar-badge" id="admin-notifications-count" style="display:none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="admin-notifications-list">
              <span class="dropdown-header">Notifications</span>
              <div class="dropdown-divider"></div>
              <div id="admin-notifications-content">
                <div class="text-center p-3">
                  <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item dropdown-footer" onclick="adminLoadAllNotifications()">View All Notifications</a>
            </div>
          </li>
          <!-- Navbar Search -->
          <!-- <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
              <form class="form-inline">
                <div class="input-group input-group-sm">
                  <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </li> -->
          <!-- Messages Dropdown Menu -->
          <li class="nav-item">
            <div class="btn-group nav-link">
                  <button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span><img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2 user-img" alt="User Image"></span>
                    <span class="ml-3"><?php echo ucwords($_settings->userdata('firstname').' '.$_settings->userdata('lastname')) ?></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="<?php echo base_url.'admin/?page=user' ?>"><span class="fa fa-user"></span> My Account</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url.'/classes/Login.php?f=logout' ?>"><span class="fas fa-sign-out-alt"></span> Logout</a>
                  </div>
              </div>
          </li>
          <li class="nav-item">
            
          </li>
         <!--  <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
            </a>
          </li> -->
        </ul>
      </nav>
      <!-- /.navbar -->
<script>
(function(){
  function adminLoadNotificationsCount(){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=get_notifications_count",
      method: "POST",
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          var c = parseInt(resp.count || 0, 10);
          var $badge = $('#admin-notifications-count');
          $badge.text(c);
          if(c > 0){ $badge.show(); } else { $badge.hide(); }
        }
      }
    });
  }

  function adminFormatTimeAgo(dateString){
    var now = new Date();
    var date = new Date(dateString);
    var diff = now - date;
    var seconds = Math.floor(diff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    if(days > 0) return days + 'd ago';
    if(hours > 0) return hours + 'h ago';
    if(minutes > 0) return minutes + 'm ago';
    return 'Just now';
  }

  window.adminLoadNotifications = function(){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=get_notifications",
      method: "POST",
      data: {limit: 7},
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          var html = '';
          if(resp.data && resp.data.length){
            resp.data.forEach(function(n){
              html += '<a href="#" class="dropdown-item notification-item ' + (n.is_read == 0 ? 'unread' : '') + '" onclick="adminMarkNotificationRead(' + n.id + ')">';
              html += '<div class="d-flex align-items-start">';
              html += '<div class="notification-icon mr-2"><i class="fas fa-bell text-warning"></i></div>';
              html += '<div class="notification-content">';
              html += '<div class="notification-title">' + (n.title || 'Notification') + '</div>';
              html += '<div class="notification-text">' + (n.message || n.description || '') + '</div>';
              html += '<div class="notification-time">' + adminFormatTimeAgo(n.date_created) + '</div>';
              html += '</div></div></a>';
              html += '<div class="dropdown-divider"></div>';
            });
          } else {
            html = '<div class="text-center p-3 text-muted">No notifications</div>';
          }
          $('#admin-notifications-content').html(html);
        }
      }
    });
  }

  window.adminMarkNotificationRead = function(id){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=mark_notification_read",
      method: "POST",
      data: {notification_id: id},
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          adminLoadNotificationsCount();
          adminLoadNotifications();
        }
      }
    });
  }

  window.adminLoadAllNotifications = function(){
    // Placeholder for an admin notifications list page
    toastr.info('Coming soon: Notifications list page');
  }

  $(document).ready(function(){
    adminLoadNotificationsCount();
    adminLoadNotifications();
    setInterval(adminLoadNotificationsCount, 30000);
    $('#admin-notifications-dropdown').on('click', function(){
      adminLoadNotifications();
    });
  });
})();
</script>