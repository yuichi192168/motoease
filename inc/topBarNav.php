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
            <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'services'? "class='nav-link fw-bold active text-center'" : '' ?> href="./?p=services">Services</a>
            <a class="nav-link fw-bold text-center" style="font-size:1.2rem; font-weight: 700;" <?= isset($page) && $page == 'about'? "class='nav-link fw-bold active text-center'" : '' ?> href="./?p=about">About Us</a>
        </div>
        
        <!-- Right side menu - Only show on desktop -->
        <div class="navbar-nav ms-auto d-none d-lg-flex">
            <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                <!-- Cart -->
                <div class="nav-item">
                    <?php 
                    $cart_count = $conn->query("SELECT SUM(quantity) from cart_list where client_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
                    $cart_count = $cart_count > 0 ? number_format($cart_count) : 0;
                    ?>
                    <a href="./?p=cart" class="nav-link position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart_count" class="badge badge-danger rounded-circle position-absolute top-0 start-100 translate-middle" <?= $cart_count == 0 ? 'style="display:none;"' : '' ?>><?= $cart_count ?></span>
                        <span class="ms-1">Cart</span>
                    </a>
                </div>
                
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link position-relative" data-toggle="dropdown" href="#" id="notifications-dropdown">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge" id="notifications-count">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-list">
                        <span class="dropdown-header">Notifications</span>
                        <div class="dropdown-divider"></div>
                        <div id="notifications-content">
                            <div class="text-center p-3">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer" onclick="loadAllNotifications()">View All Notifications</a>
                    </div>
                </div>
                
                <!-- Profile Settings -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="rounded-circle me-2" style="width: 25px; height: 25px; object-fit: cover;" alt="Avatar">
                        <span><?= $_settings->userdata('firstname') ? ucwords($_settings->userdata('firstname')) : $_settings->userdata('email') ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdownMenuLink">
                        <a class="dropdown-item" href="./?p=my_orders"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        <a class="dropdown-item" href="./?p=my_services"><i class="fas fa-tools me-2"></i>My Service Requests</a>
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
                        <i class="far fa-bell"></i>
                        <span>Notifications</span>
                        <span class="badge badge-warning" id="mobile-notifications-count">0</span>
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
                <li class="nav-item">
                    <a class="nav-link fw-bold <?= isset($page) && $page == 'services'? "active" : '' ?>" href="./?p=services" style="font-size:1.1rem; font-weight: 700;">
                        <i class="fas fa-tools"></i>
                        <span>Services</span>
                    </a>
                </li>
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
                            <span>My Service Requests</span>
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
                        html += '<a href="#" class="dropdown-item notification-item ' + (notification.is_read == 0 ? 'unread' : '') + '" onclick="markNotificationRead(' + notification.id + ')">';
                        html += '<div class="d-flex align-items-start">';
                        html += '<div class="notification-icon me-2">';
                        html += '<i class="fas fa-bell text-warning"></i>';
                        html += '</div>';
                        html += '<div class="notification-content">';
                        html += '<div class="notification-title">' + notification.title + '</div>';
                        html += '<div class="notification-text">' + notification.description + '</div>';
                        html += '<div class="notification-time">' + formatTimeAgo(notification.date_created) + '</div>';
                        html += '</div>';
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
  
  function markNotificationRead(id){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_notification_read",
        method: "POST",
        data: {notification_id: id},
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
  
  // Load notifications on page load
  $(document).ready(function(){
    if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' == 1){
        loadNotificationsCount();
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(function(){
            loadNotificationsCount();
        }, 30000);
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