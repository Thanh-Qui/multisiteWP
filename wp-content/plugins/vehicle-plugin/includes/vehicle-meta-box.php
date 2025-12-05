<?php 

/* 
*   custom fields
*/
// add new fields for specific posts
// add_meta_boxes -> create interface form
function vehicle_add_meta()
{
    add_meta_box(
        // id vehicle --> meta box ID
        'vehicle_info',
        // display infomation vehicle
        'Vehicle Info',
        // create func dipslay data
        'vehicle_meta_box_html',
        'vehicle'
    );
}
add_action('add_meta_boxes', 'vehicle_add_meta');

// render meta box
// get_post_meta -> use to saved data
function vehicle_meta_box_html($post)
{
    // load data to wp_postmeta table
    $model = get_post_meta($post->ID, '_vehicle_model', true);
    $year = get_post_meta($post->ID, '_vehicle_year', true);
    $badge = get_post_meta($post->ID, '_vehicle_badge', true);
    $color = get_post_meta($post->ID, '_vehicle_color', true);
    $odometer = get_post_meta($post->ID, '_vehicle_odometer', true);
    $price = get_post_meta($post->ID, '_vehicle_price', true);
    $image_id = get_post_meta($post->ID, '_vehicle_image', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

    // create nonce (temporary security code) to security fake request
    wp_nonce_field('vehicle_save_meta', 'vehicle_meta_nonce');

?>
    <div>
        <p>
            <label>Model:</label><br>
            <input type="text" name="vehicle_model" value="<?php echo esc_attr($model); ?>" style="width:100%;">
        </p>
        <p>
            <label>Year:</label><br>
            <input type="text" name="vehicle_year" value="<?php echo esc_attr($year); ?>" style="width:100%;">
        </p>
        <p>
            <label>Badge:</label><br>
            <input type="text" name="vehicle_badge" value="<?php echo esc_attr($badge); ?>" style="width:100%;">
        </p>
        <p>
            <label>Color:</label><br>
            <input type="color" name="vehicle_color" value="<?php echo esc_attr($color); ?>">
        </p>
        <p>
            <label>Odometer:</label><br>
            <input type="number" name="vehicle_odometer" value="<?php echo esc_attr($odometer); ?>" style="width:100%;">
        </p>
        <p>
            <label>Price:</label><br>
            <input type="number" name="vehicle_price" value="<?php echo esc_attr($price); ?>" style="width:100%;">
        </p>
        <p>
            <label>Image:</label><br>
            <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:150px; display:block; margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="vehicle_image">
        </p>
    </div>
<?php
}

// add enctype to form to save file
function vehicle_post_edit_form_tag()
{
    echo ' enctype="multipart/form-data"';
}
// hook (post_edit_form_tag) add HTML attributes
add_action('post_edit_form_tag', 'vehicle_post_edit_form_tag');


// save meta
// update_post_meta -> save and update data
function vehicle_save_meta($post_id)
{
    // create array to save meta box
    $fields = [
        'vehicle_model' => '_vehicle_model',
        'vehicle_year' => '_vehicle_year',
        'vehicle_badge' => '_vehicle_badge',
        'vehicle_color' => '_vehicle_color',
        'vehicle_odometer' => '_vehicle_odometer',
        'vehicle_price' => '_vehicle_price',
    ];

    foreach ($fields as $name => $meta_key) {
        if (isset($_POST[$name])) {
            // save and update meta box for wp_postmeta (update_post_meta)
            // clean data input (sanitize_text_field)
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$name]));
        }
    }


    // save image
    if (!function_exists('media_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
    }

    if (isset($_FILES['vehicle_image']) && !empty($_FILES['vehicle_image']['name'])) {
        // Process uploaded files, save to Media Library, return attachment ID --> media_handle_upload
        $attachment_id = media_handle_upload('vehicle_image', $post_id);
        // check object
        if (!is_wp_error($attachment_id)) {
            update_post_meta($post_id, '_vehicle_image', $attachment_id);
        }
    }
}
add_action('save_post', 'vehicle_save_meta');