<?php

if (!defined('ABSPATH')) {
    exit;
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
            $filteroptionsfromurl["rplurand[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rplurand']))));
        }
        // check if 'authors' is set and sanitize it
        if (isset($_GET['rpluthor'])) {
            $filteroptionsfromurl["rpluthor[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rpluthor']))));
        }
        // check if 'stock status' is set and sanitize it
        if (isset($_GET['rplutock_status'])) {
            $filteroptionsfromurl["rplutock_status[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rplutock_status']))));
        }
        // check if 'sale status' is set and sanitize it
        if (isset($_GET['rpn_sale'])) {
            $filteroptionsfromurl["rpn_sale[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rpn_sale']))));
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
        // check if rating is set and sanitize it
        if (isset($_GET['rating'])) {
            $filteroptionsfromurl["rating[]"] = array_map('sanitize_text_field', explode(",", sanitize_text_field(wp_unslash($_GET['rating']))));
        }

        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['mn_price']) && isset($_GET['mx_price'])) {
            $filteroptionsfromurl["min_price"] = sanitize_text_field(wp_unslash($_GET['mn_price']));
            $filteroptionsfromurl["max_price"] = sanitize_text_field(wp_unslash($_GET['mx_price']));
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
            $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = $arrayCata;
            $dapfforwc_options['default_filters'][$dapfforwc_slug]["tag[]"] = $tagValue;
            $dapfforwc_options['default_filters'][$dapfforwc_slug]["attribute"] = $attrvalue;
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
                'operator_second' => isset($attributes['terms_operator']) ? $attributes['terms_operator'] : $attributes['tag_operator'] ?? $attributes['cat_operator'] ?? 'IN'
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

    if (is_shop() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === [""] && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
        $all_cata_slugs = array_column($all_cata, 'slug');
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = $all_cata_slugs;
        $is_all_cata = true;
        $make_default_selected = true;
    }

    if (is_product_category()) {
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
    if (!is_shop() && !is_product_category() && !is_product_tag() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? sanitize_text_field(wp_unslash($_GET['filters'])) : '') === [""]  && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
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
                        $reverse_prefix[$custom_val] = ['type' => 'attribute', 'key' => $custom_key]; //maybe a error here
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
                } else {
                    $parsed_filters[$info['type'] . "[]"] = $values;
                }
            }
        }
    }

    update_option('dapfforwc_options', $dapfforwc_options);
    $second_operator = strtoupper($dapfforwc_options["product_show_settings"][$dapfforwc_slug]["operator_second"] ?? "IN");

    $default_filter = (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"]))) ? array_merge(
        $dapfforwc_options["default_filters"][$dapfforwc_slug] ?? [],
        $parsed_filters,
        isset($_GET['filters']) && $_GET['filters'] !== "1" ? explode(',', sanitize_text_field(wp_unslash($_GET['filters'] ?? ''))) : [],
        $request_parts,
        $filteroptionsfromurl
    ) :  array_merge_recursive(
        $filteroptionsfromurl,
        $dapfforwc_options["default_filters"][$dapfforwc_slug] ?? []
    );

    $ratings = isset($default_filter["rating[]"])
        ? array_map('intval', (array)$default_filter["rating[]"])
        : array_map('intval', array_values(array_filter($default_filter, 'is_numeric')));


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
                return floatval($product['rating']) >= floatval($min_rating);
            }),
            'ID'
        );
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
    $all_data_objects["sku"] = isset($default_filter["sku"]) ? $default_filter["sku"] : "";
    $all_data_objects["discount"] = isset($default_filter["discount"]) ? $default_filter["discount"] : "";
    $all_data_objects["date_filter"] = isset($default_filter["date_filter"]) ? $default_filter["date_filter"] : "";
    $all_data_objects["rating[]"] = isset($default_filter["rating[]"]) ? $default_filter["rating[]"] : [];

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
    if ($second_operator === 'AND') {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_intersect(...array_values($matched_cata_with_ids)));
    } else {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_cata_with_ids))));
    }

    // filter by brands
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_brand_with_ids = array_intersect_key($brands_lookup, array_flip(array_filter(isset($default_filter["rplurand[]"]) ? $default_filter["rplurand[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $brand_slugs = array_filter(isset($default_filter["rplurand[]"]) ? $default_filter["rplurand[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $brand_slugs, true)) {
                $brand_slugs[] = $val;
            }
        }
        $matched_brand_with_ids = array_intersect_key($brands_lookup, array_flip($brand_slugs));
    }
    $all_data_objects["rplurand[]"] = array_keys($matched_brand_with_ids);
    if ($second_operator === 'AND') {
        $products_id_by_brand = empty($matched_brand_with_ids) ? [] : array_values(array_intersect(...array_values($matched_brand_with_ids)));
    } else {
        $products_id_by_brand = empty($matched_brand_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_brand_with_ids))));
    }
    // filter by authors
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_author_with_ids = array_intersect_key($authors_lookup, array_flip(array_filter(isset($default_filter["rpluthor[]"]) ? $default_filter["rpluthor[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $author_slugs = array_filter(isset($default_filter["rpluthor[]"]) ? $default_filter["rpluthor[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $author_slugs, true)) {
                $author_slugs[] = $val;
            }
        }
        $matched_author_with_ids = array_intersect_key($authors_lookup, array_flip($author_slugs));
    }
    $all_data_objects["rpluthor[]"] = array_keys($matched_author_with_ids);
    if ($second_operator === 'AND') {
        $products_id_by_author = empty($matched_author_with_ids) ? [] : array_values(array_intersect(...array_values($matched_author_with_ids)));
    } else {
        $products_id_by_author = empty($matched_author_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_author_with_ids))));
    }
    // filter by stock status
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_stock_status_with_ids = array_intersect_key($stock_status_lookup, array_flip(array_filter(isset($default_filter["rplutock_status[]"]) ? $default_filter["rplutock_status[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $stock_status_slugs = array_filter(isset($default_filter["rplutock_status[]"]) ? $default_filter["rplutock_status[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $stock_status_slugs, true)) {
                $stock_status_slugs[] = $val;
            }
        }
        $matched_stock_status_with_ids = array_intersect_key($stock_status_lookup, array_flip($stock_status_slugs));
    }
    $all_data_objects["rplutock_status[]"] = array_keys($matched_stock_status_with_ids);
    if ($second_operator === 'AND') {
        $products_id_by_stock_status = empty($matched_stock_status_with_ids) ? [] : array_values(array_intersect(...array_values($matched_stock_status_with_ids)));
    } else {
        $products_id_by_stock_status = empty($matched_stock_status_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_stock_status_with_ids))));
    }
    // filter by sale status
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_sale_status_with_ids = array_intersect_key($sale_status_lookup, array_flip(array_filter(isset($default_filter["rpn_sale[]"]) ? $default_filter["rpn_sale[]"] : [])));
    } else {
        // Merge both possible category sources: 'brands[]' and numeric keys (0,1,2,...)
        $sale_status_slugs = array_filter(isset($default_filter["rpn_sale[]"]) ? $default_filter["rpn_sale[]"] : []);
        // Collect numeric keys as possible category slugs
        foreach ($default_filter as $key => $val) {
            if (is_numeric($key) && is_string($val) && !in_array($val, $sale_status_slugs, true)) {
                $sale_status_slugs[] = $val;
            }
        }
        $matched_sale_status_with_ids = array_intersect_key($sale_status_lookup, array_flip($sale_status_slugs));
    }
    $all_data_objects["rpn_sale[]"] = array_keys($matched_sale_status_with_ids);
    if ($second_operator === 'AND') {
        $products_id_by_sale_status = empty($matched_sale_status_with_ids) ? [] : array_values(array_intersect(...array_values($matched_sale_status_with_ids)));
    } else {
        $products_id_by_sale_status = empty($matched_sale_status_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_sale_status_with_ids))));
    }
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

    if ($second_operator === 'AND') {
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

    if ($second_operator === 'AND') {
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

    if ($second_operator === 'AND') {
        foreach ($match_custom_meta_with_ids as $taxonomy => $products) {
            $products_id_by_custom_meta[] = array_values(array_intersect(...$products));
        }
    } else {
        foreach ($match_custom_meta_with_ids as $taxonomy => $products) {
            $products_id_by_custom_meta[] = array_values(array_unique(array_merge(...$products)));
        }
    }

    $common_values_custom_meta = empty($products_id_by_custom_meta) ? [] : array_intersect(...$products_id_by_custom_meta);

    $products_ids = dapfforwc_getFilteredProductIds(
        $products_id_by_cata,
        $products_id_by_tag,
        $products_id_by_brand,
        $common_values,
        $common_values_custom_meta,
        $products_id_by_author,
        $products_id_by_stock_status,
        $products_id_by_sale_status
    );
    if (!empty($products_id_by_rating) && !empty($products_ids)) {
        $products_ids = array_values(array_intersect($products_ids, $products_id_by_rating));
    } elseif (!empty($products_id_by_rating) && empty($products_ids)) {
        $products_ids = $products_id_by_rating;
    }

    $updated_filters = dapfforwc_get_updated_filters($products_ids, $all_data) ?? [];

    // Cache file path
    $cache_file = __DIR__ . '/min_max_prices_cache.json';
    $referer = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));

    // Use $referer to generate a unique cache key
    $cache_key = md5($referer);
    $min_max_prices_cache = [];

    // Load existing cache if available
    if (file_exists($cache_file)) {
        $min_max_prices_cache = json_decode(file_get_contents($cache_file), true);
        if (!is_array($min_max_prices_cache)) {
            $min_max_prices_cache = [];
        }
    }

    if (isset($_GET['gm-product-filter-nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
        $cache_key_url = isset($_GET["_wp_http_referer"]) ? sanitize_text_field(wp_unslash($_GET["_wp_http_referer"])) : '';
        $cache_key = md5($cache_key_url);
        if (isset($min_max_prices_cache[$cache_key])) {
            $min_max_prices = $min_max_prices_cache[$cache_key];
        } else {
            $min_max_prices = dapfforwc_get_min_max_price($product_details, $products_ids);
            $min_max_prices_cache[$cache_key] = $min_max_prices;
            file_put_contents($cache_file, json_encode($min_max_prices_cache, JSON_UNESCAPED_UNICODE));
        }
    } else {
        $min_max_prices = dapfforwc_get_min_max_price($product_details, $products_ids);
        $min_max_prices_cache[$cache_key] = $min_max_prices;
        file_put_contents($cache_file, json_encode($min_max_prices_cache, JSON_UNESCAPED_UNICODE));
    }

    $all_data_objects["min_price"] = isset($default_filter["min_price"]) ? floatval($default_filter["min_price"]) : 0;
    $all_data_objects["max_price"] = isset($default_filter["max_price"]) ? floatval($default_filter["max_price"]) : (isset($dapfforwc_styleoptions["price"]["auto_price"]) ? ceil(floatval($min_max_prices['max'])) : floatval(isset($dapfforwc_styleoptions["price"]["max_price"]) ? $dapfforwc_styleoptions["price"]["max_price"] : 100000000000));

    ob_start(); // Start output buffering
    if ($atts['layout'] === 'top_view') {
        // Add your custom styles for the top_view layout here
?>
        <style>
            @media (min-width: 781px) {

                /* Product Filter Styles */
                #product-filter {
                    display: flex;
                    flex-direction: row !important;
                    gap: 12px;
                    overflow-x: auto;
                    overflow-y: hidden;
                    padding-bottom: 50vh;
                    margin-bottom: -50vh;
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
                .filter-group {
                    position: relative;
                    min-width: max-content;
                    flex-shrink: 0;
                }

                /* Filter group title */
                .filter-group .title {
                    white-space: nowrap;
                    font-weight: 500;
                    user-select: none;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }

                #product-filter .filter-group.rating {
                    overflow: visible !important;
                }

                /* Dropdown items container */
                .filter-group>*:not(.title) {
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
                .filter-group:hover .items,
                .filter-group:focus-within .items {
                    transform: translateY(4px);
                }

            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const productFilter = document.getElementById('product-filter');
                const nextButton = document.querySelector('.plugincy-next-button');
                const prevButton = document.querySelector('.plugincy-prev-button');

                if (productFilter) {
                    productFilter.addEventListener('wheel', function(e) {
                        // Prevent default vertical scroll
                        e.preventDefault();

                        // Scroll horizontally instead
                        // Use deltaY for better cross-browser compatibility
                        const scrollAmount = e.deltaY || e.deltaX;
                        productFilter.scrollLeft += scrollAmount;
                    });

                    if (nextButton) {
                        nextButton.addEventListener('click', function() {
                            productFilter.scrollLeft += 200; // Scroll right
                        });
                    }

                    if (prevButton) {
                        prevButton.addEventListener('click', function() {
                            productFilter.scrollLeft -= 200; // Scroll left
                        });
                    }
                }

                // on click outside of #product-filter add dapfforwc-hidden-important class on .items
                document.addEventListener('click', function(event) {
                    if (!productFilter.contains(event.target)) {
                        const items = productFilter.querySelectorAll("#product-filter .filter-group .items");
                        const svgs = productFilter.querySelectorAll("#product-filter .filter-group .title svg");

                        items.forEach(function(item) {
                            if (!item.classList.contains('dapfforwc-hidden-important')) {
                                item.classList.add('dapfforwc-hidden-important');
                            }
                        });

                        // remove .rotated from svgs
                        svgs.forEach(function(svg) {
                            if (svg.classList.contains('rotated')) {
                                svg.classList.remove('rotated');
                            }
                        });
                    }
                });
            });
        </script>
    <?php
    }

    if ($template_options['active_template'] && $template_options['active_template'] === 'clean') { ?>
        <style>
            #product-filter .filter-group .title {
                padding: 10px 0 14px;
            }

            #product-filter .filter-group .items {
                padding: 20px 0 10px;
            }

            #product-filter .filter-group {
                margin-bottom: 0;
            }
        </style>

    <?php } elseif ($template_options['active_template'] && $template_options['active_template'] === 'shadow') { ?>
        <style>
            #product-filter .filter-group {
                box-shadow: rgba(99, 99, 99, 0.2) 0 2px 8px 0;
            }

            #product-filter .filter-group .title {
                padding: 10px 13px 10px 14px;
            }

            #product-filter .filter-group .items {
                padding: 20px 10px;
            }

            #product-filter .filter-group {
                margin-bottom: 15px;
                border-radius: 8px;
            }
        </style>

    <?php } ?>
    <style>
        #product-filter .plugrogress-percentage:after,
        #product-filter .plugrogress-percentage:before,
        #product-filter .plugincy_slider .plugrogress,
        #product-filter .plugincy-search-submit {
            background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?> !important;
        }

        #product-filter .filter-group {
            background: <?php echo esc_html(isset($template_options["background_color"]) ? $template_options["background_color"] : 'rgba(255, 255, 255, 0.7)'); ?>;
        }

        #product-filter .filter-group,
        #product-filter .filter-group .title,
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

        #product-filter .filter-group .items {
            width: 100%;
            border-top: 1px solid <?php echo esc_html(isset($template_options["border_color"]) ? $template_options["border_color"] : '#eee'); ?>;
        }

        #product-filter span.reset-value {
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
        @media (max-width: 781px) {

            .rfilterbuttons,
            #product-filter .items {
                display: none !important;
            }

            #product-filter .filter-group div .title {
                cursor: pointer !important;
            }

            #product-filter:before {
                content: "Filter";
                background: <?php echo esc_html(isset($template_options["primary_color"]) ? $template_options["primary_color"] : '#432fb8'); ?>;
                color: white;
                padding: 10px 11px;
                width: 60px;
                height: 45px;
                position: absolute;
                left: 0px;
            }

            form#product-filter {
                display: flex;
                flex-direction: row !important;
                overflow: scroll;
                gap: 10px;
                height: 66px;
                margin-left: 64px;
                scrollbar-width: thin;
            }

            .filter-group .title {
                font-size: 16px !important;
            }

            .child-categories {
                display: block !important;
            }

            .filter-group {
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
            @media (min-width: 781px) {

                #mobileonly,
                #filter-button {
                    display: none !important;
                }
            }

            @media (max-width: 781px) {
                .mobile-filter {
                    position: fixed;
                    z-index: 999;
                    background: #ffffff;
                    width: 88%;
                    padding-bottom: 200px;
                    height: 100%;
                    overflow: scroll;
                    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                    border-radius: 30px;
                    margin: 5px !important;
                    display: none;
                    top: 20%;
                    transform: translateX(-50%);
                    left: 50%;
                }

                .rfilterselected ul {
                    flex-wrap: nowrap;
                    overflow: scroll;
                }
            }
        </style>
    <?php } ?>
    <?php if ($atts['mobile_responsive'] === 'style_4') { ?>

        <style>
            @media (min-width: 781px) {

                #mobileonly,
                #filter-button {
                    display: none !important;
                }
            }

            @media (max-width: 781px) {
                .mobile-filter {
                    position: fixed;
                    z-index: 999;
                    background: #ffffff;
                    width: 80%;
                    height: 100%;
                    overflow: scroll;
                    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                    bottom: 0;
                    right: 0;
                    transition: transform 0.3s ease-in-out;
                    transform: translateX(150%);
                }

                .mobile-filter.open {
                    transform: translateX(0%);
                }

                .rfilterselected ul {
                    flex-wrap: nowrap;
                    overflow: scroll;
                }
            }
        </style>
    <?php }

    if ($atts['mobile_responsive'] === 'style_3' ||  $atts['mobile_responsive'] === 'style_4') { ?>
        <button id="filter-button" style="position: fixed;z-index: 9999999999;bottom: 20px;right: 20px;background-color: #041a57;color: white;border: none;border-radius: 50%;aspect-ratio: 1;display: flex;align-items: center;justify-content: center;width: 40px;height: 40px;padding: 0;">
            <svg style=" width: 20px; fill: #fff; " xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512" role="graphics-symbol" aria-hidden="false" aria-label="">
                <path d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z"></path>
            </svg>
        </button>
        <div class="mobile-filter">
            <div class="sm-top-btn" id="mobileonly" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding: 20px;margin-bottom: 10px;">
                <button id="filter-cancel-button" style="background: none !important;padding:0;color: #000;"> Cancel </button>
                <p style="margin: 0;" id="rcountproduct">Showing(All)</p>
            </div>
        <?php
        echo '<div class="rfilterselected" id="mobileonly"><div><ul></ul></div></div>';
    }
    if ($atts['mobile_responsive'] === 'style_3') {
        wp_add_inline_script('urlfilter-ajax', "
         jQuery(document).ready(function($) {
                    let isMobile = false;
                    if (window.innerWidth <= 768) {
                        isMobile = true;
                    }

                    if (isMobile) {
                        $('#filter-cancel-button').on('click', function(event) {
                            event.preventDefault();
                            $('.mobile-filter').slideUp();
                        });

                        $('#filter-button').on('click', function(event) {
                            event.preventDefault();
                            $('.mobile-filter').slideDown();
                        });

                        $(document).on('click', function(event) {
                            if (!$(event.target).closest('.mobile-filter, #filter-button').length) {
                                $('.mobile-filter').slideUp();
                            }
                        });
                    }
                });
         ");
    }

    if ($atts['mobile_responsive'] === 'style_4') {
        wp_add_inline_script('urlfilter-ajax', "
         jQuery(document).ready(function($) {
                    let isMobile = false;
                    if (window.innerWidth <= 768) {
                        isMobile = true;
                    }

                    if (isMobile) {
                        $('#filter-button').on('click', function(event) {
                            event.preventDefault();
                            $('.mobile-filter').toggleClass('open');
                        });

                        $('#filter-cancel-button').on('click', function(event) {
                            event.preventDefault();
                            $('.mobile-filter').removeClass('open');
                        });

                        $(document).on('click', function(event) {
                            if (!$(event.target).closest('.mobile-filter, #filter-button').length) {
                                $('.mobile-filter').removeClass('open');
                            }
                        });
                    }
                });
         ");
    } ?>
        <div class="plugincy_filter_wrapper" style="position: relative;">
            <!-- Navigation Buttons -->
            <button type="button" class="plugincy-prev-button" aria-label="Previous" style="display: none;">
                &#8592;
            </button>
            <button type="button" class="plugincy-next-button" aria-label="Next" style="display: none;">
                &#8594;
            </button>
            <form id="product-filter" class="plugincy_layout_<?php echo esc_attr($atts['layout']); ?>" method="POST" data-layout='<?php echo esc_attr($atts['layout']); ?>' data-mobile-style='<?php echo esc_attr($atts['mobile_responsive']); ?>'
                data-product_show_settings='<?php
                                            echo isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug]) ? json_encode($dapfforwc_options['product_show_settings'][$dapfforwc_slug]) : "";
                                            ?>'
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
                if (!empty($all_data_objects) && is_array($all_data_objects) && (!$is_all_cata || (isset($dapfforwc_advance_settings["default_value_selected"]) && $dapfforwc_advance_settings["default_value_selected"] === 'on'))) {
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
                    ...$filteroptionsfromurl
                ];
                echo wp_kses(dapfforwc_filter_form($updated_filters, !$make_default_selected || (isset($dapfforwc_advance_settings["default_value_selected"]) && $dapfforwc_advance_settings["default_value_selected"] === 'on') ? $all_data_objects : $default_data_objects, $use_anchor, $use_filters_word, $atts, $min_price, $max_price, $min_max_prices, '', false, false), $dapfforwc_allowed_tags);
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

