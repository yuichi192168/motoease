<?php
require_once('./../../config.php');
// Accept either 'pid' or 'product_id' for compatibility
if(isset($_GET['pid']) && !empty($_GET['pid']))
	$product_id = $_GET['pid'];
if(isset($_GET['product_id']) && !empty($_GET['product_id']))
	$product_id = $_GET['product_id'];
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `stock_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="stock-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name ="product_id" value="<?php echo isset($product_id) ? $product_id : (isset($product_id) ? $product_id : '') ?>">
		
		<?php if(isset($id) && $id > 0): ?>
		<!-- Edit Mode: Show product info as read-only -->
		<div class="form-group">
			<label class="control-label">Product</label>
			<div class="form-control-plaintext border rounded p-2 bg-light">
				<?php 
				$product_info = $conn->query("SELECT p.*, b.name as brand FROM product_list p INNER JOIN brand_list b ON p.brand_id = b.id WHERE p.id = '{$product_id}'")->fetch_assoc();
				if($product_info):
				?>
					<strong><?= $product_info['brand'] ?> - <?= $product_info['name'] ?></strong>
					<br><small class="text-muted">Category: <?= $product_info['category'] ?? 'N/A' ?></small>
				<?php else: ?>
					<span class="text-muted">Product not found</span>
				<?php endif; ?>
			</div>
		</div>
		<?php else: ?>
		<!-- Add Mode: Show product selection only if no product_id is provided -->
		<?php if(!isset($product_id) || empty($product_id)): ?>
		<div class="form-group">
			<label for="product_id" class="control-label">Product <span class="text-danger">*</span></label>
			<select name="product_id" id="product_id" class="custom-select select2" required>
				<option value="" disabled selected>-- Select Product --</option>
				<?php 
				$product = $conn->query("SELECT p.*,b.name as brand from `product_list` p inner join brand_list b on p.brand_id = b.id where p.delete_flag = 0 and p.status = 1 order by (p.`name`) asc ");
				while($row= $product->fetch_assoc()):
				?>
				<option value="<?= $row['id'] ?>"><?= $row['brand'].' - '.$row['name'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<?php endif; ?>
		<?php endif; ?>
		<div class="form-group">
			<label for="quantity" class="control-label">Quantity <span class="text-danger">*</span></label>
			<input name="quantity" id="quantity" type="number" min="0.01" step="0.01" class="form-control rounded-0 text-right" value="<?php echo isset($quantity) ? $quantity : 1; ?>" required>
			<small class="text-muted">Enter the quantity to add or adjust</small>
		</div>
		
		<?php if(isset($product_id)): ?>
		<?php 
		// Get product colors
		$product_colors = $conn->query("SELECT available_colors FROM product_list WHERE id = '{$product_id}'")->fetch_assoc();
		$colors = array();
		if($product_colors && !empty($product_colors['available_colors'])) {
			$colors = array_map('trim', explode(',', $product_colors['available_colors']));
		}
		?>
		<?php if(!empty($colors)): ?>
		<div class="form-group">
			<label for="color_variant" class="control-label">Color Variant</label>
			<select name="color_variant" id="color_variant" class="custom-select">
				<option value="">-- Select Color Variant --</option>
				<?php foreach($colors as $color): ?>
				<option value="<?= htmlspecialchars($color) ?>" <?= isset($color_variant) && $color_variant == $color ? 'selected' : '' ?>><?= htmlspecialchars($color) ?></option>
				<?php endforeach; ?>
			</select>
			<small class="text-muted">Specify which color variant this stock applies to</small>
		</div>
		<?php endif; ?>
		<?php endif; ?>
		
	</form>
</div>
<script>
	$(document).ready(function(){
		$('#uni_modal').on('shown.bs.modal',function(){
			$('.select2').select2({
				width:'100%',
				placeholder:"Please Select Here",
				dropdownParent:$('#uni_modal')
			})
			$('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
		})
		
		$('#stock-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			
			// Validate form before submission
			var product_id = $('input[name="product_id"]').val();
			var quantity = $('#quantity').val();
			
			if(!product_id || product_id == '') {
				alert_toast('Please select a product.', 'error');
				return false;
			}
			
			if(!quantity || quantity <= 0) {
				alert_toast('Please enter a valid quantity greater than 0.', 'error');
				return false;
			}
			
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_stock",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					// Try to parse server response in case extra whitespace or warnings broke the JSON parse
					try{
						if(err && err.responseText){
							// Try direct parse first
							try{ var parsed = JSON.parse(err.responseText); } catch(e) { parsed = null; }
							// If direct parse failed, attempt to extract first JSON object from responseText
							if(!parsed){
								var txt = err.responseText;
								var start = txt.indexOf('{');
								var end = txt.lastIndexOf('}');
								if(start !== -1 && end !== -1 && end > start){
									var substr = txt.substring(start, end+1);
									try{ parsed = JSON.parse(substr); } catch(e2){ parsed = null; }
								}
							}
							if(parsed && parsed.status == 'success'){
								// treat as success
								location.reload();
								return;
							} else if(parsed && parsed.status == 'failed' && parsed.msg){
								$('.err-msg').remove();
								var el = $('<div>')
								el.addClass("alert alert-danger err-msg").text(parsed.msg)
								_this.prepend(el)
								el.show('slow')
								$("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
								end_loader();
								return;
							}
						}
					}catch(e){
						console.log('Response parse failed', e)
					}
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

        
	})
</script>