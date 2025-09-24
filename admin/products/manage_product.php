<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
// Default brand to Honda
$honda_id = null;
$bq = $conn->query("SELECT id FROM brand_list WHERE name='Honda' ORDER BY id ASC LIMIT 1");
if($bq && $bq->num_rows>0){ $honda_id = $bq->fetch_array()[0]; }
?>
<div class="card card-outline card-info rounded-0">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Product</h3>
	</div>
	<div class="card-body">
        <form action="" id="product-form">
            <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <input type="hidden" name="brand_id" value="<?php echo isset($brand_id) ? $brand_id : ($honda_id ?? ''); ?>">
            
            <div class="form-group">
				<label for="category_id" class="control-label">Category</label>
                <select name="category_id" id="category_id" class="custom-select select2">
                    <option value="" <?= !isset($category_id) ? "selected" : "" ?> disabled></option>
                    <?php 
                    $categories = $conn->query("SELECT * FROM categories where delete_flag = 0 ".(isset($category_id) ? " or id = '{$category_id}'" : "")." order by `category` asc ");
                    while($row= $categories->fetch_assoc()):
                    ?>
                    <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? "selected" : "" ?>><?= $row['category'] ?> <?= $row['delete_flag'] == 1 ? "<small>Deleted</small>" : "" ?></option>
                    <?php endwhile; ?>
                </select>
			</div>

			<div class="form-group">
				<label for="name" class="control-label">Name</label>
                <input name="name" id="name" type="text" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required>
			</div>

			<div class="form-group">
				<label for="models" class="control-label">Compatible for: <small>(model)</small></label>
				<select name="models" id="models" class="custom-select select2" required>
					<?php 
					$models_list = [
						"ADV 160","Airblade 150","Airblade 160","Beat","Click 125i","Click 125i SE","Click 160","CRF150L","DIO","Giorno+","PCX 150","PCX 160 ABS","PCX 160 CBS","RS 125","Supra GTR 150","TMX 125 ALPHA","TMX SUPREMO","Wave RSX(DISC)","Wave RSX(DRUM)","Winner X (Premium)","Winner X (Racing)","XR 150i","XRM 125 Dual Sport Fi","CB150x"
					];
					$current_model = isset($models) ? $models : '';
					foreach($models_list as $m):
					?>
						<option value="<?= htmlspecialchars($m) ?>" <?= $current_model == $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-group">
				<label for="available_colors" class="control-label">Available Colors <small>(comma-separated, e.g., Red, Blue, Black)</small></label>
				<input name="available_colors" id="available_colors" type="text" class="form-control rounded-0" value="<?php echo isset($available_colors) ? htmlspecialchars($available_colors) : ''; ?>">
			</div>

			<div class="form-group">
				<label class="control-label d-block">Color Images (optional)</label>
				<small class="text-muted d-block mb-1">Upload one image per color name listed above. Leave blank to use main product image.</small>
				<div id="colorImagesContainer"></div>
			</div>

            <!-- Template selection -->
            <div class="form-group">
                <label for="template" class="control-label">Template</label>
                <select id="template" class="custom-select">
                    <option value="" selected disabled>Select a Template</option>
                    <option value="motorcycle">Motorcycle</option>
                    <option value="motorcycle_helmet">Motorcycle Helmet</option>
                    <option value="crash_guard">Crash Guard</option>
                    <option value="steering_damper">Steering Damper</option>
                    <option value="exhaust_system">Exhaust System</option>
                    <option value="brake_system">Brake System</option>
                    <option value="lighting">Lighting System</option>
                    <option value="performance">Performance Kit</option>
                </select>
                <small class="form-text text-muted">Choosing a template will auto-fill the description field.</small>
            </div>

            <div class="form-group">
				<label for="description" class="control-label">Description</label>
                <textarea name="description" id="description" type="text" class="form-control rounded-0 summernote" required><?php echo isset($description) ? $description : ''; ?></textarea>
			</div>

			<div class="form-group">
				<label for="price" class="control-label">Price</label>
				<input name="price" id="price" type="text" class="form-control form-control-sm text-right" value="<?php echo isset($price) ? $price : ''; ?>" placeholder="000000.00" required>
			</div>

            <!-- ABC Classification Fields -->
            <div class="form-group">
				<label for="abc_category" class="control-label">ABC Category</label>
                <select name="abc_category" id="abc_category" class="custom-select select2">
                    <option value="A" <?php echo isset($abc_category) && $abc_category == 'A' ? 'selected' : '' ?>>Category A - High Value (80% of total value)</option>
                    <option value="B" <?php echo isset($abc_category) && $abc_category == 'B' ? 'selected' : '' ?>>Category B - Medium Value (15% of total value)</option>
                    <option value="C" <?php echo isset($abc_category) && $abc_category == 'C' ? 'selected' : '' ?>>Category C - Low Value (5% of total value)</option>
                </select>
                <small class="text-muted">ABC classification helps prioritize inventory management</small>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="reorder_point" class="control-label">Reorder Point</label>
                        <input name="reorder_point" id="reorder_point" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($reorder_point) ? $reorder_point : '0'; ?>">
                        <small class="text-muted">Stock level to trigger reorder</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_stock" class="control-label">Minimum Stock</label>
                        <input name="min_stock" id="min_stock" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($min_stock) ? $min_stock : '0'; ?>">
                        <small class="text-muted">Minimum stock level</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="max_stock" class="control-label">Maximum Stock</label>
                        <input name="max_stock" id="max_stock" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($max_stock) ? $max_stock : '0'; ?>">
                        <small class="text-muted">Maximum stock level</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="unit_cost" class="control-label">Unit Cost</label>
                        <input name="unit_cost" id="unit_cost" type="number" step="0.01" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($unit_cost) ? $unit_cost : '0.00'; ?>">
                        <small class="text-muted">Cost per unit for inventory valuation</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lead_time_days" class="control-label">Lead Time (Days)</label>
                        <input name="lead_time_days" id="lead_time_days" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($lead_time_days) ? $lead_time_days : '7'; ?>">
                        <small class="text-muted">Days to receive new stock</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md-6">
						<label for="" class="control-label">Product Image</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label" for="customFile">Choose file</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-center">
							<img src="<?php echo validate_image(isset($image_path) ? $image_path : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="product-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=products">Cancel</a>
	</div>
