<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="topNavBar">
    <div class="container px-4 px-lg-5">
        <!-- Mobile sidebar toggle button -->
        <button class="navbar-toggler d-lg-none" type="button" id="mobileSidebarToggle" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Brand logo -->
        <a class="navbar-brand" href="./">
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
            <?php echo $_settings->info('short_name') ?>
        </a>
        
        <!-- Desktop navigation -->
        <div class="d-none d-lg-flex navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
            <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'home'? "class='nav-link fw-bold active text-center'" : '' ?> href="./">Home</a>
            <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'products'? "class='nav-link fw-bold active text-center'" : '' ?> href="./?p=products">Products</a>
            <!-- <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'services'? "class='nav-link fw-bold active text-center'" : '' ?> href="./?p=services">Services</a> -->
            <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'about'? "class='nav-link fw-bold active text-center'" : '' ?> href="./?p=about">About Us</a>
        </div>
        
        <!-- Right side menu - Only show on desktop -->
        <div class="navbar-nav ms-auto d-none d-lg-flex gap-2">
            <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                <!-- Cart -->
                <div class="nav-item d-flex align-items-center">
                    <?php 
                    $cart_count = $conn->query("SELECT SUM(quantity) from cart_list where client_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
                    $cart_count = $cart_count > 0 ? number_format($cart_count) : 0;
                    ?>
                    <a href="./?p=cart" class="nav-link position-relative d-flex align-items-center" style="padding: 0; height: 56px; width: 40px; border-radius: 50%;">
                        <i class="fas fa-shopping-cart" style="font-size: 1.2rem; color: #ffffff;"></i>
                        <span id="cart_count" class="badge badge-danger rounded-circle position-absolute" style="top: -5px; right: -8px; min-width: 20px; height: 20px; padding: 2px 4px; font-size: 11px; line-height: 16px; <?= $cart_count == 0 ? 'display:none;' : '' ?>"><?= $cart_count ?></span>
                    </a>
                </div>
                
                <!-- Notifications -->
                <div class="nav-item dropdown d-flex align-items-center">
                    <a class="nav-link position-relative client-notification-toggle d-flex align-items-center" data-toggle="dropdown" href="#" id="notifications-dropdown" style="padding: 0; height: 56px; width: 40px; border-radius: 50%;">
                        <i class="fas fa-bell" style="font-size: 1.2rem; color: #ffffff;"></i>
                        <span class="badge badge-danger rounded-circle position-absolute" id="notifications-count" style="display:none; top: -5px; right: -8px; min-width: 20px; height: 20px; padding: 2px 4px; font-size: 11px; line-height: 16px;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-list">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <span class="dropdown-header mb-0">Notifications</span>
                            <button class="btn btn-sm btn-link text-primary p-0" type="button" onclick="markAllNotificationsRead()">
                                Mark all read
                            </button>
                        </div>
                        <div id="notifications-content">
                            <div class="text-center p-3">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                        <div class="dropdown-divider mb-0"></div>
                        <a href="#" class="dropdown-item dropdown-footer" onclick="openNotificationHistory(event)">View Notification History</a>
                    </div>
                </div>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-list">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <span class="dropdown-header mb-0">Notifications</span>
                            <button class="btn btn-sm btn-link text-primary p-0" type="button" onclick="markAllNotificationsRead()">
                                Mark all read
                            </button>
                        </div>
                        <div id="notifications-content">
                            <div class="text-center p-3">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                        <div class="dropdown-divider mb-0"></div>
                        <a href="#" class="dropdown-item dropdown-footer" onclick="openNotificationHistory(event)">View Notification History</a>
                    </div>
                </div>
                
                <!-- Profile Settings -->
                <div class="nav-item dropdown d-flex align-items-center">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 0.5rem 0.75rem; height: 56px;">
                        <div class="position-relative" style="margin-right: 8px;">
                            <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="rounded-circle" style="width: 25px; height: 25px; object-fit: cover;" alt="Avatar">
                        </div>
                        <span class="d-lg-inline d-none"><?= $_settings->userdata('firstname') ? ucwords($_settings->userdata('firstname')) : $_settings->userdata('email') ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdownMenuLink">
                        <a class="dropdown-item" href="./?p=my_orders"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        <a class="dropdown-item" href="./?p=my_services"><i class="fas fa-tools me-2"></i>My Service History</a>
                        <a class="dropdown-item" href="./?p=my_invoices"><i class="fas fa-file-invoice me-2"></i>My Invoices & Receipts</a>
                        <a class="dropdown-item" href="./?p=manage_account"><i class="fas fa-user-cog me-2"></i>Manage Account</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="./classes/Login.php?f=logout_client"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest menu -->
                <div class="nav-item">
                    <a href="./login.php" class="nav-link"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                </div>
                <div class="nav-item">
                    <a href="./register.php" class="nav-link"><i class="fas fa-user-plus me-1"></i>Register</a>
                </div>
                <!-- <div class="nav-item">
                    <a href="./admin" class="nav-link"><i class="fas fa-cog me-1"></i>Admin</a>
                </div> -->
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Notification History Modal -->
<div class="modal fade" id="notificationHistoryModal" tabindex="-1" role="dialog" aria-labelledby="notificationHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationHistoryLabel">Notification History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <div id="notificationHistoryList" class="list-group list-group-flush">
          <div class="text-center p-4">
            <i class="fas fa-spinner fa-spin"></i> Loading history...
          </div>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <small class="text-muted" id="notificationHistoryMeta"></small>
        <button type="button" class="btn btn-outline-primary btn-sm" id="notificationHistoryLoadMore" onclick="loadNotificationHistory()" style="display:none;">
          Load more
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="30" height="30" alt="Logo">
            <span><?php echo $_settings->info('short_name') ?></span>
        </div>
        <button class="sidebar-close" id="mobileSidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-content">
        <!-- User Profile Section -->
        <div class="sidebar-user">
            <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                <div class="user-info">
                    <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="user-avatar" alt="Avatar">
                    <div class="user-details">
                        <div class="user-name"><?= $_settings->userdata('firstname') ? ucwords($_settings->userdata('firstname')) : $_settings->userdata('email') ?></div>
                        <div class="user-email"><?= $_settings->userdata('email') ?></div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="./?p=cart" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>My Cart</span>
                        <?php if($cart_count > 0): ?>
                            <span class="badge badge-danger"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="#" class="quick-action-btn" onclick="loadNotifications()">
                        <span class="quick-action-icon position-relative d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-danger rounded-circle position-absolute" id="mobile-notifications-count" style="display:none; top: -5px; right: -5px; min-width: 18px; height: 18px; padding: 2px 5px; font-size: 10px; line-height: 14px;">0</span>
                        </span>
                        <span class="ms-2">Notifications</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link fw-bold <?= isset($page) && $page == 'home'? "active" : '' ?>" href="./" style="font-size:1.1rem; font-weight: 700;">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold <?= isset($page) && $page == 'products'? "active" : '' ?>" href="./?p=products" style="font-size:1.1rem; font-weight: 700;">
                        <i class="fas fa-motorcycle"></i>
                        <span>Products</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link fw-bold <?= isset($page) && $page == 'services'? "active" : '' ?>" href="./?p=services" style="font-size:1.1rem; font-weight: 700;">
                        <i class="fas fa-tools"></i>
                        <span>Services</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link fw-bold <?= isset($page) && $page == 'about'? "active" : '' ?>" href="./?p=about" style="font-size:1.1rem; font-weight: 700;">
                        <i class="fas fa-info-circle"></i>
                        <span>About Us</span>
                    </a>
                </li>
                
                <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link" href="./?p=my_orders">
                            <i class="fas fa-shopping-bag"></i>
                            <span>My Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./?p=my_services">
                            <i class="fas fa-tools"></i>
                            <span>My Service History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./?p=my_invoices">
                            <i class="fas fa-file-invoice"></i>
                            <span>My Invoices & Receipts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./?p=manage_account">
                            <i class="fas fa-user-cog"></i>
                            <span>Manage Account</span>
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link" href="./classes/Login.php?f=logout_client">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link" href="./login.php">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./register.php">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" h   ref="./admin">
                            <i class="fas fa-cog"></i>
                            <span>Admin</span>
                        </a>
                    </li> -->
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>
<script>
  $(function(){
    $('#login-btn').click(function(){
      uni_modal("","login.php")
    })
    $('#navbarResponsive').on('show.bs.collapse', function () {
        $('#mainNav').addClass('navbar-shrink')
    })
    $('#navbarResponsive').on('hidden.bs.collapse', function () {
        if($('body').offset.top == 0)
          $('#mainNav').removeClass('navbar-shrink')
    })
  })

  $('#search-form').submit(function(e){
    e.preventDefault()
     var sTxt = $('[name="search"]').val()
     if(sTxt != '')
      location.href = './?p=products&search='+sTxt;
  })
  
  // Notification functions
  var notificationHistoryPage = 0;
  var notificationHistoryLimit = 20;
  var notificationHistoryHasMore = false;
  var notificationHistoryLoading = false;
  
  function loadNotificationsCount(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_notifications_count",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                $('#notifications-count').text(resp.count);
                $('#mobile-notifications-count').text(resp.count);
                if(resp.count > 0){
                    $('#notifications-count').show();
                    $('#mobile-notifications-count').show();
                } else {
                    $('#notifications-count').hide();
                    $('#mobile-notifications-count').hide();
                }
            }
        }
    });
  }
  
  function resolveNotificationData(notification){
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

  function getNotificationTarget(notification){
    var data = resolveNotificationData(notification);
    if(notification.url) return notification.url;
    if(data.url) return data.url;
    if(data.href) return data.href;
    if(data.link) return data.link;
    var type = (notification.type || '').toLowerCase();
    var defaultHistory = './?p=notifications';
    var orderView = './?p=my_orders' + (data.order_id ? '&id=' + data.order_id : '');
    var serviceView = data.service_id ? './view_service.php?id=' + data.service_id : './?p=my_services';
    var appointmentView = data.appointment_id ? './view_appointment.php?id=' + data.appointment_id : './?p=appointments';
    var accountView = './?p=my_invoices' + (data.account_id ? '&account_id=' + data.account_id : '');
    var invoiceView = data.invoice_id ? './view_invoice.php?id=' + data.invoice_id : './?p=my_invoices';

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
            return data.product_id ? './?p=products&product_id=' + data.product_id : './?p=products';
        case 'payment':
        case 'payment_upcoming':
        case 'payment_missed':
        case 'payment_received':
        case 'late_payment':
        case 'account_status':
            return './?p=my_invoices' + (data.account_id ? '&account_id=' + data.account_id : '');
        case 'invoice':
        case 'invoice_status':
        case 'invoice_paid':
        case 'invoice_due':
            return invoiceView;
        case 'account':
            return './?p=manage_account';
        default:
            if(data.order_id) return orderView;
            if(data.service_id) return serviceView;
            if(data.appointment_id) return appointmentView;
            if(data.invoice_id) return invoiceView;
            if(data.account_id) return './?p=my_invoices&account_id=' + data.account_id;
            return defaultHistory;
    }
  }

  function loadNotifications(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_notifications",
        method: "POST",
        data: {limit: 7},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                var html = '';
                if(resp.data.length > 0){
                    resp.data.forEach(function(notification){
                        var message = notification.message || '';
                        var title = notification.title || 'Notification';
                        var targetUrl = getNotificationTarget(notification);
                        var isUnread = notification.is_read == 0;
                        html += '<a href="' + targetUrl + '" class="dropdown-item notification-item ' + (isUnread ? 'unread' : '') + '" data-id="' + notification.id + '" data-url="' + targetUrl + '">';
                        html += '<div class="d-flex align-items-start">';
                        html += '<div class="notification-icon me-2">';
                        html += '<i class="fas fa-bell text-warning"></i>';
                        html += '</div>';
                        html += '<div class="notification-content flex-grow-1">';
                        html += '<div class="notification-title">' + title + '</div>';
                        if(message){
                            html += '<div class="notification-text">' + message + '</div>';
                        }
                        html += '<div class="notification-time">' + formatTimeAgo(notification.date_created) + '</div>';
                        html += '</div>';
                        if(isUnread){
                            html += '<div class="notification-dot ms-2"></div>';
                        }
                        html += '</div>';
                        html += '</a>';
                    });
                } else {
                    html = '<div class="text-center p-3 text-muted">No notifications</div>';
                }
                $('#notifications-content').html(html);
            }
        }
    });
  }
  
  function markNotificationRead(id, options){
    options = options || {};
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_notification_read",
        method: "POST",
        data: {notification_id: id},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                // Update the notification count immediately
                loadNotificationsCount();
                // If not redirecting, update the notification list
                if(!options.redirectTo){
                    loadNotifications();
                }
                if(typeof options.onSuccess === 'function'){
                    options.onSuccess(resp);
                }
                // Only redirect on success
                if(options.redirectTo){
                    window.location.href = options.redirectTo;
                }
            }else{
                // Show error message if marking as read failed
                if(typeof options.onError === 'function'){
                    options.onError(resp);
                } else {
                    alert_toast(resp.msg || 'Failed to mark notification as read', 'error');
                }
            }
        },
        error: function(err){
            if(typeof options.onError === 'function'){
                options.onError(err);
            } else {
                alert_toast('An error occurred while processing the notification', 'error');
            }
        }
    });
  }
  
  function markAllNotificationsRead(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_all_notifications_read",
        method: "POST",
        dataType: "json",
        complete: function(){
            loadNotificationsCount();
            loadNotifications();
        }
    });
  }
  
  function formatTimeAgo(dateString){
    var now = new Date();
    var date = new Date(dateString);
    var diff = now - date;
    var seconds = Math.floor(diff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    
    if(days > 0) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
    if(hours > 0) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
    if(minutes > 0) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
    return 'Just now';
  }
  
  function loadAllNotifications(){
    window.location.href = './?p=notifications';
  }
  
  function openNotificationHistory(event){
    if(event) event.preventDefault();
    notificationHistoryPage = 0;
    notificationHistoryHasMore = false;
    $('#notificationHistoryList').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading history...</div>');
    $('#notificationHistoryMeta').text('');
    $('#notificationHistoryLoadMore').hide();
    $('#notificationHistoryModal').modal('show');
    loadNotificationHistory(true);
  }
  
  function loadNotificationHistory(reset){
    if(notificationHistoryLoading) return;
    if(!reset && !notificationHistoryHasMore) return;
    notificationHistoryLoading = true;
    var offset = notificationHistoryPage * notificationHistoryLimit;
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_notification_history",
        method: "POST",
        data: {limit: notificationHistoryLimit, offset: offset},
        dataType: "json",
        success: function(resp){
            if(resp.status === 'success'){
                renderNotificationHistory(resp.items || [], reset);
                notificationHistoryHasMore = !!resp.has_more;
                $('#notificationHistoryMeta').text((resp.total || 0) + ' total notifications');
                if(notificationHistoryHasMore){
                    notificationHistoryPage++;
                    $('#notificationHistoryLoadMore').show();
                }else{
                    $('#notificationHistoryLoadMore').hide();
                }
            }else{
                $('#notificationHistoryList').html('<div class="text-center p-4 text-muted">Unable to load history.</div>');
                $('#notificationHistoryLoadMore').hide();
            }
        },
        complete: function(){
            notificationHistoryLoading = false;
        }
    });
  }
  
  function renderNotificationHistory(items, reset){
    var container = $('#notificationHistoryList');
    if(reset){
        container.empty();
    }
    if(!items.length && reset){
        container.html('<div class="text-center p-4 text-muted">No notifications yet.</div>');
        return;
    }
    items.forEach(function(notification){
        var message = notification.message || '';
        var title = notification.title || 'Notification';
        var targetUrl = getNotificationTarget(notification);
        var isUnread = notification.is_read == 0;
        var badge = isUnread ? '<span class="badge badge-warning badge-pill ml-2">New</span>' : '';
        var item = '<a href="' + targetUrl + '" class="list-group-item list-group-item-action notification-history-item ' + (isUnread ? 'unread' : '') + '" data-id="' + notification.id + '" data-url="' + targetUrl + '">';
        item += '<div class="d-flex justify-content-between align-items-center">';
        item += '<h6 class="mb-1">' + title + ' ' + badge + '</h6>';
        item += '<small class="text-muted">' + formatTimeAgo(notification.date_created) + '</small>';
        item += '</div>';
        if(message){
            item += '<p class="mb-1 text-muted">' + message + '</p>';
        }
        item += '</a>';
        container.append(item);
    });
  }
  
  // Load notifications on page load
  $(document).ready(function(){
    if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' == 1){
        loadNotificationsCount();
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(function(){
            loadNotificationsCount();
        }, 30000);
        
        $('#notifications-dropdown').on('click', function(){
            loadNotifications();
        });
        $('#notifications-dropdown').on('show.bs.dropdown', function(){
            loadNotifications();
        });
        if(!window.__clientNotifClickBound){
            $(document).on('click', '#notifications-content .notification-item', function(e){
                e.preventDefault();
                e.stopPropagation();
                var $item = $(this);
                var id = $item.data('id');
                var targetUrl = $item.data('url') || $item.attr('href') || './?p=notifications';
                if(!id){
                    window.location.href = targetUrl;
                    return;
                }
                // Close the dropdown before redirecting
                var $dropdownToggle = $('#notifications-dropdown');
                if($dropdownToggle.length && $dropdownToggle.data('bs.dropdown')){
                    $dropdownToggle.dropdown('hide');
                } else {
                    // Fallback: remove show class from dropdown menu
                    $('.dropdown-menu.show').removeClass('show');
                }
                // Mark as read and redirect
                markNotificationRead(id, { redirectTo: targetUrl });
            });
            window.__clientNotifClickBound = true;
        }
        
        // Handle notification history modal clicks
        if(!window.__historyNotifClickBound){
            $(document).on('click', '#notificationHistoryList .notification-history-item', function(e){
                e.preventDefault();
                e.stopPropagation();
                var $item = $(this);
                var id = $item.data('id');
                var targetUrl = $item.data('url') || $item.attr('href') || './?p=notifications';
                if(!id){
                    window.location.href = targetUrl;
                    return;
                }
                // Close the modal before redirecting
                $('#notificationHistoryModal').modal('hide');
                // Mark as read and redirect
                markNotificationRead(id, { redirectTo: targetUrl });
            });
            window.__historyNotifClickBound = true;
        }
    }
    
    // Mobile sidebar functionality
    $('#mobileSidebarToggle').click(function() {
        $('#mobileSidebar').addClass('show');
        $('#mobileSidebarOverlay').addClass('show');
        $('body').addClass('sidebar-open');
    });
    
    $('#mobileSidebarClose, #mobileSidebarOverlay').click(function() {
        $('#mobileSidebar').removeClass('show');
        $('#mobileSidebarOverlay').removeClass('show');
        $('body').removeClass('sidebar-open');
    });
    
    // Close sidebar when clicking on nav links
    $('.mobile-sidebar .nav-link').click(function() {
        $('#mobileSidebar').removeClass('show');
        $('#mobileSidebarOverlay').removeClass('show');
        $('body').removeClass('sidebar-open');
    });
    
    // Prevent body scroll when sidebar is open
    $(document).on('touchmove', function(e) {
        if ($('body').hasClass('sidebar-open')) {
            e.preventDefault();
        }
    });
  });
</script>