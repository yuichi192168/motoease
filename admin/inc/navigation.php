<?php 
$usertype = $_settings->userdata('type');
$role_type = $_settings->userdata('role_type') ?: 'admin';
$user_permissions = getUserPermissions();
$is_admin = ($role_type === 'admin' || in_array('all', $user_permissions));
$service_roles = ['service_admin','service_receptionist'];
$is_service_role = in_array($role_type, $service_roles);
// Service roles but only service_admin should manage images; receptionists are service-only
$is_service_admin = ($role_type === 'service_admin');
// Explicit flag for service receptionist
$is_service_receptionist = ($role_type === 'service_receptionist');
$data_admin_roles = ['data_admin'];
$is_data_admin = in_array($role_type, $data_admin_roles);
// Role flags for cleaner navigation conditions
$is_system_admin = ($role_type === 'system_admin');
$is_desk_staff = ($role_type === 'desk_staff');
$is_inventory = ($role_type === 'inventory');

// Role display names
$role_display_names = [
    'admin' => 'Admin',
    'system_admin' => 'System Admin',
    'desk_staff' => 'Desk/Staff',
    'data_admin' => 'Data Admin',
    'inventory' => 'Inventory',
    'service_admin' => 'Service Receptionist',
    'service_receptionist' => 'Service Receptionist'
];
$role_display = isset($role_display_names[$role_type]) ? $role_display_names[$role_type] : ucfirst(str_replace('_', ' ', $role_type));
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
                <small class="text-muted"><?php echo $role_display ?></small>
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
                <?php if($is_admin || $is_system_admin || hasPermission('user_management') || hasPermission('customer_management')): ?>
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
                        <?php if($is_admin || $is_system_admin || hasPermission('user_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=user/list" class="nav-link <?php echo ($page == 'user' || $page == 'user/list') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>System Users</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($is_admin || $is_system_admin || hasPermission('customer_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=clients" class="nav-link <?php echo $page == 'clients' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Customers</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
               <!-- Inventory Management -->
<?php if($is_admin || hasPermission('inventory_management') || hasPermission('product_management') || hasPermission('stock_management')): ?>
<li class="nav-item <?php echo in_array($page, ['products','inventory','inventory/abc_analysis']) ? 'menu-open' : '' ?>">
    <a href="#" class="nav-link <?php echo in_array($page, ['products','inventory','inventory/abc_analysis']) ? 'active' : '' ?>">
        <i class="nav-icon fas fa-boxes"></i>
        <p>
            Inventory
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <?php if($is_admin || hasPermission('product_management')): ?>
        <li class="nav-item">
            <a href="./?page=products" class="nav-link <?php echo $page == 'products' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Products</p>
            </a>
        </li>
        <?php endif; ?>
        <?php if($is_admin || hasPermission('stock_management')): ?>
        <li class="nav-item">
            <a href="./?page=inventory" class="nav-link <?php echo $page == 'inventory' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Stock Management</p>
            </a>
        </li>
        <?php endif; ?>
        <?php if($is_admin || hasPermission('inventory_management')): ?>
        <li class="nav-item">
            <a href="./?page=inventory/abc_analysis" class="nav-link <?php echo $page == 'inventory/abc_analysis' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>ABC Analysis</p>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>
                
                <!-- Service Management -->
                <?php if($is_admin || $is_service_role || hasPermission('service_requests') || hasPermission('mechanics')): ?>
                <li class="nav-item <?php echo in_array($page, ['service_management','service_requests','mechanics']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['service_management','service_requests','mechanics']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Services
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if($is_admin || $is_service_role || hasPermission('service_requests')): ?>
                        <li class="nav-item">
                            <a href="./?page=service_requests" data-href="./?page=service_requests&status=pending" id="link-services" class="nav-link <?php echo $page == 'service_requests' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Requests <span id="dot-services" class="sidebar-dot" style="display:none;"></span></p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <!-- Appointments commented out per requirements -->
                        <!-- <li class="nav-item">
                            <a href="./?page=appointments" data-href="./?page=appointments&status=pending" id="link-appointments" class="nav-link <?php echo $page == 'appointments' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Appointments <span id="dot-appointments" class="sidebar-dot" style="display:none;"></span></p>
                            </a>
                        </li> -->
                        <?php if($is_admin || $is_service_role || hasPermission('mechanics')): ?>
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
                
                <!-- Promo & Customer Images Management -->
                <?php 
                // Images Management is strictly role-based: only Admin, Service Admin, and Data Admin.
                // Service Receptionist must NEVER see this section, even if permissions are misconfigured.
                if(in_array($role_type, ['admin', 'data_admin'])): ?>
                <li class="nav-item <?php echo in_array($page, ['promo_images','customer_images']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['promo_images','customer_images']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-images"></i>
                        <p>
                            Images Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if(in_array($role_type, ['admin', 'data_admin'])): ?>
                        <li class="nav-item">
                            <a href="./?page=promo_images" class="nav-link <?php echo $page == 'promo_images' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Promo Images</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array($role_type, ['admin', 'data_admin'])): ?>
                        <li class="nav-item">
                            <a href="./?page=customer_images" class="nav-link <?php echo $page == 'customer_images' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Customer Images</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Content & Data Management -->
                <?php if($is_admin || $is_data_admin || hasPermission('reviews_management') || hasPermission('announcements_management') || hasPermission('content_management')): ?>
                <!-- <li class="nav-item <?php echo in_array($page, ['reviews','announcements','content']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['reviews','announcements','content']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p>
                            Content & Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if($is_admin || $is_data_admin || hasPermission('reviews_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=reviews" class="nav-link <?php echo $page == 'reviews' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Reviews</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($is_admin || $is_data_admin || hasPermission('announcements_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=announcements" class="nav-link <?php echo $page == 'announcements' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Announcements</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($is_admin || $is_data_admin || hasPermission('content_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=content" class="nav-link <?php echo $page == 'content' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Content Management</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li> -->
                <?php endif; ?>
                
                <!-- Order Management -->
                <?php if($is_admin || hasPermission('order_management')): ?>
                <li class="nav-item">
                    <a href="./?page=orders" data-href="./?page=orders&status=pending" id="link-orders" class="nav-link <?php echo $page == 'orders' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders <span id="dot-orders" class="sidebar-dot" style="display:none;"></span></p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Reports -->
                <?php if($is_admin || $is_desk_staff || hasPermission('reports')): ?>
                <li class="nav-item <?php echo in_array($page, ['report', 'report/orders', 'report/service_requests', 'report/invoices', 'user_log_history']) ? 'menu-open' : '' ?>">
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
                        <?php if($is_admin || $is_desk_staff || hasPermission('reports')): ?>
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
                            <a href="./?page=report/invoices" class="nav-link <?php echo $page == 'report/invoices' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Invoice Report</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if($is_admin || hasPermission('reports')): ?>
                        <li class="nav-item">
                            <a href="./?page=user_log_history" class="nav-link <?php echo $page == 'user_log_history' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User Activity Log</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Services & Categories Management -->
                <?php if($is_admin || $role_type === 'desk_staff' || hasPermission('system_settings')): ?>
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

                 <!-- Customer Account Management -->
                 <?php if($is_admin || $is_system_admin || $is_desk_staff || hasPermission('customer_accounts') || hasPermission('invoice_management') || hasPermission('orcr_documents')): ?>
                <li class="nav-item <?php echo in_array($page, ['customer_accounts', 'customer_account_balances', 'invoices', 'orcr_documents']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['customer_accounts', 'customer_account_balances', 'invoices', 'orcr_documents']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Customer Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if($is_admin || $is_system_admin || hasPermission('customer_accounts')): ?>
                        <li class="nav-item">
                            <a href="./?page=customer_account_balances" class="nav-link <?php echo $page == 'customer_account_balances' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Account Balances</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <!-- <li class="nav-item">
                            <a href="./?page=customer_accounts" class="nav-link <?php echo $page == 'customer_accounts' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Account Balances (Legacy)</p>
                            </a>
                        </li> -->
                        <?php if($is_admin || hasPermission('invoice_management')): ?>
                        <li class="nav-item">
                            <a href="./?page=invoices" class="nav-link <?php echo $page == 'invoices' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Invoices & Receipts</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($is_admin || $is_desk_staff || hasPermission('orcr_documents')): ?>
                        <li class="nav-item">
                            <a href="./?page=orcr_documents" class="nav-link <?php echo $page == 'orcr_documents' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>OR/CR Documents</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- System Settings -->
                <?php if($is_admin || $role_type === 'desk_staff' || hasPermission('system_settings')): ?>
                <li class="nav-item">
                    <a href="./?page=system_info" class="nav-link <?php echo $page == 'system_info' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>System Settings</p>
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
                </li>
                <?php endif; ?> -->
                
               
                
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>