<?php
/*
Plugin Name: Warranty Management
Description: A plugin to manage warranty information.
Version: 1.0
Author: Your Name
*/

// Hook to add menu in admin
add_action('admin_menu', 'warranty_management_menu');

// Create the database table when the plugin is activated
register_activation_hook(__FILE__, 'create_warranty_table');
function create_warranty_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'warranty_data';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        customer_name varchar(100) NOT NULL,
        customer_lastname varchar(100) NOT NULL,
        national_code varchar(10) NOT NULL,
        mobile_number varchar(15) NOT NULL,
        phone_number varchar(15) DEFAULT NULL,
        address text NOT NULL,
        postal_code varchar(10) NOT NULL,
        state varchar(50) NOT NULL,
        city varchar(50) NOT NULL,
        product_name varchar(100) NOT NULL,
        invoice_number varchar(50) NOT NULL,
        serial_number varchar(50) NOT NULL,
        purchase_date date NOT NULL,
        warranty_duration int NOT NULL, -- اضافه کردن فیلد مدت گارانتی
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function warranty_management_menu() {
    // Add a new top-level menu
    add_menu_page(
        'پنل مدیریت گارانتی', // Page title
        'پنل مدیریت گارانتی', // Menu title
        'manage_options',      // Capability
        'warranty-management', // Menu slug
        'warranty_management_page', // Function to display the page content
        'dashicons-admin-tools', // Icon
        6                      // Position
    );

    // Add a submenu for the warranty list
    add_submenu_page(
        'warranty-management', // Parent slug
        'لیست گارانتی‌ها',   // Page title
        'لیست گارانتی‌ها',   // Menu title
        'manage_options',      // Capability
        'warranty-list',      // Menu slug
        'warranty_list_page'  // Function to display the list
    );

    // Add a submenu for warranty search
    add_submenu_page(
        'warranty-management', // Parent slug
        'جستجوی گارانتی',     // Page title
        'جستجوی گارانتی',     // Menu title
        'manage_options',      // Capability
        'warranty-search',     // Menu slug
        'warranty_search_page' // Function to display the search page
    );
}

