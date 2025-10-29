<?php
if(!in_array($_settings->userdata('role_type'), ['admin','branch_supervisor','service_admin'])){
    echo '<div class="card"><div class="card-body">Access denied.</div></div>';
    return;
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Notifications</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-flat btn-primary" id="refresh-notifs"><i class="fa fa-sync"></i> Refresh</button>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <button class="btn btn-sm btn-secondary" id="mark-all-read"><i class="fa fa-check-double"></i> Mark All Read</button>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-primary" id="load-more">Load more</button>
            </div>
        </div>
        <div id="admin-notifications-list-page">
            <div id="admin-notifications-content-page" class="list-group">
                <div class="text-center p-3 text-muted">Loading...</div>
            </div>
        </div>
    </div>
</div>

<script>
var notif_offset = 0;
var notif_limit = 20;
function loadAdminNotificationsPage(append = false){
    $.ajax({
        url: _base_url_ + 'classes/Master.php?f=get_admin_notifications',
        method: 'POST',
        data: {offset: notif_offset, limit: notif_limit},
        dataType: 'json',
        success:function(resp){
            if(resp && resp.status == 'success'){
                var html = '';
                if(resp.data && resp.data.length){
                    resp.data.forEach(function(n){
                        var itemId = n.id;
                        var readClass = (n.is_read == 0) ? 'bg-light' : '';
                        var targetAttr = n.target ? 'data-target="'+n.target+'"' : '';
                        var parsedMsg = (n.message) ? n.message : '';
                        // If data is JSON, try to parse and enrich with links
                        try{
                            var d = typeof n.data === 'string' ? JSON.parse(n.data) : n.data;
                            if(d && d.order_id){
                                parsedMsg += ' <a href="./?page=orders/view_order&id='+d.order_id+'" class="ml-2">View Order</a>';
                            }
                            if(d && d.request_id){
                                parsedMsg += ' <a href="./?page=service_requests/view_request&id='+d.request_id+'" class="ml-2">View Request</a>';
                            }
                        }catch(e){}

                        html += '<div class="list-group-item d-flex justify-content-between align-items-start '+readClass+'" data-id="'+itemId+'" '+targetAttr+'>';
                        html += '<div>';
                        html += '<div class="fw-bold">' + (n.title || 'Notification') + '</div>';
                        html += '<div class="small text-muted notif-message">' + parsedMsg + '</div>';
                        html += '</div>';
                        html += '<div class="text-right">';
                        html += '<div class="small text-muted">' + (new Date(n.date_created)).toLocaleString() + '</div>';
                        html += '<div class="mt-2">';
                        html += '<button class="btn btn-sm btn-outline-danger delete-notif" data-id="'+itemId+'"><i class="fa fa-trash"></i></button> ';
                        html += '<button class="btn btn-sm btn-outline-success mark-read" data-id="'+itemId+'"><i class="fa fa-check"></i></button>';
                        html += '</div></div></div>';
                    });
                }else{
                    if(!append) html = '<div class="text-center p-3 text-muted">No notifications</div>';
                }
                if(append){
                    $('#admin-notifications-content-page').append(html);
                }else{
                    $('#admin-notifications-content-page').html(html);
                }
            }
        }
    })
}

$(function(){
    loadAdminNotificationsPage();
    $('#refresh-notifs').click(function(){ notif_offset = 0; loadAdminNotificationsPage(false); loadAdminNotificationsCount();});
    $('#load-more').click(function(){ notif_offset += notif_limit; loadAdminNotificationsPage(true);});
    $('#mark-all-read').click(function(){
        $.post(_base_url_ + 'classes/Master.php?f=mark_all_admin_notifications_read',{},function(res){
            if(res && res.status == 'success'){
                notif_offset = 0; loadAdminNotificationsPage(); loadAdminNotificationsCount();
            }
        },'json');
    });

    $(document).on('click', '.mark-read', function(e){
        var id = $(this).attr('data-id');
        var numericId = id.toString().replace(/^n_/, '');
        $.post(_base_url_ + 'classes/Master.php?f=mark_admin_notification_read', {id: numericId}, function(resp){
            if(resp && resp.status == 'success'){
                $(e.target).closest('.list-group-item').removeClass('bg-light');
                loadAdminNotificationsCount();
            }
        }, 'json');
    });

    $(document).on('click', '.delete-notif', function(e){
        var id = $(this).attr('data-id');
        var numericId = id.toString().replace(/^n_/, '');
        if(!confirm('Delete this notification?')) return;
        $.post(_base_url_ + 'classes/Master.php?f=delete_admin_notification',{id:numericId},function(resp){
            if(resp && resp.status == 'success'){
                notif_offset = 0; loadAdminNotificationsPage(); loadAdminNotificationsCount();
            }
        },'json');
    });

    $(document).on('click', '#admin-notifications-content-page .list-group-item', function(e){
        var target = $(this).attr('data-target');
        if(target){
            window.location.href = target;
        }
    });
})
</script>