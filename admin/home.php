<h1 class="">Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>

<div class="row">
  <!-- Staff & Client Group -->
  <div class="col-12 mb-3">
    <h5><i class="fas fa-users mr-2"></i> Staff & Clients</h5>
  </div>
  
  <!-- Mechanics -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-gray elevation-1"><i class="fas fa-users-cog"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Mechanics</span>
        <span class="info-box-number-sm">
          <?php 
            $mechanics = $conn->query("SELECT COUNT(id) as total FROM mechanics_list WHERE status = 1")->fetch_assoc()['total'];
            echo number_format($mechanics);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Registered Clients -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-white elevation-1"><i class="fas fa-users"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Registered Clients</span>
        <span class="info-box-number-sm">
          <?php 
            $clients = $conn->query("SELECT COUNT(id) as total FROM client_list WHERE status = 1 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($clients);
          ?>
        </span>
      </div>
    </div>
  </div>
  
  <!-- Services & Appointments Group -->
  <div class="col-12 mb-3 mt-4">
    <h5><i class="fas fa-tools mr-2"></i> Services & Appointments</h5>
  </div>

  <!-- Service Requests -->
  <div class="col-12 col-sm-6 col-md-6">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-info elevation-1"><i class="fas fa-tools"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Service Requests</span>
        <span class="info-box-number-sm">
          <?php 
            $new_requests = $conn->query("SELECT COUNT(id) as total FROM service_requests WHERE status = 0 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($new_requests);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Finished Requests -->
  <div class="col-12 col-sm-6 col-md-6">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Finished Requests</span>
        <span class="info-box-number-sm">
          <?php 
            $finished_requests = $conn->query("SELECT COUNT(id) as total FROM service_requests WHERE status = 3 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($finished_requests);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Appointments -->
  <div class="col-12 col-sm-6 col-md-6">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-warning elevation-1"><i class="fas fa-calendar-alt"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Appointments</span>
        <span class="info-box-number-sm">
          <?php 
            $new_appointments = $conn->query("SELECT COUNT(id) as total FROM appointments WHERE status = 'pending' AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($new_appointments);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Confirmed Appointments -->
  <div class="col-12 col-sm-6 col-md-6">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-primary elevation-1"><i class="fas fa-calendar-alt"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Confirmed Appointments</span>
        <span class="info-box-number-sm">
          <?php 
            $confirmed_appointments = $conn->query("SELECT COUNT(id) as total FROM appointments WHERE status = 'confirmed' AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($confirmed_appointments);
          ?>
        </span>
      </div>
    </div>
  </div>
  
  <!-- Orders Group -->
  <div class="col-12 mb-3 mt-4">
    <h5><i class="fas fa-shopping-cart mr-2"></i> Orders</h5>
  </div>

  <!-- Order Status Cards -->
  <div class="col-12 col-sm-6 col-md-4">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-warning elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Pending Orders</span>
        <span class="info-box-number-sm">
          <?php 
            $pending_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 0 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($pending_orders);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Confirmed Orders -->
  <div class="col-12 col-sm-6 col-md-4">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-green elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Confirmed Orders</span>
        <span class="info-box-number-sm">
          <?php 
            $confirmed_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 1 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($confirmed_orders);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Cancelled Orders -->
  <div class="col-12 col-sm-6 col-md-4">
    <div class="shadow info-box-sm mb-3">
      <span class="info-box-icon-sm bg-danger elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content-sm">
        <span class="info-box-text-sm">Cancelled Orders</span>
        <span class="info-box-number-sm">
          <?php 
            $cancelled_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 5 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($cancelled_orders);
          ?>
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions Section -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-bolt"></i> Quick Actions
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 col-sm-6 mb-3">
            <a href="?page=products/manage_product" class="btn btn-primary btn-block btn-lg quick-action-btn quick-action-add-product" style="height: 100px; display: flex; flex-direction: row; justify-content: flex-start; align-items: center; text-align: left; padding: 15px; border-radius: 8px; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('https://i.imgur.com/zmYxD0p.jpg') center center / cover no-repeat; color: white; text-decoration: none; position: relative; overflow: hidden;" title="Add new motorcycle product">
              <div class="quick-action-overlay"></div>
              <i class="fas fa-plus" style="font-size: 1.5rem; margin-right: 15px; min-width: 40px; text-align: center; position: relative; z-index: 2;"></i>
              <div style="display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 2;">
                <strong style="font-size: 0.9rem; display: block;">Add Product</strong>
                <small style="opacity: 0.9; display: block;">New motorcycle part</small>
              </div>
            </a>
          </div>
          <div class="col-md-4 col-sm-6 mb-3">
            <a href="?page=orders" class="btn btn-info btn-block btn-lg quick-action-btn quick-action-manage-orders" style="height: 100px; display: flex; flex-direction: row; justify-content: flex-start; align-items: center; text-align: left; padding: 15px; border-radius: 8px; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('https://i.imgur.com/4rHyuj1.jpg') center center / cover no-repeat; color: white; text-decoration: none; position: relative; overflow: hidden;" title="Manage customer orders">
              <div class="quick-action-overlay"></div>
              <i class="fas fa-shopping-cart" style="font-size: 1.5rem; margin-right: 15px; min-width: 40px; text-align: center; position: relative; z-index: 2;"></i>
              <div style="display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 2;">
                <strong style="font-size: 0.9rem; display: block;">Manage Orders</strong>
                <small style="opacity: 0.9; display: block;">View and manage</small>
              </div>
            </a>
          </div>
          <div class="col-md-4 col-sm-6 mb-3">
            <a href="?page=service_requests" class="btn btn-success btn-block btn-lg quick-action-btn quick-action-service" style="height: 100px; display: flex; flex-direction: row; justify-content: flex-start; align-items: center; text-align: left; padding: 15px; border-radius: 8px; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('https://i.imgur.com/Yx85Kk9.jpg') center center / cover no-repeat; color: white; text-decoration: none; position: relative; overflow: hidden;" title="Manage service requests and appointments">
              <div class="quick-action-overlay"></div>
              <i class="fas fa-cogs" style="font-size: 1.5rem; margin-right: 15px; min-width: 40px; text-align: center; position: relative; z-index: 2;"></i>
              <div style="display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 2;">
                <strong style="font-size: 0.9rem; display: block;">Service Management</strong>
                <small style="opacity: 0.9; display: block;">Requests & appointments</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Smaller info box styles */
.info-box-sm {
  display: flex;
  align-items: center;
  padding: 10px;
  min-height: 80px;
  border-radius: 8px;
  background: #fff;
}

.info-box-icon-sm {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  min-width: 60px;
  border-radius: 8px;
  margin-right: 15px;
  color: white;
  font-size: 1.5rem;
}

.info-box-content-sm {
  flex: 1;
  min-width: 0;
}

.info-box-text-sm {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #6c757d;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.info-box-number-sm {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #343a40;
}

/* Quick action buttons */
.quick-action-btn {
  transition: all 0.3s ease;
  border: none;
  position: relative;
  overflow: hidden;
}

.quick-action-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7));
  z-index: 1;
  transition: all 0.3s ease;
}

.quick-action-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  text-decoration: none;
}

.quick-action-btn:hover::before {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6));
}