// Function to display the content of the menu page
function warranty_management_page() {
	if (isset($_POST['submit'])) {
		// Handle form submission
		global $wpdb;
		$table_name = $wpdb->prefix . 'warranty_data';
	
		$customer_name = sanitize_text_field($_POST['customer_name']);
		$customer_lastname = sanitize_text_field($_POST['customer_lastname']);
		$national_code = sanitize_text_field($_POST['national_code']);
		$mobile_number = sanitize_text_field($_POST['mobile_number']);
		$phone_number = sanitize_text_field($_POST['phone_number']);
		$address = sanitize_textarea_field($_POST['address']);
		$postal_code = sanitize_text_field($_POST['postal_code']);
		$state = sanitize_text_field($_POST['state']);
		$city = sanitize_text_field($_POST['city']);
		$product_name = sanitize_text_field($_POST['product_name']);
		$invoice_number = sanitize_text_field($_POST['invoice_number']);
		$serial_number = sanitize_text_field($_POST['serial_number']);
		$purchase_date = sanitize_text_field($_POST['purchase_date']);
		$warranty_duration = intval($_POST['warranty_duration']); // اضافه کردن مدت گارانتی

		
		// Check if the table exists
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
		    // Insert data into the database
		    $data = array(
			'customer_name' => $customer_name,
			'customer_lastname' => $customer_lastname,
			'national_code' => $national_code,
			'mobile_number' => $mobile_number,
			'phone_number' => $phone_number,
			'address' => $address,
			'postal_code' => $postal_code,
			'state' => $state,
			'city' => $city,
			'product_name' => $product_name,
			'invoice_number' => $invoice_number,
			'serial_number' => $serial_number,
			'purchase_date' => $purchase_date,
			'warranty_duration' => $warranty_duration, // اضافه کردن مدت گارانتی

		    );
	
		    $wpdb->insert($table_name, $data);
		    echo "<div class='updated'><p>اطلاعات با موفقیت ذخیره شد.</p></div>";
		} else {
		    // If table does not exist, create it
		    create_warranty_table();
		    // Now insert the data
		    $data = array(
			'customer_name' => $customer_name,
			'customer_lastname' => $customer_lastname,
			'national_code' => $national_code,
			'mobile_number' => $mobile_number,
			'phone_number' => $phone_number,
			'address' => $address,
			'postal_code' => $postal_code,
			'state' => $state,
			'city' => $city,
			'product_name' => $product_name,
			'invoice_number' => $invoice_number,
			'serial_number' => $serial_number,
			'purchase_date' => $purchase_date,
			'warranty_duration' => $warranty_duration, // اضافه کردن مدت گارانتی
		    );
	
		    $wpdb->insert($table_name, $data);
		    echo "<div class='updated'><p>جدول جدید ساخته شد و اطلاعات با موفقیت ذخیره شد.</p></div>";
		}
	    }
	
	    // Form HTML
	    ?>
	    <div class="wrap">
		<h1>پنل مدیریت گارانتی</h1>
		<form method="post" action="">
		    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
			<div style="border: 1px solid #ccc; padding: 20px; width: 48%;">
			    <h2>اطلاعات مشتری</h2>
			    <label style="margin-bottom: 10px;">👤 نام: <input type="text" name="customer_name" required /></label><br>
			    <label style="margin-bottom: 10px;">👨‍👩‍👦 نام خانوادگی: <input type="text" name="customer_lastname" required /></label><br>
			    <label style="margin-bottom: 10px;">🆔 کد ملی: <input type="text" name="national_code" required /></label><br>
			    <label style="margin-bottom: 10px;">📱 شماره همراه: <input type="text" name="mobile_number" required /></label><br>
			    <label style="margin-bottom: 10px;">📞 شماره ثابت: <input type="text" name="phone_number" /></label><br>
			    <label style="margin-bottom: 10px;">🏠 آدرس: <textarea name="address" required></textarea></label><br>
			    <label style="margin-bottom: 10px;">📬 کدپستی: <input type="text" name="postal_code" required /></label><br>
			    <label style="margin-bottom: 10px;">🌆 استان: <input type="text" name="state" required /></label><br>
			    <label style="margin-bottom: 10px;">🏙️ شهر: <input type="text" name="city" required /></label><br>
			</div>
			<div style="border: 1px solid #ccc; padding: 20px; width: 48%;">
			    <h2>اطلاعات محصول</h2>
			    <label style="margin-bottom: 10px;">📦 نام محصول: <input type="text" name="product_name" required /></label><br>
			    <label style="margin-bottom: 10px;">🧾 شماره فاکتور خرید: <input type="text" name="invoice_number" required /></label><br>
			    <label style="margin-bottom: 10px;">🔢 شماره سریال محصول: <input type="text" name="serial_number" required /></label><br>
			    <label style="margin-bottom: 10px;">📅 تاریخ خرید و ثبت گارانتی: <input type="date" name="purchase_date" required /></label><br>
			    <label style="margin-bottom: 10px;">📆 مدت گارانتی (ماه): <input type="number" name="warranty_duration" required min="1" /></label><br>
			</div>
		    </div>
		    <input type="submit" name="submit" value="ذخیره اطلاعات" class="button button-primary" />
		</form>
	    </div>
	    <?php
}

function warranty_list_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'warranty_data';
    
	// Fetch all warranty data
	$results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
	?>
    
	<div class="wrap">
	<h1>لیست گارانتی‌ها</h1>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>شماره سریال محصول</th>
            <th>شماره فاکتور خرید</th>
            <th>کد ملی مشتری</th>
            <th>مدت گارانتی (ماه)</th> <!-- اضافه کردن ستون مدت گارانتی -->
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($results) : ?>
            <?php foreach ($results as $row) : ?>
                <tr>
                    <td style="color: green; font-weight: bold;"><?php echo esc_html($row['serial_number']); ?></td>
                    <td><?php echo esc_html($row['invoice_number']); ?></td>
                    <td><?php echo esc_html($row['national_code']); ?></td>
                    <td><?php echo esc_html($row['warranty_duration']); ?></td> <!-- نمایش مدت گارانتی -->
                    <td>
                        <button class="button button-primary" onclick="showDetails(<?php echo esc_js($row['id']); ?>)">مشاهده جزئیات</button>
                        <button class="button button-secondary" onclick="editWarranty(<?php echo esc_js($row['id']); ?>)">ویرایش</button>
                        <button class="button button-danger" onclick="deleteWarranty(<?php echo esc_js($row['id']); ?>)">حذف</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5">اطلاعاتی موجود نیست.</td> <!-- تعداد ستون‌ها را با توجه به تعداد آن‌ها در ردیف‌ها تنظیم کنید -->
            </tr>
        <?php endif; ?>
    </tbody>
