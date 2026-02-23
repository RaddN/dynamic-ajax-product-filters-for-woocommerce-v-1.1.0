<?php
// filter-template.php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('dapfforwc_is_product_attribute')) {
    function dapfforwc_is_product_attribute()
    {
        global $wp_query;
        if (!is_tax()) return false;
        $taxonomy = get_queried_object()->taxonomy;
        return strpos($taxonomy, 'pa_') === 0;
    }
}


/**
 * Check if current page is a product brand archive page
 * Supports multiple brand taxonomy implementations:
 * - WooCommerce "product_brand" taxonomy
 * - Perfect Brands for WooCommerce
 * - JetEngine custom taxonomies
 * - Custom brand taxonomies
 *
 * @return bool True if on a brand archive page, false otherwise
 * @since 1.0.0
 */
if (!function_exists('dapfforwc_is_product_brand')) {
    function dapfforwc_is_product_brand()
    {
        global $wp_query;

        // Check if we're on a taxonomy archive page
        if (!is_tax()) {
            return false;
        }

        // Get the current queried object
        $queried_object = get_queried_object();

        if (!$queried_object || !isset($queried_object->taxonomy)) {
            return false;
        }

        $taxonomy = $queried_object->taxonomy;

        // List of known brand taxonomies
        $brand_taxonomies = [
            'product_brand',           // Standard WooCommerce brand taxonomy
            'pwb-brand',               // Perfect Brands for WooCommerce
            'jetengine-brand',         // JetEngine brand
            'product_suppliers',       // Alternative brand/supplier taxonomy
            'product_distributor',     // Distributor taxonomy
            'wc_brand',               // Another common brand taxonomy
        ];

        // Check if current taxonomy is in the brand taxonomy list
        if (in_array($taxonomy, $brand_taxonomies, true)) {
            return true;
        }

        // Check for custom taxonomies with "brand" in the name
        if (strpos($taxonomy, 'brand') !== false && 0 === strpos($taxonomy, 'product_')) {
            return true;
        }

        // Check if the taxonomy is associated with products
        $tax_object = get_taxonomy($taxonomy);
        if ($tax_object && in_array('product', $tax_object->object_type, true)) {
            // Additional check: if taxonomy name contains brand, it might be a brand taxonomy
            if (
                strpos(strtolower($taxonomy), 'brand') !== false ||
                strpos(strtolower($tax_object->label), 'brand') !== false
            ) {
                return true;
            }
        }

        return false;
    }
}


/**
 * Get brand info from current archive page
 * Returns brand details if on brand archive, null otherwise
 *
 * @return array|null Array with term_id, name, slug, taxonomy or null
 * @since 1.0.0
 */
if (!function_exists('dapfforwc_get_current_brand')) {
    function dapfforwc_get_current_brand()
    {
        if (!dapfforwc_is_product_brand()) {
            return null;
        }

        $brand = get_queried_object();

        if (!$brand) {
            return null;
        }

        return [
            'term_id' => $brand->term_id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'taxonomy' => $brand->taxonomy,
            'description' => $brand->description ?? '',
            'count' => $brand->count ?? 0,
        ];
    }
}


/**
 * Get all registered brand taxonomies for the product post type
 * Useful for filtering operations that need to know all brand-related taxonomies
 *
 * @return array Array of brand taxonomy names
 * @since 1.0.0
 */
if (!function_exists('dapfforwc_get_brand_taxonomies')) {
    function dapfforwc_get_brand_taxonomies()
    {
        $brand_taxonomies = [];

        // Get all taxonomies associated with products
        $product_taxonomies = get_object_taxonomies('product', 'objects');

        if (empty($product_taxonomies)) {
            return $brand_taxonomies;
        }

        foreach ($product_taxonomies as $tax) {
            $tax_name = strtolower($tax->name);
            $tax_label = strtolower($tax->label);

            // Check if taxonomy name or label contains "brand"
            if (strpos($tax_name, 'brand') !== false || strpos($tax_label, 'brand') !== false) {
                $brand_taxonomies[] = $tax->name;
            }
        }

        // Also check predefined brand taxonomies
        $known_brands = ['product_brand', 'pwb-brand', 'wc_brand', 'product_suppliers'];
        foreach ($known_brands as $brand_tax) {
            if (taxonomy_exists($brand_tax) && in_array($brand_tax, get_object_taxonomies('product'), true)) {
                if (!in_array($brand_tax, $brand_taxonomies, true)) {
                    $brand_taxonomies[] = $brand_tax;
                }
            }
        }

        return array_unique($brand_taxonomies);
    }
}


