<?php

// get slug to url
$slug = isset($_GET['slug']) ? sanitize_title($_GET['slug']) : '';

// check exists data
if (empty($slug)) {
    echo '<p>No matching vehicle found</p>';
    return;
}

// create args to display data
$args = [
    'post_type' => 'vehicle',
    'name' => $slug
];

$query = new WP_Query($args);

if ($query->have_posts()) {
?>
    <div class="vehicle-detail-container">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <div class="vehicle-detail-item">
                <!-- get title -->
                <h3><?php the_title(); ?></h3>

                <!-- get image -->
                <div class="image-vehicle-single">
                    <?php
                    $image_array = get_post_meta(get_the_ID(), '_vehicle_image');
                    // get first element
                    $image_id = $image_array[0];
                    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                    ?>

                    <img class="image-vehicle" src="<?php echo esc_url($image_url); ?>" alt="">
                </div>


                <!-- get color, price, brand, series -->
                <div class="feature-vehicle">
                    <?php
                    $color = get_post_meta(get_the_ID(), '_vehicle_color', true);
                    $price = get_post_meta(get_the_ID(), '_vehicle_price', true);
                    $brand = get_the_terms(get_the_ID(), 'vehicle_brand');
                    $series = get_the_terms(get_the_ID(), 'vehicle_series');
                    ?>
                    <span>Color: <i style="color: <?php echo $color ?>">■</i></span>
                    <span>Price: <?php echo $price; ?></span>
                    <span>Brand: <?php echo $brand ? esc_html($brand[0]->name) : 'N/A'; ?></span>
                    <span>Series: <?php echo $series ? esc_html($series[0]->name) : 'N/A'; ?></span>

                </div>

                <!-- get content -->
                <div class="vehicle-content">
                    <?php
                    $content = get_post_field('post_content', get_the_ID());
                    echo apply_filters('the_content', $content);
                    ?>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
<?php
}

wp_reset_postdata();