<?php if ($atts['mobile_responsive'] === 'style_1') {
        wp_add_inline_script('urlfilter-ajax', "
         function initializeMobileFilters() {
  if (window.innerWidth <= 768) {
    const titles = document.querySelectorAll('.filter-group .title');
    const items  = document.querySelectorAll('.filter-group .items');

    function hideAll() {
      items.forEach(item => item.style.setProperty('display', 'none', 'important'));
      titles.forEach(title => {
        const svg = title.querySelector('svg');
        if (svg) svg.classList.remove('rotated');
      });
    }

    // Start hidden
    hideAll();

    titles.forEach(title => {
      title.addEventListener('click', function (e) {
        e.stopPropagation();

        // Toggle current
        const currentItems = this.nextElementSibling;
        if (currentItems.style.display === 'block') {
            currentItems.style.setProperty('display', 'none', 'important'); // Hide items
        } else {
            hideAll(); // Hide all first                                
            if (currentItems.classList.contains('image') || currentItems.classList.contains('image_no_border') || currentItems.classList.contains('button_check')) {
                currentItems.style.setProperty('display', 'grid', 'important');
            } else if (currentItems.classList.contains('plugincy_color') || currentItems.classList.contains('color_no_border') || currentItems.classList.contains('color_circle')) {
                currentItems.style.setProperty('display', 'flex', 'important');
            } else {
                currentItems.style.setProperty('display', 'block', 'important');
            }
        }

        // Toggle arrow rotation
        const svg = this.querySelector('svg');
        if (svg) svg.classList.toggle('rotated');
      });
    });

    // Click outside to hide all
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.plugincy_filter_wrapper')) {
        hideAll();
      }
    });
  }}

    // Call function on DOM content loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeMobileFilters();
    });

    // Call function after AJAX requests complete
    jQuery(document).ajaxComplete(function() {
        initializeMobileFilters();
    });
