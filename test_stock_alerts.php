<?php
require_once('./config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Stock Alerts</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Stock Alerts Test</h1>
    
    <div>
        <button id="create_test_alerts">Create Test Alerts</button>
        <button id="load_alerts">Load Alerts</button>
        <button id="clear_alerts">Clear All Alerts</button>
    </div>
    
    <div id="alerts_container" style="margin-top: 20px;">
        <!-- Alerts will be displayed here -->
    </div>
    
    <div id="debug_info" style="margin-top: 20px; padding: 10px; background: #f5f5f5;">
        <h3>Debug Information</h3>
        <div id="debug_output"></div>
    </div>

    <script>
        $(document).ready(function(){
            // Create test alerts
            $('#create_test_alerts').click(function(){
                $.ajax({
                    url: 'classes/Master.php?f=create_test_alerts',
                    method: 'POST',
                    dataType: 'json',
                    success: function(resp){
                        console.log('Create test alerts response:', resp);
                        $('#debug_output').append('<p>Create Test Alerts: ' + JSON.stringify(resp) + '</p>');
                        if(resp.status == 'success'){
                            alert('Test alerts created successfully!');
                            loadAlerts();
                        } else {
                            alert('Failed to create test alerts: ' + resp.msg);
                        }
                    },
                    error: function(err){
                        console.log('Error creating test alerts:', err);
                        $('#debug_output').append('<p>Error creating test alerts: ' + JSON.stringify(err) + '</p>');
                    }
                });
            });
            
            // Load alerts
            $('#load_alerts').click(function(){
                loadAlerts();
            });
            
            // Clear alerts
            $('#clear_alerts').click(function(){
                if(confirm('Clear all alerts?')){
                    $.ajax({
                        url: 'classes/Master.php?f=clear_all_alerts',
                        method: 'POST',
                        dataType: 'json',
                        success: function(resp){
                            console.log('Clear alerts response:', resp);
                            $('#debug_output').append('<p>Clear Alerts: ' + JSON.stringify(resp) + '</p>');
                            loadAlerts();
                        },
                        error: function(err){
                            console.log('Error clearing alerts:', err);
                            $('#debug_output').append('<p>Error clearing alerts: ' + JSON.stringify(err) + '</p>');
                        }
                    });
                }
            });
            
            function loadAlerts(){
                $.ajax({
                    url: 'classes/Master.php?f=get_stock_alerts',
                    method: 'POST',
                    dataType: 'json',
                    success: function(resp){
                        console.log('Load alerts response:', resp);
                        $('#debug_output').append('<p>Load Alerts: ' + JSON.stringify(resp) + '</p>');
                        
                        var html = '';
                        if(resp.status == 'success' && resp.alerts.length > 0){
                            $.each(resp.alerts, function(index, alert){
                                console.log('Processing alert:', alert);
                                html += '<div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">';
                                html += '<h4>' + alert.alert_type + '</h4>';
                                html += '<p>' + alert.message + '</p>';
                                html += '<p>Product: ' + alert.product_name + '</p>';
                                html += '<p>Alert ID: ' + alert.id + '</p>';
                                html += '<button class="resolve_alert" data-id="' + alert.id + '">Resolve</button>';
                                html += '</div>';
                            });
                        } else {
                            html = '<p>No alerts found</p>';
                        }
                        $('#alerts_container').html(html);
                    },
                    error: function(err){
                        console.log('Error loading alerts:', err);
                        $('#debug_output').append('<p>Error loading alerts: ' + JSON.stringify(err) + '</p>');
                    }
                });
            }
            
            // Resolve alert
            $(document).on('click', '.resolve_alert', function(){
                var alert_id = $(this).data('id');
                console.log('Resolving alert with ID:', alert_id);
                
                if(!alert_id || alert_id === 'undefined' || alert_id === '') {
                    alert('Invalid alert ID: ' + alert_id);
                    return;
                }
                
                if(confirm('Mark this alert as resolved?')){
                    $.ajax({
                        url: 'classes/Master.php?f=resolve_stock_alert',
                        method: 'POST',
                        data: {alert_id: alert_id},
                        dataType: 'json',
                        success: function(resp){
                            console.log('Resolve response:', resp);
                            $('#debug_output').append('<p>Resolve Alert: ' + JSON.stringify(resp) + '</p>');
                            if(resp.status == 'success'){
                                alert('Alert resolved successfully!');
                                loadAlerts();
                            } else {
                                alert('Failed to resolve alert: ' + resp.msg);
                            }
                        },
                        error: function(err){
                            console.log('Error resolving alert:', err);
                            $('#debug_output').append('<p>Error resolving alert: ' + JSON.stringify(err) + '</p>');
                        }
                    });
                }
            });
            
            // Load alerts on page load
            loadAlerts();
        });
    </script>
</body>
</html>

