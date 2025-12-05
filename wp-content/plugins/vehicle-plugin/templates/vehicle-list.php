<?php

// get data to url
$search = sanitize_text_field($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? '';
$brand = $_GET['brand'] ?? '';
$series = $_GET['series'] ?? '';

// current page
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// create args to display data
$args = [
    'post_type' => 'vehicle',
    'posts_per_page' => 6,
    'paged' => $paged
];

// search
// s is paramaters of WP_Query
if (!empty($search)) {
    $args['s'] = $search;
}

// sort of vehicle by price
if ($sort === 'asc' || $sort === 'desc') {
    $args['meta_key'] = '_vehicle_price';
    // meta_value_num ensures the values are treated as numbers
    $args['orderby'] = 'meta_value_num';
    $args['order'] = $sort === 'asc' ? 'ASC' : 'DESC';
}

// filter by brand and series
// get all data brand
$brands = get_terms([
    'taxonomy'   => 'vehicle_brand',
    'hide_empty' => false,
]);

// get add data series
$series_all = get_terms([
    'taxonomy' => 'vehicle_series',
    'hide_empty' => false,
]);

$tax_query = [];

if (!empty($brand)) {
    $tax_query[] = [
        'taxonomy' => 'vehicle_brand',
        'field'    => 'slug',
        'terms'    => $brand,
    ];
}

if (!empty($series)) {
    $tax_query[] = [
        'taxonomy' => 'vehicle_series',
        'field'    => 'slug',
        'terms'    => $series,
    ];
}

if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}

if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// build wp_query
$query = new WP_Query($args);
?>

<!-- feature search, filter and sort -->
<?php if (is_page('list-vehicle')): ?>
    <div>
        <!-- sort by price -->
        <form method="GET" class="vehicle-filter">
            <input class="text-search" type="text" name="search" placeholder="Search vehicle name..." value="<?php echo esc_attr($_GET['search'] ?? ''); ?>">

            <select class="text-search" name="sort">
                <option value="">Sort by price</option>
                <option value="asc" <?php selected($_GET['sort'] ?? '', 'asc'); ?>>Low → High</option>
                <option value="desc" <?php selected($_GET['sort'] ?? '', 'desc'); ?>>High → Low</option>
            </select>

            <select class="text-search" name="brand" id="">
                <option value="">All Brands</option>
                <?php foreach ($brands as $brand_term) : ?>
                    <option value="<?php echo esc_attr($brand_term->slug); ?>"
                        <?php selected($brand, $brand_term->slug); ?>>
                        <?php echo esc_html($brand_term->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select class="text-search" name="series" id="">
                <option value="">All Series</option>
                <?php foreach ($series_all as $data) : ?>
                    <option value="<?php echo esc_attr($data->slug) ?>"
                        <?php selected($series, $data->slug) ?>>
                        <?php echo esc_html($data->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Apply</button>
        </form>
    </div>
<?php endif; ?>


<?php
// display list vehicle
if ($query->have_posts()) : ?>

    <div class="vehicle-list">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <div class="vehicle-item">

                <!-- Title -->
                <h3><?php the_title(); ?></h3>
                <a class="vehicle-image-link" href="<?php echo site_url('/single-vehicle/?slug=' . get_post_field('post_name', get_the_ID())); ?>">
                    <?php $image_array = get_post_meta(get_the_ID(), '_vehicle_image');
                    $image_id = !empty($image_array) ? $image_array[0] : 0;
                    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                    ?>
                    <img class="image-vehicle-list" src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
                </a>

                <!-- price -->
                <p>
                    <span><strong>Price: </strong><?php
                                                    // get_post_meta use to read custome field (post_id, meta_key, true)
                                                    // get_the_ID get current post
                                                    $price = get_post_meta(get_the_ID(), '_vehicle_price', true);
                                                    echo $price ? esc_html(number_format($price)) . ' VND' : 'N/A'; ?>
                    </span>


                </p>

                <!-- color -->
                <p>
                    <?php
                    // get_post_meta use to read custome field (post_id, meta_key, true)
                    // get_the_ID get current post
                    $color = get_post_meta(get_the_ID(), '_vehicle_color', true); ?>
                    <strong>Color: <span class="vehicle-color-swatch" style="color: <?php echo esc_attr($color); ?>">■</span></strong>
                </p>

                <!-- brand -->
                <p>
                    <span><strong>Brand: </strong><?php
                                                    $terms = get_the_terms(get_the_ID(), 'vehicle_brand');
                                                    echo !empty($terms) ? esc_html($terms[0]->name) : 'N/A';
                                                    ?></span>

                </p>

                <!-- series -->
                <p>
                    <span><strong>Series: </strong><?php
                                                    $terms = get_the_terms(get_the_ID(), 'vehicle_series');
                                                    echo !empty($terms) ? esc_html($terms[0]->name) : 'N/A';
                                                    ?></span>

                </p>

                <!-- LINK (OPTIONAL) -->
                <a class="view-details-link" href="<?php echo site_url('/single-vehicle/?slug=' . get_post_field('post_name', get_the_ID())); ?>">View Details</a>

            </div>

        <?php endwhile; ?>
    </div>

<?php else : ?>

    <p>No matching vehicle found.</p>

<?php endif; ?>

<!-- Pagination -->

<?php if (is_page('list-vehicle')) : ?>
    <div class="pagination-list-vehicle">
        <?php
        $base_url = site_url('/list-vehicle/page/%#%');
        echo paginate_links([
            'total' => $query->max_num_pages,
            'current' => $paged,
            'base' => $base_url,
            'format' => '',
        ])
        ?>
    </div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>