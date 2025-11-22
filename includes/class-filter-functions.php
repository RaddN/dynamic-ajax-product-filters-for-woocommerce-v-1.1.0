<?php
if (!defined('ABSPATH')) {
    exit;
}

class DAPFFORWC_WC_Query_Filter_Enhanced
{

    private static $instance = null;
    private $nonce_valid = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_loaded', array($this, 'init_elementor_hooks'));
    }

    public function init()
    {
        // Hook into pre_get_posts to modify queries
        add_action('pre_get_posts', array($this, 'modify_product_query'));

        // Hook into WooCommerce product query
        add_filter('woocommerce_product_query_meta_query', array($this, 'modify_wc_query'), 10, 2);

        // Hook into main query for shop/archive pages
        add_action('woocommerce_product_query', array($this, 'modify_wc_product_query'));

        // Hook into WooCommerce shortcodes
        add_filter('woocommerce_shortcode_products_query', array($this, 'filter_shortcode_products_query'), 10, 2);

        // Hook into WooCommerce REST API
        add_filter('woocommerce_rest_product_object_query', array($this, 'filter_rest_product_query'), 10, 2);

        // Hook into WooCommerce blocks
        add_filter('woocommerce_blocks_product_grid_query_args', array($this, 'filter_blocks_query'), 10, 2);

        // Hook into general WP_Query for products
        add_filter('posts_clauses', array($this, 'filter_posts_clauses'), 10, 2);

        // Hook into WooCommerce product data store
        add_filter('woocommerce_product_data_store_cpt_get_products_query', array($this, 'filter_data_store_query'), 10, 2);

        // AJAX handlers
        add_action('wp_ajax_woocommerce_get_products', array($this, 'ajax_handler'));
        add_action('wp_ajax_nopriv_woocommerce_get_products', array($this, 'ajax_handler'));
    }

    public function init_elementor_hooks()
    {
        // Hook into Elementor after it's loaded
        if (class_exists('\Elementor\Plugin')) {
            add_action('elementor/query/custom_query', array($this, 'elementor_custom_query'), 10, 2);
            add_filter('elementor_pro/woocommerce/query', array($this, 'elementor_woocommerce_query'), 10, 2);
        }

        // Hook into common third-party plugins
        $this->init_third_party_hooks();
    }

    public function init_third_party_hooks()
    {
        // Hook into popular product grid plugins
        add_filter('woo_product_grid_query_args', array($this, 'filter_product_grid_args'), 10, 2);
        add_filter('wc_product_table_query_args', array($this, 'filter_product_grid_args'), 10, 2);
        add_filter('wcpgsk_query_args', array($this, 'filter_product_grid_args'), 10, 2);

        // Hook into WooCommerce Product Filter plugins
        add_filter('woocommerce_product_filter_query_args', array($this, 'filter_product_grid_args'), 10, 2);

        // Hook into JetEngine (Crocoblock)
        add_filter('jet-engine/listing/grid/query-args', array($this, 'filter_jet_engine_query'), 10, 2);

        // Hook into WooCommerce Product Blocks
        add_filter('woocommerce_blocks_product_grid_query_args', array($this, 'filter_product_grid_args'), 10, 2);

        // Hook into custom post type queries
        add_action('parse_query', array($this, 'parse_custom_query'));
    }

    /**
     * Modify the main WordPress query for products
     */
    public function modify_product_query($query)
    {
        // Only modify frontend queries and product queries
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Get all filter parameters
        $params = $this->get_filter_params();

        // If no parameters, return
        if (empty($params)) {
            return;
        }

        // Check if this is a product query
        if (
            $query->is_shop() || $query->is_product_category() || $query->is_product_tag() ||
            (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'product')
        ) {

            $this->apply_filters_to_query($query);
        }
    }

    /**
     * Modify WooCommerce specific product queries
     */
    public function modify_wc_product_query($query)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return;
        }

        $this->apply_filters_to_query($query);
    }

    /**
     * Modify WooCommerce meta query
     */
    public function modify_wc_query($meta_query, $query)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $meta_query;
        }

        // Apply taxonomy filters if we have the query object
        if ($query && method_exists($query, 'set')) {
            $this->apply_filters_to_query($query);
        }

        return $meta_query;
    }

    /**
     * Filter shortcode products query
     */
    public function filter_shortcode_products_query($args, $atts)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Filter REST API product query
     */
    public function filter_rest_product_query($args, $request)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Filter WooCommerce blocks query
     */
    public function filter_blocks_query($args, $attributes)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Filter posts clauses for any product query
     */
    public function filter_posts_clauses($clauses, $query)
    {
        // Only apply to product queries
        if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'product') {
            return $clauses;
        }

        $params = $this->get_filter_params();

        if (empty($params)) {
            return $clauses;
        }

        // Apply additional WHERE clauses if needed
        $additional_where = $this->get_additional_where_clauses($params);
        if (!empty($additional_where)) {
            $clauses['where'] .= ' AND ' . $additional_where;
        }

        return $clauses;
    }

    /**
     * Filter WooCommerce data store query
     */
    public function filter_data_store_query($args, $query_vars)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Handle Elementor custom queries
     */
    public function elementor_custom_query($query, $widget)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return;
        }

        $this->apply_filters_to_query($query);
    }

    /**
     * Handle Elementor WooCommerce queries
     */
    public function elementor_woocommerce_query($query, $widget)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $query;
        }

        return $this->apply_filters_to_args($query);
    }

    /**
     * Filter product grid arguments
     */
    public function filter_product_grid_args($args, $settings = null)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Filter JetEngine query
     */
    public function filter_jet_engine_query($args, $widget)
    {
        // Only apply to product queries
        if (!isset($args['post_type']) || $args['post_type'] !== 'product') {
            return $args;
        }

        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        return $this->apply_filters_to_args($args);
    }

    /**
     * Parse custom queries
     */
    public function parse_custom_query($query)
    {
        // Only apply to product queries
        if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'product') {
            return;
        }

        $params = $this->get_filter_params();

        if (empty($params)) {
            return;
        }

        $this->apply_filters_to_query($query);
    }

    /**
     * Determine if the current request carries a verified nonce.
     */
    private function is_nonce_valid(): bool
    {
        if ($this->nonce_valid !== null) {
            return $this->nonce_valid;
        }

        if (!isset($_GET['gm-product-filter-nonce'])) {
            return $this->nonce_valid = false;
        }

        $nonce = sanitize_text_field(wp_unslash($_GET['gm-product-filter-nonce']));
        return $this->nonce_valid = (bool) wp_verify_nonce($nonce, 'gm-product-filter-action');
    }

    /**
     * Determine if the current request is a safe, read-only GET.
     */
    private function request_is_read_only(): bool
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        return $method === 'GET';
    }

    /**
     * Check if the request contains any filter-related query vars.
     */
    private function has_filter_query_vars(): bool
    {
        $simple_keys = array(
            'product-category',
            'tags',
            'mn_price',
            'mx_price',
            'rating',
            'plugincy_search',
            'rcata',
            'rtag',
            'operator_second',
            'tax_relation',
            'meta_relation',
            'rplurand',
            'rplutock_status',
            'rpluthor',
            'rpn_sale',
            'min_length',
            'max_length',
            'min_width',
            'max_width',
            'min_height',
            'max_height',
            'min_weight',
            'max_weight',
            'sku',
            'product_id',
            'discount',
            'new_arrivals',
            'date_filter',
            'date_from',
            'date_to',
            'per_page',
            'orderby',
            'order'
        );

        foreach ($simple_keys as $key) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                return true;
            }
        }

        foreach ($_GET as $key => $value) {
            if (($value === '' || $value === null)) {
                continue;
            }

            if (strpos($key, 'rplugpa_') === 0 || strpos($key, 'rplugcusf_') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply filters to query object
     */
    private function apply_filters_to_query($query)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return;
        }

        $tax_query = array();
        $meta_query = array();
        $tax_operator = isset($params['operator_second']) ? $params['operator_second'] : 'IN';

        if (isset($params['per_page']) && $params['per_page'] > 0) {
            $query->set('posts_per_page', $params['per_page']);
        }
        $ordering_args = $this->resolve_ordering_args($params);
        foreach ($ordering_args as $key => $value) {
            $query->set($key, $value);
        }

        // Apply category filter
        if (isset($params["product-category"]) && !empty($params["product-category"])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $params["product-category"],
                'operator' => $tax_operator,
            );
        }

        // Apply tag filter
        if (isset($params['tags']) && !empty($params['tags'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $params['tags'],
                'operator' => $tax_operator,
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
                        'operator' => $tax_operator,
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

            if ($max > 0) {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => array($min, $max),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } else {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => $min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        }

        // Apply rating filter
        if (isset($params['rating']) && !empty($params['rating'])) {
            $min_rating = is_array($params['rating']) ? min(array_map('floatval', $params['rating'])) : floatval($params['rating']);
            $meta_query[] = array(
                'key'     => '_wc_average_rating',
                'value'   => $min_rating,
                'compare' => '>=',
                'type'    => 'DECIMAL',
            );
        }

        // Apply plugincy_search filter
        if (isset($params['plugincy_search']) && !empty($params['plugincy_search'])) {
            $query->set('s', $params['plugincy_search']);
        }

        // Apply rplurand filter (assuming rplurand is a taxonomy)
        if (isset($params['rplurand']) && !empty($params['rplurand'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field'    => 'slug',
                'terms'    => $params['rplurand'],
                'operator' => $tax_operator,
            );
        }

        // Apply stock status filter
        if (isset($params['rplutock_status']) && !empty($params['rplutock_status'])) {
            $meta_query[] = array(
                'key'     => '_stock_status',
                'value'   => $params['rplutock_status'],
                'compare' => 'IN',
            );
        }

        // Apply rpluthor filter
        if (isset($params['rpluthor']) && !empty($params['rpluthor'])) {
            $query->set('author__in', $params['rpluthor']);
        }

        // Apply on sale filter
        if (isset($params['rpn_sale']) && $params['rpn_sale']) {
            $sale_product_ids = wc_get_product_ids_on_sale();
            if (!empty($sale_product_ids)) {
                $query->set('post__in', $sale_product_ids);
            } else {
                $query->set('post__in', array(0)); // No products found
            }
        }

        // Apply dimension filters
        if (isset($params['min_length']) || isset($params['max_length'])) {
            $length_query = array('key' => '_length', 'type' => 'NUMERIC');
            if (isset($params['min_length']) && isset($params['max_length'])) {
                $length_query['value'] = array($params['min_length'], $params['max_length']);
                $length_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_length'])) {
                $length_query['value'] = $params['min_length'];
                $length_query['compare'] = '>=';
            } else {
                $length_query['value'] = $params['max_length'];
                $length_query['compare'] = '<=';
            }
            $meta_query[] = $length_query;
        }

        if (isset($params['min_width']) || isset($params['max_width'])) {
            $width_query = array('key' => '_width', 'type' => 'NUMERIC');
            if (isset($params['min_width']) && isset($params['max_width'])) {
                $width_query['value'] = array($params['min_width'], $params['max_width']);
                $width_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_width'])) {
                $width_query['value'] = $params['min_width'];
                $width_query['compare'] = '>=';
            } else {
                $width_query['value'] = $params['max_width'];
                $width_query['compare'] = '<=';
            }
            $meta_query[] = $width_query;
        }

        if (isset($params['min_height']) || isset($params['max_height'])) {
            $height_query = array('key' => '_height', 'type' => 'NUMERIC');
            if (isset($params['min_height']) && isset($params['max_height'])) {
                $height_query['value'] = array($params['min_height'], $params['max_height']);
                $height_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_height'])) {
                $height_query['value'] = $params['min_height'];
                $height_query['compare'] = '>=';
            } else {
                $height_query['value'] = $params['max_height'];
                $height_query['compare'] = '<=';
            }
            $meta_query[] = $height_query;
        }

        if (isset($params['min_weight']) || isset($params['max_weight'])) {
            $weight_query = array('key' => '_weight', 'type' => 'NUMERIC');
            if (isset($params['min_weight']) && isset($params['max_weight'])) {
                $weight_query['value'] = array($params['min_weight'], $params['max_weight']);
                $weight_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_weight'])) {
                $weight_query['value'] = $params['min_weight'];
                $weight_query['compare'] = '>=';
            } else {
                $weight_query['value'] = $params['max_weight'];
                $weight_query['compare'] = '<=';
            }
            $meta_query[] = $weight_query;
        }

        // Apply custom meta filters
        if (isset($params['custom_meta']) && is_array($params['custom_meta'])) {
            foreach ($params['custom_meta'] as $meta_key => $meta_values) {
                if (!empty($meta_values)) {
                    $meta_query[] = array(
                        'key'     => $meta_key,
                        'value'   => $meta_values,
                        'compare' => 'IN',
                    );
                }
            }
        }

        // Apply SKU filter
        if (isset($params['sku']) && !empty($params['sku'])) {
            $meta_query[] = array(
                'key'     => '_sku',
                'value'   => $params['sku'],
                'compare' => 'IN',
            );
        }

        // Apply product ID filter
        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $query->set('post__in', $params['product_id']);
        }

        // Apply discount filter
        if (isset($params['discount']) && $params['discount'] > 0) {
            $discount_products = $this->get_products_with_discount($params['discount']);
            if (!empty($discount_products)) {
                $query->set('post__in', $discount_products);
            } else {
                $query->set('post__in', array(0)); // No products found
            }
        }

        // Apply new arrivals filter
        if (isset($params['new_arrivals']) && $params['new_arrivals'] > 0) {
            $date_from = gmdate('Y-m-d', strtotime('-' . $params['new_arrivals'] . ' days'));
            $query->set('date_query', array(
                array(
                    'after' => $date_from,
                    'inclusive' => true,
                ),
            ));
        }

        // Apply date filters
        if (isset($params['date_filter'])) {
            $date_query = $this->get_date_query($params);
            if (!empty($date_query)) {
                $query->set('date_query', $date_query);
            }
        }

        // Apply tax_query if we have any
        if (!empty($tax_query)) {
            $existing_tax_query = $query->get('tax_query');

            if (!empty($existing_tax_query)) {
                $tax_query = array_merge($existing_tax_query, $tax_query);
            }

            if (count($tax_query) > 1) {
                $tax_query['relation'] = $params['tax_relation'] ?? 'AND';
            }

            $query->set('tax_query', $tax_query);
        }

        // Apply meta_query if we have any
        if (!empty($meta_query)) {
            $existing_meta_query = $query->get('meta_query');

            if (!empty($existing_meta_query)) {
                $meta_query = array_merge($existing_meta_query, $meta_query);
            }

            if (count($meta_query) > 1) {
                $meta_query['relation'] = $params['meta_relation'] ?? 'AND';
            }

            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * Apply filters to arguments array
     */
    private function apply_filters_to_args($args)
    {
        $params = $this->get_filter_params();

        if (empty($params)) {
            return $args;
        }

        $tax_query = isset($args['tax_query']) ? $args['tax_query'] : array();
        $meta_query = isset($args['meta_query']) ? $args['meta_query'] : array();
        $tax_operator = isset($params['operator_second']) ? $params['operator_second'] : 'IN';

        if (isset($params['per_page']) && $params['per_page'] > 0) {
            $args['posts_per_page'] = $params['per_page'];
        }
        $ordering_args = $this->resolve_ordering_args($params);
        foreach ($ordering_args as $key => $value) {
            $args[$key] = $value;
        }

        // Apply category filter
        if (isset($params["product-category"]) && !empty($params["product-category"])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $params["product-category"],
                'operator' => $tax_operator,
            );
        }

        // Apply tag filter
        if (isset($params['tags']) && !empty($params['tags'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $params['tags'],
                'operator' => $tax_operator,
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
                        'operator' => $tax_operator,
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

            if ($max > 0) {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => array($min, $max),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } else {
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => $min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            }
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

        // Apply plugincy_search filter
        if (isset($params['plugincy_search']) && !empty($params['plugincy_search'])) {
            $args['s'] = $params['plugincy_search'];
        }

        // Apply rplurand filter
        if (isset($params['rplurand']) && !empty($params['rplurand'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field'    => 'slug',
                'terms'    => $params['rplurand'],
                'operator' => $tax_operator,
            );
        }

        // Apply stock status filter
        if (isset($params['rplutock_status']) && !empty($params['rplutock_status'])) {
            $meta_query[] = array(
                'key'     => '_stock_status',
                'value'   => $params['rplutock_status'],
                'compare' => 'IN',
            );
        }

        // Apply rpluthor filter
        if (isset($params['rpluthor']) && !empty($params['rpluthor'])) {
            $args['author__in'] = $params['rpluthor'];
        }

        // Apply on sale filter
        if (isset($params['rpn_sale']) && $params['rpn_sale']) {
            $sale_product_ids = wc_get_product_ids_on_sale();
            if (!empty($sale_product_ids)) {
                $args['post__in'] = $sale_product_ids;
            } else {
                $args['post__in'] = array(0);
            }
        }

        // Apply dimension filters
        // Length query
        if (isset($params['min_length']) || isset($params['max_length'])) {
            $length_query = array('key' => '_length', 'type' => 'NUMERIC');
            if (isset($params['min_length']) && isset($params['max_length'])) {
                $length_query['value'] = array($params['min_length'], $params['max_length']);
                $length_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_length'])) {
                $length_query['value'] = $params['min_length'];
                $length_query['compare'] = '>=';
            } else {
                $length_query['value'] = $params['max_length'];
                $length_query['compare'] = '<=';
            }
            $meta_query[] = $length_query;
        }

        // Width query
        if (isset($params['min_width']) || isset($params['max_width'])) {
            $width_query = array('key' => '_width', 'type' => 'NUMERIC');
            if (isset($params['min_width']) && isset($params['max_width'])) {
                $width_query['value'] = array($params['min_width'], $params['max_width']);
                $width_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_width'])) {
                $width_query['value'] = $params['min_width'];
                $width_query['compare'] = '>=';
            } else {
                $width_query['value'] = $params['max_width'];
                $width_query['compare'] = '<=';
            }
            $meta_query[] = $width_query;
        }

        // Height query
        if (isset($params['min_height']) || isset($params['max_height'])) {
            $height_query = array('key' => '_height', 'type' => 'NUMERIC');
            if (isset($params['min_height']) && isset($params['max_height'])) {
                $height_query['value'] = array($params['min_height'], $params['max_height']);
                $height_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_height'])) {
                $height_query['value'] = $params['min_height'];
                $height_query['compare'] = '>=';
            } else {
                $height_query['value'] = $params['max_height'];
                $height_query['compare'] = '<=';
            }
            $meta_query[] = $height_query;
        }

        // Weight query
        if (isset($params['min_weight']) || isset($params['max_weight'])) {
            $weight_query = array('key' => '_weight', 'type' => 'NUMERIC');
            if (isset($params['min_weight']) && isset($params['max_weight'])) {
                $weight_query['value'] = array($params['min_weight'], $params['max_weight']);
                $weight_query['compare'] = 'BETWEEN';
            } elseif (isset($params['min_weight'])) {
                $weight_query['value'] = $params['min_weight'];
                $weight_query['compare'] = '>=';
            } else {
                $weight_query['value'] = $params['max_weight'];
                $weight_query['compare'] = '<=';
            }
            $meta_query[] = $weight_query;
        }

        // Apply custom meta filters
        if (isset($params['custom_meta']) && is_array($params['custom_meta'])) {
            foreach ($params['custom_meta'] as $meta_key => $meta_values) {
                if (!empty($meta_values)) {
                    $meta_query[] = array(
                        'key'     => $meta_key,
                        'value'   => $meta_values,
                        'compare' => 'IN',
                    );
                }
            }
        }

        // Apply SKU filter
        if (isset($params['sku']) && !empty($params['sku'])) {
            $meta_query[] = array(
                'key'     => '_sku',
                'value'   => $params['sku'],
                'compare' => 'IN',
            );
        }

        // Apply product ID filter
        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $args['post__in'] = $params['product_id'];
        }

        // Apply discount filter
        if (isset($params['discount']) && $params['discount'] > 0) {
            $discount_products = $this->get_products_with_discount($params['discount']);
            if (!empty($discount_products)) {
                $args['post__in'] = $discount_products;
            } else {
                $args['post__in'] = array(0);
            }
        }

        // Apply new arrivals filter
        if (isset($params['new_arrivals']) && $params['new_arrivals'] > 0) {
            $date_from = gmdate('Y-m-d', strtotime('-' . $params['new_arrivals'] . ' days'));
            $args['date_query'] = array(
                array(
                    'after' => $date_from,
                    'inclusive' => true,
                ),
            );
        }

        // Apply date filters
        if (isset($params['date_filter'])) {
            $date_query = $this->get_date_query($params);
            if (!empty($date_query)) {
                $args['date_query'] = $date_query;
            }
        }

        // Apply queries
        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = $params['tax_relation'] ?? 'AND';
            }
            $args['tax_query'] = $tax_query;
        }

        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = $params['meta_relation'] ?? 'AND';
            }
            $args['meta_query'] = $meta_query;
        }

        return $args;
    }

    /**
     * Helper method to get products with specific discount percentage
     */
    private function get_products_with_discount($min_discount)
    {
        $product_ids = array();
        $product_details = dapfforwc_get_woocommerce_product_details();

        foreach ($product_details['products'] as $product) {
            if ($product['discount_percentage'] >= $min_discount) {
                $product_ids[] = $product['ID'];
            }
        }

        return $product_ids;
    }

    /**
     * Helper method to get date query based on filter parameters
     */
    private function get_date_query($params)
    {
        $date_query = array();

        if (isset($params['date_filter'])) {
            switch ($params['date_filter']) {
                case 'today':
                    $date_query = array(
                        array(
                            'year'  => gmdate('Y'),
                            'month' => gmdate('n'),
                            'day'   => gmdate('j'),
                        ),
                    );
                    break;

                case 'this_week':
                    $date_query = array(
                        array(
                            'after' => gmdate('Y-m-d', strtotime('monday this week')),
                            'before' => gmdate('Y-m-d', strtotime('sunday this week')),
                            'inclusive' => true,
                        ),
                    );
                    break;

                case 'this_month':
                    $date_query = array(
                        array(
                            'year'  => gmdate('Y'),
                            'month' => gmdate('n'),
                        ),
                    );
                    break;

                case 'this_year':
                    $date_query = array(
                        array(
                            'year' => gmdate('Y'),
                        ),
                    );
                    break;

                case 'custom':
                    if (isset($params['date_from']) && isset($params['date_to'])) {
                        $date_query = array(
                            array(
                                'after' => $params['date_from'],
                                'before' => $params['date_to'],
                                'inclusive' => true,
                            ),
                        );
                    } elseif (isset($params['date_from'])) {
                        $date_query = array(
                            array(
                                'after' => $params['date_from'],
                                'inclusive' => true,
                            ),
                        );
                    } elseif (isset($params['date_to'])) {
                        $date_query = array(
                            array(
                                'before' => $params['date_to'],
                                'inclusive' => true,
                            ),
                        );
                    }
                    break;
            }
        }

        return $date_query;
    }

    /**
     * Map user friendly ordering values to WooCommerce query args.
     */
    private function resolve_ordering_args($params)
    {
        if (empty($params['orderby']) && empty($params['order'])) {
            return array();
        }

        if (!function_exists('wc_get_catalog_ordering_args')) {
            $ordering = array();
        } else {
            $ordering = wc_get_catalog_ordering_args(
                $params['orderby'] ?? '',
                $params['order'] ?? ''
            );
        }

        $resolved = array();
        foreach (array('orderby', 'order', 'meta_key', 'meta_value') as $key) {
            if (isset($ordering[$key]) && $ordering[$key] !== '') {
                $resolved[$key] = $ordering[$key];
            }
        }

        return $resolved;
    }


    /**
     * Get additional WHERE clauses
     */
    private function get_additional_where_clauses($params)
    {

        $where_clauses = array();

        // Add any additional WHERE conditions here if needed
        // For example, for complex price filtering or custom fields

        return !empty($where_clauses) ? implode(' AND ', $where_clauses) : '';
    }

    /**
     * Get all filter parameters from URL
     */
    public function get_filter_params()
    {
        $nonce_valid = $this->is_nonce_valid();
        if (!$nonce_valid) {
            if (!$this->request_is_read_only()) {
                return array();
            }
            if (!$this->has_filter_query_vars()) {
                return array();
            }
        }

        $params = array();
        $tax_operator = 'IN';

        // Category filter
        if (isset($_GET['product-category']) && !empty($_GET['product-category'])) {
            $params['product-category'] = is_array($_GET['product-category'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['product-category']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['product-category'])));
        }

        // Tag filter
        if (isset($_GET['tags']) && !empty($_GET['tags'])) {
            $params['tags'] = is_array($_GET['tags'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['tags']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['tags'])));
        }

        // Attribute filters
        $attributes = array();
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'rplugpa_') === 0 && !empty($value)) {
                $attribute_name = str_replace('rplugpa_', '', $key);
                $attributes[$attribute_name] = is_array($value)
                    ? array_map('sanitize_text_field', wp_unslash($value))
                    : explode(',', sanitize_text_field(wp_unslash($value)));
            }
        }
        if (!empty($attributes)) {
            $params['attributes'] = $attributes;
        }

        // Price range
        if (isset($_GET['mn_price']) && !empty($_GET['mn_price'])) {
            $params['mn_price'] = floatval($_GET['mn_price']);
        }
        if (isset($_GET['mx_price']) && !empty($_GET['mx_price'])) {
            $params['mx_price'] = floatval($_GET['mx_price']);
        }

        // Pagination / ordering
        if (isset($_GET['per_page']) && $_GET['per_page'] !== '') {
            $per_page = absint($_GET['per_page']);
            if ($per_page > 0) {
                $params['per_page'] = max(1, min($per_page, 120));
            }
        }

        if (isset($_GET['orderby']) && $_GET['orderby'] !== '') {
            $orderby_key = sanitize_key(str_replace('-', '_', $_GET['orderby']));
            $allowed_orderby = array(
                'date'        => 'date',
                'price'       => 'price',
                'price_desc'  => 'price-desc',
                'popularity'  => 'popularity',
                'rating'      => 'rating',
                'title'       => 'title',
                'menu_order'  => 'menu_order',
                'rand'        => 'rand',
            );
            if (isset($allowed_orderby[$orderby_key])) {
                $params['orderby'] = $allowed_orderby[$orderby_key];
            }
        }

        if (isset($_GET['order']) && $_GET['order'] !== '') {
            $order = strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
            if (in_array($order, array('ASC', 'DESC'), true)) {
                $params['order'] = $order;
            }
        }

        if (isset($_GET['operator_second']) && $_GET['operator_second'] !== '') {
            $operator = strtoupper(sanitize_text_field(wp_unslash($_GET['operator_second'])));
            if (in_array($operator, array('IN', 'NOT IN', 'AND'), true)) {
                $params['operator_second'] = $operator;
                $tax_operator = $operator;
            }
        } else {
            $params['operator_second'] = $tax_operator;
        }

        if (isset($_GET['tax_relation'])) {
            $relation = strtoupper(sanitize_text_field(wp_unslash($_GET['tax_relation'])));
            if (in_array($relation, array('AND', 'OR'), true)) {
                $params['tax_relation'] = $relation;
            }
        }

        if (isset($_GET['meta_relation'])) {
            $relation = strtoupper(sanitize_text_field(wp_unslash($_GET['meta_relation'])));
            if (in_array($relation, array('AND', 'OR'), true)) {
                $params['meta_relation'] = $relation;
            }
        }

        // Rating filter
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $params['rating'] = is_array($_GET['rating'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['rating']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['rating'])));
        }

        // plugincy_search filter
        if (isset($_GET['plugincy_search']) && !empty($_GET['plugincy_search'])) {
            $params['plugincy_search'] = sanitize_text_field(wp_unslash($_GET['plugincy_search']));
        }

        // Backward compatibility with old parameters
        if (isset($_GET['rcata']) && !empty($_GET['rcata'])) {
            $category_ids = $this->parse_ids(sanitize_text_field(wp_unslash($_GET['rcata'])));
            if (!empty($category_ids)) {
                $category_slugs = array();
                foreach ($category_ids as $id) {
                    $term = get_term($id, 'product_cat');
                    if ($term && !is_wp_error($term)) {
                        $category_slugs[] = $term->slug;
                    }
                }
                if (!empty($category_slugs)) {
                    $params['product-category'] = $category_slugs;
                }
            }
        }

        if (isset($_GET['rtag']) && !empty($_GET['rtag'])) {
            $tag_ids = $this->parse_ids(sanitize_text_field(wp_unslash($_GET['rtag'])));
            if (!empty($tag_ids)) {
                $tag_slugs = array();
                foreach ($tag_ids as $id) {
                    $term = get_term($id, 'product_tag');
                    if ($term && !is_wp_error($term)) {
                        $tag_slugs[] = $term->slug;
                    }
                }
                if (!empty($tag_slugs)) {
                    $params['tags'] = $tag_slugs;
                }
            }
        }

        // rplurand filter
        if (isset($_GET['rplurand']) && !empty($_GET['rplurand'])) {
            $params['rplurand'] = is_array($_GET['rplurand'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['rplurand']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['rplurand'])));
        }

        // Stock status filter
        if (isset($_GET['rplutock_status']) && !empty($_GET['rplutock_status'])) {
            $params['rplutock_status'] = is_array($_GET['rplutock_status'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['rplutock_status']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['rplutock_status'])));
        }

        // rpluthor filter
        // rpluthor filter (using username instead of user ID)
        if (isset($_GET['rpluthor']) && !empty($_GET['rpluthor'])) {
            $usernames = is_array($_GET['rpluthor'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['rpluthor']))
                : array_map('sanitize_text_field', explode(',', sanitize_text_field(wp_unslash($_GET['rpluthor']))));

            // Convert usernames to user IDs
            $user_ids = array();
            foreach ($usernames as $username) {
                $user = get_user_by('login', $username);
                if ($user) {
                    $user_ids[] = $user->ID;
                }
            }

            if (!empty($user_ids)) {
                $params['rpluthor'] = $user_ids;
            }
        }

        // On sale filter
        if (isset($_GET['rpn_sale']) && $_GET['rpn_sale'] === 'onsale') {
            $params['rpn_sale'] = true;
        }

        // Dimensions filters
        if (isset($_GET['min_length']) && !empty($_GET['min_length'])) {
            $params['min_length'] = floatval($_GET['min_length']);
        }
        if (isset($_GET['max_length']) && !empty($_GET['max_length'])) {
            $params['max_length'] = floatval($_GET['max_length']);
        }
        if (isset($_GET['min_width']) && !empty($_GET['min_width'])) {
            $params['min_width'] = floatval($_GET['min_width']);
        }
        if (isset($_GET['max_width']) && !empty($_GET['max_width'])) {
            $params['max_width'] = floatval($_GET['max_width']);
        }
        if (isset($_GET['min_height']) && !empty($_GET['min_height'])) {
            $params['min_height'] = floatval($_GET['min_height']);
        }
        if (isset($_GET['max_height']) && !empty($_GET['max_height'])) {
            $params['max_height'] = floatval($_GET['max_height']);
        }
        if (isset($_GET['min_weight']) && !empty($_GET['min_weight'])) {
            $params['min_weight'] = floatval($_GET['min_weight']);
        }
        if (isset($_GET['max_weight']) && !empty($_GET['max_weight'])) {
            $params['max_weight'] = floatval($_GET['max_weight']);
        }

        // Custom meta filters
        $custom_meta = array();
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'rplugcusf_') === 0 && !empty($value)) {
                $meta_key = str_replace('rplugcusf_', '', $key);
                $custom_meta[$meta_key] = is_array($value)
                    ? array_map('sanitize_text_field', wp_unslash($value))
                    : explode(',', sanitize_text_field(wp_unslash($value)));
            }
        }
        if (!empty($custom_meta)) {
            $params['custom_meta'] = $custom_meta;
        }

        // SKU filter
        if (isset($_GET['sku']) && !empty($_GET['sku'])) {
            $params['sku'] = is_array($_GET['sku'])
                ? array_map('sanitize_text_field', wp_unslash($_GET['sku']))
                : explode(',', sanitize_text_field(wp_unslash($_GET['sku'])));
        }

        // Product ID filter
        if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
            $params['product_id'] = is_array($_GET['product_id'])
                ? array_map('intval', $_GET['product_id'])
                : array_map('intval', explode(',', sanitize_text_field(wp_unslash($_GET['product_id']))));
        }

        // Discount filter
        if (isset($_GET['discount']) && !empty($_GET['discount'])) {
            $params['discount'] = floatval($_GET['discount']);
        }

        // New arrivals filter
        if (isset($_GET['new_arrivals']) && !empty($_GET['new_arrivals'])) {
            $params['new_arrivals'] = intval($_GET['new_arrivals']); // days
        }

        // Date filters
        if (isset($_GET['date_filter']) && !empty($_GET['date_filter'])) {
            $params['date_filter'] = sanitize_text_field(wp_unslash($_GET['date_filter']));
        }
        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
            $params['date_from'] = sanitize_text_field(wp_unslash($_GET['date_from']));
        }
        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
            $params['date_to'] = sanitize_text_field(wp_unslash($_GET['date_to']));
        }

        return $params;
    }

    /**
     * Parse comma-separated IDs
     */
    private function parse_ids($ids_string)
    {
        if (empty($ids_string)) {
            return array();
        }

        $ids = array_map('trim', explode(',', $ids_string));
        $ids = array_filter($ids, 'is_numeric');
        $ids = array_map('intval', $ids);

        return array_unique($ids);
    }

    /**
     * AJAX request handler
     */
    public function ajax_handler()
    {

        if (!isset($_GET['gm-product-filter-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
            return;
        }
        
        // Existing filter params
        $filter_params = array(
            'product-category',
            'tags',
            'mn_price',
            'mx_price',
            'rating',
            'plugincy_search',
            'rcata',
            'rtag',
            'operator_second',
            'tax_relation',
            'meta_relation',
            'per_page',
            'orderby',
            'order',
            'rplurand',
            'rplutock_status',
            'rpluthor', // This will now handle usernames
            'rpn_sale',
            'min_length',
            'max_length',
            'min_width',
            'max_width',
            'min_height',
            'max_height',
            'min_weight',
            'max_weight',
            'sku',
            'product_id',
            'discount',
            'new_arrivals',
            'date_filter',
            'date_from',
            'date_to'
        );

        foreach ($filter_params as $param) {
            if (isset($_REQUEST[$param])) {
                $_GET[$param] = sanitize_text_field(wp_unslash($_REQUEST[$param]));
            }
        }

        // Handle custom meta parameters
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'rplugcusf_') === 0) {
                $_GET[$key] = sanitize_text_field(wp_unslash($value));
            }
        }

        // Handle attribute parameters
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'rplugpa_') === 0) {
                $_GET[$key] = sanitize_text_field(wp_unslash($value));
            }
        }
    }
}