function dapfforwc_product_filter_shortcode($atts)
{
    global $dapfforwc_styleoptions, $post, $dapfforwc_options, $dapfforwc_advance_settings, $wp, $dapfforwc_seo_permalinks_options, $template_options, $dapfforwc_allowed_tags;


    // Define default attributes and merge with user-defined attributes
    $atts = shortcode_atts(array(
        'attribute' => '',
        'terms' => '',
        'category' => '',
        'tag' => '',
        'layout' => 'sidebar',
        'product_selector' => '',
        'pagination_selector' => '',
        'mobile_responsive' => 'style_4',
        'use_custom_template_design' => 'no',
        'per_page' => '',
    ), $atts);

    $mobile_breakpoint = dapfforwc_get_mobile_breakpoint();
    $desktop_breakpoint = $mobile_breakpoint + 1;

    $use_anchor = isset($dapfforwc_seo_permalinks_options["use_anchor"]) ? $dapfforwc_seo_permalinks_options["use_anchor"] : "";
    $use_filters_word = isset($dapfforwc_seo_permalinks_options["use_filters_word_in_permalinks"]) ? $dapfforwc_seo_permalinks_options["use_filters_word_in_permalinks"] : "";

    $request = $wp->request;
    $request_parts = explode('/', $request);
    $request_parts = is_archive() ? [end($request_parts)] : $request_parts;
    $dapfforwc_slug = is_archive() ? end($request_parts) : (isset($post) ? dapfforwc_get_full_slug($post->ID) : "");
    // Get Categories, Tags, attributes using the existing function
    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $all_cata = isset($all_data['categories']) ? $all_data['categories'] : [];
    $all_brands = isset($all_data['brands']) ? $all_data['brands'] : [];
    $all_authors = isset($all_data['authors']) ? $all_data['authors'] : [];
    $all_stock_status = isset($all_data['stock_status']) ? $all_data['stock_status'] : [];
    $all_sale_status = isset($all_data['sale_status']) ? $all_data['sale_status'] : [];
    $all_tags = isset($all_data['tags']) ? $all_data['tags'] : [];
    $all_attributes = isset($all_data['attributes']) ? $all_data['attributes'] : [];
    $custom_fields = isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [];
    if ($atts['category'] === '' && $atts['attribute'] === '' && $atts['terms'] === '' && $atts['tag'] === '') {
        $shortcode = isset($dapfforwc_advance_settings["product_shortcode"]) ? $dapfforwc_advance_settings["product_shortcode"] : 'products'; // Shortcode to search for
        $attributes_list = dapfforwc_get_shortcode_attributes_from_page($post->post_content ?? "", $shortcode);
        // If no shortcodes found, try page builders
        if (empty($attributes_list) && isset($post->ID)) {
            $attributes_list = dapfforwc_get_pagebuilder_product_attributes($post->ID);
        }
    }
    $is_all_cata = false;
    $make_default_selected = false;
    $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
    // Validate and sanitize host
    $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';

    // Validate and sanitize request URI
    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';

    // Build the sanitized URL
    if (!empty($host) && !empty($request_uri)) {
        $url_page = esc_url("http://{$host}{$request_uri}");
    } else {
        $url_page = home_url(); // Fallback to homepage if values are missing
    }

    // Parse the URL
    $parsed_url = wp_parse_url($url_page);
    // Parse the query string into an associative array
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_params);
    }

    // Get the value of 'filters'
    $filters = isset($query_params['filters']) ? $query_params['filters'] : null;


    $filteroptionsfromurl = [];

    // Support shorthand permalinks (?filters=white,laptop) by mapping to categories
    if ($filters !== '1') {
        $prefix_options = $dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options'];
        $reverse_prefix = [];

        if (isset($prefix_options) && is_array($prefix_options)) {
            // Flatten and reverse the prefix
            foreach ($prefix_options as $key => $val) {
                if ($key === 'attribute') {
                    foreach ($val as $attr_key => $attr_val) {
                        $reverse_prefix[$attr_val] = ['type' => 'attribute', 'key' => $attr_key];
                    }
                } else if ($key === 'custom') {
                    foreach ($val as $custom_key => $custom_val) {
                        $reverse_prefix[$custom_val] = ['type' => 'custom_meta', 'key' => $custom_key];
                    }
                } else {
                    $reverse_prefix[$val] = ['type' => $key];
                }
            }
        }
        $filter_value = isset($filters) ? array_filter(array_map('sanitize_text_field', explode(',', $filters))) : [];
        $all_cata_slug = array_column($all_cata, 'slug');
        $cata_in_filter = array_intersect($filter_value, $all_cata_slug);

        $all_tags_slug = array_column($all_tags, 'slug');
        $tag_in_filter = array_intersect($filter_value, $all_tags_slug);

        $all_brands_slug = array_column($all_brands, 'slug');
        $brands_in_filter = array_intersect($filter_value, $all_brands_slug);

        $all_authors_slug = array_column($all_authors, 'slug');
        $author_in_filter = array_intersect($filter_value, $all_authors_slug);

        $all_stock_status_slug = array_column($all_stock_status, 'slug');
        $all_stock_status_slug_in_filter = array_intersect($filter_value, $all_stock_status_slug);

        $all_sale_status_slug = array_column($all_sale_status, 'slug');
        $all_sale_status_in_filter = array_intersect($filter_value, $all_sale_status_slug);

        $all_date_filter_slug = ["today", "this_week", "this_month", "this_year"];
        $all_date_in_filter = array_intersect($filter_value, $all_date_filter_slug);


        // Filter numeric values between 1 and 5 and convert them to integers
        $rating_num = array_map('intval', array_values(array_filter($filter_value, function ($value) {
            return is_numeric($value) && $value >= 1 && $value <= 5;
        })));

        if (!empty($cata_in_filter)) {
            $filteroptionsfromurl["product-category[]"] = isset($filteroptionsfromurl["product-category[]"])
                ? array_values(array_unique(array_merge($filteroptionsfromurl["product-category[]"], $cata_in_filter)))
                : $cata_in_filter;
        }
        if (!empty($tag_in_filter)) {
            $filteroptionsfromurl["tag[]"] = isset($filteroptionsfromurl["tag[]"])
                ? array_values(array_unique(array_merge($filteroptionsfromurl["tag[]"], $tag_in_filter)))
                : $tag_in_filter;
        }
        if (!empty($brands_in_filter)) {
            $filteroptionsfromurl["brand[]"] = isset($filteroptionsfromurl["brand[]"])
                ? array_values(array_unique(array_merge($filteroptionsfromurl["brand[]"], $brands_in_filter)))
                : $brands_in_filter;
        }
        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['mn_price']) && isset($_GET['mx_price'])) {
            $filteroptionsfromurl["min_price"] = sanitize_text_field(wp_unslash($_GET['mn_price']));
            $filteroptionsfromurl["max_price"] = sanitize_text_field(wp_unslash($_GET['mx_price']));
            $filteroptionsfromurl["price[]"] = sanitize_text_field(wp_unslash($_GET['mn_price'])) . '-' . sanitize_text_field(wp_unslash($_GET['mx_price']));
        }

        if (!empty($rating_num)) {
            $filteroptionsfromurl["rating[]"] = $rating_num;
        }

        if (!empty($_GET[$prefix_options["plugincy_search"] ?? 'title'])) {
            $filteroptionsfromurl["plugincy_search[]"] = sanitize_text_field(wp_unslash($_GET[$prefix_options["plugincy_search"] ?? 'title']));
        }

        if (!empty($author_in_filter)) {
            $filteroptionsfromurl["author[]"] = isset($filteroptionsfromurl["author[]"])
                ? array_values(array_unique(array_merge($filteroptionsfromurl["author[]"], $author_in_filter)))
                : $author_in_filter;
        }
        if (!empty($all_stock_status_slug_in_filter)) {
            $filteroptionsfromurl["stock_status[]"] = $all_stock_status_slug_in_filter;
        }

        $dimensions = ['length', 'width', 'height', 'weight'];
        foreach ($dimensions as $dim) {

            $dim = isset($prefix_options[$dim]) ? $prefix_options[$dim] : $dim;

            if (!empty($_GET[$dim])) {
                // sanitize incoming value
                $raw = sanitize_text_field(wp_unslash($_GET[$dim]));
                $min = null;
                $max = null;

                if (strpos($raw, '-') !== false) {
                    list($min_raw, $max_raw) = explode('-', $raw, 2);
                } elseif (strpos($raw, ',') !== false) {
                    list($min_raw, $max_raw) = explode(',', $raw, 2);
                } else {
                    $min_raw = $raw;
                    $max_raw = null;
                }

                $min_raw = trim($min_raw);
                $max_raw = $max_raw !== null ? trim($max_raw) : null;

                if ($min_raw !== '' && is_numeric($min_raw)) {
                    $min = floatval($min_raw);
                }

                if ($max_raw !== null && $max_raw !== '' && is_numeric($max_raw)) {
                    $max = floatval($max_raw);
                }

                if ($min !== null) {
                    $filteroptionsfromurl['min_' . $reverse_prefix[$dim]['type']] = $min;
                }
                if ($max !== null) {
                    $filteroptionsfromurl['max_' . $reverse_prefix[$dim]['type']] = $max;
                }
            }
        }

        if (!empty($all_sale_status_in_filter)) {
            $filteroptionsfromurl["sale_status[]"] = $all_sale_status_in_filter;
        }

        if (!empty($_GET[$prefix_options["sku"] ?? 'sku'])) {
            $filteroptionsfromurl["sku"] = sanitize_text_field(wp_unslash($_GET[$prefix_options["sku"] ?? 'sku']));
        }
        if (!empty($_GET[$prefix_options["discount"] ?? 'discount'])) {
            $filteroptionsfromurl["discount"] = sanitize_text_field(wp_unslash($_GET[$prefix_options["discount"] ?? 'discount']));
        }
        if (!empty($all_date_in_filter)) {
            $filteroptionsfromurl["date_filter"] = $all_date_in_filter[0];
        }
    }

    if (isset($_GET['gm-product-filter-nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        if (isset($_GET['product-category'])) {
            $filteroptionsfromurl["product-category[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['product-category']))));
        }
        // check if 'tags' is set and sanitize it
        if (isset($_GET['tags'])) {
            $filteroptionsfromurl["tag[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['tags']))));
        }
        // check if 'brands' is set and sanitize it
        if (isset($_GET['rplurand'])) {
            $filteroptionsfromurl["brand[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rplurand']))));
        }
        // check if 'authors' is set and sanitize it
        if (isset($_GET['rpluthor'])) {
            $filteroptionsfromurl["author[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rpluthor']))));
        }
        // check if 'stock status' is set and sanitize it
        if (isset($_GET['rplutock_status'])) {
            $filteroptionsfromurl["stock_status[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rplutock_status']))));
        }
        // check if 'sale status' is set and sanitize it
        if (isset($_GET['rpn_sale'])) {
            $filteroptionsfromurl["sale_status[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rpn_sale']))));
        }
        // check if 'rplugpa_color', 'rplugpa_size', etc. are set and sanitize them
        // Dynamically get all attribute taxonomies from $all_attributes
        $attribute_taxonomies = [];
        if (!empty($all_attributes) && is_array($all_attributes)) {
            foreach (array_keys($all_attributes) as $attr_key) {
                $attribute_taxonomies[] = 'rplugpa_' . $attr_key;
            }
        }
        foreach ($attribute_taxonomies as $taxonomy) {
            if (isset($_GET[$taxonomy])) {
                // Convert 'rplugpa_brand' to 'attribute[brand][]' style key
                if (strpos($taxonomy, 'rplugpa_') === 0) {
                    $attr_name = substr($taxonomy, 8); // Remove 'rplugpa_' prefix
                    $filteroptionsfromurl["attribute"][$attr_name] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET[$taxonomy]))));
                }
            }
        }
        // Dynamically get all custom taxonomies from $custom_fields
        $custom_taxonomies = [];
        if (!empty($custom_fields) && is_array($custom_fields)) {
            foreach (array_keys($custom_fields) as $attr_key) {
                $custom_taxonomies[] = 'rplugcusf_' . $attr_key;
            }
        }
        foreach ($custom_taxonomies as $taxonomy) {
            if (isset($_GET[$taxonomy])) {
                // Convert 'rplugcusf_brand' to 'custom_meta[brand][]' style key
                if (strpos($taxonomy, 'rplugcusf_') === 0) {
                    $attr_name = substr($taxonomy, 10); // Remove 'rplugcusf_' prefix
                    $filteroptionsfromurl["custom_meta"][$attr_name] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET[$taxonomy]))));
                }
            }
        }


        // Support prefixed attribute/custom meta keys when permalinks prefixes are enabled
        if (
            isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) &&
            $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on" &&
            isset($dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options']) &&
            is_array($dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options'])
        ) {
            $prefix_options = $dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options'];

            if (isset($prefix_options['attribute']) && is_array($prefix_options['attribute'])) {
                foreach ($prefix_options['attribute'] as $attr_key => $attr_prefix) {
                    $attr_prefix = trim((string)$attr_prefix);
                    if ($attr_prefix === '' || !isset($_GET[$attr_prefix])) {
                        continue;
                    }
                    $existing = $filteroptionsfromurl["attribute"][$attr_key] ?? [];
                    $incoming = is_array($_GET[$attr_prefix]) ? array_map('sanitize_text_field', wp_unslash($_GET[$attr_prefix])) : array_map('sanitize_text_field', explode(',', sanitize_text_field(wp_unslash($_GET[$attr_prefix]))));
                    $filteroptionsfromurl["attribute"][$attr_key] = array_values(array_unique(array_merge($existing, $incoming)));
                }
            }

            if (isset($prefix_options['custom']) && is_array($prefix_options['custom'])) {
                foreach ($prefix_options['custom'] as $custom_key => $custom_prefix) {
                    $custom_prefix = trim((string)$custom_prefix);
                    if ($custom_prefix === '' || !isset($_GET[$custom_prefix])) {
                        continue;
                    }
                    $existing = $filteroptionsfromurl["custom_meta"][$custom_key] ?? [];
                    $incoming = is_array($_GET[$custom_prefix]) ? array_map('sanitize_text_field', wp_unslash($_GET[$custom_prefix])) : array_map('sanitize_text_field', explode(',', sanitize_text_field(wp_unslash($_GET[$custom_prefix]))));
                    $filteroptionsfromurl["custom_meta"][$custom_key] = array_values(array_unique(array_merge($existing, $incoming)));
                }
            }
        }

        // check if rating is set and sanitize it
        if (isset($_GET['rating'])) {
            $filteroptionsfromurl["rating[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rating']))));
        }

        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['mn_price']) && isset($_GET['mx_price'])) {
            $filteroptionsfromurl["min_price"] = sanitize_text_field(wp_unslash($_GET['mn_price']));
            $filteroptionsfromurl["max_price"] = sanitize_text_field(wp_unslash($_GET['mx_price']));
            $filteroptionsfromurl["price[]"] = sanitize_text_field(wp_unslash($_GET['mn_price'])) . '-' . sanitize_text_field(wp_unslash($_GET['mx_price']));
        }
        if (isset($_GET['default_min']) && isset($_GET['default_max'])) {
            $filteroptionsfromurl["default_min"] = sanitize_text_field(wp_unslash($_GET['default_min']));
            $filteroptionsfromurl["default_max"] = sanitize_text_field(wp_unslash($_GET['default_max']));
        }

        // check if plugincy_search is set and sanitize it
        if (isset($_GET['plugincy_search'])) {
            $filteroptionsfromurl["plugincy_search"] = sanitize_text_field(wp_unslash($_GET['plugincy_search']));
        }

        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['min_length'])) {
            $filteroptionsfromurl["min_length"] = sanitize_text_field(wp_unslash($_GET['min_length']));
        }
        if (isset($_GET['max_length'])) {
            $filteroptionsfromurl["max_length"] = sanitize_text_field(wp_unslash($_GET['max_length']));
        }
        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['min_width'])) {
            $filteroptionsfromurl["min_width"] = sanitize_text_field(wp_unslash($_GET['min_width']));
        }
        if (isset($_GET['max_width'])) {
            $filteroptionsfromurl["max_width"] = sanitize_text_field(wp_unslash($_GET['max_width']));
        }
        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['min_height'])) {
            $filteroptionsfromurl["min_height"] = sanitize_text_field(wp_unslash($_GET['min_height']));
        }
        if (isset($_GET['max_height'])) {
            $filteroptionsfromurl["max_height"] = sanitize_text_field(wp_unslash($_GET['max_height']));
        }
        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['min_weight'])) {
            $filteroptionsfromurl["min_weight"] = sanitize_text_field(wp_unslash($_GET['min_weight']));
        }
        if (isset($_GET['max_weight'])) {
            $filteroptionsfromurl["max_weight"] = sanitize_text_field(wp_unslash($_GET['max_weight']));
        }
        if (isset($_GET['max_weight'])) {
            $filteroptionsfromurl["max_weight"] = sanitize_text_field(wp_unslash($_GET['max_weight']));
        }
        if (isset($_GET['sku'])) {
            $filteroptionsfromurl["sku"] = sanitize_text_field(wp_unslash($_GET['sku']));
        }
        if (isset($_GET['discount'])) {
            $filteroptionsfromurl["discount"] = sanitize_text_field(wp_unslash($_GET['discount']));
        }
        if (isset($_GET['date_filter'])) {
            $filteroptionsfromurl["date_filter"] = sanitize_text_field(wp_unslash($_GET['date_filter']));
        }
    }

    if ($atts['category'] === '' && $atts['attribute'] === '' && $atts['terms'] === '' && $atts['tag'] === '') {
        foreach ($attributes_list as $attributes) {
            // Ensure that the "product-category", 'attribute', and 'terms' keys exist
            $arrayCata = isset($attributes["category"]) ? array_map('trim', explode(",", $attributes["category"])) : [];
            $tagValue = isset($attributes['tags']) ? array_map('trim', explode(",", $attributes['tags'])) : [];
            $termsValue = isset($attributes['terms']) ? array_map('trim', explode(",", $attributes['terms'])) : [];
            $attrvalue = [];
            if (isset($attributes['attribute']) && isset($attributes['terms'])) {
                $attribute_keys = array_map('trim', explode(",", $attributes['attribute']));
                $terms_values = array_map('trim', explode("},{", trim($attributes['terms'], "{}")));

                foreach ($attribute_keys as $index => $key) {
                    if (isset($terms_values[$index])) {
                        $attrvalue[$key] = array_map('trim', explode(",", $terms_values[$index]));
                    }
                }
            } else {
                $attrvalue = isset($attributes['attribute']) ? array_map('trim', explode(",", $attributes['attribute'])) : [];
            }
            $filters = !empty($arrayCata) ? $arrayCata : (!empty($tagValue) ? $tagValue : $termsValue);

            // Use the combined full slug as the key in default_filters
            $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
            if (!empty($arrayCata)) {
                $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = $arrayCata;
            }

            if (!empty($tagValue)) {
                $dapfforwc_options['default_filters'][$dapfforwc_slug]["tag[]"] = $tagValue;
            }

            if (!empty($attrvalue)) {
                $dapfforwc_options['default_filters'][$dapfforwc_slug]["attribute"] = $attrvalue;
            }
            if (empty($filters) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === [""] && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
                $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = array_column($all_cata, 'slug');
                $is_all_cata = true;
                $make_default_selected = true;
            } else {
                if (empty($filteroptionsfromurl)) {
                    $make_default_selected = true;
                }
            }

            $dapfforwc_options['product_show_settings'][$dapfforwc_slug] = [
                'per_page'        => isset($attributes['limit']) ? $attributes['limit'] : $attributes['per_page'] ?? null,
                'orderby'         => isset($attributes['orderby']) ? $attributes['orderby'] : '',
                'order'           => isset($attributes['order']) ? $attributes['order'] : '',
                'terms_operator' => $attributes['terms_operator'] ?? 'IN',
                'tag_operator' => $attributes['tag_operator'] ?? 'IN',
                'cat_operator' => $attributes['cat_operator'] ?? 'IN',
                'brand_operator' => $attributes['brand_operator'] ?? 'IN',
            ];
        }
    }



    if ($atts['category'] !== '' || ($atts['attribute'] !== '' && $atts['terms'] !== '') || $atts['tag'] !== '') {
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = !empty($atts['category']) ? array_map('trim', explode(',', $atts['category'])) : [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["tag[]"] = !empty($atts['tag']) ? array_map('trim', explode(',', $atts['tag'])) : [];
        if (!empty($atts['attribute']) && !empty($atts['terms'])) {
            $terms = array_map('trim', explode(',', $atts['terms']));
            $dapfforwc_options['default_filters'][$dapfforwc_slug]["attribute"][$atts['attribute']] = $terms;
        }

        if (empty($filteroptionsfromurl)) {
            $make_default_selected = true;
        }
    }



    if (is_shop() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && (explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === [""] || explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === ["1"]) && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
        $all_cata_slugs = array_column($all_cata, 'slug');
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = $all_cata_slugs;
        $is_all_cata = true;
        $make_default_selected = true;
        $dapfforwc_options["product_show_settings"][$dapfforwc_slug]["cat_operator"] = "OR";
    }

    if (is_product_category()) {
        $dapfforwc_options["product_show_settings"][$dapfforwc_slug]["cat_operator"] = "AND";
        $current_category = get_queried_object();
        $category_slug = $current_category->slug;
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = [$category_slug];
        if (empty($filteroptionsfromurl)) {
            $make_default_selected = true;
        }
    }

    if (is_product_tag()) {
        $current_tag = get_queried_object();
        $tag_slug = $current_tag->slug;
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["tag[]"] = [$tag_slug];
        if (empty($filteroptionsfromurl)) {
            $make_default_selected = true;
        }
    }

    // if attribute archive page
    if (dapfforwc_is_product_attribute()) {
        $current_attribute = get_queried_object();
        $attribute_slug = $current_attribute->slug;
        $taxonomy = $current_attribute->taxonomy;
        $attribute_name = str_replace('pa_', '', $taxonomy);
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["attribute"][$attribute_name] = [$attribute_slug];
        if (empty($filteroptionsfromurl)) {
            $make_default_selected = true;
        }
    }

    // if brand archive page

    if (dapfforwc_is_product_brand()) {
        $current_brand = dapfforwc_get_current_brand();

        $dapfforwc_options["product_show_settings"][$dapfforwc_slug]["brand_operator"] = "AND";


        if ($current_brand) {
            $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];

            // Use the correct brand taxonomy key based on detected taxonomy
            $brand_filter_key = "brand[]"; // This is used in the plugin for brands

            $dapfforwc_options['default_filters'][$dapfforwc_slug][$brand_filter_key] = [$current_brand['slug']];

            if (empty($filteroptionsfromurl)) {
                $make_default_selected = true;
            }
        }
    }

    if (!is_shop() && !is_product_category() && !is_product_tag() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && (explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === [""] || explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === ["1"])  && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = array_column($all_cata, 'slug');
        $is_all_cata = true;
        $make_default_selected = true;
    }
    if (isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page']) && $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] === 12) {
        $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] = $atts['per_page'];
    } elseif (!isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'])) {
        $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] = $atts['per_page'];
    }

    // Initialize result
    $parsed_filters = [];

    // filters === 1

    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $prefix = isset($dapfforwc_seo_permalinks_options["dapfforwc_permalinks_prefix_options"]) ? $dapfforwc_seo_permalinks_options["dapfforwc_permalinks_prefix_options"] : "";

        // Get all query variables
        $query_vars = $_GET;

        // Reverse the $prefix to find key from value
        $reverse_prefix = [];

        if (isset($prefix) && is_array($prefix)) {
            // Flatten and reverse the prefix
            foreach ($prefix as $key => $val) {
                if ($key === 'attribute') {
                    foreach ($val as $attr_key => $attr_val) {
                        $reverse_prefix[$attr_val] = ['type' => 'attribute', 'key' => $attr_key];
                    }
                } else if ($key === 'custom') {
                    foreach ($val as $custom_key => $custom_val) {
                        $reverse_prefix[$custom_val] = ['type' => 'custom_meta', 'key' => $custom_key]; //maybe a error here
                    }
                } else {
                    $reverse_prefix[$val] = ['type' => $key];
                }
            }

            // Process query vars
            foreach ($query_vars as $key => $value) {
                if (!isset($reverse_prefix[$key])) {
                    continue;
                }

                $info = $reverse_prefix[$key];

                // Handle comma-separated values
                $values = explode(',', $value);

                if ($info['type'] === 'attribute') {
                    $parsed_filters['attribute'][$info['key']] = $values;
                } else if ($info['type'] === 'custom_meta') {
                    $parsed_filters['custom_meta'][$info['key']] = $values;
                } else {
                    $parsed_filters[$info['type'] . "[]"] = $values;
                }
            }
        }
        // if $query_vars["length"] .... which value can be length=0-10000&width=0-10000&height=0-10000&weight=0-10000 collect them & store in min_length, max_length, min_width, max_width,min_height, max_height, min_weight, max_weight
        // Collect dimension ranges (length, width, height, weight) from query vars and store min/max values
        $dimensions = ['length', 'width', 'height', 'weight'];
        foreach ($dimensions as $dim) {

            $dim = isset($prefix[$dim]) ? $prefix[$dim] : $dim;

            if (!empty($query_vars[$dim])) {
                // sanitize incoming value
                $raw = sanitize_text_field(wp_unslash($query_vars[$dim]));
                $min = null;
                $max = null;

                if (strpos($raw, '-') !== false) {
                    list($min_raw, $max_raw) = explode('-', $raw, 2);
                } elseif (strpos($raw, ',') !== false) {
                    list($min_raw, $max_raw) = explode(',', $raw, 2);
                } else {
                    $min_raw = $raw;
                    $max_raw = null;
                }

                $min_raw = trim($min_raw);
                $max_raw = $max_raw !== null ? trim($max_raw) : null;

                if ($min_raw !== '' && is_numeric($min_raw)) {
                    $min = floatval($min_raw);
                }

                if ($max_raw !== null && $max_raw !== '' && is_numeric($max_raw)) {
                    $max = floatval($max_raw);
                }

                if ($min !== null) {
                    $parsed_filters['min_' . $reverse_prefix[$dim]['type']] = $min;
                }
                if ($max !== null) {
                    $parsed_filters['max_' . $reverse_prefix[$dim]['type']] = $max;
                }
            }
        }
    }

    update_option('dapfforwc_options', $dapfforwc_options);

    $default_filter = (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"]))) ?
        array_merge(
            $dapfforwc_options["default_filters"][$dapfforwc_slug] ?? [],
            $parsed_filters,
            isset($_GET['filters']) && $_GET['filters'] !== "1" ? explode(',', sanitize_text_field(wp_unslash($_GET['filters'] ?? ''))) : [],
            $request_parts,
            $filteroptionsfromurl
        ) :  array_merge_recursive(
            $filteroptionsfromurl,
            $parsed_filters,
            $dapfforwc_options["default_filters"][$dapfforwc_slug] ?? []
        );

    $ratings = isset($default_filter["rating[]"])
        ? array_map('intval', (array)$default_filter["rating[]"])
        : 0;


    if (!empty($atts['use_custom_template_design']) && $atts['use_custom_template_design'] === "yes") {
        // Ensure it's an array
        $dapfforwc_options['use_custom_template_in_page'] = isset($dapfforwc_options['use_custom_template_in_page']) ? $dapfforwc_options['use_custom_template_in_page'] : [];

        // Merge and ensure uniqueness
        if (is_string($dapfforwc_slug)) {
            $dapfforwc_options['use_custom_template_in_page'] = array_unique(array_merge($dapfforwc_options['use_custom_template_in_page'], [$dapfforwc_slug]));
        }

        // Update options
        update_option('dapfforwc_options', $dapfforwc_options);
    } else {
        // Remove the slug from the settings if it exists
        if (isset($dapfforwc_options['use_custom_template_in_page']) && is_array($dapfforwc_options['use_custom_template_in_page'])) {
            $dapfforwc_options['use_custom_template_in_page'] = array_values(array_diff($dapfforwc_options['use_custom_template_in_page'], [$dapfforwc_slug]));

            // Update options after removal
            update_option('dapfforwc_options', $dapfforwc_options);
        }
    }
    $product_details = array_values(dapfforwc_get_woocommerce_product_details()["products"] ?? []);
    $products_id_by_rating = [];
    if (!empty($ratings)) {
        // Collect products that have at least the minimum rating specified
        $min_rating = min($ratings);
        $products_id_by_rating = array_column(
            array_filter($product_details, function ($product) use ($min_rating) {
                global $dapfforwc_styleoptions;
                if ($dapfforwc_styleoptions['rating']['sub_option'] === "rating-text") {
                    return floatval($product['rating']) >= floatval($min_rating);
                } else {
                    return floatval($product['rating']) == floatval($min_rating);
                }
            }),
            'ID'
        );
    }


    // Filter products by search term
    $products_id_by_search = [];
    $search_filters = [];

    $search_behavior_settings = array('title');

    if (!empty($default_filter["plugincy_search"])) {
        $search_filters[] = $default_filter["plugincy_search"];
    } elseif (!empty($default_filter["plugincy_search[]"])) {
        $search_filters = (array)$default_filter["plugincy_search[]"];
    }

    if (!empty($search_filters)) {
        $search_string = trim(implode(' ', array_filter(array_map('sanitize_text_field', $search_filters))));

        if ($search_string !== '') {

            // Add filter to search according to configured behavior
            add_filter('posts_search', 'dapfforwc_search_by_title_only', 500, 2);

            $search_query = new WP_Query([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                's'              => $search_string,
                'fields'         => 'ids',
                'posts_per_page' => -1,
                'no_found_rows'  => true,
                'dapfforwc_search_post_title' => true,
                'dapfforwc_search_behavior' => $search_behavior_settings,
            ]);

            // Remove filter after query
            remove_filter('posts_search', 'dapfforwc_search_by_title_only', 500);

            if (!empty($search_query->posts)) {
                $products_id_by_search = array_map('intval', $search_query->posts);
            }

            wp_reset_postdata();
        }
    }

    // Filter products by price range
    $products_id_by_price = [];
    if (!empty($default_filter["price[]"])) {
        $price_ranges = (array)$default_filter["price[]"];
        foreach ($price_ranges as $range) {
            if (strpos($range, '-') !== false) {
                list($min, $max) = explode('-', $range, 2);
                $min = floatval($min);
                $max = floatval($max);

                foreach ($product_details as $product) {
                    $product_price = floatval($product['price'] ?: $product['regular_price'] ?: 0);
                    if ($product_price >= $min && $product_price <= $max) {
                        if (!in_array($product['ID'], $products_id_by_price)) {
                            $products_id_by_price[] = $product['ID'];
                        }
                    }
                }
            }
        }
    }

    $products_id_by_dimensions = [];
    $dimension_filters = [];
    foreach (['length', 'width', 'height', 'weight'] as $dimension) {
        $min_key = 'min_' . $dimension;
        $max_key = 'max_' . $dimension;

        $min = isset($default_filter[$min_key]) && $default_filter[$min_key] !== '' ? floatval($default_filter[$min_key]) : null;
        $max = isset($default_filter[$max_key]) && $default_filter[$max_key] !== '' ? floatval($default_filter[$max_key]) : null;

        if ($min !== null || $max !== null) {
            $dimension_filters[$dimension] = ['min' => $min, 'max' => $max];
        }
    }

    if (!empty($dimension_filters)) {
        foreach ($product_details as $product) {
            $matches = true;

            foreach ($dimension_filters as $dimension => $range) {
                $value = isset($product[$dimension]) && $product[$dimension] !== '' ? floatval($product[$dimension]) : null;

                if ($value === null || ($range['min'] !== null && $value < $range['min']) || ($range['max'] !== null && $value > $range['max'])) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $products_id_by_dimensions[] = $product['ID'];
            }
        }
    }

    $products_id_by_sku = [];
    $raw_sku_filter = $default_filter['sku'] ?? ($default_filter['sku[]'] ?? null);

    if (!empty($raw_sku_filter)) {
        $sku_filters = is_array($raw_sku_filter) ? $raw_sku_filter : explode(',', $raw_sku_filter);
        $sku_filters = array_filter(array_map(static function ($sku) {
            return strtolower(trim($sku));
        }, $sku_filters));

        if (!empty($sku_filters)) {
            $sku_map = array_fill_keys($sku_filters, true);
            foreach ($product_details as $product) {
                $product_sku = strtolower($product['product_sku']);
                if ($product_sku !== '' && isset($sku_map[$product_sku])) {
                    $products_id_by_sku[$product['ID']] = true;
                }
            }

            $products_id_by_sku = array_keys($products_id_by_sku);
        }
    }

    $products_id_by_discount = [];
    $raw_discount_filter = $default_filter['discount'] ?? ($default_filter['discount[]'][0] ?? null);

    if ($raw_discount_filter !== null && $raw_discount_filter !== '') {
        $min_discount = floatval($raw_discount_filter);

        if ($min_discount > 0) {
            foreach ($product_details as $product) {
                if (!empty($product['discount_percentage']) && floatval($product['discount_percentage']) >= $min_discount) {
                    $products_id_by_discount[] = $product['ID'];
                }
            }
        }
    }

    $products_id_by_date_filter = [];
    $date_filter_value = $default_filter['date_filter'] ?? ($default_filter['date_filter[]'][0] ?? null);

    if (!empty($date_filter_value)) {
        $start = null;
        $end = null;

        switch ($date_filter_value) {
            case 'today':
                $start = strtotime('today');
                $end = strtotime('tomorrow') - 1;
                break;
            case 'this_week':
                $start = strtotime('monday this week');
                $end = strtotime('sunday this week 23:59:59');
                break;
            case 'this_month':
                $start = strtotime('first day of this month');
                $end = strtotime('first day of next month') - 1;
                break;
            case 'this_year':
                $current_year = (int) gmdate('Y');
                $start = strtotime('first day of january ' . $current_year);
                $end = strtotime('first day of january ' . ($current_year + 1)) - 1;
                break;
            case 'custom':
                $date_from = $default_filter['date_from'] ?? null;
                $date_to = $default_filter['date_to'] ?? null;

                if (!empty($date_from)) {
                    $start = strtotime($date_from);
                }

                if (!empty($date_to)) {
                    $end = strtotime($date_to . ' 23:59:59');
                }
                break;
        }

        if ($start !== null || $end !== null) {
            foreach ($product_details as $product) {
                $published = strtotime($product['publish_date'] ?? '');
                if ($published === false) {
                    continue;
                }

                if (($start === null || $published >= $start) && ($end === null || $published <= $end)) {
                    $products_id_by_date_filter[] = $product['ID'];
                }
            }
        }
    }


    // Create Lookup Arrays
    $cata_lookup = array_combine(
        array_column($all_cata, 'slug'),
        array_column($all_cata, 'products')
    );
    $brands_lookup = array_combine(
        array_column($all_brands, 'slug'),
        array_column($all_brands, 'products')
    );
    $authors_lookup = array_combine(
        array_column($all_authors, 'slug'),
        array_column($all_authors, 'products')
    );
    $stock_status_lookup = array_combine(
        array_column($all_stock_status, 'slug'),
        array_column($all_stock_status, 'products')
    );
    $sale_status_lookup = array_combine(
        array_column($all_sale_status, 'slug'),
        array_column($all_sale_status, 'products')
    );
    $tag_lookup = array_combine(
        array_column($all_tags, 'slug'),
        array_column($all_tags, 'products')
    );
    $all_data_objects = [];
    $all_data_objects["plugincy_search"] = isset($default_filter["plugincy_search"]) ? $default_filter["plugincy_search"] : "";
    $all_data_objects["min_length"] = isset($default_filter["min_length"]) ? $default_filter["min_length"] : "";
    $all_data_objects["max_length"] = isset($default_filter["max_length"]) ? $default_filter["max_length"] : "";
    $all_data_objects["min_width"] = isset($default_filter["min_width"]) ? $default_filter["min_width"] : "";
    $all_data_objects["max_width"] = isset($default_filter["max_width"]) ? $default_filter["max_width"] : "";
    $all_data_objects["min_height"] = isset($default_filter["min_height"]) ? $default_filter["min_height"] : "";
    $all_data_objects["max_height"] = isset($default_filter["max_height"]) ? $default_filter["max_height"] : "";
    $all_data_objects["min_weight"] = isset($default_filter["min_weight"]) ? $default_filter["min_weight"] : "";
    $all_data_objects["max_weight"] = isset($default_filter["max_weight"]) ? $default_filter["max_weight"] : "";
    $all_data_objects["length"] = $default_filter["length[]"][0] ?? "";
    $all_data_objects["width"] = $default_filter["width[]"][0] ?? "";
    $all_data_objects["height"] = $default_filter["height[]"][0] ?? "";
    $all_data_objects["weight"] = $default_filter["weight[]"][0] ?? "";
    $all_data_objects["sku"] = $default_filter["sku"] ?? $default_filter["sku[]"][0] ?? "";
    $all_data_objects["discount"] = $default_filter["discount"] ?? ($default_filter["discount[]"][0] ?? "");
    $all_data_objects["date_filter"] = $default_filter["date_filter"] ?? ($default_filter["date_filter[]"][0] ?? "");
    $all_data_objects["rating[]"] = $default_filter["rating[]"] ?? [];

    // Match Filters
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_cata_with_ids = array_intersect_key($cata_lookup, array_flip(array_filter($default_filter["product-category[]"] ?? [])));
    } else {
        // Merge both possible category sources: 'product-category[]' and numeric keys (0,1,2,...)
        $category_slugs = array_filter($default_filter["product-category[]"] ?? []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $category_slugs, true)) {
                $category_slugs[] = $val;
            }
        }
        $matched_cata_with_ids = array_intersect_key($cata_lookup, array_flip($category_slugs));
    }
    $all_data_objects["product-category[]"] = array_keys($matched_cata_with_ids);
    if (strtoupper($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["cat_operator"] ?? "IN") === 'AND') {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_intersect(...array_values($matched_cata_with_ids)));
    } else {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_cata_with_ids))));
    }

    // filter by brands
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_brand_with_ids = array_intersect_key($brands_lookup, array_flip(array_filter(isset($default_filter["brand[]"]) ? $default_filter["brand[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $brand_slugs = array_filter(isset($default_filter["brand[]"]) ? $default_filter["brand[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $brand_slugs, true)) {
                $brand_slugs[] = $val;
            }
        }
        $matched_brand_with_ids = array_intersect_key($brands_lookup, array_flip($brand_slugs));
    }
    $all_data_objects["rplurand[]"] = array_keys($matched_brand_with_ids);
    if (strtoupper($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["brand_operator"] ?? "IN") === 'AND') {
        $products_id_by_brand = empty($matched_brand_with_ids) ? [] : array_values(array_intersect(...array_values($matched_brand_with_ids)));
    } else {
        $products_id_by_brand = empty($matched_brand_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_brand_with_ids))));
    }
    // filter by authors
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_author_with_ids = array_intersect_key($authors_lookup, array_flip(array_filter(isset($default_filter["author[]"]) ? $default_filter["author[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $author_slugs = array_filter(isset($default_filter["author[]"]) ? $default_filter["author[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $author_slugs, true)) {
                $author_slugs[] = $val;
            }
        }
        $matched_author_with_ids = array_intersect_key($authors_lookup, array_flip($author_slugs));
    }
    $all_data_objects["rpluthor[]"] = array_keys($matched_author_with_ids);
    $products_id_by_author = empty($matched_author_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_author_with_ids))));
    // filter by stock status
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_stock_status_with_ids = array_intersect_key($stock_status_lookup, array_flip(array_filter(isset($default_filter["stock_status[]"]) ? $default_filter["stock_status[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $stock_status_slugs = array_filter(isset($default_filter["stock_status[]"]) ? $default_filter["stock_status[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $stock_status_slugs, true)) {
                $stock_status_slugs[] = $val;
            }
        }
        $matched_stock_status_with_ids = array_intersect_key($stock_status_lookup, array_flip($stock_status_slugs));
    }
    $all_data_objects["rplutock_status[]"] = array_keys($matched_stock_status_with_ids);
    $products_id_by_stock_status = empty($matched_stock_status_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_stock_status_with_ids))));
    // filter by sale status
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_sale_status_with_ids = array_intersect_key($sale_status_lookup, array_flip(array_filter(isset($default_filter["sale_status[]"]) ? $default_filter["sale_status[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $sale_status_slugs = array_filter(isset($default_filter["sale_status[]"]) ? $default_filter["sale_status[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $sale_status_slugs, true)) {
                $sale_status_slugs[] = $val;
            }
        }
        $matched_sale_status_with_ids = array_intersect_key($sale_status_lookup, array_flip($sale_status_slugs));
    }
    $all_data_objects["rpn_sale[]"] = array_keys($matched_sale_status_with_ids);
    $products_id_by_sale_status = empty($matched_sale_status_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_sale_status_with_ids))));
    // filter by tags

    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_tag_with_ids = array_intersect_key($tag_lookup, array_flip(array_filter(isset($default_filter["tag[]"]) ? $default_filter["tag[]"] : [])));
    } else {
        // Merge both possible tag sources: 'tag[]' and numeric keys (0,1,2,...)
        $tag_slugs = array_filter(isset($default_filter["tag[]"]) ? $default_filter["tag[]"] : []);
        // Collect numeric keys as possible tag slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $tag_slugs, true)) {
                $tag_slugs[] = $val;
            }
        }
        $matched_tag_with_ids = array_intersect_key($tag_lookup, array_flip($tag_slugs));
    }
    $all_data_objects["tag[]"] = array_keys($matched_tag_with_ids);

    if (strtoupper($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["tag_operator"] ?? "IN") === 'AND') {
        $products_id_by_tag = empty($matched_tag_with_ids) ? [] : array_values(array_intersect(...array_values($matched_tag_with_ids)));
    } else {
        $products_id_by_tag = empty($matched_tag_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_tag_with_ids))));
    }

    // filter by attribute

    // Match Attributes
    $products_id_by_attributes = [];
    $match_attributes_with_ids = [];

    // Collect all attribute slugs from default_filter["attribute"] (array) and numeric keys (string values)
    $attribute_slugs_by_tax = [];
    if (isset($default_filter["attribute"]) && is_array($default_filter["attribute"])) {
        foreach ($default_filter["attribute"] as $taxonomy => $slugs) {
            if (is_array($slugs)) {
                foreach ($slugs as $slug) {
                    $attribute_slugs_by_tax[$taxonomy][] = $slug;
                }
            }
        }
    }

    // Also check numeric keys for possible attribute slugs
    foreach ($default_filter as $key => $val) {
        if (is_numeric($key) && is_string($val) && $val !== '') {
            // Try to match this value to any attribute term slug
            foreach ($all_attributes as $taxonomy => $lookup) {
                if (isset($lookup['terms']) && is_array($lookup['terms'])) {
                    foreach ($lookup['terms'] as $term) {
                        if ($term['slug'] === $val) {
                            $attribute_slugs_by_tax[$taxonomy][] = $val;
                        }
                    }
                }
            }
        }
    }

    // Remove duplicates
    foreach ($attribute_slugs_by_tax as $taxonomy => $slugs) {
        $attribute_slugs_by_tax[$taxonomy] = array_unique($slugs);
    }

    // Now build $match_attributes_with_ids and $all_data_objects
    if ((is_array($all_attributes) || is_object($all_attributes))) {
        foreach ($all_attributes as $taxonomy => $lookup) {
            if (isset($lookup['terms']) && is_array($lookup['terms'])) {
                foreach ($lookup['terms'] as $term) {
                    if (in_array($term['slug'], $attribute_slugs_by_tax[$taxonomy] ?? [])) {
                        $match_attributes_with_ids[$taxonomy][] = $term['products'];
                        $all_data_objects['attribute[' . $taxonomy . '][]'][] = $term['slug'];
                    }
                }
            }
        }
    }

    if (strtoupper($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["terms_operator"] ?? "IN") === 'AND') {
        foreach ($match_attributes_with_ids as $taxonomy => $products) {
            $products_id_by_attributes[] = array_values(array_intersect(...$products));
        }
    } else {
        foreach ($match_attributes_with_ids as $taxonomy => $products) {
            $products_id_by_attributes[] = array_values(array_unique(array_merge(...$products)));
        }
    }

    $common_values = empty($products_id_by_attributes) ? [] : array_intersect(...$products_id_by_attributes);

    // filter by custom_meta
    // Match custom_meta
    $products_id_by_custom_meta = [];
    $match_custom_meta_with_ids = [];

    // Collect all custom_meta slugs from default_filter["custom_meta"] (array) and numeric keys (string values)
    $custom_meta_slugs_by_tax = [];
    if (isset($default_filter["custom_meta"]) && is_array($default_filter["custom_meta"])) {
        foreach ($default_filter["custom_meta"] as $taxonomy => $slugs) {
            if (is_array($slugs)) {
                foreach ($slugs as $slug) {
                    $custom_meta_slugs_by_tax[$taxonomy][] = $slug;
                }
            }
        }
    }

    // Also check numeric keys for possible custom_meta slugs
    foreach ($default_filter as $key => $val) {
        if (is_numeric($key) && is_string($val) && $val !== '') {
            // Try to match this value to any custom_meta term slug
            foreach ($custom_fields as $taxonomy => $lookup) {
                if (isset($lookup['terms']) && is_array($lookup['terms'])) {
                    foreach ($lookup['terms'] as $term) {
                        if ($term['slug'] === $val) {
                            $custom_meta_slugs_by_tax[$taxonomy][] = $val;
                        }
                    }
                }
            }
        }
    }

    // Remove duplicates
    foreach ($custom_meta_slugs_by_tax as $taxonomy => $slugs) {
        $custom_meta_slugs_by_tax[$taxonomy] = array_unique($slugs);
    }

    // Now build $match_custom_meta_with_ids and $all_data_objects
    if ((is_array($custom_fields) || is_object($custom_fields))) {
        foreach ($custom_fields as $taxonomy => $lookup) {
            if (isset($lookup['terms']) && is_array($lookup['terms'])) {
                foreach ($lookup['terms'] as $term) {
                    if (in_array($term['slug'], $custom_meta_slugs_by_tax[$taxonomy] ?? [])) {
                        $match_custom_meta_with_ids[$taxonomy][] = $term['products'];
                        $all_data_objects['custom_meta[' . $taxonomy . '][]'][] = $term['slug'];
                    }
                }
            }
        }
    }

    foreach ($match_custom_meta_with_ids as $taxonomy => $products) {
        $products_id_by_custom_meta[] = array_values(array_unique(array_merge(...$products)));
    }

    $common_values_custom_meta = empty($products_id_by_custom_meta) ? [] : array_intersect(...$products_id_by_custom_meta);


    // echo json_encode(
    //     [
    //         "products_id_by_cata" => $products_id_by_cata,
    //         "products_id_by_tag" => $products_id_by_tag,
    //         "products_id_by_brand" => $products_id_by_brand,
    //         "attribute" =>  $common_values,
    //         "custom_meta" => $common_values_custom_meta,
    //         "products_id_by_author" => $products_id_by_author,
    //         "products_id_by_stock_status" => $products_id_by_stock_status,
    //         "products_id_by_sale_status" => $products_id_by_sale_status,
    //         "products_id_by_search" => $products_id_by_search,
    //         "products_id_by_price" => $products_id_by_price,
    //         "products_id_by_dimensions" => $products_id_by_dimensions,
    //         "products_id_by_rating" => $products_id_by_rating,
    //         "products_id_by_sku" => $products_id_by_sku,
    //         "products_id_by_discount" => $products_id_by_discount,
    //         "products_id_by_date_filter" => $products_id_by_date_filter,
    //     ]
    // );

    $products_ids = dapfforwc_getFilteredProductIds(
        [
            $products_id_by_cata,
            $products_id_by_tag,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_stock_status,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter
        ]
    );

    
    $all_product_ids = array_map('intval', array_column($product_details, 'ID'));


    $cat_op  = strtoupper($dapfforwc_styleoptions["operator"]["product-category"] ?? 'OR');
    if (is_product_category()) {
        $cat_op = 'AND';
    }
    $tag_op  = strtoupper($dapfforwc_styleoptions["operator"]["tag"] ?? 'OR');
    if (is_product_tag()) {
        $tag_op = 'AND';
    }
    $brand_op = strtoupper($dapfforwc_styleoptions["operator"]["brands"] ?? 'OR');
    if (dapfforwc_is_product_brand()) {
        $brand_op = 'AND';
    }
    $author_op = strtoupper($dapfforwc_styleoptions["operator"]["authors"] ?? 'OR');
    $status_op = strtoupper($dapfforwc_styleoptions["operator"]["status"] ?? 'OR');
    $sale_status_op = strtoupper($dapfforwc_styleoptions["operator"]["sale_status"] ?? 'OR');
    $attribute_ops = $dapfforwc_styleoptions['operator']['attributes'] ?? [];
    $products_id_by_attributes = [];
    foreach ($match_attributes_with_ids as $taxonomy => $products) {
        $op = strtoupper($attribute_ops[$taxonomy] ?? ($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["terms_operator"] ?? 'OR'));
        if ($op === 'AND') {
            $products_id_by_attributes[$taxonomy] = array_values(array_intersect(...$products));
        } else { // OR
            $products_id_by_attributes[$taxonomy] = array_values(array_unique(array_merge(...$products)));
        }
    }
    $common_values = empty($products_id_by_attributes) ? [] : array_intersect(...array_values($products_id_by_attributes));
    $cm_op = strtoupper($dapfforwc_styleoptions['operator']['custom_meta'] ?? 'OR');

    $products_for_categories = ($cat_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_tag,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_stock_status,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids; // AND keeps self-filtering

    $products_for_tags = ($tag_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_cata,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_stock_status,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids;

    $products_for_brands = ($brand_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_cata,
            $products_id_by_tag,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_stock_status,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids;

    $products_for_authors = ($author_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_cata,
            $products_id_by_tag,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_stock_status,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids;

    $products_for_stock_status = ($status_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_cata,
            $products_id_by_tag,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_sale_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids;

    $products_for_sale_status = ($sale_status_op === 'OR')
        ? dapfforwc_getFilteredProductIds([
            $products_id_by_cata,
            $products_id_by_tag,
            $products_id_by_brand,
            $common_values,
            $common_values_custom_meta,
            $products_id_by_author,
            $products_id_by_stock_status,
            $products_id_by_search,
            $products_id_by_price,
            $products_id_by_dimensions,
            $products_id_by_rating,
            $products_id_by_sku,
            $products_id_by_discount,
            $products_id_by_date_filter,
            $all_product_ids
        ])
        : $products_ids;

    $products_for_attributes = [];
    foreach ($products_id_by_attributes as $taxonomy => $set) {
        $other_attr_sets = array_diff_key($products_id_by_attributes, [$taxonomy => true]);
        $products_for_attributes[$taxonomy] = dapfforwc_getFilteredProductIds(array_merge(
            [
                $products_id_by_cata,
                $products_id_by_tag,
                $products_id_by_brand,
                $common_values_custom_meta,
                $products_id_by_author,
                $products_id_by_stock_status,
                $products_id_by_sale_status,
                $products_id_by_search,
                $products_id_by_price,
                $products_id_by_dimensions,
                $products_id_by_rating,
                $products_id_by_sku,
                $products_id_by_discount,
                $products_id_by_date_filter,
            ],
            array_values($other_attr_sets),
            [$all_product_ids] // fallback when nothing else is active
        ));
    }

    $products_id_by_custom_meta = [];
    foreach ($match_custom_meta_with_ids as $taxonomy => $products) {
        $op = strtoupper($custom_meta_ops[$taxonomy] ?? 'OR'); // fallback
        if ($op === 'AND') {
            $products_id_by_custom_meta[$taxonomy] = array_values(array_intersect(...$products));
        } else { // OR
            $products_id_by_custom_meta[$taxonomy] = array_values(array_unique(array_merge(...$products)));
        }
    }
    $common_values_custom_meta = empty($products_id_by_custom_meta)
        ? []
        : array_intersect(...array_values($products_id_by_custom_meta));

    $products_for_custom_meta = [];
    foreach ($products_id_by_custom_meta as $taxonomy => $set) {
        $other_cm_sets = array_diff_key($products_id_by_custom_meta, [$taxonomy => true]);
        $products_for_custom_meta[$taxonomy] = dapfforwc_getFilteredProductIds(array_merge(
            [
                $products_id_by_cata,
                $products_id_by_tag,
                $products_id_by_brand,
                $common_values, // attributes constraint
                $products_id_by_author,
                $products_id_by_stock_status,
                $products_id_by_sale_status,
                $products_id_by_search,
                $products_id_by_price,
                $products_id_by_dimensions,
                $products_id_by_rating,
                $products_id_by_sku,
                $products_id_by_discount,
                $products_id_by_date_filter,
            ],
            array_values($other_cm_sets),
            [$all_product_ids] // fallback when nothing else is active
        ));
    }

    if (!empty($products_ids)) {
        // Store in a global for immediate use
        $GLOBALS['dapfforwc_filtered_product_ids'] = $products_ids;

        // Also store a hash of current filters to validate freshness
        $filter_hash = md5(wp_json_encode($filteroptionsfromurl));
        $GLOBALS['dapfforwc_filter_hash'] = $filter_hash;

        // Optionally store in transient for AJAX requests (short-lived, 5 minutes)
        set_transient('dapfforwc_filtered_ids_' . $filter_hash, $products_ids, 300);
    }

    $updated_filters = dapfforwc_get_updated_filters($products_ids, $all_data, [
        'categories' => $products_for_categories,
        'tags'       => $products_for_tags,
        'brands'     => $products_for_brands,
        'authors'    => $products_for_authors,
        'stock_status' => $products_for_stock_status,
        'sale_status' => $products_for_sale_status,
        'attributes'   => $products_for_attributes,
        'custom_fields' => $products_for_custom_meta
    ]) ?? [];

    $min_max_prices = dapfforwc_get_min_max_price($product_details, $products_ids);

    if (isset($filteroptionsfromurl["default_min"]) && isset($filteroptionsfromurl["default_max"]) && $filteroptionsfromurl["default_min"] <= $min_max_prices["min"] && $filteroptionsfromurl["default_max"] >= $min_max_prices["max"]) {
        $min_max_prices = [
            "min" => $filteroptionsfromurl["default_min"],
            "max" => $filteroptionsfromurl["default_max"],
        ];
    }

    $all_data_objects["min_price"] = isset($default_filter["min_price"]) ? floatval($default_filter["min_price"]) : (isset($dapfforwc_styleoptions["price"]["auto_price"]) ? ceil(floatval($min_max_prices['min'])) : floatval($dapfforwc_styleoptions["price"]["min_price"] ?? 0));
    $all_data_objects["max_price"] = isset($default_filter["max_price"]) ? floatval($default_filter["max_price"]) : (isset($dapfforwc_styleoptions["price"]["auto_price"]) ? ceil(floatval($min_max_prices['max'])) : floatval($dapfforwc_styleoptions["price"]["max_price"] ?? 100000000000));

    ob_start(); // Start output buffering
    if ($atts['layout'] === 'top_view') {
        // Add your custom styles for the top_view layout here
?>
        <style>
            @media (min-width: <?php echo intval($desktop_breakpoint); ?>px) {

                /* Product Filter Styles */
                #product-filter {
                    display: flex;
                    flex-direction: row !important;
                    gap: 12px;
                    overflow-x: auto;
                    overflow-y: hidden;
                    padding-bottom: 100vh;
                    margin-bottom: -100vh;
                    padding-left: 2px;
                    scrollbar-width: thin;
                    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
                    scrollbar-width: none;
                }

                .plugincy_layout_top_view .items {
                    display: none !important;
                }

                .plugincy_filter_wrapper {
                    position: relative;
                }

                .plugincy_filter_wrapper:hover .plugincy-next-button,
                .plugincy_filter_wrapper:hover .plugincy-prev-button {
                    display: flex !important;
                    position: absolute;
                    z-index: 9999;
                    justify-content: center;
                    top: 10px;
                    transform: translate(0);
                    align-items: center;
                }

                .plugincy_filter_wrapper:hover .plugincy-next-button {
                    right: 0;
                }

                .plugincy_filter_wrapper:hover .plugincy-prev-button {
                    left: 0;
                }

                .plugincy-next-button,
                .plugincy-prev-button {
                    width: 30px;
                    height: 30px;
                    min-width: 30px;
                    min-height: 30px;
                    max-width: 30px;
                    max-height: 30px;
                    padding: 0;
                    margin: 0;
                    border-radius: 50%;
                    background: #ffffff;
                    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
                    color: #000;
                    transition: all 0.5s;
                }

                .navigation-buttons button:hover {
                    background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?> !important;
                    color: #ffffff;
                }

                /* Webkit scrollbar styling for better cross-browser support */
                #product-filter::-webkit-scrollbar {
                    display: none;
                    /* Hide default scrollbar for WebKit browsers */
                }

                #product-filter::-webkit-scrollbar-track {
                    background: transparent;
                }

                #product-filter::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, 0.2);
                    border-radius: 3px;
                }

                #product-filter::-webkit-scrollbar-thumb:hover {
                    background: rgba(0, 0, 0, 0.3);
                }

                /* Filter group container */
                .plugincy-filter-group {
                    position: relative;
                    min-width: max-content;
                    flex-shrink: 0;
                }

                /* Filter group title */
                .plugincy-filter-group .plugincy_title {
                    white-space: nowrap;
                    font-weight: 500;
                    user-select: none;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }

                #product-filter .plugincy-filter-group.rating {
                    overflow: visible !important;
                }

                /* Dropdown items container */
                .plugincy-filter-group .items {
                    position: absolute !important;
                    top: 100%;
                    left: 0;
                    right: 0;
                    z-index: 1000;
                    background: #ffffff;
                    border: 1px solid rgba(0, 0, 0, 0.1);
                    border-radius: 4px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    transform: translateY(8px);
                    transition: all 0.2s ease;
                    min-width: 200px;
                }

                /* Show dropdown items on hover or focus */
                .plugincy-filter-group:hover .items,
                .plugincy-filter-group:focus-within .items {
                    transform: translateY(4px);
                }

            }
        </style>
    <?php wp_add_inline_script('urlfilter-ajax', "
            document.addEventListener('DOMContentLoaded', function () {
  if (window.innerWidth > " . intval($mobile_breakpoint) . ") {
    const productFilter = document.getElementById('product-filter');
    const nextButton = document.querySelector('.plugincy-next-button');
    const prevButton = document.querySelector('.plugincy-prev-button');

    if (productFilter) {
      // Horizontal scroll for the filter rail, but NOT when hovering .items
      productFilter.addEventListener('wheel', function (e) {
        // If the wheel event started inside .items, let the browser handle it
        if (e.target.closest('#product-filter .items')) return;

        // Otherwise, hijack to horizontal scroll
        e.preventDefault();
        const delta = (typeof e.deltaY === 'number' ? e.deltaY : 0) ||
                      (typeof e.deltaX === 'number' ? e.deltaX : 0) ||
                      (typeof e.wheelDelta === 'number' ? -e.wheelDelta : 0);
        productFilter.scrollLeft += delta;
      }, { passive: false }); // ensure preventDefault works

      if (nextButton) {
        nextButton.addEventListener('click', function () {
          productFilter.scrollLeft += 200;
        });
      }
      if (prevButton) {
        prevButton.addEventListener('click', function () {
          productFilter.scrollLeft -= 200;
        });
      }
    }
  }
});", 100);
    }

    if ($template_options['active_template'] && $template_options['active_template'] === 'shadow') { ?>
        <style>
            #product-filter .plugincy-filter-group {
                box-shadow: rgba(99, 99, 99, 0.2) 0 2px 8px 0;
            }

            #product-filter .plugincy-filter-group .plugincy_title {
                padding: 10px 13px 10px 14px;
            }

            #product-filter .plugincy-filter-group .items {
                padding: 20px 10px;
            }

            #product-filter .plugincy-filter-group {
                margin-bottom: 15px;
                border-radius: 8px;
            }
        </style>

    <?php } else { ?>
        <style>
            #product-filter .plugincy-filter-group .plugincy_title {
                padding: 10px 0 14px;
            }

            #product-filter .plugincy-filter-group .items {
                padding: 20px 0 10px;
            }

            #product-filter .plugincy-filter-group {
                margin-bottom: 0;
            }
        </style>
    <?php
    } ?>
    <style>
        #product-filter .plugrogress-percentage:after,
        #product-filter .plugrogress-percentage:before,
        #product-filter .plugincy_slider .plugrogress,
        #product-filter .plugincy-search-submit,
        #product-filter .plugincy-term-search-submit,
        #product-filter .dapfforwc-apply-filters-btn {
            background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?> !important;
        }

        #product-filter .plugincy-filter-group {
            background: <?php echo esc_html(isset($template_options["background_color"]) ? $template_options["background_color"] : 'rgba(255, 255, 255, 0.7)'); ?>;
        }

        #product-filter .plugincy-filter-group,
        #product-filter .plugincy-filter-group .plugincy_title,
        form#product-filter label,
        form#product-filter legend {
            color: <?php echo esc_html(isset($template_options["text_color"]) ? $template_options["text_color"] : '#000000'); ?>;
        }

        span.plugincy-stars svg {
            fill: <?php echo esc_html(isset($template_options["text_color"]) ? $template_options["text_color"] : '#000000'); ?>;
        }

        .rfilterbuttons ul li.checked,
        .rfilterselected ul li.checked,
        #product-filter .items.button_check label {
            border: 1px solid <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
        }

        #product-filter label.image-option {
            border: 2px solid <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
        }

        #product-filter input[type="range"]::-webkit-slider-thumb {
            background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
        }

        #product-filter input[type="range"]::-moz-range-thumb {
            background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
        }

        #product-filter .checkbox_hide .filter-checkbox:checked+span {
            color: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
        }

        #product-filter .plugincy-filter-group .items {
            width: 100%;
            border-top: 1px solid <?php echo esc_html(isset($template_options["border_color"]) ? $template_options["border_color"] : '#eee'); ?>;
        }

        #product-filter span.reset-value,
        #product-filter .dapfforwc-reset-filters-btn {
            background: <?php echo esc_html(isset($template_options["secondary_color"]) ? $template_options["secondary_color"] : '#ff4d4d'); ?>;
        }

        #product-filter .plugrogress-percentage:before {
            content: "0";
            content: "<?php echo esc_html($all_data_objects["min_price"]); ?>";
        }

        #product-filter .plugrogress-percentage:after {
            content: "";
            content: "<?php echo esc_html($all_data_objects["max_price"]); ?>";
        }

        <?php if ($atts['mobile_responsive'] === 'style_1') { ?>
        /* responsive filter */
        @media (max-width: <?php echo intval($desktop_breakpoint); ?>px) {

            .rfilterbuttons,
            #product-filter .items {
                display: none !important;
            }

            #product-filter .plugincy-filter-group div .plugincy_title {
                cursor: pointer !important;
            }

            #product-filter:before {
                content: "Filter";
                background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
                color: white;
                padding: 10px 11px;
                width: 60px;
                height: 52px;
                position: absolute;
                left: 0px;
                border-radius: 5px 0 0 5px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            form#product-filter {
                display: flex;
                flex-direction: row !important;
                overflow: auto;
                gap: 10px;
                height: 66px;
                margin-left: 64px;
                scrollbar-width: thin;
            }

            .plugincy-filter-group .plugincy_title {
                font-size: 16px !important;
            }

            .child-categories {
                display: block !important;
            }

            span.show-sub-cata {
                display: none !important;
            }

            .plugincy-filter-group {
                min-width: max-content;
                height: min-content;
            }

            #product-filter .items {
                position: absolute;
                left: 0;
                background: white;
                padding: 20px 15px;
                box-shadow: #efefef99 0 -4px 10px 4px;
                z-index: 999;
                top: 70px;
            }

            .dafilter-word--show #product-filter:before {
                display: block;
            }

            .dafilter-word--show form#product-filter {
                margin-left: 64px;
            }

            .dafilter-word--hide #product-filter:before {
                display: none;
            }

            .dafilter-word--hide form#product-filter {
                margin-left: 0;
            }
        }

        <?php } ?><?php if ($atts['mobile_responsive'] === 'style_2') { ?><?php } ?>
    </style>
    <?php if ($atts['mobile_responsive'] === 'style_3') { ?>

        <style>
            @media (min-width: <?php echo intval($desktop_breakpoint); ?>px) {

                #mobileonly,
                #filter-button {
                    display: none !important;
                }
            }

            @media (max-width: <?php echo intval($desktop_breakpoint); ?>px) {
                .mobile-filter {
                    position: fixed;
                    z-index: 2147483647;
                    background: #ffffff;
                    width: 86vw;
                    max-width: 420px;
                    padding-bottom: 200px;
                    height: 100dvh;
                    overflow: auto;
                    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                    border-radius: 30px;
                    margin: 5px !important;
                    display: none;
                    top: 20%;
                    transform: translateX(-50%);
                    left: 50%;
                    scrollbar-width: thin;
                }


                .mobile-filter-overlay {
                    position: fixed;
                    z-index: 2147483646;
                    background: rgba(0, 0, 0, 0.55);
                    inset: 0;
                    display: none;
                }

                .rfilterselected ul {
                    flex-wrap: nowrap;
                    overflow: auto;
                    margin: 0 0 10px !important;
                }
            }
        </style>
    <?php } ?>
    <?php if ($atts['mobile_responsive'] === 'style_4') { ?>

        <style>
            @media (min-width: <?php echo intval($desktop_breakpoint); ?>px) {

                #mobileonly,
                #filter-button {
                    display: none !important;
                }
            }

            @media (max-width: <?php echo intval($desktop_breakpoint); ?>px) {
                .mobile-filter {
                    position: fixed;
                    z-index: 2147483647;
                    background: #ffffff;
                    width: 86vw;
                    max-width: 420px;
                    height: 100dvh;
                    overflow: auto;
                    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                    top: 0;
                    right: 0;
                    transition: transform 0.3s ease-in-out;
                    transform: translateX(150%);
                    scrollbar-width: thin;
                }

                .mobile-filter.open {
                    transform: translateX(0%);
                }


                .mobile-filter-overlay {
                    position: fixed;
                    z-index: 2147483646;
                    background: rgba(0, 0, 0, 0.55);
                    inset: 0;
                    display: none;
                }

                .rfilterselected ul {
                    flex-wrap: nowrap;
                    overflow: auto;
                    margin: 0 0 10px !important;
                }
            }
        </style>
    <?php }

    if ($atts['mobile_responsive'] === 'style_3' ||  $atts['mobile_responsive'] === 'style_4') { ?>
        <button id="filter-button" style="position: fixed;z-index: 2147483645;bottom: 20px;right: 20px;background-color: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#041a57'); ?>;color: white;border: none;border-radius: 50%;aspect-ratio: 1;display: flex;align-items: center;justify-content: center;width: 40px;height: 40px;padding: 0;">
            <svg style=" width: 20px; fill: #fff; " xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512" role="graphics-symbol" aria-hidden="false" aria-label="">
                <path d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z"></path>
            </svg>
        </button>
        <div class="mobile-filter-overlay" aria-hidden="true"></div>
        <div class="mobile-filter">
            <div class="sm-top-btn" id="mobileonly" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding: 20px;margin-bottom: 10px;">
                <button class="filter-cancel-button" aria-label="Close filters" style="background: none !important;padding:0;color: #000;display: inline-flex;align-items: center;justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                </button>
                <p style="margin: 0;" id="rcountproduct">Showing(All)</p>
            </div>
        <?php
        echo '<div class="rfilterselected" id="mobileonly"><div><ul></ul></div></div>';
    }
    if ($atts['mobile_responsive'] === 'style_3') {
        wp_add_inline_script('urlfilter-ajax', '
         jQuery(document).ready(function($) {
                    const MOBILE_BP = ' . intval($mobile_breakpoint) . ';
                    const $mobileFilter = $(".mobile-filter");
                    const $mobileOverlay = $(".mobile-filter-overlay");
                    const $filterButton = $("#filter-button");
                    const $loader = $("div#loader");
                    const originalPositions = {
                        mobileFilter: $mobileFilter.length ? { parent: $mobileFilter.parent(), next: $mobileFilter.next() } : null,
                        mobileOverlay: $mobileOverlay.length ? { parent: $mobileOverlay.parent(), next: $mobileOverlay.next() } : null,
                        filterButton: $filterButton.length ? { parent: $filterButton.parent(), next: $filterButton.next() } : null,
                        loader: $loader.length ? { parent: $loader.parent(), next: $loader.next() } : null
                    };

                    const moveElementToBody = function ($el) {
                        if ($el.length && !$el.parent().is("body")) {
                            $el.appendTo("body");
                        }
                    };

                    const restoreElement = function ($el, position) {
                        if (!$el.length || !position || !position.parent || !position.parent.length) {
                            return;
                        }

                        if (position.next && position.next.length && position.next.parent().is(position.parent)) {
                            $el.insertBefore(position.next);
                        } else {
                            position.parent.append($el);
                        }
                    };

                    const moveElementsToBody = function () {
                        moveElementToBody($mobileFilter);
                        moveElementToBody($mobileOverlay);
                        moveElementToBody($filterButton);
                        moveElementToBody($loader);

                        closeFilter(true);
                    };

                    const restoreElements = function () {
                        restoreElement($mobileFilter, originalPositions.mobileFilter);
                        restoreElement($mobileOverlay, originalPositions.mobileOverlay);
                        restoreElement($filterButton, originalPositions.filterButton);
                        restoreElement($loader, originalPositions.loader);
                    };

                    const openFilter = function () {
                        $mobileFilter.slideDown();
                        $mobileOverlay.fadeIn();
                    };

                    const closeFilter = function (immediate) {
                        if (immediate) {
                            $mobileFilter.stop(true, true).hide();
                            $mobileOverlay.stop(true, true).hide();
                            return;
                        }

                        $mobileFilter.slideUp();
                        $mobileOverlay.fadeOut();
                    };

                    const showDesktop = function () {
                        $mobileFilter.stop(true, true).show();
                        $mobileOverlay.stop(true, true).hide();
                    };

                    let isMobile = false;

                    const updateIsMobile = function () {
                        const nowMobile = window.innerWidth <= MOBILE_BP;

                        if (nowMobile) {
                            moveElementsToBody();
                        } else {
                            restoreElements();
                            showDesktop();
                        }

                        isMobile = nowMobile;
                    };

                    $(".filter-cancel-button").on("click", function(event) {
                        event.preventDefault();

                        if (!isMobile) {
                            return;
                        }

                        closeFilter();
                    });

                    $("#filter-button").on("click", function(event) {
                        event.preventDefault();

                        if (!isMobile) {
                            return;
                        }

                        if ($mobileFilter.is(":visible")) {
                            closeFilter();
                        } else {
                            openFilter();
                        }
                    });

                    $mobileOverlay.on("click", function() {
                        if (!isMobile) {
                            return;
                        }

                        closeFilter();
                    });

                    $(document).on("click", function(event) {
                        if (!isMobile) {
                            return;
                        }

                        if (!$(event.target).closest(".mobile-filter, #filter-button").length) {
                            closeFilter();
                        }
                    });

                    updateIsMobile();

                    $(window).on("resize", function() {
                        const nowMobile = window.innerWidth <= MOBILE_BP;
                        if (nowMobile) {
                            return;
                        }
                        updateIsMobile();
                    });
                });
         ');
    }

    if ($atts['mobile_responsive'] === 'style_4') {
        wp_add_inline_script('urlfilter-ajax', '
        jQuery(document).ready(function($) {
                    const MOBILE_BP = ' . intval($mobile_breakpoint) . ';
                    const $mobileFilter = $(".mobile-filter");
                    const $mobileOverlay = $(".mobile-filter-overlay");
                    const $filterButton = $("#filter-button");
                    const $loader = $("div#loader");
                    const originalPositions = {
                        mobileFilter: $mobileFilter.length ? { parent: $mobileFilter.parent(), next: $mobileFilter.next() } : null,
                        mobileOverlay: $mobileOverlay.length ? { parent: $mobileOverlay.parent(), next: $mobileOverlay.next() } : null,
                        filterButton: $filterButton.length ? { parent: $filterButton.parent(), next: $filterButton.next() } : null,
                        loader: $loader.length ? { parent: $loader.parent(), next: $loader.next() } : null
                    };

                    const moveElementToBody = function ($el) {
                        if ($el.length && !$el.parent().is("body")) {
                            $el.appendTo("body");
                        }
                    };

                    const restoreElement = function ($el, position) {
                        if (!$el.length || !position || !position.parent || !position.parent.length) {
                            return;
                        }

                        if (position.next && position.next.length && position.next.parent().is(position.parent)) {
                            $el.insertBefore(position.next);
                        } else {
                            position.parent.append($el);
                        }
                    };

                    const moveElementsToBody = function () {
                        moveElementToBody($mobileFilter);
                        moveElementToBody($mobileOverlay);
                        moveElementToBody($filterButton);
                        moveElementToBody($loader);

                        closeFilter(true);
                    };

                    const restoreElements = function () {
                        restoreElement($mobileFilter, originalPositions.mobileFilter);
                        restoreElement($mobileOverlay, originalPositions.mobileOverlay);
                        restoreElement($filterButton, originalPositions.filterButton);
                        restoreElement($loader, originalPositions.loader);
                    };

                    const openFilter = function () {
                        $mobileFilter.addClass("open");
                        $mobileOverlay.fadeIn();
                    };

                    const closeFilter = function (immediate) {
                        $mobileFilter.removeClass("open");

                        if (immediate) {
                            $mobileOverlay.stop(true, true).hide();
                            return;
                        }

                        $mobileOverlay.fadeOut();
                    };

                    const showDesktop = function () {
                        $mobileFilter.removeClass("open").stop(true, true).show();
                        $mobileOverlay.stop(true, true).hide();
                    };

                    let isMobile = false;

                    const updateIsMobile = function () {
                        const nowMobile = window.innerWidth <= MOBILE_BP;

                        if (nowMobile) {
                            moveElementsToBody();
                        } else {
                            restoreElements();
                            showDesktop();
                        }

                        isMobile = nowMobile;
                    };

                    $("#filter-button").on("click", function(event) {
                        event.preventDefault();

                        if (!isMobile) {
                            return;
                        }

                        if ($mobileFilter.hasClass("open")) {
                            closeFilter();
                        } else {
                            openFilter();
                        }
                    });

                    $(".filter-cancel-button").on("click", function(event) {
                        event.preventDefault();

                        if (!isMobile) {
                            return;
                        }

                        closeFilter();
                    });

                    $mobileOverlay.on("click", function() {
                        if (!isMobile) {
                            return;
                        }

                        closeFilter();
                    });

                    $(document).on("click", function(event) {
                        if (!isMobile) {
                            return;
                        }

                        if (!$(event.target).closest(".mobile-filter, #filter-button").length) {
                            closeFilter();
                        }
                    });

                    updateIsMobile();

                    $(window).on("resize", function() {
                        const nowMobile = window.innerWidth <= MOBILE_BP;
                        if (nowMobile) {
                            return;
                        }
                        updateIsMobile();
                    });
                });
        ');
    } ?>
        <div class="plugincy_filter_wrapper" style="position: relative;">
            <!-- Navigation Buttons -->
            <button type="button" class="plugincy-prev-button" aria-label="Previous" style="display: none;">
                &#8592;
            </button>
            <button type="button" class="plugincy-next-button" aria-label="Next" style="display: none;">
                &#8594;
            </button>
            <?php
            $product_show_settings_attr = '';
            if (isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug])) {
                $product_show_settings_attr = wp_json_encode($dapfforwc_options['product_show_settings'][$dapfforwc_slug]);
            }
            ?>
            <form id="product-filter" class="plugincy_layout_<?php echo esc_attr($atts['layout']); ?>" method="POST" data-layout='<?php echo esc_attr($atts['layout']); ?>' data-mobile-style='<?php echo esc_attr($atts['mobile_responsive']); ?>' data-mobile-breakpoint='<?php echo esc_attr($mobile_breakpoint); ?>'
                data-product_show_settings='<?php echo esc_attr($product_show_settings_attr); ?>'
                <?php if (!empty($atts['product_selector'])) {
                    echo 'data-product_selector="' . esc_attr($atts["product_selector"]) . '"';
                } ?>
                <?php if (!empty($atts['pagination_selector'])) {
                    echo 'data-pagination_selector="' . esc_attr($atts["pagination_selector"]) . '"';
                } ?>>

                <?php
                $default_filter = isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on" ? $all_data_objects : $default_filter;
                // Get min price from URL if present, using the correct prefix from SEO permalinks options
                $min_price = $all_data_objects["min_price"];

                // Get max price from URL if present, using the correct prefix from SEO permalinks options
                $max_price = $all_data_objects["max_price"];

                // Check for price filter in URL
                $price_prefix = 'price';
                if (
                    isset($dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options']['price']) &&
                    !empty($dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options']['price'])
                ) {
                    $price_prefix = $dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options']['price'];
                }

                // Look for price in $_GET using the prefix
                $price_from_url = null;
                if ($price_prefix && isset($_GET[$price_prefix])) {
                    $price_from_url = sanitize_text_field(wp_unslash($_GET[$price_prefix]));
                }

                if ($price_from_url) {
                    // Accept both "min-max" and "min,max" formats
                    if (strpos($price_from_url, '-') !== false) {
                        list($min_from_url, $max_from_url) = explode('-', $price_from_url, 2);
                    } elseif (strpos($price_from_url, ',') !== false) {
                        list($min_from_url, $max_from_url) = explode(',', $price_from_url, 2);
                    } else {
                        $min_from_url = $price_from_url;
                        $max_from_url = null;
                    }
                    if (is_numeric($min_from_url)) {
                        $min_price = floatval($min_from_url);
                    }
                    if (isset($max_from_url) && is_numeric($max_from_url)) {
                        $max_price = floatval($max_from_url);
                    }
                }
                echo '<div class="default_values" style="display:none;">';
                if (!empty($all_data_objects) && is_array($all_data_objects) && (!$is_all_cata || (isset($dapfforwc_advance_settings["default_value_selected"]) && $dapfforwc_advance_settings["default_value_selected"] === 'on' && !is_shop()))) {
                    foreach ($all_data_objects as $key => $value) {
                        if (empty($value) || $key === "plugincy_search" || $key === "min_price" || $key === "max_price") {
                            continue;
                        }
                        if ($key === "tag[]") {
                            $key = "tags[]";
                        }
                        if (is_array($value)) {
                            foreach ($value as $v) {
                                echo '<input type="checkbox" name="' . esc_attr($key) . '" value="' . esc_attr($v) . '" checked>';
                            }
                        } else {
                            echo '<input type="checkbox" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" checked>';
                        }
                    }
                }
                echo '</div>';
                wp_nonce_field('gm-product-filter-action', 'gm-product-filter-nonce');
                $default_data_objects = [
                    "min_price" => $min_price,
                    "max_price" => $max_price,
                    ...$filteroptionsfromurl,
                    ...$parsed_filters
                ];
                echo wp_kses(dapfforwc_filter_form($updated_filters, !$make_default_selected || (isset($dapfforwc_advance_settings["default_value_selected"]) && $dapfforwc_advance_settings["default_value_selected"] === 'on' && !is_shop()) ? $all_data_objects : $default_data_objects, $use_anchor, $use_filters_word, $atts, $min_price, $max_price, $min_max_prices, '', false, false), $dapfforwc_allowed_tags);
                echo '</form></div>';
                if ($atts['mobile_responsive'] === 'style_3' || $atts['mobile_responsive'] === 'style_4') { ?>
        </div>
    <?php }
    ?>

    <!-- Loader HTML -->
    <?php

    echo wp_kses('<div id="loader" style="display:none;"></div>', $dapfforwc_allowed_tags);
    ?>
    <style>
        <?php echo wp_kses('#loader {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: conic-gradient(#0000 10%, ' . esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8') . ');
                -webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 9px), #000 0);
                animation: spinner-zp9dbg 1s infinite linear;
            }

            @keyframes spinner-zp9dbg {
                to {
                    transform: rotate(1turn);
                }
            }', $dapfforwc_allowed_tags); ?>
    </style>
    <div id="roverlay" style="display: none;"></div>

