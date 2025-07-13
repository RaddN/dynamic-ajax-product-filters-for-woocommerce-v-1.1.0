<?php
/**
 * WooCommerce Custom Class Injection System - Enhanced Version
 * Adds consistent custom classes to WooCommerce elements for reliable filtering
 * Compatible with more themes and handles edge cases better
 * Now includes support for WooCommerce block templates
 */

class dapfforwc_Custom_Class_Injector {
    
    private $custom_classes = array(
        'products' => 'plugincy-filter-products',
        'pagination' => 'plugincy-filter-pagination',
        'orderby' => 'plugincy-filter-orderby',
        'result_count' => 'plugincy-filter-result-count',
        'product_item' => 'plugincy-filter-product-item'
    );
    
    private $wrapper_added = false;
    
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize all hooks
     */
    private function init_hooks() {
        // Products loop classes - multiple approaches for better compatibility
        add_filter('woocommerce_product_loop_start', array($this, 'add_products_wrapper_class'), 10, 2);
        add_filter('woocommerce_shortcode_products_container_classes', array($this, 'add_shortcode_container_classes'));
        add_action('woocommerce_before_shop_loop', array($this, 'before_shop_loop_wrapper'), 5);
        add_action('woocommerce_after_shop_loop', array($this, 'after_shop_loop_wrapper'), 25);
        
        // Product item classes
        add_filter('woocommerce_post_class', array($this, 'add_product_item_class'), 10, 2);
        add_filter('post_class', array($this, 'add_product_item_class_fallback'), 10, 3);
        
        // Pagination classes - multiple hooks for different implementations
        add_filter('woocommerce_pagination_args', array($this, 'add_pagination_class'));
        add_action('woocommerce_after_shop_loop', array($this, 'inject_pagination_class'), 30);
        
        // Orderby dropdown classes
        add_action('woocommerce_before_shop_loop', array($this, 'start_orderby_capture'), 19);
        add_action('woocommerce_before_shop_loop', array($this, 'end_orderby_capture'), 21);
        
        // Result count classes
        add_filter('woocommerce_result_count', array($this, 'add_result_count_class'));
        add_action('woocommerce_before_shop_loop', array($this, 'inject_result_count_class'), 25);

        // WooCommerce Block Template support - only server-side rendering
        add_filter('render_block', array($this, 'add_block_template_class'), 10, 2);
    }
    
    /**
     * Improved products wrapper class addition
     */
    public function add_products_wrapper_class($html, $wc_get_template_part_args = array()) {
        // More robust class injection
        $class_to_add = $this->custom_classes['products'];
        
        // Handle different HTML structures
        if (preg_match('/<(ul|div|section)([^>]*?)class=(["\'])([^"\']*?)\3([^>]*?)>/i', $html, $matches)) {
            $existing_classes = $matches[4];
            $new_classes = $existing_classes . ' ' . $class_to_add;
            $html = str_replace($matches[0], '<' . $matches[1] . $matches[2] . 'class=' . $matches[3] . $new_classes . $matches[3] . $matches[5] . '>', $html);
        } elseif (preg_match('/<(ul|div|section)([^>]*?)>/i', $html, $matches)) {
            $html = str_replace($matches[0], '<' . $matches[1] . ' class="' . $class_to_add . '"' . $matches[2] . '>', $html);
        }
        
        return $html;
    }
    
    /**
     * Add class to WooCommerce block templates during render
     */
    public function add_block_template_class($block_content, $block) {
        // Check if it's a WooCommerce product template block
        if (isset($block['blockName']) && $block['blockName'] === 'woocommerce/product-template') {
            $class_to_add = $this->custom_classes['products'];
            
            // Add class to elements with data-block-name="woocommerce/product-template"
            $block_content = preg_replace_callback(
                '/<(ul|div|section)([^>]*?)data-block-name=["\']woocommerce\/product-template["\']([^>]*?)>/i',
                function($matches) use ($class_to_add) {
                    $element = $matches[1];
                    $before_attrs = $matches[2];
                    $after_attrs = $matches[3];
                    
                    // Check if class attribute exists
                    if (preg_match('/class=(["\'])([^"\']*?)\1/', $before_attrs . $after_attrs, $class_matches)) {
                        // Add to existing class
                        $existing_classes = $class_matches[2];
                        $new_classes = trim($existing_classes . ' ' . $class_to_add);
                        $full_attrs = $before_attrs . 'data-block-name="woocommerce/product-template"' . $after_attrs;
                        $full_attrs = preg_replace('/class=(["\'])([^"\']*?)\1/', 'class=$1' . $new_classes . '$1', $full_attrs);
                        return '<' . $element . $full_attrs . '>';
                    } else {
                        // Add new class attribute
                        return '<' . $element . $before_attrs . 'class="' . $class_to_add . '" data-block-name="woocommerce/product-template"' . $after_attrs . '>';
                    }
                },
                $block_content
            );
        }
        
        return $block_content;
    }
    
