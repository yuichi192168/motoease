<?php 
require_once('./../../config.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `order_list` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="update_order">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : "" ?>">
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="custom-select form-control-sm">
                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : "" ?>>Pending</option>
                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : "" ?>>Approved Order</option>
                <option value="6" <?= isset($status) && $status == 6 ? 'selected' : "" ?>>Claimed</option>
                <option value="5" <?= isset($status) && $status == 5 ? 'selected' : "" ?>>Cancelled</option>
            </select>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#update_order').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=update_order_status",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				success:function(resp){
					end_loader();
					// Handle response - check if it's actually an error or success
					if(typeof resp =='object' && resp.status == 'success'){
						$('#uni_modal').modal('hide');
						alert_toast(resp.msg || "Order status updated successfully.", 'success');
						setTimeout(() => {
							location.reload();
						}, 1500);
					}else if(resp && resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    }else{
						alert_toast(resp.msg || "An error occured",'error');
                        console.log('Unexpected response:', resp)
					}
				},
				error:function(xhr, status, error){
					end_loader();
					console.error('AJAX Error:', status, error);
					console.log('Response:', xhr.responseText);
					
					// Try to parse response even if it's in error handler
					var response = null;
					try {
						if(xhr.responseText){
							response = JSON.parse(xhr.responseText);
						}
					} catch(e){
						console.log('Could not parse response as JSON');
					}
					
					// If we got a valid JSON response with success status, treat it as success
					if(response && response.status === 'success'){
						$('#uni_modal').modal('hide');
						alert_toast(response.msg || "Order status updated successfully.", 'success');
						setTimeout(() => {
							location.reload();
						}, 1500);
						return;
					}
					
					// Otherwise show error
					var errorMsg = 'An error occurred while updating order status';
					if(response && response.msg){
						errorMsg = response.msg;
					} else if(xhr.responseText && xhr.responseText.length < 200){
						errorMsg = xhr.responseText;
					}
					alert_toast(errorMsg, 'error');
				}
			})
		})
    })
</script>