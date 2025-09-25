<h1 class="">Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<div class="row">
  <!-- Total Brands -->
  <!-- <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-copyright"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Brands</span>
        <span class="info-box-number">
          <?php 
            $brands = $conn->query("SELECT COUNT(id) as total FROM brand_list WHERE delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($brands);
          ?>
        </span>
      </div>
    </div>
  </div> -->

  <!-- Total Category -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-light elevation-1"><i class="fas fa-th-list"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Category</span>
        <span class="info-box-number">
          <?php 
            $categories = $conn->query("SELECT COUNT(id) as total FROM categories WHERE delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($categories);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Mechanics -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-gray elevation-1"><i class="fas fa-users-cog"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Mechanics</span>
        <span class="info-box-number">
          <?php 
            $mechanics = $conn->query("SELECT COUNT(id) as total FROM mechanics_list WHERE status = 1")->fetch_assoc()['total'];
            echo number_format($mechanics);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Services -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-success elevation-1"><i class="fas fa-th-list"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Services</span>
        <span class="info-box-number">
          <?php 
            $services = $conn->query("SELECT COUNT(id) as total FROM service_list WHERE status = 1")->fetch_assoc()['total'];
            echo number_format($services);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Registered Clients -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-white elevation-1"><i class="fas fa-users"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Registered Clients</span>
        <span class="info-box-number">
          <?php 
            $clients = $conn->query("SELECT COUNT(id) as total FROM client_list WHERE status = 1 AND delete_flag = 0")->fetch_assoc()['total'];
            echo number_format($clients);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Pending Orders -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-yellow elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pending Orders</span>
        <span class="info-box-number">
          <?php 
            $pending_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 0")->fetch_assoc()['total'];
            echo number_format($pending_orders);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Confirmed Orders -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-green elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Confirmed Orders</span>
        <span class="info-box-number">
          <?php 
            $confirmed_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 1")->fetch_assoc()['total'];
            echo number_format($confirmed_orders);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Cancelled Orders -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-red elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Cancelled Orders</span>
        <span class="info-box-number">
          <?php 
            $cancelled_orders = $conn->query("SELECT COUNT(id) as total FROM order_list WHERE status = 5")->fetch_assoc()['total'];
            echo number_format($cancelled_orders);
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Finished Requests -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="shadow info-box mb-3">
      <span class="info-box-icon bg-yellow elevation-1"><i class="fas fa-file-invoice"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Finished Requests</span>
        <span class="info-box-number">
          <?php 
            $finished_requests = $conn->query("SELECT COUNT(id) as total FROM service_requests WHERE status = 3")->fetch_assoc()['total'];
            echo number_format($finished_requests);
          ?>
        </span>
      </div>
    </div>
  </div>
</div>
