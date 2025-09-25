<?php 
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
        <img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="System Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light" title="<?php echo $_settings->info('name') ?>">
            <?php 
            $short_name = $_settings->info('short_name');
            if(!empty($short_name)) {
                echo $short_name;
            } else {
                // Create a shorter version if no short name is set
                $full_name = $_settings->info('name');
                if(strlen($full_name) > 20) {
                    echo "Star Honda BPSMS";
                } else {
                    echo $full_name;
                }
            }
            ?>
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo ucwords($_settings->userdata('firstname').' '.$_settings->userdata('lastname')) ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="./" class="nav-link <?php echo $page == 'home' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=products" class="nav-link <?php echo $page == 'products' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Products
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=inventory" class="nav-link <?php echo $page == 'inventory' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Inventory
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=inventory/motorcycle_variants" class="nav-link <?php echo $page == 'inventory/motorcycle_variants' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-motorcycle"></i>
                        <p>
                            Motorcycle Variants
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=orders" class="nav-link <?php echo $page == 'orders' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Orders
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=service_requests" class="nav-link <?php echo $page == 'service_requests' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Service Requests
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=clients" class="nav-link <?php echo $page == 'clients' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Clients
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=user_log_history" class="nav-link <?php echo $page == 'user_log_history' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                            User Activity Log
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=mechanics" class="nav-link <?php echo $page == 'mechanics' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Mechanics
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=reports" class="nav-link <?php echo $page == 'reports' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reports
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=maintenance" class="nav-link <?php echo $page == 'maintenance' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Maintenance
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=system_info" class="nav-link <?php echo $page == 'system_info' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-info-circle"></i>
                        <p>
                            System Info
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=users" class="nav-link <?php echo $page == 'users' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>