</table>

	</div>
    
	<!-- Popup Modal for Details -->
	<div id="popup-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); z-index:999;">
	    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:10px; width:400px;">
		<h2>جزئیات گارانتی</h2>
		<div id="warranty-details"></div>
		<button onclick="closeModal()" class="button button-secondary">بستن</button>
	    </div>
	</div>
    
	<!-- Popup Modal for Edit -->
	<div id="edit-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); z-index:999;">
	    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:10px; width:400px;">
		<h2>ویرایش اطلاعات گارانتی</h2>
		<div id="edit-form"></div>
		<button onclick="saveEdit()" class="button button-primary">ذخیره</button>
		<button onclick="closeEditModal()" class="button button-secondary">بستن</button>
	    </div>
	</div>
    
	<script>
	    function showDetails(id) {
		// Fetch warranty details via AJAX
		jQuery.ajax({
		    url: "<?php echo admin_url('admin-ajax.php'); ?>",
		    type: "POST",
		    data: {
			action: "get_warranty_details",
			id: id
		    },
		    success: function(data) {
			document.getElementById('warranty-details').innerHTML = data;
			document.getElementById('popup-modal').style.display = 'block';
		    }
		});
	    }
    
	    function closeModal() {
		document.getElementById('popup-modal').style.display = 'none';
	    }
    
	    function deleteWarranty(id) {
		if (confirm("آیا از حذف این گارانتی اطمینان دارید؟")) {
		    jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type: "POST",
			data: {
			    action: "delete_warranty",
			    id: id
			},
			success: function(data) {
			    location.reload(); // Refresh the page to update the list
			}
		    });
		}
	    }
    
	    function editWarranty(id) {
		// Fetch warranty details for editing
		jQuery.ajax({
		    url: "<?php echo admin_url('admin-ajax.php'); ?>",
		    type: "POST",
		    data: {
			action: "get_warranty_for_edit",
			id: id
		    },
		    success: function(data) {
			document.getElementById('edit-form').innerHTML = data;
			document.getElementById('edit-modal').style.display = 'block';
		    }
		});
	    }
    
	    function closeEditModal() {
		document.getElementById('edit-modal').style.display = 'none';
	    }
    
	    function saveEdit() {
		var formData = jQuery('#edit-form').find(':input').serialize();
		formData += '&action=save_warranty_edit';
		jQuery.ajax({
		    url: "<?php echo admin_url('admin-ajax.php'); ?>",
		    type: "POST",
		    data: formData,
		    success: function(data) {
			location.reload(); // Refresh the page to update the list
		    }
		});
	    }
    
	    // Close the modal on ESC key
	    document.addEventListener('keydown', function(event) {
		if (event.key === "Escape") {
		    closeModal();
		    closeEditModal();
		}
	    });
	</script>
    
	<style>
	    .wp-list-table th,
	    .wp-list-table td {
		padding: 10px; /* Add padding for cells */
	    }
	</style>
    
	<?php
    }
    
    // AJAX function to fetch warranty details
    add_action('wp_ajax_get_warranty_details', 'get_warranty_details');
    function get_warranty_details() {
	global $wpdb;
	$id = intval($_POST['id']);
	$table_name = $wpdb->prefix . 'warranty_data';
	
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
	
	if ($row) {
	    echo "<strong>نام مشتری:</strong> " . esc_html($row['customer_name']) . "<br/>";
	    echo "<strong>نام خانوادگی مشتری:</strong> " . esc_html($row['customer_lastname']) . "<br/>";
	    echo "<strong>کد ملی:</strong> " . esc_html($row['national_code']) . "<br/>";
	    echo "<strong>شماره موبایل:</strong> " . esc_html($row['mobile_number']) . "<br/>";
	    echo "<strong>آدرس:</strong> " . esc_html($row['address']) . "<br/>";
	    echo "<strong>کد پستی:</strong> " . esc_html($row['postal_code']) . "<br/>";
	    echo "<strong>استان:</strong> " . esc_html($row['state']) . "<br/>";
	    echo "<strong>شهر:</strong> " . esc_html($row['city']) . "<br/>";
	    echo "<strong>نام محصول:</strong> " . esc_html($row['product_name']) . "<br/>";
	    echo "<strong>شماره فاکتور:</strong> " . esc_html($row['invoice_number']) . "<br/>";
	    echo "<strong>شماره سریال:</strong> " . esc_html($row['serial_number']) . "<br/>";
	    echo "<strong>تاریخ خرید:</strong> " . esc_html($row['purchase_date']) . "<br/>";
	    echo "<strong>مدت گارانتی (ماه):</strong> " . esc_html($row['warranty_duration']) . "<br/>"; // اضافه کردن مدت گارانتی
	    echo "<strong>تاریخ ایجاد:</strong> " . esc_html($row['created_at']) . "<br/>";
	}
	wp_die(); // This is required to terminate immediately and return a proper response
    }
    
    
    // AJAX function to delete warranty
    add_action('wp_ajax_delete_warranty', 'delete_warranty');
    function delete_warranty() {
	global $wpdb;
	$id = intval($_POST['id']);
	$table_name = $wpdb->prefix . 'warranty_data';
    
	$wpdb->delete($table_name, array('id' => $id));
	wp_die();
    }
    
    // AJAX function to get warranty for edit
    add_action('wp_ajax_get_warranty_for_edit', 'get_warranty_for_edit');
    function get_warranty_for_edit() {
	global $wpdb;
	$id = intval($_POST['id']);
	$table_name = $wpdb->prefix . 'warranty_data';
    
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    
	if ($row) {
		echo '<input type="hidden" name="id" value="' . esc_attr($row['id']) . '">';
		echo '<label for="customer_name">نام مشتری:</label><input type="text" name="customer_name" value="' . esc_attr($row['customer_name']) . '"><br/>';
		echo '<label for="customer_lastname">نام خانوادگی مشتری:</label><input type="text" name="customer_lastname" value="' . esc_attr($row['customer_lastname']) . '"><br/>';
		echo '<label for="national_code">کد ملی:</label><input type="text" name="national_code" value="' . esc_attr($row['national_code']) . '"><br/>';
		echo '<label for="mobile_number">شماره موبایل:</label><input type="text" name="mobile_number" value="' . esc_attr($row['mobile_number']) . '"><br/>';
		echo '<label for="address">آدرس:</label><textarea name="address">' . esc_html($row['address']) . '</textarea><br/>';
		echo '<label for="postal_code">کد پستی:</label><input type="text" name="postal_code" value="' . esc_attr($row['postal_code']) . '"><br/>';
		echo '<label for="state">استان:</label><input type="text" name="state" value="' . esc_attr($row['state']) . '"><br/>';
		echo '<label for="city">شهر:</label><input type="text" name="city" value="' . esc_attr($row['city']) . '"><br/>';
		echo '<label for="product_name">نام محصول:</label><input type="text" name="product_name" value="' . esc_attr($row['product_name']) . '"><br/>';
		echo '<label for="invoice_number">شماره فاکتور:</label><input type="text" name="invoice_number" value="' . esc_attr($row['invoice_number']) . '"><br/>';
		echo '<label for="serial_number">شماره سریال:</label><input type="text" name="serial_number" value="' . esc_attr($row['serial_number']) . '"><br/>';
		echo '<label for="purchase_date">تاریخ خرید:</label><input type="date" name="purchase_date" value="' . esc_attr($row['purchase_date']) . '"><br/>';
		echo '<label for="warranty_duration">مدت گارانتی (ماه):</label><input type="number" name="warranty_duration" value="' . esc_attr($row['warranty_duration']) . '" min="1"><br/>'; // اضافه کردن فیلد مدت گارانتی
	    }
	    
	wp_die();
    }
    
    // AJAX function to save warranty edit
    add_action('wp_ajax_save_warranty_edit', 'save_warranty_edit');
    function save_warranty_edit() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'warranty_data';
	
	$data = array(
	    'customer_name' => sanitize_text_field($_POST['customer_name']),
	    'customer_lastname' => sanitize_text_field($_POST['customer_lastname']),
	    'national_code' => sanitize_text_field($_POST['national_code']),
	    'mobile_number' => sanitize_text_field($_POST['mobile_number']),
	    'address' => sanitize_textarea_field($_POST['address']),
	    'postal_code' => sanitize_text_field($_POST['postal_code']),
	    'state' => sanitize_text_field($_POST['state']),
	    'city' => sanitize_text_field($_POST['city']),
	    'product_name' => sanitize_text_field($_POST['product_name']),
	    'invoice_number' => sanitize_text_field($_POST['invoice_number']),
	    'serial_number' => sanitize_text_field($_POST['serial_number']),
	    'purchase_date' => sanitize_text_field($_POST['purchase_date']),
	    'warranty_duration' => intval($_POST['warranty_duration']), // اضافه کردن فیلد مدت گارانتی

	);
    
	$id = intval($_POST['id']);
	
	$wpdb->update($table_name, $data, array('id' => $id));
	wp_die();
    }


    function warranty_search_page() {
	?>
    
	<div class="wrap">
	    <h1>جستجوی گارانتی</h1>
	    <form id="warranty-search-form">
		<input type="text" name="search" placeholder="شماره سریال محصول، کد ملی، نام و فامیل، عنوان کالا، شماره همراه، شماره فاکتور" required>
		<button type="submit" class="button button-primary">جستجو</button>
	    </form>
	    <div id="search-results" style="display:none; margin-top:20px;"></div>
	</div>
    
	<script>
	    jQuery('#warranty-search-form').on('submit', function(e) {
		e.preventDefault();
		var searchValue = jQuery('input[name="search"]').val();
		jQuery.ajax({
		    url: "<?php echo admin_url('admin-ajax.php'); ?>",
		    type: "POST",
		    data: {
			action: "search_warranty",
			search: searchValue
		    },
		    success: function(data) {
			document.getElementById('search-results').innerHTML = data;
			document.getElementById('search-results').style.display = 'block';
		    }
		});
	    });
	</script>
    
	<?php
    }
    
    // AJAX function to search warranties
    add_action('wp_ajax_search_warranty', 'search_warranty');
    function search_warranty() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'warranty_data';
	$search = sanitize_text_field($_POST['search']);
	
	$results = $wpdb->get_results($wpdb->prepare(
	    "SELECT * FROM $table_name WHERE serial_number LIKE %s OR national_code LIKE %s OR customer_name LIKE %s OR customer_lastname LIKE %s OR product_name LIKE %s OR invoice_number LIKE %s OR mobile_number LIKE %s",
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%',
	    '%' . $wpdb->esc_like($search) . '%'
	), ARRAY_A);
	
	if ($results) {
	    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>شماره سریال محصول</th><th>شماره فاکتور</th><th>کد ملی مشتری</th><th>مدت گارانتی (ماه)</th></tr></thead><tbody>';
	    foreach ($results as $row) {
		echo '<tr>';
		echo '<td style="color: green; font-weight: bold;">' . esc_html($row['serial_number']) . '</td>';
		echo '<td>' . esc_html($row['invoice_number']) . '</td>';
		echo '<td>' . esc_html($row['national_code']) . '</td>';
		echo '<td>' . esc_html($row['warranty_duration']) . '</td>'; // نمایش مدت گارانتی
		echo '</tr>';
	    }
	    echo '</tbody></table>';
	} else {
	    echo 'نتیجه‌ای یافت نشد.';
	}
	wp_die();
    }
    

    add_shortcode('warranty_search_form', 'warranty_search_form_shortcode');

    function warranty_search_form_shortcode() {
	ob_start(); // شروع بافر خروجی
    
	// بررسی اینکه آیا فرم ارسال شده است
	if (isset($_POST['search_serial_number'])) {
	    global $wpdb;
	    $serial_number = sanitize_text_field($_POST['serial_number']);
	    $table_name = $wpdb->prefix . 'warranty_data';
    
	    // جستجو در دیتابیس
	    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE serial_number = %s", $serial_number));
    
	    // نمایش نتایج
	    
	    if ($result) {
		// خواندن مدت زمان گارانتی از دیتابیس
		$warranty_duration_months = (int)$result->warranty_duration; // فرض بر این است که این مقدار به ماه است
	    
		// تاریخ خرید محصول
		$purchase_date = new DateTime($result->purchase_date);
		// تاریخ انقضای گارانتی
		$expiry_date = (clone $purchase_date)->modify("+$warranty_duration_months months");
		
		// تاریخ فعلی
		$current_date = new DateTime();
	    
		// محاسبه مدت زمان گذشته و باقی‌مانده
		$interval = $current_date->diff($expiry_date);
	    
		echo "<div id='warrantyResult' style='border: 2px solid red; padding: 20px; border-radius: 5px; font-family: Vazirmatn; background-color: #fffacd; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3); position: relative;'>";
		echo "<h3>نتایج وضعیت گارانتی محصول:</h3>";
		echo "<p>🔍 سریال وارد شده یافت شد ، این محصول با اطلاعات زیر در وضعیت گارانتی شرکت می باشد.</p>";
		echo "<p>📦 نام محصول: " . esc_html($result->product_name) . "</p>";
		echo "<p>🧾 شماره فاکتور: " . esc_html($result->invoice_number) . "</p>";
		echo "<p>🗓️ تاریخ خرید: " . esc_html($result->purchase_date) . "</p>";
		echo "<p>🕒 تاریخ ثبت گارانتی: " . esc_html($result->created_at) . "</p>";
		
		// نمایش وضعیت گارانتی
		if ($current_date > $expiry_date) {
		    // گارانتی منقضی شده است
		    echo "<p>⏳ گارانتی منقضی شده است. مدت زمان انقضا: " . $interval->format('%y سال و %m ماه و %d روز') . " قبل.</p>";
		} else {
		    // گارانتی هنوز فعال است
		    echo "<p>✅ گارانتی هنوز فعال است. مدت زمان باقی‌مانده: " . $interval->format('%y سال و %m ماه و %d روز') . " باقی‌مانده.</p>";
		}
	    
		// دکمه بستن
		echo "<button id='closeButton' style='background-color: #003366; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer; margin-top: 10px;'>بستن</button>";
		
		echo "</div>";
		
		// اسکریپت برای بستن کادر
		echo "<script type='text/javascript'>
		    document.getElementById('closeButton').onclick = function() {
			document.getElementById('warrantyResult').style.display = 'none';
		    };
		</script>";
	    } else {
		echo "<p>🚫 سریال وارد شده یافت نشد.</p>";
	    }
	    
	    
	    
	}
    
	// فرم جستجو
	?>
	<div style="border: 2px solid #C0392B; padding: 20px; border-radius: 10px; background-color: #fffacd; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); width: 500px; margin: 20px auto; font-family: Vazirmatn;">
    <form method="post" style="display: flex; align-items: center; justify-content: center;">
        <label for="serial_number" style="margin-right: 15px;">سریال محصول: 📦</label>
        <input type="text" name="serial_number" id="serial_number" required placeholder="سریال محصول را وارد کنید" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%; max-width: 150px; margin-right: 5px; margin-left: 5px;">
        <input type="submit" name="search_serial_number" value="بررسی وضعیت گارانتی" style="background-color: #C0392B; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer;">
    </form>
</div>

	<?php
    
	return ob_get_clean(); // برگرداندن خروجی بافر
    }
       
    
    ?>