</div>

<script>
const templates = {
    motorcycle: {
        description: `<p><strong>Complete Motorcycle</strong></p>
        <p>Experience the thrill and freedom of the open road with this high-performance motorcycle. Features include:</p>
        <ul>
            <li>Powerful engine for smooth acceleration</li>
            <li>Durable frame and suspension for stability</li>
            <li>Fuel-efficient design for longer rides</li>
            <li>Modern styling with aerodynamic design</li>
            <li>Equipped with essential safety features like brakes and lights</li>
        </ul>
        <p>Perfect choice for both daily commuting and weekend adventures.</p>`
    },
    motorcycle_helmet: {
        description: `<p><strong>Premium Motorcycle Helmet</strong></p>
        <p>Ride safely and comfortably with this top-quality motorcycle helmet. Features include:</p>
        <ul>
            <li>DOT and ECE certified for maximum safety</li>
            <li>Lightweight yet durable shell construction</li>
            <li>Removable and washable inner lining for hygiene</li>
            <li>Adjustable visor with anti-scratch and anti-fog coating</li>
            <li>Ergonomic design for comfortable long rides</li>
        </ul>
        <p>Essential gear for every rider to ensure safety without compromising style.</p>`
    },
    crash_guard: { description: `<p><strong>High-Quality Crash Guard</strong></p><p>This premium crash guard provides excellent protection for your motorcycle's engine and body parts...</p>` },
    steering_damper: { description: `<p><strong>Performance Steering Damper</strong></p><p>Enhance your motorcycle's stability and handling...</p>` },
    exhaust_system: { description: `<p><strong>Performance Exhaust System</strong></p><p>Upgrade your motorcycle's performance and sound...</p>` },
    brake_system: { description: `<p><strong>High-Performance Brake System</strong></p><p>Upgrade your motorcycle's braking performance...</p>` },
    lighting: { description: `<p><strong>LED Lighting System</strong></p><p>Illuminate your path with this advanced LED lighting system...</p>` },
    performance: { description: `<p><strong>Performance Enhancement Kit</strong></p><p>Boost your motorcycle's performance...</p>` }
};

window.displayImg = function(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
            _this.siblings('.custom-file-label').html(input.files[0].name)
        }
        reader.readAsDataURL(input.files[0]);
    }else{
        $('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : "") ?>");
        _this.siblings('.custom-file-label').html("Choose file")
    }
}

$(document).ready(function(){
    $('.select2').select2({
        width:'100%',
        placeholder:"Please Select Here"
    });

    // Auto-fill description
    $('#template').change(function(){
        let selected = $(this).val();
        if(templates[selected]){
            $('#description').summernote('code', templates[selected].description);
        }
    });

    // Dynamic color image inputs based on available_colors
    function renderColorImageInputs(){
        var colorsRaw = $('#available_colors').val() || '';
        var colors = colorsRaw.split(',').map(function(c){ return c.trim(); }).filter(function(c){ return c.length > 0; });
        var container = $('#colorImagesContainer');
        container.empty();
        if(colors.length === 0){ return; }
        colors.forEach(function(color){
            var safe = color.replace(/[^a-z0-9]+/gi,'_').toLowerCase();
            var group = $(
                '<div class="mb-2">'
                + '<label class="mb-1">'+ $('<div>').text(color).html() +'</label>'
                + '<div class="custom-file">'
                + '<input type="file" class="custom-file-input" name="color_image['+ safe +']" id="color_image_'+ safe +'">'
                + '<label class="custom-file-label" for="color_image_'+ safe +'">Choose image for '+ $('<div>').text(color).html() +'</label>'
                + '</div>'
                + '</div>'
            );
            container.append(group);
        });
        // hook up filenames display
        container.find('.custom-file-input').on('change', function(){
            var fileName = this.files && this.files[0] ? this.files[0].name : 'Choose file';
            $(this).siblings('.custom-file-label').text(fileName);
        });
    }
    $('#available_colors').on('input', renderColorImageInputs);
    renderColorImageInputs();

    // Price input handling: allow large numbers while typing; format on blur
    $('#price').on('focus', function(){
        // remove commas for editing
        $(this).val($(this).val().replace(/,/g, ''));
    });
    $('#price').on('input', function(){
        let v = $(this).val();
        // keep digits and a single dot
        v = v.replace(/[^0-9.]/g, '');
        const d = v.indexOf('.');
        if(d !== -1){
            v = v.slice(0, d + 1) + v.slice(d + 1).replace(/\./g, '');
        }
        $(this).val(v);
    });
    $('#price').on('blur', function(){
        let v = $(this).val().replace(/,/g, '');
        if(!v){ return; }
        const num = parseFloat(v);
        if(!isNaN(num)){
            const fixed = num.toFixed(2);
            const parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            $(this).val(parts.join('.'));
        }
    });

    $('#product-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        // sanitize price: remove commas before submit
        var rawPrice = $('#price').val();
        if(rawPrice){ $('#price').val(rawPrice.replace(/,/g, '')); }
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_product",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occured",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp =='object' && resp.status == 'success'){
                    location.href = "./?page=products/view_product&id="+resp.id;
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
    });

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
    });
});
</script>