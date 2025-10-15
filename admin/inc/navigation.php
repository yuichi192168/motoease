$<?php 
$usertype = $_settings->userdata('type');
$role_type = $_settings->userdata('role_type') ?: 'admin';
?>
<style>
    /* Small red notification dot for sidebar */
    .sidebar-dot{
        display:inline-block;
        width:10px;
        height:10px;
        background:#dc3545; /* bootstrap danger */
        border-radius:50%;
        margin-left:6px;
        vertical-align:middle;
        box-shadow: 0 0 0 2px rgba(220,53,69,0.08);
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link bg-primary">
        <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="System Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $_settings->info('name') ?></span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2" alt="User Image" style="width: 40px; height: 40px; object-fit: cover;">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo ucwords($_settings->userdata('firstname').' '.$_settings->userdata('lastname')) ?></a>
                <small class="text-muted"><?php echo ucfirst($role_type) ?></small>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="./" class="nav-link <?php echo $page == 'home' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Notifications removed: using per-section indicators instead -->
                
                <!-- Role-based Menu Items -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'admin_assistant'])): ?>
                <!-- User Management -->
                <li class="nav-item <?php echo in_array($page, ['user','clients']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['user','clients']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            User Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if(in_array($role_type, ['admin'])): ?>
                        <li class="nav-item">
                            <a href="./?page=user/list" class="nav-link <?php echo ($page == 'user' || $page == 'user/list') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>System Users</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a href="./?page=clients" class="nav-link <?php echo $page == 'clients' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Customers</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
               <!-- Inventory Management -->
<?php if(in_array($role_type, ['admin', 'branch_supervisor', 'stock_admin'])): ?>
<li class="nav-item <?php echo in_array($page, ['products','inventory','inventory/abc_analysis']) ? 'menu-open' : '' ?>">
    <a href="#" class="nav-link <?php echo in_array($page, ['products','inventory','inventory/abc_analysis']) ? 'active' : '' ?>">
        <i class="nav-icon fas fa-boxes"></i>
        <p>
            Inventory
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="./?page=products" class="nav-link <?php echo $page == 'products' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Products</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="./?page=inventory" class="nav-link <?php echo $page == 'inventory' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Stock Management</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="./?page=inventory/abc_analysis" class="nav-link <?php echo $page == 'inventory/abc_analysis' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>ABC Analysis</p>
            </a>
        </li>
    </ul>
</li>
<?php endif; ?>
                
                <!-- Service Management -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'service_admin', 'mechanic'])): ?>
                <li class="nav-item <?php echo in_array($page, ['service_management','service_requests','appointments','mechanics']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['service_management','service_requests','appointments','mechanics']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Services
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./?page=service_requests" data-href="./?page=service_requests&status=pending" id="link-services" class="nav-link <?php echo $page == 'service_requests' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Requests <span id="dot-services" class="sidebar-dot" style="display:none;"></span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=appointments" data-href="./?page=appointments&status=pending" id="link-appointments" class="nav-link <?php echo $page == 'appointments' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Appointments <span id="dot-appointments" class="sidebar-dot" style="display:none;"></span></p>
                            </a>
                        </li>
                        <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'service_admin'])): ?>
                        <li class="nav-item">
                            <a href="./?page=mechanics" class="nav-link <?php echo $page == 'mechanics' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mechanics</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Order Management -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'admin_assistant', 'service_admin'])): ?>
                <li class="nav-item">
                    <a href="./?page=orders" data-href="./?page=orders&status=pending" id="link-orders" class="nav-link <?php echo $page == 'orders' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders <span id="dot-orders" class="sidebar-dot" style="display:none;"></span></p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Reports -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'service_admin'])): ?>
                <li class="nav-item <?php echo in_array($page, ['report', 'user_log_history']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['report', 'user_log_history']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- <li class="nav-item">
                            <a href="./?page=report" class="nav-link <?php echo $page == 'report' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Reports</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="./?page=report/orders" class="nav-link <?php echo $page == 'report/orders' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Order Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=report/service_requests" class="nav-link <?php echo $page == 'report/service_requests' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Request Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=invoices" class="nav-link <?php echo $page == 'invoices' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Invoices & Receipts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=orcr_documents" class="nav-link <?php echo $page == 'orcr_documents' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>OR/CR Documents</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=user_log_history" class="nav-link <?php echo $page == 'user_log_history' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User Activity Log</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Services & Categories Management -->
                <?php if(in_array($role_type, ['admin'])): ?>
                <li class="nav-item <?php echo in_array($page, ['maintenance/category','maintenance/services','maintenance/manage_category','maintenance/manage_service']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['maintenance/category','maintenance/services','maintenance/manage_category','maintenance/manage_service']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./?page=maintenance/category" class="nav-link <?php echo in_array($page, ['maintenance/category','maintenance/manage_category']) ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=maintenance/services" class="nav-link <?php echo in_array($page, ['maintenance/services','maintenance/manage_service']) ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Services</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- System Settings -->
                <?php if(in_array($role_type, ['admin'])): ?>
                <li class="nav-item">
                    <a href="./?page=system_info" class="nav-link <?php echo $page == 'system_info' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>System Settings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./?page=promo_management" class="nav-link <?php echo $page == 'promo_management' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-images"></i>
                        <p>Promo & Customer Images</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Branch Management (Admin Only) -->
                <!-- <?php if(in_array($role_type, ['admin'])): ?>
                <li class="nav-item">
                    <a href="./?page=branches" class="nav-link <?php echo $page == 'branches' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Branch Management</p>
                    </a>
                </li> -->
                <?php endif; ?>
                
                <!-- Customer Account Management -->
                <!-- <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'admin_assistant'])): ?>
                <li class="nav-item <?php echo in_array($page, ['customer_accounts']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['customer_accounts']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Customer Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- <li class="nav-item">
                            <a href="./?page=customer_accounts" class="nav-link <?php echo $page == 'customer_accounts' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Account Balances</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <?php endif; ?> -->
                
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
