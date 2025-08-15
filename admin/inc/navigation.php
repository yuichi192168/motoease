<?php 
$usertype = $_settings->userdata('type');
$role_type = $_settings->userdata('role_type') ?: 'admin';
?>
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
                <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2" alt="User Image">
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
                            <a href="./?page=user" class="nav-link <?php echo $page == 'user' ? 'active' : '' ?>">
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
                <li class="nav-item <?php echo in_array($page, ['products','inventory','maintenance']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['products','inventory','maintenance']) ? 'active' : '' ?>">
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
                            <a href="./?page=maintenance" class="nav-link <?php echo $page == 'maintenance' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Maintenance</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Service Management -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'service_admin', 'mechanic'])): ?>
                <li class="nav-item <?php echo in_array($page, ['service_requests','mechanics']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['service_requests','mechanics']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Services
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./?page=service_requests" class="nav-link <?php echo $page == 'service_requests' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Requests</p>
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
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'admin_assistant'])): ?>
                <li class="nav-item">
                    <a href="./?page=orders" class="nav-link <?php echo $page == 'orders' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Reports -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor'])): ?>
                <li class="nav-item <?php echo in_array($page, ['report']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['report']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./?page=report" class="nav-link <?php echo $page == 'report' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service Reports</p>
                            </a>
                        </li>
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
                <?php endif; ?>
                
                <!-- Branch Management (Admin Only) -->
                <?php if(in_array($role_type, ['admin'])): ?>
                <li class="nav-item">
                    <a href="./?page=branches" class="nav-link <?php echo $page == 'branches' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Branch Management</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Customer Account Management -->
                <?php if(in_array($role_type, ['admin', 'branch_supervisor', 'admin_assistant'])): ?>
                <li class="nav-item <?php echo in_array($page, ['customer_accounts','orcr_documents']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, ['customer_accounts','orcr_documents']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Customer Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./?page=customer_accounts" class="nav-link <?php echo $page == 'customer_accounts' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Account Balances</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./?page=orcr_documents" class="nav-link <?php echo $page == 'orcr_documents' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>OR/CR Documents</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Logout -->
                <li class="nav-item">
                    <a href="<?php echo base_url.'admin/logout.php' ?>" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>