<?php

    // End output buffering and return content
    return ob_get_clean();
}
add_shortcode('plugincy_filters', 'dapfforwc_product_filter_shortcode');

function dapfforwc_generatePremiumBadge($title)
{
    $gradientId = 'gradient-' . uniqid();
    $shadowId = 'shadow-' . uniqid();
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    return sprintf(
        '
        <svg style="width: 100%%; height: 100%%;" width="78" height="80" viewBox="0 0 78 80" 
             xmlns="http://www.w3.org/2000/svg" role="img" aria-label="%s - Premium Brand">
            <defs>
                <linearGradient id="%s" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
                    <stop offset="0%%" stop-color="#2563eb" stop-opacity="1"/>
                    <stop offset="100%%" stop-color="#1d4ed8" stop-opacity="1"/>
                </linearGradient>
                <filter id="%s" x="-20%%" y="-20%%" width="140%%" height="140%%">
                    <feDropShadow dx="2" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.3"/>
                </filter>
            </defs>
            <rect width="78" height="80" rx="8" ry="8" fill="url(#%s)" filter="url(#%s)"/>
            <text x="39" y="32" font-family="Arial, Helvetica, sans-serif" font-size="10" 
                  font-weight="bold" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">%s</text>
            <line x1="12" y1="42" x2="66" y2="42" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1"/>
            <text x="39" y="52" font-family="Arial, Helvetica, sans-serif" font-size="7" 
                  fill="rgba(255, 255, 255, 0.7)" text-anchor="middle" dominant-baseline="middle">PREMIUM BRAND</text>
        </svg>',
        $safeTitle,
        $gradientId,
        $shadowId,
        $gradientId,
        $shadowId,
        $safeTitle
    );
}

function dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, $name, $attribute, $singlevalueSelect, $count, $min_price = 0, $max_price = null, $min_max_prices = [], $disable_unselected = false)
{
    $default_max_price = isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["auto_price"]) ? (ceil(floatval($min_max_prices['max'] ?? $max_price))) : (isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["max_price"]) ? $dapfforwc_styleoptions["price"]["max_price"] : 10000);
    $default_min_price = isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["auto_price"]) ? (ceil(floatval($min_max_prices['min'] ?? $min_price))) : (isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["min_price"]) ? $dapfforwc_styleoptions["price"]["min_price"] : 0);
    $output = '';

    switch ($sub_option) {
        case 'checkbox':
            $output .= '<label><input  ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></label>';
            break;
        case 'button_check':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></label>';
            break;

        case 'radio_check':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-radio-check" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></label>';
            break;

        case 'radio':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-radio" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></label>';
            break;

        case 'square':
        case 'square_check':
            $output .= '<label class="square-option"><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-square" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span><span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></span></label>';
            break;

        case 'checkbox_hide':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . ' style="display:none;"> <span><span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></span></label>';
            break;

        case 'plugincy_color':
        case 'color_no_border':
        case 'color_circle':
        case 'color_value':
            if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
                $sub_option = 'plugincy_color';
            }
            $color = isset($dapfforwc_styleoptions[$attribute]) && isset($dapfforwc_styleoptions[$attribute]['colors']) && isset($dapfforwc_styleoptions[$attribute]['colors'][$value]) ? $dapfforwc_styleoptions[$attribute]['colors'][$value] : '#000'; // Default color
            $border = ($sub_option === 'color_no_border') ? 'none' : '1px solid #000';
            $value_show = ($sub_option === 'color_value') ? 'block' : 'none';
            $output .= '<label style="position: relative;"><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-color" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '>
                <span class="color-box" style="background-color: ' . $color . '; border: ' . $border . '; width: 30px; height: 30px;"></span><span class="value" style="display:' . $value_show . ';">' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '<span></label>';
            break;

        case 'image':
        case 'image_no_border':
            $brand_image_url = dapfforwc_get_wc_brand_image_by_slug($value);
            $image_url = isset($brand_image_url) && !empty($brand_image_url) ? $brand_image_url : ($dapfforwc_styleoptions[$attribute]['images'][$value] ?? 'default-image.jpg');
            $border_class = ($sub_option === 'image_no_border') ? 'no-border' : '';
            $output .= '<label class="image-option ' . $border_class . '">
            <span class="image-title"><span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></span>
    <input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-image" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '>';

            if ($image_url !== 'default-image.jpg') {
                $attachment_id = attachment_url_to_postid($image_url);
                if ($attachment_id) {
                    $output .= wp_get_attachment_image($attachment_id, 'thumbnail', false, array('alt' => esc_attr($title)));
                } else {
                    // generate svg with name
                    $output .= dapfforwc_generatePremiumBadge($title);
                }
            } else {
                // generate svg with name
                $output .= dapfforwc_generatePremiumBadge($title);
            }

            $output .= '</label>';
            break;

        case 'pluginy_select2':
        case 'select2_classic':
        case 'select':
            $output .= '<option  ' . ($disable_unselected && !$checked ? "disabled" : "") . ' class="filter-option"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></option>';
            break;
        case 'input-price-range':
            $min_label = isset($dapfforwc_styleoptions["input_label"]["price"]["min"]) ? $dapfforwc_styleoptions["input_label"]["price"]["min"] : "Min Price:";
            $min_placeholder = isset($dapfforwc_styleoptions["input_placeholder"]["price"]["min"]) ? $dapfforwc_styleoptions["input_placeholder"]["price"]["min"] : "Min";
            $max_placeholder = isset($dapfforwc_styleoptions["input_placeholder"]["price"]["max"]) ? $dapfforwc_styleoptions["input_placeholder"]["price"]["max"] : "Max";
            $max_label = isset($dapfforwc_styleoptions["input_label"]["price"]["max"]) ? $dapfforwc_styleoptions["input_label"]["price"]["max"] : "Max Price:";
            $output .= '<div class="range-input"><label for="min-price">' . $min_label . '</label>
        <input type="number" id="min-price" name="mn_price" min="' . $default_min_price . '" max="' . $default_max_price . '" step="1" placeholder="' . $min_placeholder . '" value="' . $min_price . '" style="min-height: 30px;position: relative;top: unset;pointer-events: all;border: 1px solid #ccc;padding: 5px 6px;border-radius: 3px;width: 100%;max-width: 100%;height: 30px;max-height: 30px;">
        
        <label for="max-price">' . $max_label . '</label>
        <input type="number" id="max-price" name="mx_price" min="' . $default_min_price . '" max="' . $default_max_price . '" step="1" placeholder="' . $max_placeholder . '" value="' . $max_price . '" style="min-height: 30px;position: relative;top: unset;pointer-events: all;border: 1px solid #ccc;padding: 5px 6px;border-radius: 3px;width: 100%;max-width: 100%;height: 30px;max-height: 30px;"></div>';
            break;
        case 'slider':
            $min_label = isset($dapfforwc_styleoptions["input_label"]["price"]["min"]) ? $dapfforwc_styleoptions["input_label"]["price"]["min"] : "Min Price:";
            $min_placeholder = isset($dapfforwc_styleoptions["input_placeholder"]["price"]["min"]) ? $dapfforwc_styleoptions["input_placeholder"]["price"]["min"] : "Min";
            $max_placeholder = isset($dapfforwc_styleoptions["input_placeholder"]["price"]["max"]) ? $dapfforwc_styleoptions["input_placeholder"]["price"]["max"] : "Max";
            $max_label = isset($dapfforwc_styleoptions["input_label"]["price"]["max"]) ? $dapfforwc_styleoptions["input_label"]["price"]["max"] : "Max Price:";

            $output .= '<div class="price-input">
        <div class="field">
          <span>' . $min_label . '</span>
          <input  type="number" id="min-price" name="mn_price" class="input-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
          <span>' . $max_label . '</span>
          <input  type="number" id="max-price" name="mx_price" min="' . $default_min_price . '" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
      </div>
      <div class="plugincy_slider">
        <div class="plugrogress"></div>
      </div>
      <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '" >
        <input  type="range" id="price-range-max" class="range-max" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $max_price . '">
      </div>';
            break;
        case 'slider2':
            $output .= '<div class="price-input plugincy-align-center">
        <div class="field">
          <input  type="number" id="min-price" name="mn_price" class="input-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
          <input  type="number" id="max-price" name="mx_price" min="' . $default_min_price . '" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
      </div>
      <div class="plugincy_slider">
        <div class="plugrogress"></div>
      </div>
      <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '" >
        <input  type="range" id="price-range-max" class="range-max" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $max_price . '">
      </div>';
            break;
        case 'price':
            $output .= '<div class="price-input" style="visibility: hidden; margin: 0;">
        <div class="field">
            <input  type="number" id="min-price" name="mn_price" class="input-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
            <input  type="number" id="max-price" name="mx_price" min="' . $default_min_price . '" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
        </div>
        <div class="plugincy_slider">
        <div class="plugrogress plugrogress-percentage"></div>
        </div>
        <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $min_price . '">
        <input  type="range" id="price-range-max" class="range-max" min="' . $default_min_price . '" max="' . $default_max_price . '" value="' . $max_price . '">
        </div>';
            break;
        case 'rating-text':
            $additional_txt_upto_4 = isset($dapfforwc_styleoptions["additional_text"]["rating"]) && !empty($dapfforwc_styleoptions["additional_text"]["rating"]) ? $dapfforwc_styleoptions["additional_text"]["rating"] : 'Stars & Up';
            $additional_txt_1 = isset($dapfforwc_styleoptions["additional_text_1"]["rating"]) && !empty($dapfforwc_styleoptions["additional_text_1"]["rating"]) ? $dapfforwc_styleoptions["additional_text_1"]["rating"] : 'Star & Up';
            $additional_txt_5 = isset($dapfforwc_styleoptions["additional_text_5"]["rating"]) && !empty($dapfforwc_styleoptions["additional_text_5"]["rating"]) ? $dapfforwc_styleoptions["additional_text_5"]["rating"] : 'Star';
            $output .= '<label><input ' . ($disable_unselected && !in_array("5", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="5" ' . (in_array("5", $checked) ? ' checked' : '') . '> 5 ' . esc_html($additional_txt_5) . ' 
    </label>
        <label><input ' . ($disable_unselected && !in_array("4", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="4" ' . (in_array("4", $checked) ? ' checked' : '') . '> 4 ' . esc_html($additional_txt_upto_4) . '</label>
        <label><input ' . ($disable_unselected && !in_array("3", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="3" ' . (in_array("3", $checked) ? ' checked' : '') . '> 3 ' . esc_html($additional_txt_upto_4) . '</label>
        <label><input ' . ($disable_unselected && !in_array("2", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="2" ' . (in_array("2", $checked) ? ' checked' : '') . '> 2 ' . esc_html($additional_txt_upto_4) . '</label>
        <label><input ' . ($disable_unselected && !in_array("1", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="1" ' . (in_array("1", $checked) ? ' checked' : '') . '> 1 ' . esc_html($additional_txt_1) . '</label>';
            break;
        case 'rating':
            for ($i = 5; $i >= 1; $i--) {
                $star = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 576 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M381.2 150.3L524.9 171.5C536.8 173.2 546.8 181.6 550.6 193.1C554.4 204.7 551.3 217.3 542.7 225.9L438.5 328.1L463.1 474.7C465.1 486.7 460.2 498.9 450.2 506C440.3 513.1 427.2 514 416.5 508.3L288.1 439.8L159.8 508.3C149 514 135.9 513.1 126 506C116.1 498.9 111.1 486.7 113.2 474.7L137.8 328.1L33.58 225.9C24.97 217.3 21.91 204.7 25.69 193.1C29.46 181.6 39.43 173.2 51.42 171.5L195 150.3L259.4 17.97C264.7 6.954 275.9-.0391 288.1-.0391C300.4-.0391 311.6 6.954 316.9 17.97L381.2 150.3z"></path></svg>';
                $output .= '<label>';
                $output .= '<input ' . ($disable_unselected && !in_array($i, $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="' . esc_attr($i) . '" ' . (in_array($i, $checked) ? ' checked' : '') . '>';
                $output .= '<span class="plugincy-stars" style="display:flex;flex-direction: row;">';
                for ($j = 1; $j <= $i; $j++) {
                    $output .= $star;
                }
                $output .= '</span>';
                $output .= '</label>';
            }
            break;
        case 'dynamic-rating':
            $star = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 576 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M381.2 150.3L524.9 171.5C536.8 173.2 546.8 181.6 550.6 193.1C554.4 204.7 551.3 217.3 542.7 225.9L438.5 328.1L463.1 474.7C465.1 486.7 460.2 498.9 450.2 506C440.3 513.1 427.2 514 416.5 508.3L288.1 439.8L159.8 508.3C149 514 135.9 513.1 126 506C116.1 498.9 111.1 486.7 113.2 474.7L137.8 328.1L33.58 225.9C24.97 217.3 21.91 204.7 25.69 193.1C29.46 181.6 39.43 173.2 51.42 171.5L195 150.3L259.4 17.97C264.7 6.954 275.9-.0391 288.1-.0391C300.4-.0391 311.6 6.954 316.9 17.97L381.2 150.3z"></path></svg>';
            for ($i = 5; $i >= 1; $i--) {
                $output .= '<input ' . ($disable_unselected && !in_array($i, $checked) ? "disabled" : "") . ' type="radio" id="star' . $i . '" name="rating[]" value="' . esc_attr($i) . '" ' . (in_array($i, $checked) ? ' checked' : '') . ' />';
                $output .= '<label class="plugincy-stars" for="star' . $i . '" title="' . esc_html($i) . ($i === 1 ? ' star' : ' stars') . '" style="display:flex;flex-direction: row;">' . $star . '</label>';
            }

            break;
        default:
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]"  title="' . $title . '" value="' . $value . '"' . $checked . '> <span class="option_title">' . $title . ($count != 0 ? ' <span class="option_count"><span>(</span>' . $count . '<span>)</span></span>' : '') . '</span></label>';
            break;
    }

    return $output;
}
// Function to get child categories from $updated_filters["categories"]
function dapfforwc_get_child_categories($categories, $parent_id)
{
    $child_categories = array();

    foreach ($categories as $category) {
        // Check if the category is a WP_Term object
        if ($category instanceof WP_Term) {
            if ($category->parent == $parent_id) {
                $child_categories[] = $category;
            }
        }
        // Check if the category is a stdClass object
        elseif (is_object($category) && $category instanceof stdClass) {
            if (isset($category->parent) && $category->parent == $parent_id) {
                $child_categories[] = $category;
            }
        }
    }

    return $child_categories;
}
// Recursive function to render categories
function dapfforwc_render_category_hierarchy(
    $categories,
    $selected_categories,
    $sub_option,
    $dapfforwc_styleoptions,
    $singlevaluecataSelect,
    $show_count,
    $use_anchor,
    $use_filters_word,
    $hierarchical,
    $child_category
) {
    $categoryHierarchyOutput = "";
    foreach ($categories as $category) {
        if (is_object($category)) {
            $value = esc_attr($category->slug);
            $title = esc_html($category->name);
        } elseif (is_array($category)) {
            $value = esc_attr($category['slug']);
            $title = esc_html($category['name']);
        } else {
            // Handle cases where $category is neither an object nor an array
            $value = '';
            $title = '';
        }
        $count = $show_count === 'yes' ? (is_object($category) ? $category->count : $category["count"]) : 0;
        $checked = in_array($category->slug, $selected_categories) ? ($sub_option === 'select' || str_contains($sub_option, 'pluginy_select2') ? ' selected' : ' checked') : '';
        $anchorlink = $use_filters_word === 'on' ? "filters/$value" : "?filters=$value";

        // Fetch child categories
        $child_categories = dapfforwc_get_child_categories($child_category, $category->term_id);
        $is_select_mode = ($sub_option === 'select' || str_contains($sub_option, 'pluginy_select2'));
        $toggle_control = (!empty($child_categories) && $hierarchical === 'enable_hide_child' && !$is_select_mode)
            ? '<span class="show-sub-cata" style="cursor:pointer;" role="button" tabindex="0" aria-expanded="false">+</span>'
            : '';

        // Render current category
        $categoryHierarchyOutput .= $use_anchor === 'on' && $sub_option !== 'select' && !str_contains($sub_option, 'pluginy_select2')
            ? '<div class="dapfforwcpro-category-row" style="display:flex;align-items: center;text-decoration: none;"><a href="' . esc_attr($anchorlink) . '">'
            . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count)
            . '</a>'
            . $toggle_control
             : (!$is_select_mode ? '<div class="dapfforwcpro-category-row" style="display:flex;align-items: center;text-decoration: none;">'
                . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count)
                . $toggle_control
                . '</div>' : dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count));

        // Render child categories
        if (!empty($child_categories)) {
            $categoryHierarchyOutput .= $sub_option !== 'select' && !str_contains($sub_option, 'pluginy_select2') ? '<div class="child-categories" style="display:' . ($hierarchical === 'enable_hide_child' ? 'none;' : 'block;') . '">' : '';
            if ($sub_option === 'select' || $sub_option === 'pluginy_select2' || $sub_option === 'select2_classic') {
                $categoryHierarchyOutput .= '<optgroup label="' . esc_attr($title) . '">';
            }
            $categoryHierarchyOutput .= dapfforwc_render_category_hierarchy($child_categories, $selected_categories, $sub_option, $dapfforwc_styleoptions, $singlevaluecataSelect, $show_count, $use_anchor, $use_filters_word, $hierarchical, $child_category);
            if ($sub_option === 'select' || $sub_option === 'pluginy_select2' || $sub_option === 'select2_classic') {
                $categoryHierarchyOutput .= '</optgroup>';
            }
            $categoryHierarchyOutput .= $sub_option !== 'select' && !str_contains($sub_option, 'pluginy_select2') ? '</div>' : '';
        }
    }
    return $categoryHierarchyOutput;
}

