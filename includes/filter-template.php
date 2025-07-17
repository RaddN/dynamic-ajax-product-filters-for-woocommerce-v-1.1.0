<?php

if (!defined('ABSPATH')) {
    exit;
}


function dapfforwc_product_filter_shortcode($atts)
{
    global $dapfforwc_styleoptions, $post, $dapfforwc_options, $dapfforwc_advance_settings, $wp, $dapfforwc_seo_permalinks_options;

    // Define default attributes and merge with user-defined attributes
    $atts = shortcode_atts(array(
        'attribute' => '',
        'terms' => '',
        'category' => '',
        'tag' => '',
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
    $all_cata = $all_data['categories'] ?? [];
    $all_tags = $all_data['tags'] ?? [];
    $all_attributes = $all_data['attributes'] ?? [];
    if ($atts['category'] === '' && $atts['attribute'] === '' && $atts['terms'] === '' && $atts['tag'] === '') {
        $shortcode = $dapfforwc_advance_settings["product_shortcode"] ?? 'products'; // Shortcode to search for
        $attributes_list = dapfforwc_get_shortcode_attributes_from_page($post->post_content ?? "", $shortcode);
    }
    $is_all_cata = false;
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
    $filters = $query_params['filters'] ?? null;


    $filteroptionsfromurl = [];

    if (isset($_GET['gm-product-filter-nonce']) && wp_verify_nonce($_GET['gm-product-filter-nonce'], 'gm-product-filter-action')) {
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        if (isset($_GET['product-category'])) {
            $filteroptionsfromurl["product-category[]"] = array_map('sanitize_text_field', explode(",", $_GET['product-category']));
        }
        // check if 'tags' is set and sanitize it
        if (isset($_GET['tags'])) {
            $filteroptionsfromurl["tag[]"] = array_map('sanitize_text_field', explode(",", $_GET['tags']));
        }
        // check if 'pa_color', 'pa_size', etc. are set and sanitize them
        // Dynamically get all attribute taxonomies from $all_attributes
        $attribute_taxonomies = [];
        if (!empty($all_attributes) && is_array($all_attributes)) {
            foreach (array_keys($all_attributes) as $attr_key) {
                $attribute_taxonomies[] = 'pa_' . $attr_key;
            }
        }
        foreach ($attribute_taxonomies as $taxonomy) {
            if (isset($_GET[$taxonomy])) {
                // Convert 'pa_brand' to 'attribute[brand][]' style key
                if (strpos($taxonomy, 'pa_') === 0) {
                    $attr_name = substr($taxonomy, 3);
                    $filteroptionsfromurl["attribute"][$attr_name] = array_map('sanitize_text_field', explode(",", $_GET[$taxonomy]));
                }
            }
        }
        // check if rating is set and sanitize it
        if (isset($_GET['rating'])) {
            $filteroptionsfromurl["rating[]"] = array_map('sanitize_text_field', explode(",", $_GET['rating']));
        }

        // check if mn_price=10&mx_price=100 is set and sanitize it
        if (isset($_GET['mn_price']) && isset($_GET['mx_price'])) {
            $filteroptionsfromurl["min_price"] = sanitize_text_field($_GET['mn_price']);
            $filteroptionsfromurl["max_price"] = sanitize_text_field($_GET['mx_price']);
        }

        // check if plugincy_search is set and sanitize it
        if (isset($_GET['plugincy_search'])) {
            $filteroptionsfromurl["plugincy_search"] = sanitize_text_field($_GET['plugincy_search']);
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
            if (empty($filters) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? $_GET['filters'] : '') === [""]) {
                $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = array_column($all_cata, 'slug');
                if (empty($filteroptionsfromurl)) {
                    $is_all_cata = true;
                }
            }

            $dapfforwc_options['product_show_settings'][$dapfforwc_slug] = [
                'per_page'        => $attributes['limit'] ?? $attributes['per_page'] ?? null,
                'orderby'         => $attributes['orderby'] ?? '',
                'order'           => $attributes['order'] ?? '',
                'operator_second' => $attributes['terms_operator'] ?? $attributes['tag_operator'] ?? $attributes['cat_operator'] ?? 'IN'
            ];

            if (empty($filteroptionsfromurl)) {
                $is_all_cata = true;
            }
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
            $is_all_cata = true;
        }
    }

    if (is_shop() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? $_GET['filters'] : '') === [""] && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
        $all_cata_slugs = array_column($all_cata, 'slug');
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = $all_cata_slugs;
        $is_all_cata = true;
    }

    if (is_product_category()) {
        $current_category = get_queried_object();
        $category_slug = $current_category->slug;
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = [$category_slug];
        if (empty($filteroptionsfromurl)) {
            $is_all_cata = true;
        }
    }

    if (is_product_tag()) {
        $current_tag = get_queried_object();
        $tag_slug = $current_tag->slug;
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["tag[]"] = [$tag_slug];
        if (empty($filteroptionsfromurl)) {
            $is_all_cata = true;
        }
    }
    if (!is_shop() && !is_product_category() && !is_product_tag() && empty($dapfforwc_options['default_filters'][$dapfforwc_slug]) && empty($parsed_filters) && explode(',', isset($_GET['filters']) ? $_GET['filters'] : '') === [""]  && (empty($filteroptionsfromurl) || (!empty($filteroptionsfromurl) && !isset($filteroptionsfromurl["product-category[]"]) && !isset($filteroptionsfromurl["tag[]"]) && !isset($filteroptionsfromurl["attribute"])))) {
        $dapfforwc_options['default_filters'][$dapfforwc_slug] = [];
        $dapfforwc_options['default_filters'][$dapfforwc_slug]["product-category[]"] = array_column($all_cata, 'slug');
        $is_all_cata = true;
    }
    if (isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page']) && $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] === 12) {
        $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] = $atts['per_page'];
    } elseif (!isset($dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'])) {
        $dapfforwc_options['product_show_settings'][$dapfforwc_slug]['per_page'] = $atts['per_page'];
    }

    // Initialize result
    $parsed_filters = [];

    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $prefix = $dapfforwc_seo_permalinks_options["dapfforwc_permalinks_prefix_options"] ?? "";
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
        explode(',', $_GET['filters'] ?? ''),
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

    $formOutPut = "";
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
    $tag_lookup = array_combine(
        array_column($all_tags, 'slug'),
        array_column($all_tags, 'products')
    );
    $all_data_objects = [];
    $all_data_objects["plugincy_search"] = $default_filter["plugincy_search"] ?? "";
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
    if ($second_operator === 'AND') {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_intersect(...array_values($matched_cata_with_ids)));
    } else {
        $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_cata_with_ids))));
    }
    if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
        $matched_tag_with_ids = array_intersect_key($tag_lookup, array_flip(array_filter($default_filter["tag[]"] ?? [])));
    } else {
        // Merge both possible tag sources: 'tag[]' and numeric keys (0,1,2,...)
        $tag_slugs = array_filter($default_filter["tag[]"] ?? []);
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


    if (empty($products_id_by_cata) && empty($products_id_by_tag) && empty($common_values)) {
        $products_ids = [];
    } elseif (empty($products_id_by_cata) && empty($products_id_by_tag) && !empty($common_values)) {
        $products_ids = $common_values;
    } elseif (empty($products_id_by_cata) && !empty($products_id_by_tag) && empty($common_values)) {
        $products_ids = $products_id_by_tag;
    } elseif (!empty($products_id_by_cata) && empty($products_id_by_tag) && empty($common_values)) {
        $products_ids = $products_id_by_cata;
    } elseif (!empty($products_id_by_cata) && !empty($products_id_by_tag) && empty($common_values)) {
        $products_ids = array_values(array_intersect($products_id_by_cata, $products_id_by_tag));
    } elseif (!empty($products_id_by_cata) && empty($products_id_by_tag) && !empty($common_values)) {
        $products_ids = array_values(array_intersect($products_id_by_cata, $common_values));
    } elseif (empty($products_id_by_cata) && !empty($products_id_by_tag) && !empty($common_values)) {
        $products_ids = array_values(array_intersect($products_id_by_tag, $common_values));
    } else {
        $products_ids = array_values(array_intersect($products_id_by_cata, $products_id_by_tag, $common_values));
    }
    if (!empty($products_id_by_rating) && !empty($products_ids)) {
        $products_ids = array_values(array_intersect($products_ids, $products_id_by_rating));
    } elseif (!empty($products_id_by_rating) && empty($products_ids)) {
        $products_ids = $products_id_by_rating;
    }

    $updated_filters = dapfforwc_get_updated_filters($products_ids, $all_data) ?? [];
    // Cache file path
    $cache_file = __DIR__ . '/min_max_prices_cache.json';
    $referer = $_SERVER['REQUEST_URI'];

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

    if (isset($_GET['gm-product-filter-nonce']) && wp_verify_nonce($_GET['gm-product-filter-nonce'], 'gm-product-filter-action')) {
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
    $all_data_objects["max_price"] = isset($default_filter["max_price"]) ? floatval($default_filter["max_price"]) : (isset($dapfforwc_styleoptions["price"]["auto_price"]) ? ceil(floatval($min_max_prices['max'])) : floatval($dapfforwc_styleoptions["price"]["max_price"] ?? 100000000000));


    ob_start(); // Start output buffering
?>
    <style>
        #product-filter .progress-percentage:before {
            content: "0";
            content: "<?php echo esc_html($all_data_objects["min_price"]); ?>";
        }

        #product-filter .progress-percentage:after {
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
                background: linear-gradient(90deg, #041a57, #d62229);
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
                .items {
                    display: flex !important;
                    flex-direction: column;
                }

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
                .items {
                    display: flex !important;
                    flex-direction: column;
                }

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
        <button id="filter-button" style="position: fixed; z-index: 999; bottom: 20px; right: 20px; background-color: #041a57; color: white; border: none; border-radius: 50%; width: min-content; aspect-ratio: 1; display: flex ; align-items: center; justify-content: center;padding: 13px;">
            <svg style=" width: 20px; fill: #fff; " xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512" role="graphics-symbol" aria-hidden="false" aria-label="">
                <path d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z"></path>
            </svg>
        </button>
        <div class="mobile-filter">
            <div class="sm-top-btn" id="mobileonly" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding: 20px;margin-bottom: 10px;">
                <button id="filter-cancel-button" style="background: none;padding:0;color: #000;"> Cancel </button>
                <p style="margin: 0;" id="rcountproduct">Show(5)</p>
            </div>
        <?php
        echo '<div class="rfilterselected" id="mobileonly"><div><ul></ul></div></div>';
    }
    if ($atts['mobile_responsive'] === 'style_3') { ?>
            <script>
                jQuery(document).ready(function($) {
                    function isMobile() {
                        return $(window).width() < 768; // Adjust the width as needed
                    }

                    if (isMobile()) {
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
            </script>
        <?php }

    if ($atts['mobile_responsive'] === 'style_4') { ?>
            <script>
                jQuery(document).ready(function($) {
                    function isMobile() {
                        return $(window).width() < 768; // Adjust the width as needed
                    }

                    if (isMobile()) {
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
            </script>
        <?php } ?>


        <form id="product-filter" method="POST" data-mobile-style='<?php echo $atts['mobile_responsive']; ?>'
            data-product_show_settings='
        <?php
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
                $price_from_url = $_GET[$price_prefix];
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
            if (!empty($all_data_objects) && is_array($all_data_objects)) {
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
                "plugincy_search" => isset($all_data_objects["plugincy_search"]) ? $all_data_objects["plugincy_search"] : "",
                "rating[]" => isset($all_data_objects["rating[]"]) ? $all_data_objects["rating[]"] : [],
            ];
            echo dapfforwc_filter_form($updated_filters, !$is_all_cata || (isset($dapfforwc_advance_settings["default_value_selected"]) && $dapfforwc_advance_settings["default_value_selected"] === 'on') ? $all_data_objects : $default_data_objects, $use_anchor, $use_filters_word, $atts, $min_price, $max_price, $min_max_prices, '', false, false);
            echo $formOutPut;
            echo '</form>';
            if ($atts['mobile_responsive'] === 'style_3' || $atts['mobile_responsive'] === 'style_4') { ?>
        </div>
    <?php }
    ?>

    <!-- Loader HTML -->
    <?php
    global $allowed_tags;
    echo $dapfforwc_options["loader_html"] ?? '<div id="loader" style="display:none;"></div>'; ?>
    <style>
        <?php echo wp_kses($dapfforwc_options["loader_css"] ?? '#loader {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: conic-gradient(#0000 10%, #474bff);
                -webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 9px), #000 0);
                animation: spinner-zp9dbg 1s infinite linear;
            }

            @keyframes spinner-zp9dbg {
                to {
                    transform: rotate(1turn);
                }
            }', $allowed_tags); ?>
    </style>
    <div id="roverlay" style="display: none;"></div>

    <div id="filtered-products">
        <!-- AJAX results will be displayed here -->
    </div>

    <?php if ($atts['mobile_responsive'] === 'style_1') { ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to check if the device is mobile
                function isMobile() {
                    return window.innerWidth <= 768; // Adjust the width as needed
                }

                if (isMobile()) {
                    const titles = document.querySelectorAll('.filter-group .title');
                    const items = document.querySelectorAll('.filter-group .items');

                    // Function to hide all items
                    function hideAllItems() {
                        items.forEach(item => {
                            item.style.setProperty('display', 'none', 'important'); // Use !important to hide items
                        });
                    }

                    // Add click event listener to each title
                    titles.forEach(title => {
                        title.addEventListener('click', function(event) {
                            // Prevent hiding the items when clicking on the title
                            event.stopPropagation();

                            // Toggle the visibility of the items
                            const currentItems = this.nextElementSibling;
                            if (currentItems.style.display === 'block') {
                                currentItems.style.setProperty('display', 'none', 'important'); // Hide items
                            } else {
                                hideAllItems(); // Hide all first                                
                                if (currentItems.classList.contains('image') || currentItems.classList.contains('image_no_border') || currentItems.classList.contains('button_check')) {
                                    currentItems.style.setProperty('display', 'grid', 'important');
                                } else if (currentItems.classList.contains('color') || currentItems.classList.contains('color_no_border') || currentItems.classList.contains('color_circle')) {
                                    currentItems.style.setProperty('display', 'flex', 'important');
                                } else {
                                    currentItems.style.setProperty('display', 'block', 'important');
                                }
                            }
                        });
                    });

                    // Click event to hide all items when clicking outside
                    document.addEventListener('click', function() {
                        hideAllItems();
                    });
                }
            });
        </script>
    <?php } ?>


<?php

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
    $default_max_price = !isset($dapfforwc_styleoptions["price"]["auto_price"]) ? $dapfforwc_styleoptions["price"]["max_price"] : (ceil(floatval($min_max_prices['max'] ?? $max_price)));
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

        case 'color':
        case 'color_no_border':
        case 'color_circle':
        case 'color_value':
            if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
                $sub_option = 'color';
            }
            $color = $dapfforwc_styleoptions[$attribute]['colors'][$value] ?? '#000'; // Default color
            $border = ($sub_option === 'color_no_border') ? 'none' : '1px solid #000';
            $value_show = ($sub_option === 'color_value') ? 'block' : 'none';
            $output .= '<label style="position: relative;"><input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-color" name="' . $name . '[]" value="' . $value . '"' . $checked . '>
                <span class="color-box" style="background-color: ' . $color . '; border: ' . $border . '; width: 30px; height: 30px;"></span><span class="value" style="display:' . $value_show . ';">' . $value . ($count != 0 ? ' (' . $count . ')' : '') . '<span></label>';
            break;

        case 'image':
        case 'image_no_border':
            $image_url = $dapfforwc_styleoptions[$attribute]['images'][$value] ?? 'default-image.jpg';
            $border_class = ($sub_option === 'image_no_border') ? 'no-border' : '';
            $output .= '<label class="image-option ' . $border_class . '">
            <span class="image-title">' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</span>
    <input ' . ($disable_unselected && !$checked ? "disabled" : "") . ' type="' . ($singlevalueSelect === "yes" ? 'radio' : 'checkbox') . '" class="filter-image" name="' . $name . '[]" value="' . $value . '"' . $checked . '>';

            if ($image_url !== 'default-image.jpg') {
                $attachment_id = attachment_url_to_postid($image_url);
                if ($attachment_id) {
                    $output .= wp_get_attachment_image($attachment_id, 'thumbnail', false, array('alt' => esc_attr($title)));
                }
            }

            $output .= '</label>';
            break;

        case 'select2':
        case 'select2_classic':
        case 'select':
            $output .= '<option  ' . ($disable_unselected && !$checked ? "disabled" : "") . ' class="filter-option" value="' . $value . '"' . ($checked ? 'selected' : '') . '> ' . $title . ($count != 0 ? ' (' . $count . ')' : '') . '</option>';
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
      <div class="slider">
        <div class="progress"></div>
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
      <div class="slider">
        <div class="progress"></div>
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
        <div class="slider">
        <div class="progress progress-percentage"></div>
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
    $name = $atts['name'];
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
    $output = '<form class="rfilterbuttons" id="' . $atts['name'] . '"><ul>';
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

    if (!empty($product_ids)) {
        // Get attributes with terms
        if (empty($all_data)) {
            $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
        }

        // Extract categories and tags from all_data
        // Categories
        if (is_array($all_data['categories'] ?? []) || is_object($all_data['categories'] ?? [])) {
            foreach ($all_data['categories'] ?? [] as $term_id => $category) {
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
        if (is_array($all_data['tags'] ?? []) || is_object($all_data['tags'] ?? [])) {
            foreach ($all_data['tags'] ?? [] as $term_id => $tag) {
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

        // Extract attributes
        if (is_array($all_data['attributes'] ?? []) || is_object($all_data['attributes'] ?? [])) {
            foreach ($all_data['attributes'] ?? [] as $attribute) {
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
    }
    return [
        'categories' => array_values($categories), // Return as array
        'attributes' => $attributes,
        'tags' => array_values($tags), // Return as array
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

    $data = ['attributes' => [], 'categories' => [], 'tags' => []];

    // Optimized query with direct attribute taxonomy check
    $query = $wpdb->prepare("
    SELECT t.term_id, t.name, t.slug, tr.object_id, tt.taxonomy, a.attribute_name, a.attribute_label, tt.parent
    FROM {$wpdb->prefix}terms AS t
    INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id
    LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
    LEFT JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies AS a ON tt.taxonomy = CONCAT('pa_', a.attribute_name)
    INNER JOIN {$wpdb->prefix}posts AS p ON tr.object_id = p.ID
    WHERE (tt.taxonomy IN (%s, %s) OR a.attribute_name IS NOT NULL)
    AND p.post_type = 'product' 
    AND p.post_status = 'publish'
    ORDER BY t.term_id
", 'product_cat', 'product_tag');

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

    // Convert associative term arrays to indexed arrays
    foreach ($data['attributes'] as $key => $attr) {
        $data['attributes'][$key]['terms'] = array_values($attr['terms']);
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

    // Query for all products with their meta data, categories, and thumbnail URLs
    $query = "
    SELECT p.ID, p.post_title, p.post_name, p.post_modified, p.menu_order, p.post_excerpt,
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
           (SELECT GROUP_CONCAT(t.name SEPARATOR ', ') FROM {$wpdb->prefix}term_relationships tr
            INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->prefix}terms t ON t.term_id = tt.term_id
            WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_cat') AS categories,
           (SELECT GROUP_CONCAT(t.slug SEPARATOR ', ') FROM {$wpdb->prefix}term_relationships tr
            INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->prefix}terms t ON t.term_id = tt.term_id
            WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_cat') AS category_slugs,
           (SELECT CONCAT('" . home_url() . "/wp-content/uploads/', 
                          pm2.meta_value) 
            FROM {$wpdb->prefix}postmeta pm2 
            WHERE pm2.post_id = p.ID AND pm2.meta_key = '_thumbnail_id') AS thumbnail_id,
           (SELECT guid FROM {$wpdb->prefix}posts WHERE ID = (SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = p.ID AND meta_key = '_thumbnail_id')) AS product_image
    FROM {$wpdb->prefix}posts p
    LEFT JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
    WHERE p.post_type = 'product'
    AND p.post_status = 'publish'
    GROUP BY p.ID
    ";

    $results = $wpdb->get_results($query, ARRAY_A);
    $products = [];

    if (!empty($results)) {
        foreach ($results as $row) {
            $product_id = $row['ID'];

            // Determine product type and pricing
            $product_type = $row['product_type'] ?: 'simple';
            $price = '';
            $sale_active = false;

            if ($product_type === 'variable') {
                $price = $row['min_variation_price'] ?: '';
                $min_sale_price = $row['min_variation_sale_price'] ?: '';
                $sale_active = !empty($min_sale_price) && $min_sale_price == $price;
            } else {
                $regular_price = $row['regular_price'] ?: '';
                $sale_price = $row['sale_price'] ?: '';
                $price = $row['price'] ?: $regular_price;
                $sale_active = !empty($sale_price) && $sale_price == $price;
            }

            // Get rating
            $rating = floatval($row['average_rating']) ?: 0;

            // Get product image directly from the query
            $product_image = $row['product_image'];

            // Get product categories
            $product_category = array_map(function ($name, $slug) {
                return ['name' => $name, 'slug' => $slug];
            }, explode(', ', $row['categories']), explode(', ', $row['category_slugs']));

            $products[$product_id] = [
                'ID' => $product_id,
                'post_title' => $row['post_title'],
                'post_name' => $row['post_name'],
                'price' => $price,
                'rating' => $rating,
                'post_modified' => $row['post_modified'],
                'menu_order' => intval($row['menu_order']),
                'on_sale' => $sale_active,
                'product_image' => $product_image,
                'product_excerpt' => $row['post_excerpt'],
                'product_sku' => $row['sku'] ?: '',
                'product_stock' => $row['stock_status'] ?: 'instock',
                'product_category' => $product_category,
            ];
        }
    }

    // Convert to indexed array for better JSON compatibility
    $product_data = ['products' => $products];

    // Save to cache with error handling
    file_put_contents($cache_file, json_encode($product_data, JSON_UNESCAPED_UNICODE));

    return $product_data;
}



// Clear cache when a term is updated
add_action('edited_term', function ($term_id) {
    $cache_file = __DIR__ . '/woocommerce_attributes_cache.json';
    if (file_exists($cache_file)) {
        wp_delete_file($cache_file);
    }
});

// Clear cache when a product is updated
add_action('save_post_product', function ($post_id) {
    $cache_file = __DIR__ . '/woocommerce_attributes_cache.json';
    if (file_exists($cache_file)) {
        wp_delete_file($cache_file);
    }
});
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