// Initialize the plugin
DAPFFORWC_WC_Query_Filter_Enhanced::get_instance();

/**
 * Additional hooks for specific page builders and plugins
 */
add_action('wp_loaded', function () {
    $filter_instance = DAPFFORWC_WC_Query_Filter_Enhanced::get_instance();

    // Hook into Divi Builder
    if (class_exists('ET_Builder_Plugin')) {
        add_filter('et_builder_module_posts_query_args', array($filter_instance, 'filter_product_grid_args'), 10, 2);
    }

    // Hook into Beaver Builder
    if (class_exists('FLBuilder')) {
        add_filter('fl_builder_loop_query_args', array($filter_instance, 'filter_product_grid_args'), 10, 2);
    }

    // Hook into Oxygen Builder
    if (class_exists('OxygenElement')) {
        add_filter('oxygen_repeater_query_args', array($filter_instance, 'filter_product_grid_args'), 10, 2);
    }

    // Hook into Bricks Builder
    if (class_exists('Bricks\Database')) {
        add_filter('bricks/query/run', array($filter_instance, 'filter_product_grid_args'), 10, 2);
    }
});

/**
 * Force query modification for stubborn plugins
 */
add_action('wp', function () {
    if (is_admin()) {
        return;
    }

    $filter_instance = DAPFFORWC_WC_Query_Filter_Enhanced::get_instance();
    if (empty($filter_instance->get_filter_params())) {
        return;
    }

    // Force hook into get_posts without infinite recursion
    $get_posts_handler = null;
    $get_posts_handler = function ($posts, $parsed_args) use (&$get_posts_handler, $filter_instance) {
        if (isset($parsed_args['post_type']) && $parsed_args['post_type'] === 'product') {
            remove_filter('get_posts', $get_posts_handler, 10);
            $query = new WP_Query($parsed_args);
            $filter_instance->apply_filters_to_query($query);
            add_filter('get_posts', $get_posts_handler, 10, 2);
            return $query->get_posts();
        }
        return $posts;
    };

    add_filter('get_posts', $get_posts_handler, 10, 2);
});