function dapfforwc_product_filter_shortcode_single($atts)
{
    $atts = shortcode_atts(
        array(
            'name' => '', // Default attribute name
        ),
        $atts,
        'get_terms_by_attribute'
    );

    // Check if the name is provided
    if (empty($atts['name'])) {
        return '<p style="background:red;background: red;text-align: center;color: #fff;">' . esc_html__('Please provide an attribute slug.', 'dynamic-ajax-product-filters-for-woocommerce-pro') . '</p>';
    }

    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $name = esc_attr($atts['name']);
    $terms = [];

    if (isset($all_data['categories']) && $name === 'product_cat') {
        foreach ($all_data['categories'] as $cat) {
            $terms[] = [
                'term_id' => $cat['slug'],
                'name'    => $cat['name'],
            ];
        }
    } elseif (isset($all_data['tags']) && $name === 'product_tag') {
        foreach ($all_data['tags'] as $tag) {
            $terms[] = [
                'term_id' => $tag['slug'],
                'name'    => $tag['name'],
            ];
        }
    } elseif (isset($all_data['attributes'][$name])) {
        foreach ($all_data['attributes'][$name]['terms'] as $term) {
            $terms[] = [
                'term_id' => $term['slug'],
                'name'    => $term['name'],
            ];
        }
    }

    // Generate the output
    $output = '<form class="rfilterbuttons" id="' . esc_attr($atts['name']) . '"><ul>';
    // Show all terms by default
    foreach ($terms as $term) {
        $term_id = esc_attr($term['term_id']);
        $term_name = esc_html($term['name']);
        $output .= '<li>
            <input id="term_' . $term_id . '" type="checkbox" value="' . $term_id . '">
            <label for="term_' . $term_id . '">' . $term_name . '</label>
        </li>';
    }
    $output .= '</ul></form>';

    return $output;
}
add_shortcode('plugincy_filters_single', 'dapfforwc_product_filter_shortcode_single');

