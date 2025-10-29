<script>
  $(document).ready(function(){
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
  
  // Admin Notification functions
  function loadAdminNotificationsCount(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_admin_notifications_count",
        method: "POST",
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                $('#admin-notifications-count').text(resp.count);
                if(resp.count > 0){
                    $('#admin-notifications-count').show();
                } else {
                    $('#admin-notifications-count').hide();
                }
            }
        }
    });
  }
  
  function loadAdminNotifications(){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_admin_notifications",
        method: "POST",
        data: {limit: 5},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                var html = '';
                if(resp.data.length > 0){
                    resp.data.forEach(function(notification){
                        html += '<a href="#" class="dropdown-item notification-item ' + (notification.is_read == 0 ? 'unread' : '') + '" onclick="markAdminNotificationRead(' + notification.id + ')">';
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
                $('#admin-notifications-content').html(html);
            }
        }
    });
  }
  
  function markAdminNotificationRead(id){
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=mark_admin_notification_read",
        method: "POST",
        data: {id: id},
        dataType: "json",
        success: function(resp){
            if(resp.status == 'success'){
                loadAdminNotificationsCount();
                loadAdminNotifications();
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
  
  function loadAllAdminNotifications(){
    window.location.href = './?page=notifications';
  }
  
  // Load admin notifications on page load
  $(document).ready(function(){
    loadAdminNotificationsCount();
    loadAdminNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(function(){
        loadAdminNotificationsCount();
    }, 30000);
    
    // Enhanced mobile sidebar functionality
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if ($(window).width() <= 991) {
            // Toggle sidebar visibility
            $('.main-sidebar').toggleClass('show');
            $('body').toggleClass('sidebar-open');
            
            // Show/hide overlay
            if ($('.main-sidebar').hasClass('show')) {
                $('.sidebar-overlay').show();
            } else {
                $('.sidebar-overlay').hide();
            }
        }
    });
    
    // Close sidebar when clicking overlay
    $('.sidebar-overlay').on('click', function() {
        $('.main-sidebar').removeClass('show');
        $('body').removeClass('sidebar-open');
        $('.sidebar-overlay').hide();
    });
    
    // Close sidebar when clicking outside on mobile
    $(document).on('click', function(e) {
        if ($(window).width() <= 991) {
            if (!$(e.target).closest('.main-sidebar, [data-widget="pushmenu"]').length) {
                $('.main-sidebar').removeClass('show');
                $('body').removeClass('sidebar-open');
                $('.sidebar-overlay').hide();
            }
        }
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        if ($(window).width() > 991) {
            $('.main-sidebar').removeClass('show');
            $('body').removeClass('sidebar-open');
            $('.sidebar-overlay').hide();
        }
    });
    
    // Ensure sidebar overlay exists
    if ($('.sidebar-overlay').length === 0) {
        $('body').append('<div class="sidebar-overlay"></div>');
    }
  });
</script>
<footer class="main-footer text-sm">
        <strong>Copyright © <?php echo date('Y') ?>. 
        <!-- <a href=""></a> -->
        </strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
          <b><?php echo $_settings->info('short_name') ?></b> v1.0
        </div>
      </footer>
    </div>
    <!-- ./wrapper -->
   
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
    <div class="daterangepicker ltr show-ranges opensright">
      <div class="ranges">
        <ul>
          <li data-range-key="Today">Today</li>
          <li data-range-key="Yesterday">Yesterday</li>
          <li data-range-key="Last 7 Days">Last 7 Days</li>
          <li data-range-key="Last 30 Days">Last 30 Days</li>
          <li data-range-key="This Month">This Month</li>
          <li data-range-key="Last Month">Last Month</li>
          <li data-range-key="Custom Range">Custom Range</li>
        </ul>
      </div>
      <div class="drp-calendar left">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-calendar right">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
    <div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;">Idaho</div>