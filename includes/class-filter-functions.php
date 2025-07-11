<?php
if (!defined('ABSPATH')) {
    exit;
}



/**
 * Advanced Product Filter Functions
 */

/**
 * Initialize the product filter functionality
 */
function dapfforwc_dapfforwc_filter_init()
{
    // Only run on frontend
    if (is_admin() && !wp_doing_ajax()) {
        return;
    }

    // Filter the main query using pre_get_posts
    add_action('pre_get_posts', 'dapfforwc_dapfforwc_filter_products_query', 10);

    // Intercept and handle filter requests - run this VERY early
    add_action('parse_request', 'dapfforwc_dapfforwc_parse_filter_request', 1);
}


/**
 * Parse filter parameters from the request
 * 
 * @param WP $wp Current WordPress environment instance
 * @return WP
 */
function dapfforwc_dapfforwc_parse_filter_request($wp)
{
    // Start the session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Parse all the filter parameters
    $filter_params = dapfforwc_dapfforwc_get_filter_params();

    if (isset($filter_params) && !empty($filter_params)) {

        // Store filter parameters in a global variable for later use
        $GLOBALS['dapfforwc_filter_params'] = $filter_params;
        // Store filter parameters in a session variable for later use
        $_SESSION['dapfforwc_filter_params'] = $filter_params;
    }

    return $wp;
}

/**
 * Get all filter parameters from request
 * 
 * @return array
 */
function dapfforwc_dapfforwc_get_filter_params()
{
    global $dapfforwc_seo_permalinks_options;
    $params = array();

    // Get category filter

    if (isset($_GET["product-category"]) && !empty($_GET["product-category"])) {
        $params["product-category"] = dapfforwc_dapfforwc_sanitize_array($_GET["product-category"]);
    }

    // Get tag filter

    if (isset($_GET['tags']) && !empty($_GET['tags'])) {
        $params['tag'] = dapfforwc_dapfforwc_sanitize_array($_GET['tags']);
    }


    // Get attribute filters (dynamic)

    if (isset($_GET['attribute']) && is_array($_GET['attribute'])) {
        foreach ($_GET['attribute'] as $attribute_name => $value) {

            if (!empty($value)) {
                $params['attributes'][$attribute_name] = dapfforwc_dapfforwc_sanitize_array($value);
            }
        }
    }

    // Get rating filter

    if (isset($_GET['rating']) && !empty($_GET['rating'])) {
        $params['rating'] = dapfforwc_dapfforwc_sanitize_array($_GET['rating']);
    }

    // Get price range filter

    if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
        $params['mn_price'] = floatval($_GET['min_price']);
    }

    if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
        $params['mx_price'] = floatval($_GET['max_price']);
    }

    // Get search query
    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $params['search'] = sanitize_text_field($_GET['s']);
    }

    return $params;
}

/**
 * Helper function to sanitize array values
 * 
 * @param mixed $input Input to sanitize
 * @return array|string Sanitized input
 */
function dapfforwc_dapfforwc_sanitize_array($input)
{
    if (is_array($input)) {
        return array_map('sanitize_text_field', $input);
    }
    return sanitize_text_field($input);
}

/**
 * Modify the main products query to apply filters
 * 
 * @param WP_Query $query The WordPress query object
 * @return void
 */
function dapfforwc_dapfforwc_filter_products_query($query)
{
    // Only modify main query on frontend for product queries
    if (!$query->is_main_query() || is_admin()) {
        return;
    }

    // Set the number of products per page
    if (isset($_GET['per_page']) && !empty($_GET['per_page'])) {
        $query->set('posts_per_page', $_GET['per_page']);
    }

    // Get filter parameters - ensure they're always available
    if (!isset($GLOBALS['dapfforwc_filter_params']) || empty($GLOBALS['dapfforwc_filter_params'])) {
        $GLOBALS['dapfforwc_filter_params'] = dapfforwc_dapfforwc_get_filter_params();
    }

    $filter_params = $GLOBALS['dapfforwc_filter_params'];

    // Apply filters to query
    dapfforwc_apply_filters_to_query($query, $filter_params);
}

/**
 * Apply filter parameters to WP_Query
 * 
 * @param WP_Query $query The WordPress query object
 * @param array $params Filter parameters
 * @return void
 */
