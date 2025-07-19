<?php
if (!defined('ABSPATH')) {
    exit;
}

class DAPFFORWC_WC_Query_Filter_Enhanced {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_loaded', array($this, 'init_elementor_hooks'));
    }
    
    public function init() {
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
    
    public function init_elementor_hooks() {
        // Hook into Elementor after it's loaded
        if (class_exists('\Elementor\Plugin')) {
            add_action('elementor/query/custom_query', array($this, 'elementor_custom_query'), 10, 2);
            add_filter('elementor_pro/woocommerce/query', array($this, 'elementor_woocommerce_query'), 10, 2);
        }
        
        // Hook into common third-party plugins
        $this->init_third_party_hooks();
    }
    
    public function init_third_party_hooks() {
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
    public function modify_product_query($query) {
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
        if ($query->is_shop() || $query->is_product_category() || $query->is_product_tag() || 
            (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'product')) {
            
            $this->apply_filters_to_query($query);
        }
    }
    
    /**
     * Modify WooCommerce specific product queries
     */
    public function modify_wc_product_query($query) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return;
        }
        
        $this->apply_filters_to_query($query);
    }
    
    /**
     * Modify WooCommerce meta query
     */
    public function modify_wc_query($meta_query, $query) {
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
    public function filter_shortcode_products_query($args, $atts) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        return $this->apply_filters_to_args($args);
    }
    
    /**
     * Filter REST API product query
     */
    public function filter_rest_product_query($args, $request) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        return $this->apply_filters_to_args($args);
    }
    
    /**
     * Filter WooCommerce blocks query
     */
    public function filter_blocks_query($args, $attributes) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        return $this->apply_filters_to_args($args);
    }
    
    /**
     * Filter posts clauses for any product query
     */
    public function filter_posts_clauses($clauses, $query) {
        global $wpdb;
        
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
    public function filter_data_store_query($args, $query_vars) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        return $this->apply_filters_to_args($args);
    }
    
    /**
     * Handle Elementor custom queries
     */
    public function elementor_custom_query($query, $widget) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return;
        }
        
        $this->apply_filters_to_query($query);
    }
    
    /**
     * Handle Elementor WooCommerce queries
     */
    public function elementor_woocommerce_query($query, $widget) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $query;
        }
        
        return $this->apply_filters_to_args($query);
    }
    
    /**
     * Filter product grid arguments
     */
    public function filter_product_grid_args($args, $settings = null) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        return $this->apply_filters_to_args($args);
    }
    
    /**
     * Filter JetEngine query
     */
    public function filter_jet_engine_query($args, $widget) {
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
    public function parse_custom_query($query) {
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
     * Apply filters to query object
     */
    private function apply_filters_to_query($query) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return;
        }
        
        $tax_query = array();
        $meta_query = array();
        
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
        if (isset($params['tags']) && !empty($params['tags'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $params['tags'],
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
        
        // Apply tax_query if we have any
        if (!empty($tax_query)) {
            $existing_tax_query = $query->get('tax_query');
            
            if (!empty($existing_tax_query)) {
                $tax_query = array_merge($existing_tax_query, $tax_query);
            }
            
            if (count($tax_query) > 1) {
                $tax_query['relation'] = isset($_GET['tax_relation']) ? $_GET['tax_relation'] : 'AND';
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
                $meta_query['relation'] = isset($_GET['meta_relation']) ? $_GET['meta_relation'] : 'AND';
            }
            
            $query->set('meta_query', $meta_query);
        }
    }
    
    /**
     * Apply filters to arguments array
     */
    private function apply_filters_to_args($args) {
        $params = $this->get_filter_params();
        
        if (empty($params)) {
            return $args;
        }
        
        $tax_query = isset($args['tax_query']) ? $args['tax_query'] : array();
        $meta_query = isset($args['meta_query']) ? $args['meta_query'] : array();
        
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
        if (isset($params['tags']) && !empty($params['tags'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $params['tags'],
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
        
        // Apply queries
        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = isset($_GET['tax_relation']) ? $_GET['tax_relation'] : 'AND';
            }
            $args['tax_query'] = $tax_query;
        }
        
        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = isset($_GET['meta_relation']) ? $_GET['meta_relation'] : 'AND';
            }
            $args['meta_query'] = $meta_query;
        }
        
        return $args;
    }
    
    /**
     * Get additional WHERE clauses
     */
    private function get_additional_where_clauses($params) {
        global $wpdb;
        
        $where_clauses = array();
        
        // Add any additional WHERE conditions here if needed
        // For example, for complex price filtering or custom fields
        
        return !empty($where_clauses) ? implode(' AND ', $where_clauses) : '';
    }
    
    /**
     * Get all filter parameters from URL
     */
    public function get_filter_params() {
        $params = array();
        
        // Category filter
        if (isset($_GET['product-category']) && !empty($_GET['product-category'])) {
            $params['product-category'] = is_array($_GET['product-category']) 
                ? array_map('sanitize_text_field', $_GET['product-category'])
                : explode(',', sanitize_text_field($_GET['product-category']));
        }
        
        // Tag filter
        if (isset($_GET['tags']) && !empty($_GET['tags'])) {
            $params['tags'] = is_array($_GET['tags']) 
                ? array_map('sanitize_text_field', $_GET['tags'])
                : explode(',', sanitize_text_field($_GET['tags']));
        }
        
        // Attribute filters
        $attributes = array();
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'rplugpa_') === 0 && !empty($value)) {
                $attribute_name = str_replace('rplugpa_', '', $key);
                $attributes[$attribute_name] = is_array($value) 
                    ? array_map('sanitize_text_field', $value)
                    : explode(',', sanitize_text_field($value));
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
        
        // Rating filter
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $params['rating'] = is_array($_GET['rating']) 
                ? array_map('sanitize_text_field', $_GET['rating'])
                : explode(',', sanitize_text_field($_GET['rating']));
        }
        
        // plugincy_search filter
        if (isset($_GET['plugincy_search']) && !empty($_GET['plugincy_search'])) {
            $params['plugincy_search'] = sanitize_text_field($_GET['plugincy_search']);
        }
        
        // Backward compatibility with old parameters
        if (isset($_GET['rcata']) && !empty($_GET['rcata'])) {
            $category_ids = $this->parse_ids($_GET['rcata']);
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
            $tag_ids = $this->parse_ids($_GET['rtag']);
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
        
        return $params;
    }
    
    /**
     * Parse comma-separated IDs
     */
    private function parse_ids($ids_string) {
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
    public function ajax_handler() {
        $filter_params = array(
            'product-category', 'tags', 'mn_price', 'mx_price', 'rating', 'plugincy_search',
            'rcata', 'rtag', 'operator_second', 'tax_relation', 'meta_relation'
        );
        
        foreach ($filter_params as $param) {
            if (isset($_REQUEST[$param])) {
                $_GET[$param] = sanitize_text_field($_REQUEST[$param]);
            }
        }
        
        // Handle attribute parameters
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'rplugpa_') === 0) {
                $_GET[$key] = sanitize_text_field($value);
            }
        }
    }
}

// Initialize the plugin
DAPFFORWC_WC_Query_Filter_Enhanced::get_instance();

/**
 * Additional hooks for specific page builders and plugins
 */
add_action('wp_loaded', function() {
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
add_action('wp', function() {
    if (is_admin()) {
        return;
    }
    
    $filter_instance = DAPFFORWC_WC_Query_Filter_Enhanced::get_instance();
    $params = $filter_instance->get_filter_params();
    
    if (empty($params)) {
        return;
    }
    
    // Force hook into get_posts
    add_filter('get_posts', function($posts, $parsed_args) use ($filter_instance, $params) {
        if (isset($parsed_args['post_type']) && $parsed_args['post_type'] === 'product') {
            $query = new WP_Query($parsed_args);
            $filter_instance->apply_filters_to_query($query);
            return $query->get_posts();
        }
        return $posts;
    }, 10, 2);
});