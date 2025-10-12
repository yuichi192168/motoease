<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="<?php echo base_url ?>index3.html" class="navbar-brand">
        <img src="<?php echo base_url ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="index3.html" class="nav-link">Home</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">Contact</a>
          </li>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Dropdown</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="#" class="dropdown-item">Some action </a></li>
              <li><a href="#" class="dropdown-item">Some other action</a></li>

              <li class="dropdown-divider"></li>

              <!-- Level two dropdown-->
              <li class="dropdown-submenu dropdown-hover">
                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Hover for action</a>
                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                  <li>
                    <a tabindex="-1" href="#" class="dropdown-item">level 2</a>
                  </li>

                  <!-- Level three dropdown-->
                  <li class="dropdown-submenu">
                    <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
                    <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                      <li><a href="#" class="dropdown-item">3rd level</a></li>
                      <li><a href="#" class="dropdown-item">3rd level</a></li>
                    </ul>
                  </li>
                  <!-- End Level three -->

                  <li><a href="#" class="dropdown-item">level 2</a></li>
                  <li><a href="#" class="dropdown-item">level 2</a></li>
                </ul>
              </li>
              <!-- End Level two -->
            </ul>
          </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-0 ml-md-3">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-comments"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="<?php echo base_url ?>dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Brad Diesel
                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">Call me whenever you can...</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="<?php echo base_url ?>dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    John Pierce
                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">I got your message bro</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="<?php echo base_url ?>dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Nora Silvester
                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">The subject goes here</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
          </div>
        </li>
        <!-- Cart -->
        <li class="nav-item">
          <a class="nav-link" href="./?p=cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge badge-warning navbar-badge" id="cart-count">0</span>
          </a>
        </li>
        <!-- Wishlist -->
        <li class="nav-item">
          <a class="nav-link" href="./?p=wishlist">
            <i class="fas fa-heart"></i>
            <span class="badge badge-danger navbar-badge" id="wishlist-count">0</span>
          </a>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" id="notifications-dropdown">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="notifications-count">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-list">
                <span class="dropdown-header">
                    <i class="fas fa-bell me-2"></i>Notifications
                    <div class="float-right">
                        <button class="btn btn-sm btn-outline-secondary" onclick="markAllNotificationsRead()">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                    </div>
                </span>
                <div class="dropdown-divider"></div>
                <div id="notifications-content">
                    <div class="text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer" onclick="loadAllNotifications()">
                    <i class="fas fa-list me-2"></i>See All Notifications
                </a>
            </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li>
      </ul>
    </div>
  </nav>

<script>
$(document).ready(function(){
    // Load cart count
    loadCartCount();
    
    // Load wishlist count
    loadWishlistCount();
    
    // Load notifications count
    loadNotificationsCount();
    
    // Load notifications when dropdown is opened
    $('#notifications-dropdown').on('click', function(){
        loadNotifications();
    });
    
    // Auto-refresh counts every 30 seconds
    setInterval(function(){
        loadCartCount();
        loadWishlistCount();
        loadNotificationsCount();
    }, 30000);
});

function loadCartCount(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_cart_count",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                $('#cart-count').text(resp.cart_count);
            }
        }
    });
}

function loadWishlistCount(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_wishlist_count",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                $('#wishlist-count').text(resp.wishlist_count);
            }
        }
    });
}

function loadNotificationsCount(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_notifications_count",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                $('#notifications-count').text(resp.count);
            }
        }
    });
}

function loadNotifications(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_notifications",
        method: "POST",
        data: {limit: 5},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                var html = '';
                if(resp.data.length > 0){
                    resp.data.forEach(function(notification){
                        var iconClass = getNotificationIcon(notification.type);
                        var priorityClass = getNotificationPriorityClass(notification.priority);
                        
                        html += '<a href="#" class="dropdown-item notification-item ' + (notification.is_read == 0 ? 'unread' : '') + '" onclick="markNotificationRead(' + notification.id + ')">';
                        html += '<div class="d-flex align-items-start">';
                        html += '<div class="notification-icon ' + priorityClass + '">';
                        html += '<i class="' + iconClass + '"></i>';
                        html += '</div>';
                        html += '<div class="flex-grow-1 ml-2">';
                        html += '<div class="notification-title">' + notification.title + '</div>';
                        if(notification.message) {
                            html += '<div class="notification-message text-muted small">' + notification.message + '</div>';
                        }
                        html += '<div class="notification-time text-muted small">' + formatTimeAgo(notification.date_created) + '</div>';
                        html += '</div>';
                        if(notification.is_read == 0) {
                            html += '<div class="notification-dot"></div>';
                        }
                        html += '</div>';
                        html += '</a>';
                        html += '<div class="dropdown-divider"></div>';
                    });
                } else {
                    html = '<div class="text-center p-3"><i class="far fa-bell fa-2x text-muted mb-2"></i><br>No notifications</div>';
                }
                $('#notifications-content').html(html);
            }
        }
    });
}

function getNotificationIcon(type) {
    switch(type) {
        case 'order': return 'fas fa-shopping-cart';
        case 'service': return 'fas fa-tools';
        case 'booking': return 'fas fa-calendar-check';
        case 'product': return 'fas fa-box';
        case 'payment': return 'fas fa-credit-card';
        case 'account': return 'fas fa-user';
        default: return 'fas fa-bell';
    }
}

function getNotificationPriorityClass(priority) {
    switch(priority) {
        case 'high': return 'text-danger';
        case 'medium': return 'text-warning';
        case 'low': return 'text-info';
        default: return 'text-primary';
    }
}

function markNotificationRead(notificationId){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_notification_read",
        method: "POST",
        data: {notification_id: notificationId},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                loadNotificationsCount();
                loadNotifications();
            }
        }
    });
}

function formatTimeAgo(dateString){
    var date = new Date(dateString);
    var now = new Date();
    var diff = Math.floor((now - date) / 1000); // difference in seconds
    
    if(diff < 60) return 'Just now';
    if(diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if(diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

function markAllNotificationsRead(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_all_notifications_read",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                loadNotificationsCount();
                loadNotifications();
                alert_toast('All notifications marked as read', 'success');
            }
        }
    });
}

function loadAllNotifications(){
    // Redirect to notifications page or show all notifications
    window.location.href = './?p=notifications';
}
</script>