function dapfforwc_apply_filters_to_query($query, $params)
{

    // Only apply to WooCommerce product archive pages
    if (
        !$query->is_post_type_archive('product') &&
        !$query->is_tax('product_cat') &&
        !$query->is_tax('product_tag') &&
        !$query->is_tax(get_object_taxonomies('product'))
    ) {
        return;
    }
    // Initialize tax_query and meta_query arrays
    $tax_query = $query->get('tax_query', array());
    $meta_query = $query->get('meta_query', array());

    // Ensure proper structure
    if (!is_array($tax_query)) {
        $tax_query = array();
    }

    if (!is_array($meta_query)) {
        $meta_query = array();
    }

    // Add relation if we have multiple conditions
    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    if (count($meta_query) > 1) {
        $meta_query['relation'] = 'AND';
    }

    // Apply category filter
    if (isset($params["product-category"]) && !empty($params["product-category"])) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $params["product-category"],
            'operator' => isset($_GET['operator_second']) && !empty($_GET['operator_second']) ? $_GET['operator_second'] : 'IN',
        );
    }

    // Apply tag filter
    if (isset($params['tag']) && !empty($params['tag'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $params['tag'],
            'operator' => isset($_GET['operator_second']) && !empty($_GET['operator_second']) ? $_GET['operator_second'] : 'IN',
        );
    }

    // Apply attribute filters
    if (isset($params['attributes']) && is_array($params['attributes'])) {
        foreach ($params['attributes'] as $attribute_name => $attribute_values) {
            if (!empty($attribute_values)) {
                $tax_query[] = array(
                    'taxonomy' => 'pa_' . $attribute_name,
                    'field'    => 'slug',
                    'terms'    => $attribute_values,
                    'operator' => isset($_GET['operator_second']) && !empty($_GET['operator_second']) ? $_GET['operator_second'] : 'IN',
                );
            }
        }
    }

    // Apply price range filter
    if ((isset($params['mn_price']) && $params['mn_price'] > 0) ||
        (isset($params['mx_price']) && $params['mx_price'] > 0)
    ) {

        $min = isset($params['mn_price']) ? floatval($params['mn_price']) : 0;
        $max = isset($params['mx_price']) ? floatval($params['mx_price']) : 0;

        $meta_query[] = array(
            'key'     => '_price',
            'value'   => array($min, $max),
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }

    // Apply rating filter
    if (isset($params['rating']) && !empty($params['rating'])) {
        $rating_filter = array('relation' => 'OR');

        if (!is_array($params['rating'])) {
            $params['rating'] = explode(',', $params['rating']);
        }

        foreach ($params['rating'] as $rating) {
            $rating_filter[] = array(
                'key'     => '_wc_average_rating',
                'value'   => array(intval($rating) - 0.5, intval($rating) + 0.5),
                'compare' => 'BETWEEN',
                'type'    => 'DECIMAL',
            );
        }

        $meta_query[] = $rating_filter;
    }

    // Apply search filter
    if (isset($params['search']) && !empty($params['search'])) {
        $query->set('s', $params['search']);
    }

    // Update the query with our modified parameters
    if (!empty($tax_query)) {
        $query->set('tax_query', $tax_query);
    }

    if (!empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }
}

/**
 * Template redirect hook for handling AJAX requests
 * This allows us to return partial content for AJAX filtering
 */
function dapfforwc_template_redirect_filter()
{
    // Check if this is an AJAX request
    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if (!$is_ajax) {
        return;
    }

    add_filter('template_include', function ($template) {
        $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
        global $dapfforwc_seo_permalinks_options, $dapfforwc_styleoptions;

        // Start the session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure filter params are always available
        if (!isset($GLOBALS['dapfforwc_filter_params']) || empty($GLOBALS['dapfforwc_filter_params'])) {
            $GLOBALS['dapfforwc_filter_params'] = dapfforwc_dapfforwc_get_filter_params();
        }



        if (!isset($GLOBALS['dapfforwc_filter_params']) || empty($GLOBALS['dapfforwc_filter_params'])) {
            $filter_params = $_SESSION['dapfforwc_filter_params'];
        } else {
            $filter_params = $GLOBALS['dapfforwc_filter_params'];
        }

        ob_start();

        // WooCommerce loop setup
        global $woocommerce_loop;
        $woocommerce_loop['columns'] = wc_get_default_products_per_row();

        // Track rendered product IDs
        $products_ids = [];

        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $products_ids[] = get_the_ID();
                wc_get_template_part('content', 'product');
            }
        } else {
            echo '<p class="woocommerce-info">' . esc_html__('No products found matching your selection.', 'woocommerce') . '</p>';
        }

        // Setup pagination and counts
        $found_posts     = $GLOBALS['wp_query']->found_posts;
        $posts_per_page  = $GLOBALS['wp_query']->get('posts_per_page');

        $filterform = '';

        // Only run full product ID fetch + filter form if not all products are shown
        if ($found_posts > $posts_per_page) {
            $products_ids = [];

            $filter_query = new WP_Query([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ]);

            // Apply filters manually
            dapfforwc_apply_filters_to_query($filter_query, $filter_params);
            $filter_query->get_posts(); // Ensure query runs
            $products_ids = $filter_query->posts;
        }

        // Rebuild filter form with updated filters
        $updated_filters = dapfforwc_get_updated_filters($products_ids, $all_data) ?? [];


        $product_details = array_values(dapfforwc_get_woocommerce_product_details()["products"] ?? []);
        $min_max_prices = dapfforwc_get_min_max_price($product_details, $products_ids);

        $min_price = isset($filter_params['mn_price']) ? floor(floatval($filter_params['mn_price'])) : 0;

        $max_price = isset($filter_params['mx_price']) ? ceil(floatval($filter_params['mx_price'])) : null;

        $use_anchor = isset($dapfforwc_seo_permalinks_options["use_anchor"]) ? $dapfforwc_seo_permalinks_options["use_anchor"] : "";
        $disable_unselected =  $found_posts === 0 || $found_posts === 1;
        // Pass sanitized values to the function
        $filterform = dapfforwc_filter_form($updated_filters, $filter_params, $use_anchor, "", "", $min_price, $max_price, [], '',true ,$disable_unselected);


        // Output buffer contents
        $content = ob_get_clean();

        // Return AJAX response
        wp_send_json([
            'success' => true,
            'data' => [
                'html'         => $content,
                'updated_form' => $filterform,
                'pagination'   => paginate_links([
                    'total'     => $GLOBALS['wp_query']->max_num_pages,
                    'current'   => max(1, get_query_var('paged')),
                    'format'    => '?paged=%#%',
                    'type'      => 'list',
                    'prev_text' => __('←', 'woocommerce'),
                    'next_text' => __('→', 'woocommerce'),
                ]),
                'found' => $found_posts,
            ],
        ]);
        exit;
    });
}