    /**
     * Wrapper approach for themes that don't modify the loop start
     */
    public function before_shop_loop_wrapper() {
        if (!$this->wrapper_added && (is_shop() || is_product_category() || is_product_tag())) {
            echo '<div class="' . $this->custom_classes['products'] . '-wrapper">';
            $this->wrapper_added = true;
        }
    }
    
    public function after_shop_loop_wrapper() {
        if ($this->wrapper_added) {
            echo '</div>';
            $this->wrapper_added = false;
        }
    }
    
    /**
     * Add classes to shortcode container
     */
    public function add_shortcode_container_classes($classes) {
        if (!is_array($classes)) {
            $classes = array();
        }
        $classes[] = $this->custom_classes['products'];
        return $classes;
    }
    
    /**
     * Add custom class to individual product items (excluding body tag)
     */
    public function add_product_item_class($classes, $product = null) {
        // Only add class in WooCommerce contexts and not on body tag
        if ((is_woocommerce() || (is_object($product) && is_a($product, 'WC_Product'))) && !doing_filter('body_class')) {
            $classes[] = $this->custom_classes['product_item'];
        }
        return $classes;
    }
    
    /**
     * Fallback for themes that don't use woocommerce_post_class (excluding body tag)
     */
    public function add_product_item_class_fallback($classes, $css_class, $post_id) {
        // Only add class for product post types and not on body tag
        if (get_post_type($post_id) === 'product' && !doing_filter('body_class')) {
            $classes[] = $this->custom_classes['product_item'];
        }
        return $classes;
    }
    
    /**
     * Add custom class to pagination
     */
    public function add_pagination_class($args) {
        if (!isset($args['class'])) {
            $args['class'] = '';
        }
        $args['class'] .= ' ' . $this->custom_classes['pagination'];
        return $args;
    }
    
    /**
     * JavaScript injection for pagination (fallback)
     */
    public function inject_pagination_class() {
        if (is_woocommerce()) {
            echo '<script>
                jQuery(document).ready(function($) {
                    $(".woocommerce-pagination").addClass("' . $this->custom_classes['pagination'] . '");
                });
            </script>';
        }
    }
    
    /**
     * Capture and modify orderby dropdown
     */
    public function start_orderby_capture() {
        ob_start();
    }
    
    public function end_orderby_capture() {
        $content = ob_get_contents();
        ob_end_clean();
        
        // Add class to orderby form
        $content = preg_replace(
            '/<form([^>]*?)class=(["\'])([^"\']*?)\2([^>]*?)>/i',
            '<form$1class=$2$3 ' . $this->custom_classes['orderby'] . '$2$4>',
            $content
        );
        
        // If no class exists, add one
        if (strpos($content, 'class=') === false) {
            $content = str_replace('<form', '<form class="' . $this->custom_classes['orderby'] . '"', $content);
        }
        
        echo $content;
    }
    
    /**
     * Add custom class to result count
     */
    public function add_result_count_class($html) {
        $class_to_add = $this->custom_classes['result_count'];
        
        // More robust class injection
        if (preg_match('/<p([^>]*?)class=(["\'])([^"\']*?)\2([^>]*?)>/i', $html, $matches)) {
            $existing_classes = $matches[3];
            $new_classes = $existing_classes . ' ' . $class_to_add;
            $html = str_replace($matches[0], '<p' . $matches[1] . 'class=' . $matches[2] . $new_classes . $matches[2] . $matches[4] . '>', $html);
        } elseif (preg_match('/<p([^>]*?)>/i', $html, $matches)) {
            $html = str_replace($matches[0], '<p class="' . $class_to_add . '"' . $matches[1] . '>', $html);
        }
        
        return $html;
    }
    
    /**
     * JavaScript fallback for result count
     */
    public function inject_result_count_class() {
        if (is_woocommerce()) {
            echo '<script>
                jQuery(document).ready(function($) {
                    $(".woocommerce-result-count").addClass("' . $this->custom_classes['result_count'] . '");
                });
            </script>';
        }
    }

    
    /**
     * Get custom classes array (for external access)
     */
    public function get_custom_classes() {
        return $this->custom_classes;
    }
}

// Initialize the class
new dapfforwc_Custom_Class_Injector();
?>