function dapfforwc_product_filter_shortcode_selected()
{

    // Generate the output
    $output = '<form class="rfilterselected"><div><ul>';
    $output .= '</ul></div></form>';

    return $output;
}
add_shortcode('plugincy_filters_selected', 'dapfforwc_product_filter_shortcode_selected');

function dapfforwc_get_updated_filters($product_ids, $all_data = [], $context = [])
{
    $ids_for_categories = $context['categories'] ?? $product_ids;
    $ids_for_tags       = $context['tags'] ?? $product_ids;
    $ids_for_brands     = $context['brands'] ?? $product_ids;
    $ids_for_authors    = $context['authors'] ?? $product_ids;
    $ids_for_stock_status = $context['stock_status'] ?? $product_ids;
    $ids_for_sale_status = $context['sale_status'] ?? $product_ids;
    $attr_pools   = $context['attributes']    ?? $product_ids;
    $custom_pools = $context['custom_fields'] ?? [];

    $ids_for_custom_fields = $context['custom_fields'] ?? $product_ids;

    $categories = [];
    $attributes = [];
    $tags = [];
    $brands = [];
    $authors = [];
    $custom_fields = [];
    $stock_status = [];
    $sale_status = [];

    if (!empty($product_ids)) {
        // Get attributes with terms
        if (empty($all_data)) {
            $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
        }

        // Extract categories and tags from all_data
        // Categories
        if (is_array($all_data['categories'] ?? []) || is_object($all_data['categories'] ?? [])) {
            foreach ($all_data['categories'] ?? [] as $term_id => $category) {
                if (!empty(array_intersect($ids_for_categories, $category['products']))) {
                    $categories[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $category['name'],
                        'slug'    => $category['slug'],
                        'parent'  => $category['parent'],
                        'taxonomy' => 'product_cat',
                        'count'   => count(array_intersect($category['products'], $ids_for_categories)),
                    ];
                }
            }
        }

        // Tags
        if (is_array($all_data['tags'] ?? []) || is_object($all_data['tags'] ?? [])) {
            foreach ($all_data['tags'] ?? [] as $term_id => $tag) {
                if (!empty(array_intersect($ids_for_tags, $tag['products']))) {
                    $tags[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $tag['name'],
                        'slug'    => $tag['slug'],
                        'taxonomy' => 'product_tag',
                        'count'   => count(array_intersect($tag['products'], $ids_for_tags)),
                    ];
                }
            }
        }


        // Brands
        if (is_array($all_data['brands'] ?? []) || is_object($all_data['brands'] ?? [])) {
            foreach ($all_data['brands'] ?? [] as $term_id => $brand) {
                if (!empty(array_intersect($ids_for_brands, $brand['products']))) {
                    $brands[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $brand['name'],
                        'slug'    => $brand['slug'],
                        'taxonomy' => 'product_brand',
                        'count'   => count(array_intersect($brand['products'], $ids_for_brands)),
                    ];
                }
            }
        }

        // Authors
        if (is_array($all_data['authors'] ?? []) || is_object($all_data['authors'] ?? [])) {
            foreach ($all_data['authors'] ?? [] as $author_id => $author) {
                if (!empty(array_intersect($ids_for_authors, $author['products']))) {
                    $authors[$author_id] = (object) [
                        'term_id' => $author_id,
                        'name' => $author['name'],
                        'slug' => $author['slug'],
                        'count'   => count(array_intersect($author['products'], $ids_for_authors)),
                    ];
                }
            }
        }

        // Stock Status
        if (is_array($all_data['stock_status'] ?? []) || is_object($all_data['stock_status'] ?? [])) {
            foreach ($all_data['stock_status'] ?? [] as $status_id => $status) {
                if (!empty(array_intersect($ids_for_stock_status, $status['products']))) {
                    $stock_status[$status_id] = (object) [
                        'term_id' => $status_id,
                        'name'    => $status['name'],
                        'slug'    => $status['slug'],
                        'taxonomy' => 'stock_status',
                        'count'   => count(array_intersect($status['products'], $ids_for_stock_status)),
                    ];
                }
            }
        }

        // Sale Status
        if (is_array($all_data['sale_status'] ?? []) || is_object($all_data['sale_status'] ?? [])) {
            foreach ($all_data['sale_status'] ?? [] as $status_id => $status) {
                if (!empty(array_intersect($ids_for_sale_status, $status['products']))) {
                    $sale_status[$status_id] = (object) [
                        'term_id' => $status_id,
                        'name'    => $status['name'],
                        'slug'    => $status['slug'],
                        'taxonomy' => 'sale_status',
                        'count'   => count(array_intersect($status['products'], $ids_for_sale_status)),
                    ];
                }
            }
        }

        // Extract attributes
        if (is_array($all_data['attributes'] ?? []) || is_object($all_data['attributes'] ?? [])) {
            foreach ($all_data['attributes'] ?? [] as $attribute) {
                $attribute_name = $attribute['attribute_name'];
                $attribute_label = isset($attribute['attribute_label']) ? (string) $attribute['attribute_label'] : '';
                $terms = $attribute['terms'];

                $pool = $attr_pools[$attribute_name] ?? $product_ids;

                if (is_array($terms) || is_object($terms)) {
                    foreach ($terms as $term) {
                        // Check if the term's products match the provided product IDs
                        if (!empty(array_intersect($pool, $term['products']))) {
                            $attributes[$attribute_name][] = [
                                'term_id' => $term['term_id'],
                                'attribute_label' => $attribute_label,
                                'name'    => $term['name'],
                                'slug'    => $term['slug'],
                                'count' => count(array_intersect($term['products'], $pool)),
                            ];
                        }
                    }
                }
            }
        }

        // Extract custom fields (similar to attributes)
        if (is_array($all_data['custom_fields'] ?? []) || is_object($all_data['custom_fields'] ?? [])) {
            foreach ($all_data['custom_fields'] ?? [] as $custom_field) {
                $field_name = $custom_field['name'];
                $field_label = $custom_field['label'];
                $terms = $custom_field['terms'];
                $pool = $custom_pools[$field_name] ?? $product_ids;

                if (is_array($terms) || is_object($terms)) {
                    foreach ($terms as $term) {
                        // Check if the term's products match the provided product IDs
                        if (!empty(array_intersect($pool, $term['products']))) {
                            $custom_fields[$field_name][] = [
                                'field_name' => $field_name,
                                'field_label' => $field_label,
                                'name'    => $term['name'],
                                'slug'    => $term['slug'],
                                'count'   => count(array_intersect($term['products'], $pool)),
                            ];
                        }
                    }
                }
            }
        }
    }
    return [
        'categories' => array_values($categories), // Return as array
        'attributes' => $attributes,
        'tags' => array_values($tags), // Return as array
        'brands' => array_values($brands), // Return as array
        'authors' => array_values($authors), // Return as array
        'custom_fields' => $custom_fields, // Return custom fields like attributes
        'stock_status' => array_values($stock_status), // Return as array
        'sale_status' => array_values($sale_status), // Return as array
    ];
}


function dapfforwc_get_woocommerce_attributes_with_terms()
{
    global $wpdb;

    $cache_key = 'dapfforwc_attributes_cache_v2';
    $cached = get_transient($cache_key);
    if ($cached !== false && is_array($cached)) {
        return $cached;
    }

    $data = [
        'attributes' => [],
        'categories' => [],
        'tags' => [],
        'brands' => [],
        'authors' => [],
        'custom_fields' => [],
        'stock_status' => [],
        'sale_status' => []
    ];

    // Query for taxonomies (categories, tags, brands, attributes)
    $query = $wpdb->prepare("
        SELECT t.term_id, t.name, t.slug, tr.object_id, tt.taxonomy, a.attribute_name, a.attribute_label, tt.parent
        FROM {$wpdb->prefix}terms AS t
        INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies AS a ON tt.taxonomy = CONCAT('pa_', a.attribute_name)
        INNER JOIN {$wpdb->prefix}posts AS p ON tr.object_id = p.ID
        LEFT JOIN {$wpdb->prefix}termmeta AS tm ON t.term_id = tm.term_id AND tm.meta_key = 'order'
        WHERE (tt.taxonomy IN (%s, %s, %s) OR a.attribute_name IS NOT NULL)
        AND p.post_type = 'product' 
        AND p.post_status = 'publish'
        ORDER BY CAST(tm.meta_value AS UNSIGNED), t.name
    ", 'product_cat', 'product_tag', 'product_brand');

    $results = $wpdb->get_results($query, ARRAY_A);

    if (!empty($results)) {
        foreach ($results as $row) {
            $term_id = $row['term_id'];
            $taxonomy = $row['taxonomy'];

            if ($taxonomy === 'product_cat') {
                $data['categories'][$term_id] = $data['categories'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => rawurldecode($row['slug']),
                    'parent' => $row['parent'],
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['categories'][$term_id]['products'])) {
                    $data['categories'][$term_id]['products'][] = $row['object_id'];
                }
            } elseif ($taxonomy === 'product_tag') {
                $data['tags'][$term_id] = $data['tags'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => rawurldecode($row['slug']),
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['tags'][$term_id]['products'])) {
                    $data['tags'][$term_id]['products'][] = $row['object_id'];
                }
            } elseif ($taxonomy === 'product_brand') {
                $data['brands'][$term_id] = $data['brands'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => rawurldecode($row['slug']),
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['brands'][$term_id]['products'])) {
                    $data['brands'][$term_id]['products'][] = $row['object_id'];
                }
            } elseif (!empty($row['attribute_name'])) {
                $attr_name = $row['attribute_name'];
                $data['attributes'][$attr_name] = $data['attributes'][$attr_name] ?? [
                    'attribute_label' => $row['attribute_label'],
                    'attribute_name' => $attr_name,
                    'terms' => []
                ];

                // Store terms directly in arrays for faster lookups
                $data['attributes'][$attr_name]['terms'][$term_id] = $data['attributes'][$attr_name]['terms'][$term_id] ?? [
                    'term_id' => $term_id,
                    'name' => $row['name'],
                    'slug' => rawurldecode($row['slug']),
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['attributes'][$attr_name]['terms'][$term_id]['products'])) {
                    $data['attributes'][$attr_name]['terms'][$term_id]['products'][] = $row['object_id'];
                }
            }
        }
    }


     // Keep parent categories available when children contain products.
    if (!empty($data['categories'])) {
        $category_hierarchy_query = "
            SELECT t.term_id, t.name, t.slug, tt.parent, tm.meta_value AS menu_order
            FROM {$wpdb->prefix}terms AS t
            INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id
            LEFT JOIN {$wpdb->prefix}termmeta AS tm ON t.term_id = tm.term_id AND tm.meta_key = 'order'
            WHERE tt.taxonomy = 'product_cat'
        ";
        $category_hierarchy_results = $wpdb->get_results($category_hierarchy_query, ARRAY_A);

        $category_hierarchy = [];
        if (!empty($category_hierarchy_results)) {
            foreach ($category_hierarchy_results as $row) {
                $category_hierarchy[intval($row['term_id'])] = [
                    'name' => $row['name'],
                    'slug' => rawurldecode($row['slug']),
                    'parent' => intval($row['parent']),
                    'menu_order' => (isset($row['menu_order']) && $row['menu_order'] !== '' && is_numeric($row['menu_order']))
                        ? intval($row['menu_order'])
                        : 0,
                ];
            }
        }

        $category_product_sets = [];
        foreach ($data['categories'] as $term_id => $category) {
            $term_id = intval($term_id);
            $category_product_sets[$term_id] = [];

            foreach ($category['products'] as $product_id) {
                $category_product_sets[$term_id][intval($product_id)] = true;
            }

            $parent_id = intval($category['parent']);
            $visited = [];
            while ($parent_id > 0 && isset($category_hierarchy[$parent_id]) && !isset($visited[$parent_id])) {
                $visited[$parent_id] = true;

                if (!isset($data['categories'][$parent_id])) {
                    $data['categories'][$parent_id] = [
                        'name' => $category_hierarchy[$parent_id]['name'],
                        'slug' => $category_hierarchy[$parent_id]['slug'],
                        'parent' => $category_hierarchy[$parent_id]['parent'],
                        'products' => [],
                    ];
                }

                if (!isset($category_product_sets[$parent_id])) {
                    $category_product_sets[$parent_id] = [];
                }

                $parent_id = intval($category_hierarchy[$parent_id]['parent']);
            }
        }

        foreach ($category_product_sets as $term_id => $product_set) {
            if (empty($product_set)) {
                continue;
            }

            $parent_id = isset($data['categories'][$term_id]['parent']) ? intval($data['categories'][$term_id]['parent']) : 0;
            $visited = [];
            while ($parent_id > 0 && isset($data['categories'][$parent_id]) && !isset($visited[$parent_id])) {
                $visited[$parent_id] = true;

                foreach ($product_set as $product_id => $_) {
                    $category_product_sets[$parent_id][$product_id] = true;
                }

                $parent_id = intval($data['categories'][$parent_id]['parent']);
            }
        }

        foreach ($category_product_sets as $term_id => $product_set) {
            $data['categories'][$term_id]['products'] = array_map('intval', array_keys($product_set));
        }

        // Keep category output in WooCommerce menu order, then by name.
        $category_names = [];
        foreach ($data['categories'] as $term_id => $category) {
            $category_names[intval($term_id)] = $category['name'] ?? '';
        }

        uksort($data['categories'], function ($a, $b) use ($category_hierarchy, $category_names) {
            $a = intval($a);
            $b = intval($b);

            $order_a = isset($category_hierarchy[$a]['menu_order']) ? intval($category_hierarchy[$a]['menu_order']) : 0;
            $order_b = isset($category_hierarchy[$b]['menu_order']) ? intval($category_hierarchy[$b]['menu_order']) : 0;

            if ($order_a !== $order_b) {
                return $order_a <=> $order_b;
            }

            $name_cmp = strcasecmp($category_names[$a] ?? '', $category_names[$b] ?? '');
            if ($name_cmp !== 0) {
                return $name_cmp;
            }

            return $a <=> $b;
        });
    }

    // Separate query for authors (post_author)
    $authors_query = "
        SELECT p.post_author, u.display_name, u.user_login, p.ID as product_id
        FROM {$wpdb->prefix}posts p
        INNER JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
        WHERE p.post_type = 'product' 
        AND p.post_status = 'publish'
        AND p.post_author > 0
        ORDER BY u.display_name
    ";

    $authors_results = $wpdb->get_results($authors_query, ARRAY_A);

    if (!empty($authors_results)) {
        foreach ($authors_results as $row) {
            $author_id = $row['post_author'];
            $product_id = $row['product_id'];

            $data['authors'][$author_id] = $data['authors'][$author_id] ?? [
                'name' => $row['display_name'],
                'slug' => $row['user_login'],
                'products' => []
            ];

            if (!in_array($product_id, $data['authors'][$author_id]['products'])) {
                $data['authors'][$author_id]['products'][] = $product_id;
            }
        }
    }

    // Improved Query for Stock Status and Sale Status (handling variable products correctly)
    $stock_sale_query = "
        SELECT DISTINCT 
            p.ID as product_id,
            p.post_title,
            -- Get actual product type
            COALESCE(p_type.meta_value, 
                CASE WHEN EXISTS (
                    SELECT 1 FROM {$wpdb->prefix}posts v 
                    WHERE v.post_parent = p.ID AND v.post_type = 'product_variation' AND v.post_status = 'publish'
                ) THEN 'variable' ELSE 'simple' END
            ) as actual_product_type,
            p_stock.meta_value as parent_stock_status,
            p_sale.meta_value as parent_sale_price,  
            p_regular.meta_value as parent_regular_price,
            -- Check if any variation has sale price for variable products
            CASE 
                WHEN COALESCE(p_type.meta_value, 
                    CASE WHEN EXISTS (
                        SELECT 1 FROM {$wpdb->prefix}posts v 
                        WHERE v.post_parent = p.ID AND v.post_type = 'product_variation' AND v.post_status = 'publish'
                    ) THEN 'variable' ELSE 'simple' END
                ) = 'variable' THEN
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM {$wpdb->prefix}posts v
                            INNER JOIN {$wpdb->prefix}postmeta v_sale ON v.ID = v_sale.post_id AND v_sale.meta_key = '_sale_price'
                            INNER JOIN {$wpdb->prefix}postmeta v_regular ON v.ID = v_regular.post_id AND v_regular.meta_key = '_regular_price'
                            WHERE v.post_parent = p.ID 
                            AND v.post_type = 'product_variation' 
                            AND v.post_status = 'publish'
                            AND v_sale.meta_value IS NOT NULL 
                            AND v_sale.meta_value != ''
                            AND v_sale.meta_value != '0'
                            AND CAST(v_sale.meta_value AS DECIMAL(10,2)) > 0
                            AND v_regular.meta_value IS NOT NULL
                            AND v_regular.meta_value != ''
                            AND CAST(v_sale.meta_value AS DECIMAL(10,2)) < CAST(v_regular.meta_value AS DECIMAL(10,2))
                        ) THEN 1
                        ELSE 0
                    END
                ELSE
                    CASE 
                        WHEN p_sale.meta_value IS NOT NULL 
                            AND p_sale.meta_value != '' 
                            AND p_sale.meta_value != '0'
                            AND CAST(p_sale.meta_value AS DECIMAL(10,2)) > 0
                            AND p_regular.meta_value IS NOT NULL
                            AND p_regular.meta_value != ''
                            AND CAST(p_sale.meta_value AS DECIMAL(10,2)) < CAST(p_regular.meta_value AS DECIMAL(10,2))
                        THEN 1
                        ELSE 0
                    END
            END as is_on_sale,
            -- Check stock status for variable products
            CASE 
                WHEN COALESCE(p_type.meta_value, 
                    CASE WHEN EXISTS (
                        SELECT 1 FROM {$wpdb->prefix}posts v 
                        WHERE v.post_parent = p.ID AND v.post_type = 'product_variation' AND v.post_status = 'publish'
                    ) THEN 'variable' ELSE 'simple' END
                ) = 'variable' THEN
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM {$wpdb->prefix}posts v
                            LEFT JOIN {$wpdb->prefix}postmeta v_stock ON v.ID = v_stock.post_id AND v_stock.meta_key = '_stock_status'
                            WHERE v.post_parent = p.ID 
                            AND v.post_type = 'product_variation' 
                            AND v.post_status = 'publish'
                            AND (v_stock.meta_value = 'instock' OR v_stock.meta_value IS NULL)
                        ) THEN 'instock'
                        ELSE 'outofstock'
                    END
                ELSE COALESCE(p_stock.meta_value, 'instock')
            END as final_stock_status
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta p_stock ON p.ID = p_stock.post_id AND p_stock.meta_key = '_stock_status'
        LEFT JOIN {$wpdb->prefix}postmeta p_sale ON p.ID = p_sale.post_id AND p_sale.meta_key = '_sale_price'
        LEFT JOIN {$wpdb->prefix}postmeta p_regular ON p.ID = p_regular.post_id AND p_regular.meta_key = '_regular_price'
        LEFT JOIN {$wpdb->prefix}postmeta p_type ON p.ID = p_type.post_id AND p_type.meta_key = '_product_type'
        WHERE p.post_type = 'product' 
        AND p.post_status = 'publish'
    ";

    $stock_sale_results = $wpdb->get_results($stock_sale_query, ARRAY_A);

    global $dapfforwc_styleoptions;

    // Initialize stock status and sale status arrays
    $data['stock_status'] = [
        0 => ['name' => ($dapfforwc_styleoptions["stock_status_text"]["instock"] ?? 'In Stock'), 'slug' => 'instock', 'products' => []],
        1 => ['name' => ($dapfforwc_styleoptions["stock_status_text"]["outofstock"] ?? 'Out of Stock'), 'slug' => 'outofstock', 'products' => []]
    ];

    $data['sale_status'] = [
        0 => ['name' => ($dapfforwc_styleoptions["sale_status_text"]["onsale"] ?? 'On Sale'), 'slug' => 'onsale', 'products' => []],
        1 => ['name' => ($dapfforwc_styleoptions["sale_status_text"]["notonsale"] ?? 'Not on Sale'), 'slug' => 'notonsale', 'products' => []]
    ];

    if (!empty($stock_sale_results)) {
        foreach ($stock_sale_results as $row) {
            $product_id = $row['product_id'];
            $product_title = $row['post_title'];
            $actual_type = $row['actual_product_type'];
            $stock_status = $row['final_stock_status'];
            $is_on_sale = intval($row['is_on_sale']);

            // Determine stock status
            if ($stock_status === 'instock') {
                $data['stock_status'][0]['products'][] = $product_id;
            } else {
                $data['stock_status'][1]['products'][] = $product_id;
            }

            // Determine sale status
            if ($is_on_sale === 1) {
                $data['sale_status'][0]['products'][] = $product_id;
            } else {
                $data['sale_status'][1]['products'][] = $product_id;
            }
        }
    }

    // Query for custom fields (postmeta)
    $custom_fields_query = "
        SELECT pm.meta_key, pm.meta_value, pm.post_id
        FROM {$wpdb->prefix}postmeta pm
        INNER JOIN {$wpdb->prefix}posts p ON pm.post_id = p.ID
        WHERE p.post_type = 'product' 
        AND p.post_status = 'publish'
        AND pm.meta_key NOT LIKE '\\_%'  -- Exclude WordPress internal meta keys
        AND pm.meta_key NOT IN ('_visibility', '_stock_status', '_manage_stock', '_backorders', '_sold_individually')  -- Exclude WooCommerce internal fields
        AND pm.meta_value != ''
        AND pm.meta_value IS NOT NULL
        ORDER BY pm.meta_key, pm.meta_value
    ";

    $custom_fields_results = $wpdb->get_results($custom_fields_query, ARRAY_A);

    if (!empty($custom_fields_results)) {
        foreach ($custom_fields_results as $row) {
            $meta_key = $row['meta_key'];
            $meta_value = $row['meta_value'];
            $product_id = $row['post_id'];

            // Initialize custom field structure if not exists
            $data['custom_fields'][$meta_key] = $data['custom_fields'][$meta_key] ?? [
                'label' => ucwords(str_replace(['_', '-'], ' ', $meta_key)), // Generate human-readable label
                'name' => $meta_key,
                'terms' => []
            ];

            // Create a slug from the meta value
            $value_slug = sanitize_title($meta_value);

            // Initialize term structure if not exists
            $data['custom_fields'][$meta_key]['terms'][$value_slug] = $data['custom_fields'][$meta_key]['terms'][$value_slug] ?? [
                'name' => $meta_value,
                'slug' => $value_slug,
                'products' => []
            ];

            // Add product to this custom field term if not already present
            if (!in_array($product_id, $data['custom_fields'][$meta_key]['terms'][$value_slug]['products'])) {
                $data['custom_fields'][$meta_key]['terms'][$value_slug]['products'][] = $product_id;
            }
        }
    }

    // Convert associative term arrays to indexed arrays
    foreach ($data['attributes'] as $key => $attr) {
        $data['attributes'][$key]['terms'] = array_values($attr['terms']);
    }

    // Convert associative custom field term arrays to indexed arrays
    foreach ($data['custom_fields'] as $key => $field) {
        $data['custom_fields'][$key]['terms'] = array_values($field['terms']);
    }

    // Save to cache
    set_transient($cache_key, $data, 12 * HOUR_IN_SECONDS);

    return $data;
}