");
    }

    // End output buffering and return content
    return ob_get_clean();
}
add_shortcode('plugincy_filters', 'dapfforwc_product_filter_shortcode');

// General sorting function
function dapfforwc_customSort($a, $b)
{
    // Try to convert to timestamp for date comparison
    $dateA = strtotime($a);
    $dateB = strtotime($b);

    if ($dateA && $dateB) {
        return $dateA <=> $dateB; // Both are dates
    }

    // Check if both are numeric
    if (is_numeric($a) && is_numeric($b)) {
        return $a <=> $b; // Both are numbers
    }

    // Fallback to string comparison
    return strcmp($a, $b);
}
function dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, $name, $attribute, $singlevalueSelect, $count, $min_price = 0, $max_price = null, $min_max_prices = [], $disable_unselected = false)
{
    $default_max_price = isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["auto_price"]) ? (ceil(floatval($min_max_prices['max'] ?? $max_price))) : (isset($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["price"]) && isset($dapfforwc_styleoptions["price"]["max_price"]) ? $dapfforwc_styleoptions["price"]["max_price"] : 10000);
    $output = '';

    switch ($sub_option) {
        case 'checkbox':
            $output .= '<label><input  ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</label>';
            break;
        case 'button_check':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</label>';
            break;

        case 'radio_check':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-radio-check" name="' . $name . '[]" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</label>';
            break;

        case 'radio':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-radio" name="' . $name . '[]" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</label>';
            break;

        case 'square':
        case 'square_check':
            $output .= '<label class="square-option"><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-square" name="' . $name . '[]" value="' . $value . '"' . $checked . '> <span>' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</span></label>';
            break;

        case 'checkbox_hide':
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]" value="' . $value . '"' . $checked . ' style="display:none;"> <span>' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</span></label>';
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
            $output .= '<label style="position: relative;"><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-color" name="' . $name . '[]" value="' . $value . '"' . $checked . '>
                <span class="color-box" style="background-color: ' . $color . '; border: ' . $border . '; width: 30px; height: 30px;"></span><span class="value" style="display:' . $value_show . ';">' . $value . ($count != 0 ? ' (' . $count . ')' : '') . '<span></label>';
            break;

        case 'image':
        case 'image_no_border':
            $brand_image_url = dapfforwc_get_wc_brand_image_by_slug($value);
            $image_url = isset($brand_image_url) && !empty($brand_image_url) ? $brand_image_url : ($dapfforwc_styleoptions[$attribute]['images'][$value] ?? 'default-image.jpg');
            $border_class = ($sub_option === 'image_no_border') ? 'no-border' : '';
            $output .= '<label class="image-option ' . $border_class . '">
            <span class="image-title">' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</span>
    <input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-image" name="' . $name . '[]" value="' . $value . '"' . $checked . '>';

            if ($image_url !== 'default-image.jpg') {
                $attachment_id = attachment_url_to_postid($image_url);
                if ($attachment_id) {
                    $output .= wp_get_attachment_image($attachment_id, 'thumbnail', false, array('alt' => esc_attr($title)));
                } else {
                    // generate svg with name
                    $output .= '<svg style="width: 100%; height: 100%;" width="78" height="80" viewBox="0 0 78 80" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="a" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#2563eb;stop-opacity:1"/><stop offset="100%" style="stop-color:#1d4ed8;stop-opacity:1"/></linearGradient><filter id="b" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="2" dy="2" stdDeviation="2" flood-color="#000" flood-opacity=".3"/></filter></defs><rect width="78" height="80" rx="8" ry="8" fill="url(#a)" filter="url(#b)"/><text x="39" y="32" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#fff" text-anchor="middle">' . $title . '</text><path stroke="rgba(255,255,255,0.4)" d="M12 42h54"/><text x="39" y="52" font-family="Arial, Helvetica, sans-serif" font-size="7" fill="rgba(255,255,255,0.7)" text-anchor="middle">PREMIUM BRAND</text></svg>';
                }
            } else {
                $output .= '<svg style="width: 100%; height: 100%;" width="78" height="80" viewBox="0 0 78 80" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="a" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#2563eb;stop-opacity:1"/><stop offset="100%" style="stop-color:#1d4ed8;stop-opacity:1"/></linearGradient><filter id="b" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="2" dy="2" stdDeviation="2" flood-color="#000" flood-opacity=".3"/></filter></defs><rect width="78" height="80" rx="8" ry="8" fill="url(#a)" filter="url(#b)"/><text x="39" y="32" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#fff" text-anchor="middle">' . $title . '</text><path stroke="rgba(255,255,255,0.4)" d="M12 42h54"/><text x="39" y="52" font-family="Arial, Helvetica, sans-serif" font-size="7" fill="rgba(255,255,255,0.7)" text-anchor="middle">PREMIUM BRAND</text></svg>';
            }

            $output .= '</label>';
            break;

        case 'select2':
        case 'select2_classic':
        case 'select':
            $output .= '<option  ' . ($disable_unselected && !$checked ? "disabled" : "") . ' class="filter-option" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</option>';
            break;
        case 'input-price-range':
            $output .= '<div class="range-input"><label for="min-price">Min Price:</label>
        <input  type="number" id="min-price" name="mn_price" min="0" max="' . $default_max_price . '" step="1" placeholder="Min" value="' . $min_price . '" style="min-height: 30px;position: relative;top: unset;pointer-events: all;border: 1px solid #ccc;padding: 5px 6px;border-radius: 3px;width: 100%;max-width: 100%;height: 30px;max-height: 30px;">
        
        <label for="max-price">Max Price:</label>
        <input  type="number" id="max-price" name="mx_price" min="0" max="' . $default_max_price . '" step="1" placeholder="Max" value="' . $max_price + 1 . '" style="min-height: 30px;position: relative;top: unset;pointer-events: all;border: 1px solid #ccc;padding: 5px 6px;border-radius: 3px;width: 100%;max-width: 100%;height: 30px;max-height: 30px;"></div>';
            break;
        case 'slider':
            $output .= '<div class="price-input">
        <div class="field">
          <span>Min</span>
          <input  type="number" id="min-price" name="mn_price" class="input-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
          <span>Max</span>
          <input  type="number" id="max-price" name="mx_price" min="0" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
      </div>
      <div class="plugincy_slider">
        <div class="plugrogress"></div>
      </div>
      <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '" >
        <input  type="range" id="price-range-max" class="range-max" min="0" max="' . $default_max_price . '" value="' . $max_price . '">
      </div>';
            break;
        case 'slider2':
            $output .= '<div class="price-input plugincy-align-center">
        <div class="field">
          <input  type="number" id="min-price" name="mn_price" class="input-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
          <input  type="number" id="max-price" name="mx_price" min="0" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
      </div>
      <div class="plugincy_slider">
        <div class="plugrogress"></div>
      </div>
      <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '" >
        <input  type="range" id="price-range-max" class="range-max" min="0" max="' . $default_max_price . '" value="' . $max_price . '">
      </div>';
            break;
        case 'price':
            $output .= '<div class="price-input" style="visibility: hidden; margin: 0;">
        <div class="field">
            <input  type="number" id="min-price" name="mn_price" class="input-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '">
        </div>
        <div class="separator">-</div>
        <div class="field">
            <input  type="number" id="max-price" name="mx_price" min="0" max="' . $default_max_price . '" class="input-max" value="' . $max_price . '">
        </div>
        </div>
        <div class="plugincy_slider">
        <div class="plugrogress plugrogress-percentage"></div>
        </div>
        <div class="range-input">
        <input  type="range" id="price-range-min" class="range-min" min="0" max="' . $default_max_price . '" value="' . $min_price . '">
        <input  type="range" id="price-range-max" class="range-max" min="0" max="' . $default_max_price . '" value="' . $max_price . '">
        </div>';
            break;
        case 'rating-text':
            $output .= '<label><input ' . ($disable_unselected && !in_array("5", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="5" ' . (in_array("5", $checked) ? ' checked' : '') . '> 5 Stars 
    </label>
        <label><input ' . ($disable_unselected && !in_array("4", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="4" ' . (in_array("4", $checked) ? ' checked' : '') . '> 4 Stars & Up</label>
        <label><input ' . ($disable_unselected && !in_array("3", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="3" ' . (in_array("3", $checked) ? ' checked' : '') . '> 3 Stars & Up</label>
        <label><input ' . ($disable_unselected && !in_array("2", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="2" ' . (in_array("2", $checked) ? ' checked' : '') . '> 2 Stars & Up</label>
        <label><input ' . ($disable_unselected && !in_array("1", $checked) ? "disabled" : "") . ' type="checkbox" name="rating[]" value="1" ' . (in_array("1", $checked) ? ' checked' : '') . '> 1 Star & Up</label>';
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
            $output .= '<label><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-checkbox" name="' . $name . '[]" value="' . $value . '"' . $checked . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</label>';
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
        $checked = in_array($category->slug, $selected_categories) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
        $anchorlink = $use_filters_word === 'on' ? "filters/$value" : "?filters=$value";

        // Fetch child categories
        $child_categories = dapfforwc_get_child_categories($child_category, $category->term_id);

        // Render current category
        $categoryHierarchyOutput .= $use_anchor === 'on' && $sub_option !== 'select' && !str_contains($sub_option, 'select2')
            ? '<div style="display:flex;align-items: center;"><a href="' . esc_attr($anchorlink) . '">'
            . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count)
            . '</a>'
            . (!empty($child_categories) && $hierarchical === 'enable_hide_child' ? '<span class="show-sub-cata">+</span>' : '')
            . '</div>'
            : ($sub_option !== 'select' && !str_contains($sub_option, 'select2') ? '<div style="display:flex;align-items: center;text-decoration: none;">'
                . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count) . (!empty($child_categories) && $hierarchical === 'enable_hide_child' ? '<span class="show-sub-cata" style="cursor:pointer;">+</span>' : '')
                . '</div>' : dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count) . (!empty($child_categories) && $hierarchical === 'enable_hide_child' ? '<span class="show-sub-cata" style="cursor:pointer;">+</span>' : ''));

        // Render child categories
        if (!empty($child_categories)) {
            $categoryHierarchyOutput .= $sub_option !== 'select' && !str_contains($sub_option, 'select2') ? '<div class="child-categories" style="display:' . ($hierarchical === 'enable_hide_child' ? 'none;' : 'block;') . '">' : '';
            $categoryHierarchyOutput .= dapfforwc_render_category_hierarchy($child_categories, $selected_categories, $sub_option, $dapfforwc_styleoptions, $singlevaluecataSelect, $show_count, $use_anchor, $use_filters_word, $hierarchical, $child_category);
            $categoryHierarchyOutput .= $sub_option !== 'select' && !str_contains($sub_option, 'select2') ? '</div>' : '';
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
        return '<p style="background:red;background: red;text-align: center;color: #fff;">Please provide an attribute slug.</p>';
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

function dapfforwc_get_updated_filters($product_ids, $all_data = [])
{
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
        if (is_array(isset($all_data['categories']) ? $all_data['categories'] : []) || is_object(isset($all_data['categories']) ? $all_data['categories'] : [])) {
            foreach (isset($all_data['categories']) ? $all_data['categories'] : [] as $term_id => $category) {
                if (!empty(array_intersect($product_ids, $category['products']))) {
                    $categories[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $category['name'],
                        'slug'    => $category['slug'],
                        'parent'  => $category['parent'],
                        'taxonomy' => 'product_cat',
                        'count'   => count(array_intersect($category['products'], $product_ids)),
                    ];
                }
            }
        }

        // Tags
        if (is_array(isset($all_data['tags']) ? $all_data['tags'] : []) || is_object(isset($all_data['tags']) ? $all_data['tags'] : [])) {
            foreach (isset($all_data['tags']) ? $all_data['tags'] : [] as $term_id => $tag) {
                if (!empty(array_intersect($product_ids, $tag['products']))) {
                    $tags[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $tag['name'],
                        'slug'    => $tag['slug'],
                        'taxonomy' => 'product_tag',
                        'count'   => count(array_intersect($tag['products'], $product_ids)),
                    ];
                }
            }
        }

        // Brands
        if (is_array(isset($all_data['brands']) ? $all_data['brands'] : []) || is_object(isset($all_data['brands']) ? $all_data['brands'] : [])) {
            foreach (isset($all_data['brands']) ? $all_data['brands'] : [] as $term_id => $brand) {
                if (!empty(array_intersect($product_ids, $brand['products']))) {
                    $brands[$term_id] = (object) [
                        'term_id' => $term_id,
                        'name'    => $brand['name'],
                        'slug'    => $brand['slug'],
                        'taxonomy' => 'product_brand',
                        'count'   => count(array_intersect($brand['products'], $product_ids)),
                    ];
                }
            }
        }

        // Authors
        if (is_array(isset($all_data['authors']) ? $all_data['authors'] : []) || is_object(isset($all_data['authors']) ? $all_data['authors'] : [])) {
            foreach (isset($all_data['authors']) ? $all_data['authors'] : [] as $author_id => $author) {
                if (!empty(array_intersect($product_ids, $author['products']))) {
                    $authors[$author_id] = (object) [
                        'term_id' => $author_id,
                        'name' => $author['name'],
                        'slug' => $author['slug'],
                        'count'   => count(array_intersect($author['products'], $product_ids)),
                    ];
                }
            }
        }

        // Stock Status
        if (is_array(isset($all_data['stock_status']) ? $all_data['stock_status'] : []) || is_object(isset($all_data['stock_status']) ? $all_data['stock_status'] : [])) {
            foreach (isset($all_data['stock_status']) ? $all_data['stock_status'] : [] as $status_id => $status) {
                if (!empty(array_intersect($product_ids, $status['products']))) {
                    $stock_status[$status_id] = (object) [
                        'term_id' => $status_id,
                        'name'    => $status['name'],
                        'slug'    => $status['slug'],
                        'taxonomy' => 'stock_status',
                        'count'   => count(array_intersect($status['products'], $product_ids)),
                    ];
                }
            }
        }

        // Sale Status
        if (is_array(isset($all_data['sale_status']) ? $all_data['sale_status'] : []) || is_object(isset($all_data['sale_status']) ? $all_data['sale_status'] : [])) {
            foreach (isset($all_data['sale_status']) ? $all_data['sale_status'] : [] as $status_id => $status) {
                if (!empty(array_intersect($product_ids, $status['products']))) {
                    $sale_status[$status_id] = (object) [
                        'term_id' => $status_id,
                        'name'    => $status['name'],
                        'slug'    => $status['slug'],
                        'taxonomy' => 'sale_status',
                        'count'   => count(array_intersect($status['products'], $product_ids)),
                    ];
                }
            }
        }

        // Extract attributes
        if (is_array(isset($all_data['attributes']) ? $all_data['attributes'] : []) || is_object(isset($all_data['attributes']) ? $all_data['attributes'] : [])) {
            foreach (isset($all_data['attributes']) ? $all_data['attributes'] : [] as $attribute) {
                $attribute_name = $attribute['attribute_name'];
                $terms = $attribute['terms'];

                if (is_array($terms) || is_object($terms)) {
                    foreach ($terms as $term) {
                        // Check if the term's products match the provided product IDs
                        if (!empty(array_intersect($product_ids, $term['products']))) {
                            $attributes[$attribute_name][] = [
                                'term_id' => $term['term_id'],
                                'attribute_label' => $term['name'],
                                'name'    => $term['name'],
                                'slug'    => $term['slug'],
                                'count'   => count(array_intersect($term['products'], $product_ids)),
                            ];
                        }
                    }
                }
            }
        }

        // Extract custom fields (similar to attributes)
        if (is_array(isset($all_data['custom_fields']) ? $all_data['custom_fields'] : []) || is_object(isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [])) {
            foreach (isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [] as $custom_field) {
                $field_name = $custom_field['name'];
                $field_label = $custom_field['label'];
                $terms = $custom_field['terms'];

                if (is_array($terms) || is_object($terms)) {
                    foreach ($terms as $term) {
                        // Check if the term's products match the provided product IDs
                        if (!empty(array_intersect($product_ids, $term['products']))) {
                            $custom_fields[$field_name][] = [
                                'field_name' => $field_name,
                                'field_label' => $field_label,
                                'name'    => $term['name'],
                                'slug'    => $term['slug'],
                                'count'   => count(array_intersect($term['products'], $product_ids)),
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

    $cache_file = __DIR__ . '/woocommerce_attributes_cache.json';
    $cache_time = 43200; // 12 hours

    // Check and return cache if valid
    if (file_exists($cache_file) && (filemtime($cache_file) > (time() - $cache_time))) {
        return json_decode(file_get_contents($cache_file), true);
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
        WHERE (tt.taxonomy IN (%s, %s, %s) OR a.attribute_name IS NOT NULL)
        AND p.post_type = 'product' 
        AND p.post_status = 'publish'
        ORDER BY t.term_id
    ", 'product_cat', 'product_tag', 'product_brand');

    $results = $wpdb->get_results($query, ARRAY_A);

    if (!empty($results)) {
        foreach ($results as $row) {
            $term_id = $row['term_id'];
            $taxonomy = $row['taxonomy'];

            if ($taxonomy === 'product_cat') {
                $data['categories'][$term_id] = $data['categories'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'parent' => $row['parent'],
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['categories'][$term_id]['products'])) {
                    $data['categories'][$term_id]['products'][] = $row['object_id'];
                }
            } elseif ($taxonomy === 'product_tag') {
                $data['tags'][$term_id] = $data['tags'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['tags'][$term_id]['products'])) {
                    $data['tags'][$term_id]['products'][] = $row['object_id'];
                }
            } elseif ($taxonomy === 'product_brand') {
                $data['brands'][$term_id] = $data['brands'][$term_id] ?? [
                    'name' => $row['name'],
                    'slug' => $row['slug'],
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
                    'slug' => $row['slug'],
                    'products' => []
                ];

                if ($row['object_id'] && !in_array($row['object_id'], $data['attributes'][$attr_name]['terms'][$term_id]['products'])) {
                    $data['attributes'][$attr_name]['terms'][$term_id]['products'][] = $row['object_id'];
                }
            }
        }
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

    // Initialize stock status and sale status arrays
    $data['stock_status'] = [
        0 => ['name' => 'In Stock', 'slug' => 'instock', 'products' => []],
        1 => ['name' => 'Out of Stock', 'slug' => 'outofstock', 'products' => []]
    ];

    $data['sale_status'] = [
        0 => ['name' => 'On Sale', 'slug' => 'onsale', 'products' => []],
        1 => ['name' => 'Not on Sale', 'slug' => 'notonsale', 'products' => []]
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
    file_put_contents($cache_file, json_encode($data, JSON_UNESCAPED_UNICODE));

    return $data;
}

function dapfforwc_get_woocommerce_product_details()
{
    global $wpdb;
    $cache_file = __DIR__ . '/woocommerce_product_details.json';
    $cache_time = 43200; // 12 hours

    // Check and return cache if valid
    if (file_exists($cache_file) && (filemtime($cache_file) > (time() - $cache_time))) {
        return json_decode(file_get_contents($cache_file), true);
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
            ];
        }
    }

    // Convert to indexed array for better JSON compatibility
    $product_data = ['products' => $products];

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