.quick-action-btn i,
.quick-action-btn strong,
.quick-action-btn small {
  position: relative;
  z-index: 2;
}

.quick-action-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
  z-index: 1;
}

/* Background images for specific buttons */
.quick-action-add-product {
  background: url('https://i.imgur.com/zmYxD0p.jpg') center center / cover no-repeat !important;
}

.quick-action-manage-orders {
  background: url('https://i.imgur.com/4rHyuj1.jpg') center center / cover no-repeat !important;
}

.quick-action-service {
  background: url('https://i.imgur.com/Yx85Kk9.jpg') center center / cover no-repeat !important;
}

/* Group headings */
h5 {
  color: #6c757d;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
  padding-bottom: 8px;
  margin-bottom: 15px;
  font-size: 1rem;
}

/* CSS for aligning notification bell with profile icon */
.navbar-nav .nav-item {
  display: flex;
  align-items: center;
}

.navbar-nav .nav-link {
  display: flex;
  align-items: center;
  height: 38px;
  padding: 0 10px;
}

/* Specific fix for bell icon alignment */
.bell-icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

/* Ensure profile dropdown aligns with bell */
.profile-dropdown {
  display: flex;
  align-items: center;
  height: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .info-box-sm {
    min-height: 70px;
  }
  
  .info-box-icon-sm {
    width: 50px;
    height: 50px;
    min-width: 50px;
    font-size: 1.2rem;
  }
  
  .info-box-number-sm {
    font-size: 1.3rem;
  }
  
  .quick-action-btn {
    height: 90px !important;
    padding: 12px !important;
  }
  
  .quick-action-btn i {
    font-size: 1.2rem !important;
    margin-right: 10px !important;
    min-width: 35px !important;
  }
}
</style>

<!-- Add this JavaScript if you need to dynamically align elements -->
<script>
$(document).ready(function() {
  // Align notification bell with profile icon
  function alignNavIcons() {
    const bellIcon = $('.fa-bell').closest('.nav-link');
    const profileIcon = $('.user-image').closest('.nav-link');
    
    if (bellIcon.length && profileIcon.length) {
      const bellHeight = bellIcon.outerHeight();
      const profileHeight = profileIcon.outerHeight();
      
      // Set same min-height if needed
      const maxHeight = Math.max(bellHeight, profileHeight);
      bellIcon.css('min-height', maxHeight + 'px');
      profileIcon.css('min-height', maxHeight + 'px');
      
      // Center icons vertically
      bellIcon.css('display', 'flex');
      bellIcon.css('align-items', 'center');
      profileIcon.css('display', 'flex');
      profileIcon.css('align-items', 'center');
    }
  }
  
  // Run on load and window resize
  alignNavIcons();
  $(window).resize(alignNavIcons);
  
  // Preload background images for better performance
  const imageUrls = [
    'https://i.imgur.com/zmYxD0p.jpg',
    'https://i.imgur.com/4rHyuj1.jpg',
    'https://i.imgur.com/Yx85Kk9.jpg'
  ];
  
  imageUrls.forEach(url => {
    const img = new Image();
    img.src = url;
  });
});
</script>