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
              <span class="badge badge-danger rounded-circle position-absolute top-0 start-100 translate-middle" id="admin-notifications-count" style="display:none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="admin-notifications-list">
              <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="dropdown-header mb-0">Notifications</span>
                <button class="btn btn-sm btn-link text-primary p-0" type="button" onclick="adminMarkAllNotificationsRead()">
                  Mark all read
                </button>
              </div>
              <div id="admin-notifications-content">
                <div class="text-center p-3">
                  <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
              </div>
              <div class="dropdown-divider mb-0"></div>
              <a href="#" class="dropdown-item dropdown-footer" onclick="adminOpenNotificationHistory(event)">View Notification History</a>
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
      
<div class="modal fade" id="adminNotificationHistoryModal" tabindex="-1" role="dialog" aria-labelledby="adminNotificationHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adminNotificationHistoryLabel">Notification History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <div id="adminNotificationHistoryList" class="list-group list-group-flush">
          <div class="text-center p-4">
            <i class="fas fa-spinner fa-spin"></i> Loading history...
          </div>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <small class="text-muted" id="adminNotificationHistoryMeta"></small>
        <button type="button" class="btn btn-outline-primary btn-sm" id="adminNotificationHistoryLoadMore" onclick="adminLoadNotificationHistory()" style="display:none;">
          Load more
        </button>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  var adminNotificationHistoryPage = 0;
  var adminNotificationHistoryLimit = 20;
  var adminNotificationHistoryLoading = false;
  var adminNotificationHistoryHasMore = false;
  
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

  function adminToggleSidebarDot(count){
    if($('#sidebar-notif-dot').length){
      if(count > 0) $('#sidebar-notif-dot').show();
      else $('#sidebar-notif-dot').hide();
    }
  }

  window.adminLoadNotificationsCount = function(){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=get_admin_notifications_count",
      method: "POST",
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          var c = parseInt(resp.count || 0, 10);
          var $badge = $('#admin-notifications-count');
          $badge.text(c);
          if(c > 0){ $badge.show(); } else { $badge.hide(); }
          adminToggleSidebarDot(c);
        }
      }
    });
  }

  function adminResolveNotificationData(notification){
    var data = notification && notification.data ? notification.data : {};
    if(typeof data === 'string'){
      try{
        data = JSON.parse(data);
      }catch(err){
        data = {};
      }
    }
    return data || {};
  }

  function getAdminNotificationTarget(notification){
    var data = adminResolveNotificationData(notification);
    if(notification.url) return notification.url;
    if(data.url) return data.url;
    if(data.href) return data.href;
    if(data.link) return data.link;
    var type = (notification.type || '').toLowerCase();
    var defaultHistory = './?page=notifications';
    var orderView = data.order_id ? './?page=orders/view_order&id=' + data.order_id : './?page=orders';
    var serviceView = data.service_id ? './?page=service_requests/view_request&id=' + data.service_id : './?page=service_requests';
    var appointmentView = data.appointment_id ? './?page=appointments/view_appointment&id=' + data.appointment_id : './?page=appointments';
    var accountView = data.account_id ? './?page=customer_account_balances/view_account&id=' + data.account_id : './?page=customer_account_balances';
    var productView = data.product_id ? './?page=products/view_product&id=' + data.product_id : './?page=products';

    switch(type){
      case 'order':
      case 'order_status':
        return orderView;
      case 'service':
      case 'service_status':
        return serviceView;
      case 'appointment':
      case 'appointment_status':
      case 'appointment_reminder':
        return appointmentView;
      case 'product_availability':
        return productView;
      case 'payment':
      case 'payment_upcoming':
      case 'payment_missed':
      case 'payment_received':
      case 'late_payment':
      case 'account_status':
        return accountView;
      default:
        if(data.order_id) return orderView;
        if(data.service_id) return serviceView;
        if(data.appointment_id) return appointmentView;
        if(data.account_id) return accountView;
        if(data.product_id) return productView;
        return defaultHistory;
    }
  }

  window.adminLoadNotifications = function(){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=get_admin_notifications",
      method: "POST",
      data: {limit: 7},
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          var html = '';
          if(resp.data && resp.data.length){
            resp.data.forEach(function(n){
              var message = n.message || '';
              var title = n.title || 'Notification';
              var targetUrl = getAdminNotificationTarget(n);
              html += '<a href="' + targetUrl + '" class="dropdown-item notification-item ' + (n.is_read == 0 ? 'unread' : '') + '" data-id="' + n.id + '" data-url="' + targetUrl + '">';
              html += '<div class="d-flex align-items-start">';
              html += '<div class="notification-icon mr-2"><i class="fas fa-bell text-warning"></i></div>';
              html += '<div class="notification-content">';
              html += '<div class="notification-title">' + title + '</div>';
              if(message){
                html += '<div class="notification-text">' + message + '</div>';
              }
              html += '<div class="notification-time">' + adminFormatTimeAgo(n.date_created) + '</div>';
              html += '</div></div></a>';
            });
          } else {
            html = '<div class="text-center p-3 text-muted">No notifications</div>';
          }
          $('#admin-notifications-content').html(html);
        }
      }
    });
  }

  window.adminMarkNotificationRead = function(id, options){
    options = options || {};
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=mark_admin_notification_read",
      method: "POST",
      data: {id: id},
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          adminLoadNotificationsCount();
          adminLoadNotifications();
          if(typeof options.onSuccess === 'function'){
            options.onSuccess(resp);
          }
        }else if(typeof options.onError === 'function'){
          options.onError(resp);
        }
      },
      error: function(err){
        if(typeof options.onError === 'function'){
          options.onError(err);
        }
      },
      complete: function(){
        if(options.redirectTo){
          window.location.href = options.redirectTo;
        }
      }
    });
  }
  
  window.adminMarkAllNotificationsRead = function(){
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=mark_all_admin_notifications_read",
      method: "POST",
      dataType: "json",
      complete: function(){
        adminLoadNotificationsCount();
        adminLoadNotifications();
      }
    });
  }
  
  window.adminOpenNotificationHistory = function(event){
    if(event) event.preventDefault();
    adminNotificationHistoryPage = 0;
    adminNotificationHistoryHasMore = false;
    $('#adminNotificationHistoryList').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading history...</div>');
    $('#adminNotificationHistoryMeta').text('');
    $('#adminNotificationHistoryLoadMore').hide();
    $('#adminNotificationHistoryModal').modal('show');
    adminLoadNotificationHistory(true);
  }
  
  window.adminLoadNotificationHistory = function(reset){
    if(adminNotificationHistoryLoading) return;
    if(!reset && !adminNotificationHistoryHasMore) return;
    adminNotificationHistoryLoading = true;
    var offset = adminNotificationHistoryPage * adminNotificationHistoryLimit;
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=get_admin_notification_history",
      method: "POST",
      data: {limit: adminNotificationHistoryLimit, offset: offset},
      dataType: "json",
      success: function(resp){
        if(resp && resp.status === 'success'){
          renderAdminNotificationHistory(resp.items || [], reset);
          adminNotificationHistoryHasMore = !!resp.has_more;
          $('#adminNotificationHistoryMeta').text((resp.total || 0) + ' total notifications');
          if(adminNotificationHistoryHasMore){
            adminNotificationHistoryPage++;
            $('#adminNotificationHistoryLoadMore').show();
          }else{
            $('#adminNotificationHistoryLoadMore').hide();
          }
        }else{
          $('#adminNotificationHistoryList').html('<div class="text-center p-4 text-muted">Unable to load history.</div>');
          $('#adminNotificationHistoryLoadMore').hide();
        }
      },
      complete: function(){
        adminNotificationHistoryLoading = false;
      }
    });
  }
  
  function renderAdminNotificationHistory(items, reset){
    var container = $('#adminNotificationHistoryList');
    if(reset){
      container.empty();
    }
    if(!items.length && reset){
      container.html('<div class="text-center p-4 text-muted">No notifications yet.</div>');
      return;
    }
    items.forEach(function(n){
      var message = n.message || '';
      var title = n.title || 'Notification';
      var badge = n.is_read == 0 ? '<span class="badge badge-warning badge-pill ml-2">New</span>' : '';
      var item = '<div class="list-group-item">';
      item += '<div class="d-flex justify-content-between align-items-center">';
      item += '<h6 class="mb-1">' + title + ' ' + badge + '</h6>';
      item += '<small class="text-muted">' + adminFormatTimeAgo(n.date_created) + '</small>';
      item += '</div>';
      if(message){
        item += '<p class="mb-1 text-muted">' + message + '</p>';
      }
      item += '</div>';
      container.append(item);
    });
  }

  $(document).ready(function(){
    adminLoadNotificationsCount();
    adminLoadNotifications();
    setInterval(adminLoadNotificationsCount, 30000);
    $('#admin-notifications-dropdown').on('click', function(){
      adminLoadNotifications();
    });
    $('#admin-notifications-dropdown').on('show.bs.dropdown', function(){
      adminLoadNotifications();
    });
    if(!window.__adminNotifClickBound){
      $('#admin-notifications-content').on('click', '.notification-item', function(e){
        e.preventDefault();
        var $item = $(this);
        var id = $item.data('id');
        var targetUrl = $item.data('url') || $item.attr('href') || './?page=notifications';
        if(!id){
          window.location.href = targetUrl;
          return;
        }
        adminMarkNotificationRead(id, { redirectTo: targetUrl });
      });
      window.__adminNotifClickBound = true;
    }
  });
})();
</script>

  <script>
  // Poll admin sidebar counts (orders, services, appointments)
  (function(){
    function refreshAdminSidebarBadges(){
      $.ajax({
        url: _base_url_ + 'classes/Master.php?f=get_admin_sidebar_counts',
        method: 'POST',
        dataType: 'json',
        success: function(resp){
          if(resp && resp.status === 'success' && resp.counts){
            var c = resp.counts;
            if(c.orders && parseInt(c.orders) > 0) $('#dot-orders').show(); else $('#dot-orders').hide();
            if(c.services && parseInt(c.services) > 0) $('#dot-services').show(); else $('#dot-services').hide();
            if(c.appointments && parseInt(c.appointments) > 0) $('#dot-appointments').show(); else $('#dot-appointments').hide();
          }
        }
      });
    }
    $(function(){
      refreshAdminSidebarBadges();
      setInterval(refreshAdminSidebarBadges, 10000);
      // Attach click handlers to badge links (if present)
      $(document).on('click', '#link-orders', function(e){
        e.preventDefault();
        var href = $(this).attr('data-href') || $(this).attr('href');
        // mark orders section as viewed
        $.post(_base_url_ + 'classes/Master.php?f=clear_admin_section_view', {section: 'orders'}).always(function(){
          if(href) window.location.href = href;
        });
      });
      $(document).on('click', '#link-services', function(e){
        e.preventDefault();
        var href = $(this).attr('data-href') || $(this).attr('href');
        $.post(_base_url_ + 'classes/Master.php?f=clear_admin_section_view', {section: 'services'}).always(function(){
          if(href) window.location.href = href;
        });
      });
      $(document).on('click', '#link-appointments', function(e){
        e.preventDefault();
        var href = $(this).attr('data-href') || $(this).attr('href');
        $.post(_base_url_ + 'classes/Master.php?f=clear_admin_section_view', {section: 'appointments'}).always(function(){
          if(href) window.location.href = href;
        });
      });
    });
  })();
  </script>