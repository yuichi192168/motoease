<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-cogs"></i> Service Management
		</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Navigation Tabs -->
			<ul class="nav nav-tabs" id="serviceTabs" role="tablist">
				<li class="nav-item" role="presentation">
					<a class="nav-link <?= (!isset($_GET['tab']) || $_GET['tab'] == 'requests') ? 'active' : '' ?>" 
					   href="?page=service_management&tab=requests" role="tab">
						<i class="fas fa-tools"></i> Service Requests
					</a>
				</li>
				<li class="nav-item" role="presentation">
					<a class="nav-link <?= (isset($_GET['tab']) && $_GET['tab'] == 'appointments') ? 'active' : '' ?>" 
					   href="?page=service_management&tab=appointments" role="tab">
						<i class="fas fa-calendar-alt"></i> Appointments
					</a>
				</li>
			</ul>
			
			<!-- Tab Content -->
			<div class="tab-content" id="serviceTabsContent">
				<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'requests'): ?>
				<!-- Service Requests Tab -->
				<div class="tab-pane fade show active" id="requests" role="tabpanel" aria-labelledby="requests-tab">
					<?php include 'service_requests.php'; ?>
				</div>
				<?php endif; ?>
				
				<?php if (isset($_GET['tab']) && $_GET['tab'] == 'appointments'): ?>
				<!-- Appointments Tab -->
				<div class="tab-pane fade show active" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
					<?php include 'appointments.php'; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<style>
.nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 0;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 transparent;
    text-decoration: none;
    padding: 12px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #e9ecef;
    text-decoration: none;
    color: #495057;
    transform: translateY(-2px);
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: bold;
    border-bottom: 2px solid #007bff;
}

.nav-tabs .nav-link i {
    margin-right: 8px;
    font-size: 1.1em;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 25px;
    background-color: #fff;
    min-height: 500px;
    border-radius: 0 0 0.5rem 0.5rem;
}

.card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.card-header .card-title {
    color: white;
    font-weight: bold;
}
</style>

<script>
$(document).ready(function(){
    // Add active class management for better visual feedback
    $('.nav-tabs .nav-link').click(function(e){
        e.preventDefault();
        
        // Remove active class from all tabs
        $('.nav-tabs .nav-link').removeClass('active');
        
        // Add active class to clicked tab
        $(this).addClass('active');
        
        // Navigate to the URL
        window.location.href = $(this).attr('href');
    });
    
    // Highlight current tab based on URL
    var currentTab = '<?= isset($_GET['tab']) ? $_GET['tab'] : 'requests' ?>';
    $('.nav-tabs .nav-link').removeClass('active');
    if(currentTab === 'appointments') {
        $('.nav-tabs .nav-link[href*="appointments"]').addClass('active');
    } else {
        $('.nav-tabs .nav-link[href*="requests"]').addClass('active');
    }
});
</script>