function dapfforwc_get_woocommerce_product_details()
{
    global $wpdb;
    $cache_key = 'dapfforwc_product_details_cache_v1';
    $cached = get_transient($cache_key);
    if ($cached !== false && is_array($cached)) {
        return $cached;
    }

    // Query for all products with their meta data, categories, and required fields
    $query = "
        SELECT p.ID, p.post_title, p.menu_order, p.post_date AS publish_date, p.post_author,
               u.display_name AS author_name,
               MAX(CASE WHEN pm.meta_key = '_price' THEN pm.meta_value END) AS price,
               MAX(CASE WHEN pm.meta_key = '_sale_price' THEN pm.meta_value END) AS sale_price,
               MAX(CASE WHEN pm.meta_key = '_regular_price' THEN pm.meta_value END) AS regular_price,
               MAX(CASE WHEN pm.meta_key = '_min_variation_price' THEN pm.meta_value END) AS min_variation_price,
               MAX(CASE WHEN pm.meta_key = '_max_variation_price' THEN pm.meta_value END) AS max_variation_price,
               MAX(CASE WHEN pm.meta_key = '_min_variation_regular_price' THEN pm.meta_value END) AS min_variation_regular_price,
               MAX(CASE WHEN pm.meta_key = '_min_variation_sale_price' THEN pm.meta_value END) AS min_variation_sale_price,
               MAX(CASE WHEN pm.meta_key = '_wc_average_rating' THEN pm.meta_value END) AS average_rating,
               MAX(CASE WHEN pm.meta_key = '_product_type' THEN pm.meta_value END) AS product_type,
               MAX(CASE WHEN pm.meta_key = '_sku' THEN pm.meta_value END) AS sku,
               MAX(CASE WHEN pm.meta_key = '_stock_status' THEN pm.meta_value END) AS stock_status,
               MAX(CASE WHEN pm.meta_key = '_length' THEN pm.meta_value END) AS length,
               MAX(CASE WHEN pm.meta_key = '_width' THEN pm.meta_value END) AS width,
               MAX(CASE WHEN pm.meta_key = '_height' THEN pm.meta_value END) AS height,
               MAX(CASE WHEN pm.meta_key = '_weight' THEN pm.meta_value END) AS weight,
               (SELECT GROUP_CONCAT(t.name SEPARATOR ', ') FROM {$wpdb->prefix}term_relationships tr
                INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->prefix}terms t ON t.term_id = tt.term_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_cat') AS categories,
               (SELECT GROUP_CONCAT(t.slug SEPARATOR ', ') FROM {$wpdb->prefix}term_relationships tr
                INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->prefix}terms t ON t.term_id = tt.term_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_cat') AS category_slugs,
               (SELECT GROUP_CONCAT(t.name SEPARATOR ', ') FROM {$wpdb->prefix}term_relationships tr
                INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->prefix}terms t ON t.term_id = tt.term_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_brand') AS product_brands
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
        LEFT JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
        GROUP BY p.ID
    ";

    $results = $wpdb->get_results($query, ARRAY_A);
    $products = [];

    if (!empty($results)) {
        foreach ($results as $row) {
            $product_id = $row['ID'];

            // Get the actual product type - WooCommerce might store it differently
            $actual_product_type = '';

            // First try from the main query result
            if (!empty($row['product_type'])) {
                $actual_product_type = $row['product_type'];
            } else {
                // If not found, query directly for this product's type
                $type_query = $wpdb->prepare("
                    SELECT meta_value 
                    FROM {$wpdb->prefix}postmeta 
                    WHERE post_id = %d AND meta_key = '_product_type'
                ", $product_id);
                $direct_type = $wpdb->get_var($type_query);
                $actual_product_type = $direct_type ?: 'simple';
            }

            // Also check if product has variations (alternative detection method)
            $has_variations = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM {$wpdb->prefix}posts 
                WHERE post_parent = %d AND post_type = 'product_variation' AND post_status = 'publish'
            ", $product_id));

            // If we found variations but type says simple, it's likely variable
            if ($has_variations > 0 && $actual_product_type === 'simple') {
                $actual_product_type = 'variable';
            }

            // Determine product type and pricing
            $product_type = $actual_product_type;
            $price = '';
            $regular_price = '';
            $sale_price = '';
            $sale_active = false;
            $discount_percentage = 0;

            if ($product_type === 'variable') {

                // For variable products, we need to get prices directly from variations
                $variation_query = $wpdb->prepare("
                    SELECT 
                        v.ID as variation_id,
                        v.post_title as variation_title,
                        v_price.meta_value as current_price,
                        v_regular.meta_value as regular_price_val,
                        v_sale.meta_value as sale_price_val
                    FROM {$wpdb->prefix}posts v
                    LEFT JOIN {$wpdb->prefix}postmeta v_price ON v.ID = v_price.post_id AND v_price.meta_key = '_price'
                    LEFT JOIN {$wpdb->prefix}postmeta v_regular ON v.ID = v_regular.post_id AND v_regular.meta_key = '_regular_price'
                    LEFT JOIN {$wpdb->prefix}postmeta v_sale ON v.ID = v_sale.post_id AND v_sale.meta_key = '_sale_price'
                    WHERE v.post_parent = %d 
                    AND v.post_type = 'product_variation' 
                    AND v.post_status = 'publish'
                ", $product_id);

                $variation_data = $wpdb->get_results($variation_query, ARRAY_A);

                $all_prices = [];
                $all_regular_prices = [];
                $all_sale_prices = [];
                $has_any_sale = false;

                foreach ($variation_data as $variation) {
                    $variation_id = $variation['variation_id'];
                    $current_price = $variation['current_price'];
                    $regular_price_val = $variation['regular_price_val'];
                    $sale_price_val = $variation['sale_price_val'];

                    // Collect current prices (what customer actually pays)
                    if (!empty($current_price) && is_numeric($current_price) && floatval($current_price) > 0) {
                        $all_prices[] = floatval($current_price);
                    }

                    // Collect regular prices
                    if (!empty($regular_price_val) && is_numeric($regular_price_val) && floatval($regular_price_val) > 0) {
                        $all_regular_prices[] = floatval($regular_price_val);
                    }

                    // Check if this variation has a sale price
                    if (
                        !empty($sale_price_val) && is_numeric($sale_price_val) && floatval($sale_price_val) > 0 &&
                        !empty($regular_price_val) && is_numeric($regular_price_val) &&
                        floatval($sale_price_val) < floatval($regular_price_val)
                    ) {

                        $has_any_sale = true;
                        $all_sale_prices[] = floatval($sale_price_val);
                    }
                }

                // Set prices based on variation data
                $price = !empty($all_prices) ? strval(min($all_prices)) : ($row['min_variation_price'] ?: '');
                $regular_price = !empty($all_regular_prices) ? strval(min($all_regular_prices)) : ($row['min_variation_regular_price'] ?: '');
                $sale_active = $has_any_sale;

                if ($has_any_sale && !empty($all_sale_prices)) {
                    $sale_price = strval(min($all_sale_prices));
                    $min_sale = min($all_sale_prices);
                    $corresponding_regular = !empty($all_regular_prices) ? min($all_regular_prices) : 0;

                    if ($corresponding_regular > 0) {
                        $discount_percentage = round((($corresponding_regular - $min_sale) / $corresponding_regular) * 100, 2);
                    }
                } else {
                    $sale_price = '';
                }
            } else {

                // Simple product logic
                $regular_price = $row['regular_price'] ?: '';
                $sale_price = $row['sale_price'] ?: '';
                $price = $row['price'] ?: $regular_price;

                $sale_active = !empty($sale_price) && $sale_price !== '0' && !empty($regular_price) &&
                    floatval($sale_price) > 0 && floatval($sale_price) < floatval($regular_price);

                // Calculate discount percentage
                if ($sale_active) {
                    $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100, 2);
                }
            }

            // Get rating
            $rating = floatval($row['average_rating']) ?: 0;

            // Get product categories
            $product_category = [];
            if (!empty($row['categories']) && !empty($row['category_slugs'])) {
                $category_names = explode(', ', $row['categories']);
                $category_slugs = explode(', ', $row['category_slugs']);
                $product_category = array_map(function ($name, $slug) {
                    return ['name' => $name, 'slug' => $slug];
                }, $category_names, $category_slugs);
            }

            // Get stock status - handle variable products
            $stock_status = 'instock'; // default
            if ($product_type === 'variable') {
                // Check if any variation is in stock
                $variation_stock_query = $wpdb->prepare("
                    SELECT COUNT(*) as in_stock_count
                    FROM {$wpdb->prefix}posts v
                    LEFT JOIN {$wpdb->prefix}postmeta v_stock ON v.ID = v_stock.post_id AND v_stock.meta_key = '_stock_status'
                    WHERE v.post_parent = %d 
                    AND v.post_type = 'product_variation' 
                    AND v.post_status = 'publish'
                    AND (v_stock.meta_value = 'instock' OR v_stock.meta_value IS NULL)
                ", $product_id);

                $stock_result = $wpdb->get_var($variation_stock_query);
                $stock_status = ($stock_result > 0) ? 'instock' : 'outofstock';
            } else {
                $stock_status = $row['stock_status'] ?: 'instock';
            }

            // Get additional custom meta fields
            $custom_meta = [];
            $custom_meta_query = $wpdb->prepare("
                SELECT meta_key, meta_value 
                FROM {$wpdb->prefix}postmeta 
                WHERE post_id = %d 
                AND meta_key NOT LIKE '_%%' 
                AND meta_key NOT IN ('_edit_lock', '_edit_last')
                AND meta_value IS NOT NULL 
                AND meta_value != ''
            ", $product_id);

            $custom_meta_results = $wpdb->get_results($custom_meta_query, ARRAY_A);
            foreach ($custom_meta_results as $meta) {
                $custom_meta[$meta['meta_key']] = $meta['meta_value'];
            }

            $thumbnail = get_the_post_thumbnail_url($product_id, 'thumbnail');

            $products[$product_id] = [
                'ID' => $product_id,
                'post_title' => $row['post_title'],
                'publish_date' => $row['publish_date'],
                'price' => $price,
                'regular_price' => $regular_price,
                'sale_price' => $sale_price,
                'rating' => $rating,
                'menu_order' => intval($row['menu_order']),
                'on_sale' => $sale_active,
                'discount_percentage' => $discount_percentage,
                'product_sku' => $row['sku'] ?: '',
                'product_stock' => $stock_status,
                'product_type' => $product_type,
                'product_category' => $product_category,
                'product_brand' => $row['product_brands'] ?: '',
                'author' => $row['author_name'] ?: '',
                'author_id' => intval($row['post_author']),
                'length' => $row['length'] ?: '',
                'width' => $row['width'] ?: '',
                'height' => $row['height'] ?: '',
                'weight' => $row['weight'] ?: '',
                'custom_meta' => $custom_meta,
                'thumbnail' => $thumbnail ?: '',
            ];
        }
    }

    // Convert to indexed array for better JSON compatibility
    $product_data = ['products' => $products];

    // Save to cache
    set_transient($cache_key, $product_data, 12 * HOUR_IN_SECONDS);

    return $product_data;
}

/**
 * Comprehensive WooCommerce Cache Clearing System
 * Covers all product-related changes including terms, categories, tags, imports, etc.
 */

// 1. Product updates (create, update, delete, status change)
add_action('save_post_product', 'dapfforwc_clear_woocommerce_caches');
add_action('wp_trash_post', function ($post_id) {
    if (get_post_type($post_id) === 'product') {
        dapfforwc_clear_woocommerce_caches();
    }
});
add_action('untrashed_post', function ($post_id) {
    if (get_post_type($post_id) === 'product') {
        dapfforwc_clear_woocommerce_caches();
    }
});
add_action('deleted_post', function ($post_id) {
    if (get_post_type($post_id) === 'product') {
        dapfforwc_clear_woocommerce_caches();
    }
});
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if ($post->post_type === 'product' && $new_status !== $old_status) {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 3);

// 2. Product variations
add_action('save_post_product_variation', 'dapfforwc_clear_woocommerce_caches');
add_action('wp_trash_post', function ($post_id) {
    if (get_post_type($post_id) === 'product_variation') {
        dapfforwc_clear_woocommerce_caches();
    }
});
add_action('deleted_post', function ($post_id) {
    if (get_post_type($post_id) === 'product_variation') {
        dapfforwc_clear_woocommerce_caches();
    }
});

// 3. Terms (categories, tags, attributes) - create, update, delete
add_action('created_term', function ($term_id, $tt_id, $taxonomy) {
    if (dapfforwc_is_woocommerce_taxonomy($taxonomy)) {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 3);

add_action('edited_term', function ($term_id, $tt_id, $taxonomy) {
    if (dapfforwc_is_woocommerce_taxonomy($taxonomy)) {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 3);

add_action('delete_term', function ($term_id, $tt_id, $taxonomy) {
    if (dapfforwc_is_woocommerce_taxonomy($taxonomy)) {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 3);

// 4. Product meta updates (price, stock, attributes, etc.)
add_action('updated_post_meta', function ($meta_id, $post_id, $meta_key, $meta_value) {
    if (get_post_type($post_id) === 'product' || get_post_type($post_id) === 'product_variation') {
        // Clear cache for important product meta fields
        $important_meta_keys = [
            '_price',
            '_regular_price',
            '_sale_price',
            '_stock',
            '_stock_status',
            '_manage_stock',
            '_weight',
            '_length',
            '_width',
            '_height',
            '_sku',
            '_featured',
            '_visibility',
            '_downloadable',
            '_virtual',
            '_product_attributes'
        ];

        if (in_array($meta_key, $important_meta_keys) || strpos($meta_key, '_') === 0) {
            dapfforwc_clear_woocommerce_caches();
        }
    }
}, 10, 4);

add_action('added_post_meta', function ($meta_id, $post_id, $meta_key, $meta_value) {
    if (get_post_type($post_id) === 'product' || get_post_type($post_id) === 'product_variation') {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 4);

add_action('deleted_post_meta', function ($meta_ids, $post_id, $meta_key, $meta_value) {
    if (get_post_type($post_id) === 'product' || get_post_type($post_id) === 'product_variation') {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 4);

// 5. WooCommerce specific hooks
// Product stock changes
add_action('woocommerce_product_set_stock', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_variation_set_stock', 'dapfforwc_clear_woocommerce_caches');

// Product visibility changes
add_action('woocommerce_product_set_visibility', 'dapfforwc_clear_woocommerce_caches');

// Product feature status changes
add_action('woocommerce_product_set_featured', 'dapfforwc_clear_woocommerce_caches');

// 6. Bulk operations and imports
add_action('woocommerce_product_bulk_edit_save', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_product_quick_edit_save', 'dapfforwc_clear_woocommerce_caches');

// Product import/export
add_action('woocommerce_product_import_inserted_product_object', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_product_import_updated_product_object', 'dapfforwc_clear_woocommerce_caches');

// CSV import completion
add_action('woocommerce_product_csv_importer_done', 'dapfforwc_clear_woocommerce_caches');

// 7. Attribute-specific actions
add_action('woocommerce_attribute_added', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_attribute_updated', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_attribute_deleted', 'dapfforwc_clear_woocommerce_caches');

// 8. Category/Tag assignments
add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids) {
    if (get_post_type($object_id) === 'product' && dapfforwc_is_woocommerce_taxonomy($taxonomy)) {
        dapfforwc_clear_woocommerce_caches();
    }
}, 10, 6);

// 9. Third-party plugin compatibility
// Clear cache when other plugins update product data
add_action('woocommerce_process_product_meta', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_save_product_variation', 'dapfforwc_clear_woocommerce_caches');

// 10. Administrative actions
add_action('woocommerce_settings_saved', 'dapfforwc_clear_woocommerce_caches');
add_action('woocommerce_tax_settings_saved', 'dapfforwc_clear_woocommerce_caches');

// Helper function to check if taxonomy is WooCommerce related
function dapfforwc_is_woocommerce_taxonomy($taxonomy)
{
    $wc_taxonomies = [
        'product_cat',
        'product_tag',
        'product_shipping_class',
        'product_type',
        'product_visibility'
    ];

    // Include custom product attributes
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    foreach ($attribute_taxonomies as $attribute) {
        $wc_taxonomies[] = 'pa_' . $attribute->attribute_name;
    }

    return in_array($taxonomy, $wc_taxonomies);
}


function dapfforwc_get_shortcode_attributes_from_page($content, $shortcode)
{
    // Use regex to match the shortcode and capture its attributes
    preg_match_all('/\[' . preg_quote($shortcode, '/') . '([^]]*)\]/', $content, $matches);

    $attributes_list = [];
    foreach ($matches[1] as $shortcode_instance) {
        // Clean up the attribute string and parse it
        $shortcode_instance = trim($shortcode_instance);
        $attributes_list[] = shortcode_parse_atts($shortcode_instance);
    }

    return $attributes_list;
}




/**
 * Extract product display attributes from various page builders
 * Supports: Elementor, WPBakery, Gutenberg, Divi, Beaver Builder, Oxygen
 */

if (!function_exists('dapfforwc_get_pagebuilder_product_attributes')) {
    /**
     * Main function to extract product attributes from all page builders
     * 
     * @param int $post_id Post ID to extract attributes from
     * @return array Array of product display settings
     */
    function dapfforwc_get_pagebuilder_product_attributes($post_id)
    {
        if (empty($post_id)) {
            return [];
        }

        $attributes = [];

        // Check each page builder
        $attributes = array_merge($attributes, dapfforwc_get_elementor_product_attributes($post_id));
        $attributes = array_merge($attributes, dapfforwc_get_wpbakery_product_attributes($post_id));
        $attributes = array_merge($attributes, dapfforwc_get_gutenberg_product_attributes($post_id));
        $attributes = array_merge($attributes, dapfforwc_get_divi_product_attributes($post_id));
        $attributes = array_merge($attributes, dapfforwc_get_beaverbuilder_product_attributes($post_id));
        $attributes = array_merge($attributes, dapfforwc_get_oxygen_product_attributes($post_id));

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_elementor_product_attributes')) {
    /**
     * Extract product attributes from Elementor
     */
    function dapfforwc_get_elementor_product_attributes($post_id)
    {
        if (!class_exists('\Elementor\Plugin')) {
            return [];
        }

        $attributes = [];

        // Get Elementor data
        $elementor_data = get_post_meta($post_id, '_elementor_data', true);

        if (empty($elementor_data)) {
            return [];
        }

        $data = json_decode($elementor_data, true);
        if (!is_array($data)) {
            return [];
        }

        // Recursively search for product widgets
        $attributes = dapfforwc_parse_elementor_elements($data);

        return $attributes;
    }
}

if (!function_exists('dapfforwc_parse_elementor_elements')) {
    /**
     * Recursively parse Elementor elements - Works with ANY widget that has product query
     */
    function dapfforwc_parse_elementor_elements($elements)
    {
        $attributes = [];

        foreach ($elements as $element) {
            $settings = $element['settings'] ?? [];

            // Check if this element has ANY product-related query settings
            $has_product_query = false;

            // Detection methods for product queries:
            // 1. Check for post_type = 'product'
            if (!empty($settings['post_type']) && $settings['post_type'] === 'product') {
                $has_product_query = true;
            }
            // 2. Check for query_post_type = 'product' (Loop Grid/Carousel)
            if (!empty($settings['query_post_type']) && $settings['query_post_type'] === 'product') {
                $has_product_query = true;
            }
            // 3. Check for post_query_post_type = 'product' (Loop Grid new format)
            if (!empty($settings['post_query_post_type']) && $settings['post_query_post_type'] === 'product') {
                $has_product_query = true;
            }
            // 4. Check for WooCommerce-specific settings
            if (
                !empty($settings['query_include_term_ids']) || !empty($settings['query_include_tags']) ||
                !empty($settings['query_include_attributes']) || isset($settings['query_include_product_cat']) ||
                !empty($settings['post_query_include_term_ids']) || !empty($settings['post_query_include_tags'])
            ) {
                $has_product_query = true;
            }
            // 5. Check widget type contains 'product' or 'woocommerce'
            $widget_type = $element['widgetType'] ?? '';
            if (
                stripos($widget_type, 'product') !== false || stripos($widget_type, 'woocommerce') !== false ||
                stripos($widget_type, 'wc-') !== false
            ) {
                $has_product_query = true;
            }

            // If this element has product query, extract its settings
            if ($has_product_query) {
                $attr = [];

                // Posts per page - check multiple possible keys
                $posts_per_page = $settings['posts_per_page'] ??
                    $settings['query_posts_per_page'] ??
                    $settings['post_query_posts_per_page'] ??
                    $settings['limit'] ??
                    $settings['per_page'] ??
                    null;

                if (!empty($posts_per_page)) {
                    $attr['limit'] = $attr['per_page'] = intval($posts_per_page);
                }

                // Rows and columns
                if (!empty($settings['rows'])) {
                    $attr['rows'] = intval($settings['rows']);
                }
                if (!empty($settings['columns'])) {
                    $attr['columns'] = intval($settings['columns']);
                }

                // Order settings - check multiple keys
                $orderby = $settings['orderby'] ??
                    $settings['query_orderby'] ??
                    $settings['post_query_orderby'] ??
                    null;
                if (!empty($orderby)) {
                    $attr['orderby'] = sanitize_text_field($orderby);
                }

                $order = $settings['order'] ??
                    $settings['query_order'] ??
                    $settings['post_query_order'] ??
                    null;
                if (!empty($order)) {
                    $attr['order'] = sanitize_text_field($order);
                }

                // Category filters - check multiple possible keys
                $category_ids = $settings['query_include_term_ids'] ??
                    $settings['post_query_include_term_ids'] ??
                    $settings['query_include_product_cat'] ??
                    $settings['include_categories'] ??
                    $settings['category'] ??
                    null;

                if (!empty($category_ids)) {
                    $categories = is_array($category_ids) ? $category_ids : explode(',', $category_ids);
                    $category_slugs = [];

                    foreach ($categories as $cat_id) {
                        // Handle both term IDs and slugs
                        if (is_numeric($cat_id)) {
                            $term = get_term($cat_id, 'product_cat');
                            if ($term && !is_wp_error($term)) {
                                $category_slugs[] = $term->slug;
                            }
                        } else {
                            $category_slugs[] = sanitize_title($cat_id);
                        }
                    }

                    if (!empty($category_slugs)) {
                        $attr['category'] = implode(',', $category_slugs);
                    }
                }

                // Tag filters - check multiple possible keys
                $tag_ids = $settings['query_include_tags'] ??
                    $settings['post_query_include_tags'] ??
                    $settings['query_include_product_tag'] ??
                    $settings['include_tags'] ??
                    $settings['tags'] ??
                    null;

                if (!empty($tag_ids)) {
                    $tags = is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids);
                    $tag_slugs = [];

                    foreach ($tags as $tag_id) {
                        // Handle both term IDs and slugs
                        if (is_numeric($tag_id)) {
                            $term = get_term($tag_id, 'product_tag');
                            if ($term && !is_wp_error($term)) {
                                $tag_slugs[] = $term->slug;
                            }
                        } else {
                            $tag_slugs[] = sanitize_title($tag_id);
                        }
                    }

                    if (!empty($tag_slugs)) {
                        $attr['tag'] = implode(',', $tag_slugs);
                    }
                }

                // Attribute filters - check multiple possible keys
                $attributes_data = $settings['query_include_attributes'] ??
                    $settings['post_query_include_attributes'] ??
                    $settings['include_attributes'] ??
                    $settings['attributes'] ??
                    null;

                if (!empty($attributes_data) && is_array($attributes_data)) {
                    foreach ($attributes_data as $attr_data) {
                        if (!empty($attr_data['taxonomy']) && !empty($attr_data['terms'])) {
                            $taxonomy = str_replace('pa_', '', $attr_data['taxonomy']);
                            $terms = is_array($attr_data['terms']) ? $attr_data['terms'] : explode(',', $attr_data['terms']);

                            $term_slugs = [];
                            foreach ($terms as $term_id) {
                                // Handle both term IDs and slugs
                                if (is_numeric($term_id)) {
                                    $term = get_term($term_id, 'pa_' . $taxonomy);
                                    if ($term && !is_wp_error($term)) {
                                        $term_slugs[] = $term->slug;
                                    }
                                } else {
                                    $term_slugs[] = sanitize_title($term_id);
                                }
                            }

                            if (!empty($term_slugs)) {
                                $attr['attribute'] = $taxonomy;
                                $attr['terms'] = implode(',', $term_slugs);
                            }
                        }
                    }
                }

                // Exclude categories/tags if needed
                $exclude_categories = $settings['query_exclude_term_ids'] ??
                    $settings['exclude_categories'] ??
                    null;
                if (!empty($exclude_categories)) {
                    $attr['exclude_category'] = is_array($exclude_categories) ?
                        implode(',', $exclude_categories) : $exclude_categories;
                }

                // Operator settings
                if (!empty($settings['cat_operator'])) {
                    $attr['cat_operator'] = sanitize_text_field($settings['cat_operator']);
                }
                if (!empty($settings['tag_operator'])) {
                    $attr['tag_operator'] = sanitize_text_field($settings['tag_operator']);
                }
                if (!empty($settings['terms_operator'])) {
                    $attr['terms_operator'] = sanitize_text_field($settings['terms_operator']);
                }

                if (!empty($attr)) {
                    $attributes[] = $attr;
                }
            }

            // Recursively check child elements
            if (!empty($element['elements'])) {
                $child_attributes = dapfforwc_parse_elementor_elements($element['elements']);
                $attributes = array_merge($attributes, $child_attributes);
            }
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_wpbakery_product_attributes')) {
    /**
     * Extract product attributes from WPBakery (Visual Composer)
     */
    function dapfforwc_get_wpbakery_product_attributes($post_id)
    {
        $content = get_post_field('post_content', $post_id);

        if (empty($content)) {
            return [];
        }

        $attributes = [];

        // WPBakery shortcodes for products
        $shortcodes = [
            'products',
            'product_category',
            'recent_products',
            'featured_products',
            'sale_products',
            'best_selling_products',
            'top_rated_products',
            'product_attribute'
        ];

        foreach ($shortcodes as $shortcode) {
            preg_match_all('/\[' . preg_quote($shortcode, '/') . '([^\]]*)\]/', $content, $matches);

            foreach ($matches[1] as $shortcode_attrs) {
                $attrs = shortcode_parse_atts(trim($shortcode_attrs));

                if (!empty($attrs)) {
                    // Normalize attribute names
                    $normalized = [];

                    if (isset($attrs['per_page']) || isset($attrs['limit'])) {
                        $normalized['per_page'] = $normalized['limit'] = intval($attrs['per_page'] ?? $attrs['limit']);
                    }
                    if (isset($attrs['columns'])) {
                        $normalized['columns'] = intval($attrs['columns']);
                    }
                    if (isset($attrs['orderby'])) {
                        $normalized['orderby'] = sanitize_text_field($attrs['orderby']);
                    }
                    if (isset($attrs['order'])) {
                        $normalized['order'] = sanitize_text_field($attrs['order']);
                    }
                    if (isset($attrs['category'])) {
                        $normalized['category'] = sanitize_text_field($attrs['category']);
                    }
                    if (isset($attrs['tag'])) {
                        $normalized['tag'] = sanitize_text_field($attrs['tag']);
                    }
                    if (isset($attrs['attribute'])) {
                        $normalized['attribute'] = sanitize_text_field($attrs['attribute']);
                    }
                    if (isset($attrs['terms'])) {
                        $normalized['terms'] = sanitize_text_field($attrs['terms']);
                    }
                    if (isset($attrs['ids'])) {
                        $normalized['ids'] = sanitize_text_field($attrs['ids']);
                    }

                    if (!empty($normalized)) {
                        $attributes[] = $normalized;
                    }
                }
            }
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_gutenberg_product_attributes')) {
    /**
     * Extract product attributes from Gutenberg blocks
     */
    function dapfforwc_get_gutenberg_product_attributes($post_id)
    {
        $content = get_post_field('post_content', $post_id);

        if (empty($content) || !has_blocks($content)) {
            return [];
        }

        $attributes = [];
        $blocks = parse_blocks($content);

        foreach ($blocks as $block) {
            $attributes = array_merge($attributes, dapfforwc_parse_gutenberg_block($block));
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_parse_gutenberg_block')) {
    /**
     * Recursively parse Gutenberg blocks
     */
    function dapfforwc_parse_gutenberg_block($block)
    {
        $attributes = [];

        // WooCommerce block names
        $product_blocks = [
            'woocommerce/products',
            'woocommerce/product-category',
            'woocommerce/product-tag',
            'woocommerce/handpicked-products',
            'woocommerce/products-by-attribute',
            'woocommerce/product-best-sellers',
            'woocommerce/product-new',
            'woocommerce/product-on-sale',
            'woocommerce/product-top-rated'
        ];

        if (in_array($block['blockName'], $product_blocks)) {
            $attrs = $block['attrs'] ?? [];
            $normalized = [];

            if (!empty($attrs['columns'])) {
                $normalized['columns'] = intval($attrs['columns']);
            }
            if (!empty($attrs['rows'])) {
                $normalized['rows'] = intval($attrs['rows']);
            }
            if (!empty($attrs['limit'])) {
                $normalized['limit'] = $normalized['per_page'] = intval($attrs['limit']);
            }
            if (!empty($attrs['orderby'])) {
                $normalized['orderby'] = sanitize_text_field($attrs['orderby']);
            }
            if (!empty($attrs['order'])) {
                $normalized['order'] = sanitize_text_field($attrs['order']);
            }

            // Category filters
            if (!empty($attrs['categories'])) {
                $category_ids = is_array($attrs['categories']) ? $attrs['categories'] : [$attrs['categories']];
                $category_slugs = [];

                foreach ($category_ids as $cat_id) {
                    $term = get_term($cat_id, 'product_cat');
                    if ($term && !is_wp_error($term)) {
                        $category_slugs[] = $term->slug;
                    }
                }

                if (!empty($category_slugs)) {
                    $normalized['category'] = implode(',', $category_slugs);
                }
            }

            // Tag filters
            if (!empty($attrs['tags'])) {
                $tag_ids = is_array($attrs['tags']) ? $attrs['tags'] : [$attrs['tags']];
                $tag_slugs = [];

                foreach ($tag_ids as $tag_id) {
                    $term = get_term($tag_id, 'product_tag');
                    if ($term && !is_wp_error($term)) {
                        $tag_slugs[] = $term->slug;
                    }
                }

                if (!empty($tag_slugs)) {
                    $normalized['tag'] = implode(',', $tag_slugs);
                }
            }

            // Attribute filters
            if (!empty($attrs['attributes'])) {
                $attr_data = $attrs['attributes'];
                if (is_array($attr_data) && !empty($attr_data[0])) {
                    $first_attr = $attr_data[0];
                    if (!empty($first_attr['id']) && !empty($first_attr['terms'])) {
                        $taxonomy = 'pa_' . $first_attr['id'];
                        $term_ids = is_array($first_attr['terms']) ? $first_attr['terms'] : [$first_attr['terms']];

                        $term_slugs = [];
                        foreach ($term_ids as $term_id) {
                            $term = get_term($term_id, $taxonomy);
                            if ($term && !is_wp_error($term)) {
                                $term_slugs[] = $term->slug;
                            }
                        }

                        if (!empty($term_slugs)) {
                            $normalized['attribute'] = $first_attr['id'];
                            $normalized['terms'] = implode(',', $term_slugs);
                        }
                    }
                }
            }

            if (!empty($normalized)) {
                $attributes[] = $normalized;
            }
        }

        // Recursively check inner blocks
        if (!empty($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $inner_block) {
                $inner_attributes = dapfforwc_parse_gutenberg_block($inner_block);
                $attributes = array_merge($attributes, $inner_attributes);
            }
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_divi_product_attributes')) {
    /**
     * Extract product attributes from Divi Builder
     */
    function dapfforwc_get_divi_product_attributes($post_id)
    {
        $content = get_post_field('post_content', $post_id);

        if (empty($content)) {
            return [];
        }

        $attributes = [];

        // Divi uses shortcodes similar to WPBakery
        $divi_shortcodes = [
            'et_pb_shop',
            'et_pb_wc_breadcrumb',
            'et_pb_wc_cart_notice',
            'et_pb_wc_description',
            'et_pb_wc_images',
            'et_pb_wc_price',
            'et_pb_wc_rating',
            'et_pb_wc_add_to_cart',
            'et_pb_wc_meta',
            'et_pb_wc_tabs',
            'et_pb_wc_upsells',
            'et_pb_wc_related_products'
        ];

        foreach ($divi_shortcodes as $shortcode) {
            preg_match_all('/\[' . preg_quote($shortcode, '/') . '([^\]]*)\]/', $content, $matches);

            foreach ($matches[1] as $shortcode_attrs) {
                $attrs = shortcode_parse_atts(trim($shortcode_attrs));

                if (!empty($attrs['posts_number'])) {
                    $normalized = [
                        'per_page' => intval($attrs['posts_number']),
                        'limit' => intval($attrs['posts_number'])
                    ];

                    if (!empty($attrs['orderby'])) {
                        $normalized['orderby'] = sanitize_text_field($attrs['orderby']);
                    }

                    $attributes[] = $normalized;
                }
            }
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_beaverbuilder_product_attributes')) {
    /**
     * Extract product attributes from Beaver Builder
     */
    function dapfforwc_get_beaverbuilder_product_attributes($post_id)
    {
        if (!class_exists('FLBuilderModel')) {
            return [];
        }

        $attributes = [];

        // Get Beaver Builder data
        $data = get_post_meta($post_id, '_fl_builder_data', true);

        if (empty($data) || !is_array($data)) {
            return [];
        }

        foreach ($data as $node) {
            if (!is_object($node) || empty($node->type) || $node->type !== 'module') {
                continue;
            }

            // Check for WooCommerce modules
            $wc_modules = [
                'woocommerce',
                'pp-woo-products',
                'pp-woo-categories'
            ];

            if (in_array($node->settings->type ?? '', $wc_modules)) {
                $settings = $node->settings;
                $normalized = [];

                if (!empty($settings->posts_per_page)) {
                    $normalized['per_page'] = $normalized['limit'] = intval($settings->posts_per_page);
                }
                if (!empty($settings->columns)) {
                    $normalized['columns'] = intval($settings->columns);
                }
                if (!empty($settings->order_by)) {
                    $normalized['orderby'] = sanitize_text_field($settings->order_by);
                }
                if (!empty($settings->order)) {
                    $normalized['order'] = sanitize_text_field($settings->order);
                }
                if (!empty($settings->category)) {
                    $normalized['category'] = sanitize_text_field($settings->category);
                }

                if (!empty($normalized)) {
                    $attributes[] = $normalized;
                }
            }
        }

        return $attributes;
    }
}

if (!function_exists('dapfforwc_get_oxygen_product_attributes')) {
    /**
     * Extract product attributes from Oxygen Builder
     */
    function dapfforwc_get_oxygen_product_attributes($post_id)
    {
        $shortcodes = get_post_meta($post_id, 'ct_builder_shortcodes', true);

        if (empty($shortcodes)) {
            return [];
        }

        $attributes = [];

        // Oxygen stores data as JSON
        $data = json_decode($shortcodes, true);

        if (!is_array($data)) {
            return [];
        }

        $attributes = dapfforwc_parse_oxygen_tree($data);

        return $attributes;
    }
}


if (!function_exists('dapfforwc_parse_oxygen_tree')) {
    /**
     * Recursively parse Oxygen builder tree
     */
    function dapfforwc_parse_oxygen_tree($tree)
    {
        $attributes = [];

        foreach ($tree as $node) {
            if (!is_array($node)) {
                continue;
            }

            $name = $node['name'] ?? '';

            // Check for WooCommerce elements
            if (strpos($name, 'woocommerce') !== false || strpos($name, 'oxy_product') !== false) {
                $options = $node['options'] ?? [];
                $normalized = [];

                if (!empty($options['posts_per_page'])) {
                    $normalized['per_page'] = $normalized['limit'] = intval($options['posts_per_page']);
                }
                if (!empty($options['columns'])) {
                    $normalized['columns'] = intval($options['columns']);
                }
                if (!empty($options['orderby'])) {
                    $normalized['orderby'] = sanitize_text_field($options['orderby']);
                }
                if (!empty($options['order'])) {
                    $normalized['order'] = sanitize_text_field($options['order']);
                }
                if (!empty($options['category'])) {
                    $normalized['category'] = sanitize_text_field($options['category']);
                }

                if (!empty($normalized)) {
                    $attributes[] = $normalized;
                }
            }

            // Recursively check children
            if (!empty($node['children'])) {
                $child_attributes = dapfforwc_parse_oxygen_tree($node['children']);
                $attributes = array_merge($attributes, $child_attributes);
            }
        }

        return $attributes;
    }
}
