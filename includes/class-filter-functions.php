<?php
if (!defined('ABSPATH')) {
    exit;
}
class dapfforwc_Filter_Functions
{

    public function process_filter()
    {
        global $dapfforwc_options, $dapfforwc_styleoptions, $dapfforwc_advance_settings, $dapfforwc_front_page_slug;
        // Initialize variables with default values
        $update_filter_options =  isset($dapfforwc_options["update_filter_options"]) ? $dapfforwc_options["update_filter_options"] : "";
        $remove_outofStock_product = isset($dapfforwc_advance_settings["remove_outofStock"]) ? $dapfforwc_advance_settings["remove_outofStock"] : "";

        if (!isset($_POST['gm-product-filter-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
            wp_send_json_error(array('message' => 'Security check failed'), 403);
            wp_die();
        }
        // Determine the current page number
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $currentpage_slug = isset($_POST['current-page']) ? sanitize_text_field(wp_unslash($_POST['current-page'])) : "";
        $orderby = $this->get_orderby() !== "" ? $this->get_orderby() : ($wcapf_options['product_show_settings'][$currentpage_slug]['orderby'] ?? 'date');
        $currentpage_slug = $currentpage_slug == "/" ? $dapfforwc_front_page_slug : $currentpage_slug;
        $default_filter = [];
        // Check if 'selectedvalues' is set and not empty
        if (!empty($_POST['selectedvalues'])) {
            // Convert the string to an array
            if (is_string($_POST['selectedvalues'])) {
                $decoded_values = json_decode(stripslashes($_POST['selectedvalues']), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_values)) {
                    $default_filter = [];
                    foreach ($decoded_values as $key => $values) {
                        if (is_array($values)) {
                            $default_filter[$key] = array_map('sanitize_text_field', $values);
                        }
                    }
                } else {
                    $default_filter = array_map('sanitize_text_field', explode(',', sanitize_text_field(wp_unslash($_POST['selectedvalues']))));
                }
            } else {
                $default_filter = [];
            }
        }
        error_log("Selected Values: " . json_encode($default_filter));
        $ratings = array_values(array_filter($default_filter, 'is_numeric'));
        $second_operator = isset($dapfforwc_options["product_show_settings"][$currentpage_slug]["operator_second"]) ? strtoupper($dapfforwc_options["product_show_settings"][$currentpage_slug]["operator_second"]) : "IN";
        $product_details = array_values(dapfforwc_get_woocommerce_product_details()["products"] ?? []);
        $product_details_json = dapfforwc_get_woocommerce_product_details()["products"] ?? [];
        $products_id_by_rating = [];
        if (!empty($ratings)) {
            // Get product ids by rating
            foreach ($ratings as $rating) {
                $products_id_by_rating[] = array_column(array_filter($product_details, function ($product) use ($rating) {
                    return $product['rating'] == $rating;
                }), 'ID');
            }
            $products_id_by_rating = array_merge(...$products_id_by_rating);
        }
        $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
        $all_cata = $all_data['categories'] ?? [];
        $all_tags = $all_data['tags'] ?? [];
        $all_attributes = $all_data['attributes'] ?? [];
        // Create Lookup Arrays
        $cata_lookup = array_combine(
            array_column($all_cata, 'slug'),
            array_column($all_cata, 'products')
        );
        $tag_lookup = array_combine(
            array_column($all_tags, 'slug'),
            array_column($all_tags, 'products')
        );
        // Match Filters
        $matched_cata_with_ids = [];
        if (is_array($default_filter) || is_object($default_filter)) {
            if (isset($default_filter['category[]'])) {
                $categories = $default_filter['category[]'];
                $matched_cata_with_ids = array_intersect_key($cata_lookup, array_flip(array_filter($categories)));
            } 
            elseif (array_keys($default_filter) !== range(0, count($default_filter) - 1)) {
                $default_filter['category[]'] = [];
                $matched_cata_with_ids = [];
            }
            elseif (is_array($default_filter)) {
                $categories = array_filter($default_filter, function ($item) use ($cata_lookup) {
                    return array_key_exists($item, $cata_lookup);
                });
                $matched_cata_with_ids = array_intersect_key($cata_lookup, array_flip($categories));
            }
        }
        error_log("Matched Categories: " . json_encode($matched_cata_with_ids));
        if ($second_operator === 'AND') {
            $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_intersect(...array_values($matched_cata_with_ids));
        } else {
            $products_id_by_cata = empty($matched_cata_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_cata_with_ids))));
        }
        error_log("Products ID by Category: " . json_encode($products_id_by_cata));
        $matched_tag_with_ids = [];
        error_log("Type of default_filter: " . gettype($default_filter));
        if (is_array($default_filter) || is_object($default_filter)) {
            if (isset($default_filter['tag[]'])) {
                $tags = $default_filter['tag[]'];
                $matched_tag_with_ids = array_intersect_key($tag_lookup, array_flip(array_filter($tags)));
            } 
            elseif (array_keys($default_filter) !== range(0, count($default_filter) - 1)) {
                $default_filter['tag[]'] = [];
                $matched_tag_with_ids = [];
            }
            elseif (is_array($default_filter)) {
                $tags = array_filter($default_filter, function ($item) use ($tag_lookup) {
                    return array_key_exists($item, $tag_lookup);
                });
                $matched_tag_with_ids = array_intersect_key($tag_lookup, array_flip($tags));
            }
        }
        if ($second_operator === 'AND') {
            $products_id_by_tag = empty($matched_tag_with_ids) ? [] : array_intersect(...array_values($matched_tag_with_ids));
        } else {
            $products_id_by_tag = empty($matched_tag_with_ids) ? [] : array_values(array_unique(array_merge(...array_values($matched_tag_with_ids))));
        }

        error_log("Matched Tags: " . json_encode($matched_tag_with_ids));
        error_log("Products ID by Tag: " . json_encode($products_id_by_tag));
        // Match Attributes
        $products_id_by_attributes = [];
        $match_attributes_with_ids = [];

        // Process the default filter - handle both formats
        $attribute_filters = [];

        // Case 1: Object format with "attribute[key][]"
        if (is_array($default_filter) && !empty(array_filter(array_keys($default_filter), 'is_string'))) {
            foreach ($default_filter as $key => $values) {
                if (preg_match('/^attribute\[(.*?)\]/i', $key, $matches)) {
                    $attribute_name = $matches[1];
                    // Handle both single value and array of values
                    $attribute_filters[$attribute_name] = is_array($values) ? $values : [$values];
                }
            }
        }
        // Case 2: Simple array format
        else if (is_array($default_filter)) {
            // For flat array, we'll match these against all attribute terms
            $attribute_filters = ['__all__' => $default_filter]; // Use special key to indicate matching against all attributes
        }

        // Process attributes data
        if ((is_array($all_attributes) || is_object($all_attributes))) {
            foreach ($all_data['attributes'] as $taxonomy => $lookup) {
                // Ensure 'terms' key exists and is an array
                if (isset($lookup['terms']) && is_array($lookup['terms'])) {
                    foreach ($lookup['terms'] as $term) {
                        // Check if this term should be included based on filters
                        
                        $include_term = false;

                        // Case 1: If we have specific attribute filters
                        if (isset($attribute_filters[$taxonomy]) && in_array($term['slug'], $attribute_filters[$taxonomy])) {
                            $include_term = true;
                        }
                        // Case 2: For flat array format, check against all terms
                        elseif (isset($attribute_filters['__all__']) && in_array($term['slug'], $attribute_filters['__all__'])) {
                            $include_term = true;
                        }

                        if ($include_term) {
                            $match_attributes_with_ids[$taxonomy][] = $term['products'];
                        }
                    }
                }
            }
        }

        // First process each attribute to get a single array per attribute
        $processed_attributes = [];
        foreach ($match_attributes_with_ids as $attribute_name => $value_arrays) {
            if (empty($value_arrays)) {
                $processed_attributes[$attribute_name] = [];
                continue;
            }

            if ($second_operator === 'AND') {
                // For AND, find common IDs within this attribute's arrays
                if (count($value_arrays) == 1) {
                    $processed_attributes[$attribute_name] = $value_arrays[0];
                } else {
                    $processed_attributes[$attribute_name] = array_intersect(...$value_arrays);
                }
            } else {
                // For IN, merge all IDs within this attribute's arrays
                $merged = [];
                foreach ($value_arrays as $ids) {
                    $merged = array_merge($merged, $ids);
                }
                $processed_attributes[$attribute_name] = array_unique($merged);
            }
        }

        // Then combine results across different attributes
        if (empty($processed_attributes)) {
            $products_id_by_attributes = [];
        } else {
            $attribute_values = array_values($processed_attributes);
            if ($second_operator === 'AND') {
                // Intersection between different attributes
                $products_id_by_attributes = count($attribute_values) == 1
                    ? $attribute_values[0]
                    : array_intersect(...$attribute_values);
            } else {
                // Union between different attributes
                $products_id_by_attributes = array_unique(array_merge(...$attribute_values));
            }
        }

        // Convert to indexed array if it's not already
        $products_id_by_attributes = array_values($products_id_by_attributes);

        error_log("Matched Attributes: " . json_encode($match_attributes_with_ids));
        error_log("second operator: " . json_encode($second_operator));
        error_log("Products ID by Attributes: " . json_encode($products_id_by_attributes));

        if (empty($products_id_by_cata) && empty($products_id_by_tag) && empty($products_id_by_attributes)) {
            $products_ids = [];
        } elseif (empty($products_id_by_cata) && empty($products_id_by_tag) && !empty($products_id_by_attributes)) {
            $products_ids = $products_id_by_attributes;
        } elseif (empty($products_id_by_cata) && !empty($products_id_by_tag) && empty($products_id_by_attributes)) {
            $products_ids = $products_id_by_tag;
        } elseif (!empty($products_id_by_cata) && empty($products_id_by_tag) && empty($products_id_by_attributes)) {
            $products_ids = $products_id_by_cata;
        } elseif (!empty($products_id_by_cata) && !empty($products_id_by_tag) && empty($products_id_by_attributes)) {
            $products_ids = array_values(array_intersect($products_id_by_cata, $products_id_by_tag));
        } elseif (!empty($products_id_by_cata) && empty($products_id_by_tag) && !empty($products_id_by_attributes)) {
            $products_ids = array_values(array_intersect($products_id_by_cata, $products_id_by_attributes));
        } elseif (empty($products_id_by_cata) && !empty($products_id_by_tag) && !empty($products_id_by_attributes)) {
            $products_ids = array_values(array_intersect($products_id_by_tag, $products_id_by_attributes));
        } else {
            $products_ids = array_values(array_intersect($products_id_by_cata, $products_id_by_tag, $products_id_by_attributes));
        }
        if (!empty($products_id_by_rating)) {
            $products_ids = array_values(array_intersect($products_ids, $products_id_by_rating));
        }
        $price_search_value = $this->getpricevalue_search();
        $products_id_by_price = [];
        $products_id_by_search_term = [];
        if (!empty($price_search_value)) {
            $products_id_by_price = array_filter($product_details, function ($product) use ($price_search_value, $remove_outofStock_product) {
                if ($remove_outofStock_product !== "on" && empty($product['price'])) {
                    return true;
                }
                return $product['price'] >= $price_search_value['min'] && $product['price'] <= $price_search_value['max'];
            });
            $products_id_by_price = array_column($products_id_by_price, 'ID');

            if (!empty($price_search_value['s'])) {
                $products_id_by_search_term = array_filter($product_details, function ($product) use ($price_search_value) {
                    return strpos(strtolower($product['post_title']), strtolower($price_search_value['s'])) !== false;
                });
                $products_id_by_search_term = array_column($products_id_by_search_term, 'ID');
            }
        }
        if (!empty($products_id_by_price)) {
            $products_ids = array_intersect($products_ids, $products_id_by_price);
        }
        if (!empty($products_id_by_search_term)) {
            $products_ids = array_intersect($products_ids, $products_id_by_search_term);
        }
        $missing_product_ids = array_diff($products_ids, array_keys($product_details_json));
        if (!empty($missing_product_ids)) {
            $missing_products = wc_get_products(array('include' => $missing_product_ids));
            foreach ($missing_products as $product) {
                $product_details_json[$product->get_id()] = [
                    'ID' => $product->get_id(),
                    'post_title' => $product->get_title(),
                    'post_name' => $product->get_slug(),
                    'price' => $product->get_price(),
                    'product_image' => $product->get_image(),
                    'product_excerpt' => $product->get_short_description(),
                    'rating' => $product->get_average_rating(),
                    'product_category' => wc_get_product_category_list($product->get_id()),
                    'product_sku' => $product->get_sku(),
                    'product_stock' => $product->get_stock_quantity(),
                    'on_sale' => $product->is_on_sale(),
                    'menu_order' => $product->get_menu_order(),
                    'post_modified' => $product->get_date_modified()->date('Y-m-d H:i:s')
                ];
            }
        }
        // Order products based on $orderby
        if (!empty($orderby)) {
            $orderby = $orderby === 'menu_order date' ? 'menu_order' : ($orderby === 'date' ? 'post_modified' : $orderby);
            usort($products_ids, function ($a, $b) use ($product_details_json, $orderby) {
                if (!isset($product_details_json[$a][$orderby]) || !isset($product_details_json[$b][$orderby])) {
                    return 0;
                }
                return $product_details_json[$a][$orderby] <=> $product_details_json[$b][$orderby];
            });
        }
        $count_total_showing_product = count($products_ids);

        $updated_filters = dapfforwc_get_updated_filters($products_ids);

        $min_max_prices = dapfforwc_get_min_max_price($product_details, $products_ids);



        $min_price = isset($_POST['min_price']) ? floatval(sanitize_text_field(wp_unslash($_POST['min_price']))) : ($dapfforwc_styleoptions["price"]["min_price"] ?? $min_max_prices['min']);

        $max_price = isset($_POST['max_price']) ? floatval(sanitize_text_field(wp_unslash($_POST['max_price']))) : ($dapfforwc_styleoptions["price"]["max_price"] ?? $min_max_prices['max'] + 1);

        // Pass sanitized values to the function
        $filterform = dapfforwc_filter_form($updated_filters, $default_filter, "", "", "", $min_price, $max_price, [], $price_search_value['s'] ?? '');
        $cache_file = __DIR__ . '/permalinks_cache.json';
        $cache_time = 12 * 60 * 60; // 12 hours in seconds

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
            $permalinks = json_decode(file_get_contents($cache_file), true);
        } else {
            $permalinks = get_option('woocommerce_permalinks');
            file_put_contents($cache_file, json_encode($permalinks));
        }

        // Capture the product listing
        ob_start();

        
        $per_page = isset($dapfforwc_options["product_show_settings"][$currentpage_slug]["per_page"]) ? intval($dapfforwc_options["product_show_settings"][$currentpage_slug]["per_page"]) : 12;
        $total_pages = ceil($count_total_showing_product / $per_page) ?? 1;
        $start_index = ($paged - 1) * $per_page;
        $end_index = min($start_index + $per_page, $count_total_showing_product);

        for ($i = $start_index; $i < $end_index; $i++) {
            if (isset($products_ids[$i])) {
                $product_id = $products_ids[$i];
                if (isset($product_details_json[$product_id])) {
                    $product = $product_details_json[$product_id];
                    $this->display_product($product, $currentpage_slug, $permalinks);
                }
            }
        }

        $product_html = ob_get_clean();

        // Send both the filtered products and updated filters back to the AJAX request
        wp_send_json_success(array(
            'products' => $product_html,
            'total_product_fetch' => $count_total_showing_product,
            'pagination' => $this->pagination($paged, $total_pages),
            'filter_options' => $filterform
        ));

        wp_die();
    }
    private function get_orderby()
    {
        if (!isset($_POST['gm-product-filter-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
            wp_send_json_error(array('message' => 'Security check failed'), 403);
            wp_die();
        }
        return isset($_POST['orderby']) && $_POST['orderby'] !== "undefined" ? sanitize_text_field(wp_unslash($_POST['orderby'])) : "";
    }
    private function getpricevalue_search()
    {
        $price_search_txt = [];
        if (!isset($_POST['gm-product-filter-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gm-product-filter-nonce'])), 'gm-product-filter-action')) {
            wp_send_json_error(array('message' => 'Security check failed'), 403);
            wp_die();
        }
        if (isset($_POST['min_price']) && $_POST['min_price'] !== '') {
            $price_search_txt["min"] = floatval($_POST['min_price']);
        }

        // Maximum Price Filter
        if (isset($_POST['max_price']) && $_POST['max_price'] !== '') {
            $price_search_txt["max"] = floatval($_POST['max_price']);
        }
        if (!empty($_POST['s'])) {
            $search_term = sanitize_text_field(wp_unslash($_POST['s']));
            $price_search_txt["s"] = $search_term;
        }

        return $price_search_txt;
    }
    private function display_product($product, $currentpage_slug, $permalinks)
    {
        global $dapfforwc_options, $allowed_tags;
        // Get product details
        $product_link = home_url($permalinks['product_base'] . '/' . $product['post_name']);
        $product_title = $product['post_title'];
        $product_price = $product['price'];
        $product_image = $product['product_image'] === null ? '/wp-content/uploads/woocommerce-placeholder-300x300.png' : $product['product_image'];
        $attachment_id = attachment_url_to_postid($product_image);
        $product_excerpt = $product['product_excerpt'];
        $rating = $product['rating'];
        $product_category = $product['product_category'];
        $cata_output = "";
        foreach (is_array($product_category) ? $product_category : [] as $index => $category) {
            $cata_output .= '<a href="' . home_url($permalinks['category_base'] . '/' . $category['slug']) . '">' . htmlspecialchars($category['name']) . '</a>';
            if ($index < count($product_category) - 1) {
                $cata_output .= ', ';
            }
        }
        $product_sku = $product['product_sku'];
        $product_stock = $product['product_stock'];
        $on_sale = $product['on_sale'];
        $add_to_cart_url = esc_url(add_query_arg('add-to-cart', $product['ID'], $product_link));
        if (isset($dapfforwc_options['use_custom_template']) && $dapfforwc_options['use_custom_template'] === "on" && in_array($currentpage_slug, $dapfforwc_options['use_custom_template_in_page'] ?? [])) {

            // Retrieve the custom template from the database
            $custom_template = $dapfforwc_options['custom_template_code'];

            // Replace placeholders with actual values
            $custom_template = str_replace('{{product_link}}', esc_url($product_link), $custom_template);
            $custom_template = str_replace('{{product_title}}', esc_html($product_title), $custom_template);
            $custom_template = str_replace('{{product_image}}', esc_url($product_image), $custom_template);
            $custom_template = str_replace('{{product_excerpt}}', apply_filters('the_excerpt', $product_excerpt), $custom_template);
            $custom_template = str_replace('{{product_price}}', wp_kses_post($product_price), $custom_template);
            $custom_template = str_replace('{{product_category}}', $cata_output, $custom_template);
            $custom_template = str_replace('{{product_sku}}', esc_html($product_sku), $custom_template);
            $custom_template = str_replace('{{product_stock}}', esc_html($product_stock), $custom_template);
            $custom_template = str_replace('{{add_to_cart_url}}', $add_to_cart_url, $custom_template);
            $custom_template = str_replace('{{product_id}}', esc_html($product['ID']), $custom_template);


            echo wp_kses(do_shortcode($custom_template), $allowed_tags);
        } else {

            $current_theme = wp_get_theme();
            if ($current_theme->get('Name') === 'Astra') {
                echo '<li class="product type-product" style="margin: 10px; padding: 0px;">
	<div class="astra-shop-thumbnail-wrap">
	<a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
    ' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'woocommerce-placeholder wp-post-image')) . '
        </a>
        ' . ($on_sale ? '<span class="ast-on-card-button ast-onsale-card" data-notification="default">Sale!</span>' : '') . '
        <a href="?add-to-cart=' . esc_attr($product['ID']) . '" data-quantity="1" class="ast-on-card-button ast-select-options-trigger product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow" style="display:none;"> <span class="ast-card-action-tooltip"> Add to cart </span> <span class="ahfb-svg-iconset"> <span class="ast-icon icon-bag"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="ast-bag-icon-svg" x="0px" y="0px" width="100" height="100" viewBox="826 826 140 140" enable-background="new 826 826 140 140" xml:space="preserve">
                    <path d="M960.758,934.509l2.632,23.541c0.15,1.403-0.25,2.657-1.203,3.761c-0.953,1.053-2.156,1.579-3.61,1.579H833.424  c-1.454,0-2.657-0.526-3.61-1.579c-0.952-1.104-1.354-2.357-1.203-3.761l2.632-23.541H960.758z M953.763,871.405l6.468,58.29H831.77  l6.468-58.29c0.15-1.203,0.677-2.218,1.58-3.045c0.903-0.827,1.981-1.241,3.234-1.241h19.254v9.627c0,2.658,0.94,4.927,2.82,6.807  s4.149,2.82,6.807,2.82c2.658,0,4.926-0.94,6.807-2.82s2.821-4.149,2.821-6.807v-9.627h28.882v9.627  c0,2.658,0.939,4.927,2.819,6.807c1.881,1.88,4.149,2.82,6.807,2.82s4.927-0.94,6.808-2.82c1.879-1.88,2.82-4.149,2.82-6.807v-9.627  h19.253c1.255,0,2.332,0.414,3.235,1.241C953.086,869.187,953.612,870.202,953.763,871.405z M924.881,857.492v19.254  c0,1.304-0.476,2.432-1.429,3.385s-2.08,1.429-3.385,1.429c-1.303,0-2.432-0.477-3.384-1.429c-0.953-0.953-1.43-2.081-1.43-3.385  v-19.254c0-5.315-1.881-9.853-5.641-13.613c-3.76-3.761-8.298-5.641-13.613-5.641s-9.853,1.88-13.613,5.641  c-3.761,3.76-5.641,8.298-5.641,13.613v19.254c0,1.304-0.476,2.432-1.429,3.385c-0.953,0.953-2.081,1.429-3.385,1.429  c-1.303,0-2.432-0.477-3.384-1.429c-0.953-0.953-1.429-2.081-1.429-3.385v-19.254c0-7.973,2.821-14.779,8.461-20.42  c5.641-5.641,12.448-8.461,20.42-8.461c7.973,0,14.779,2.82,20.42,8.461C922.062,842.712,924.881,849.519,924.881,857.492z"></path>
                    </svg></span> </span> </a></div><div class="astra-shop-summary-wrap">			<span class="ast-woo-product-category" style="
    font-size: 15px;
">
                    ' . wp_kses_post($cata_output) . '			</span>
                <a href="' . esc_url($product_link) . '" class="ast-loop-product__link"><h2 class="woocommerce-loop-product__title">' . esc_html($product_title) . '</h2></a>
            <div class="review-rating"><div class="star-rating"><span style="width:' . (esc_attr($rating) * 20) . '%">Rated <strong class="rating">' . esc_html($rating) . '</strong> out of 5</span></div></div>
    <span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</bdi></span></span>
<a href="?add-to-cart=' . esc_attr($product['ID']) . '" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_html($product_title) . '”" rel="nofollow" data-success_message="“' . esc_html($product_title) . '” has been added to your cart">Add to cart</a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</div></li>';
            } elseif ($current_theme->get('Name') === 'Hello Elementor') {
                echo '<li class="product type-product post-' . esc_attr($product['ID']) . ' status-publish instock product_cat-diabetic-wellness product_tag-accu-answer-4-in-1 product_tag-accurate-diabetes-care product_tag-diabetic-machine sale shipping-taxable purchasable product-type-simple">
	<a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
    ' . ($on_sale ? '<span class="onsale" data-notification="default">Sale!</span>' : '') . '
    ' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'woocommerce-placeholder wp-post-image')) . '
	<h2 class="woocommerce-loop-product__title">' . esc_attr($product_title) . '</h2>
	<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</bdi></span></span>
</a><a href="?add-to-cart=' . esc_attr($product['ID']) . '" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow" data-success_message="“' . esc_attr($product_title) . '” has been added to your cart">Add to cart</a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</li>';
            } elseif ($current_theme->get('Name') === 'Kadence') {
                echo '<li class="entry content-bg loop-entry product type-product post-' . esc_attr($product['ID']) . ' status-publish last instock product_cat-diabetic-wellness product_tag-accu-answer-4-in-1 product_tag-accurate-diabetes-care product_tag-diabetic-machine sale shipping-taxable purchasable product-type-simple">
	<a href="' . esc_url($product_link) . '" class="woocommerce-loop-image-link woocommerce-LoopProduct-link woocommerce-loop-product__link">
	' . ($on_sale ? '<span class="onsale" data-notification="default">Sale!</span>' : '') . '
	' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'woocommerce-placeholder wp-post-image')) . '
	</a><div class="product-details content-bg entry-content-wrap"><h2 class="woocommerce-loop-product__title"><a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link-title woocommerce-loop-product__title_ink">' . esc_attr($product_title) . '</a></h2>
	<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</bdi></span></span>
   
<div class="product-excerpt"><p>' . wp_kses($product_excerpt, $allowed_tags) . '</p></div>
<div class="product-action-wrap"><a href="?add-to-cart=' . esc_attr($product['ID']) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow" data-success_message="“' . esc_attr($product_title) . '” has been added to your cart">Add to cart<span class="kadence-svg-iconset svg-baseline"><svg aria-hidden="true" class="kadence-svg-icon kadence-arrow-right-alt-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="27" height="28" viewBox="0 0 27 28"><title>Continue</title><path d="M27 13.953c0 0.141-0.063 0.281-0.156 0.375l-6 5.531c-0.156 0.141-0.359 0.172-0.547 0.094-0.172-0.078-0.297-0.25-0.297-0.453v-3.5h-19.5c-0.281 0-0.5-0.219-0.5-0.5v-3c0-0.281 0.219-0.5 0.5-0.5h19.5v-3.5c0-0.203 0.109-0.375 0.297-0.453s0.391-0.047 0.547 0.078l6 5.469c0.094 0.094 0.156 0.219 0.156 0.359v0z"></path>
				</svg></span><span class="kadence-svg-iconset svg-baseline"><svg class="kadence-svg-icon kadence-spinner-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><title>Loading</title><path d="M16 6h-6l2.243-2.243c-1.133-1.133-2.64-1.757-4.243-1.757s-3.109 0.624-4.243 1.757c-1.133 1.133-1.757 2.64-1.757 4.243s0.624 3.109 1.757 4.243c1.133 1.133 2.64 1.757 4.243 1.757s3.109-0.624 4.243-1.757c0.095-0.095 0.185-0.192 0.273-0.292l1.505 1.317c-1.466 1.674-3.62 2.732-6.020 2.732-4.418 0-8-3.582-8-8s3.582-8 8-8c2.209 0 4.209 0.896 5.656 2.344l2.343-2.344v6z"></path>
				</svg></span><span class="kadence-svg-iconset svg-baseline"><svg class="kadence-svg-icon kadence-check-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><title>Done</title><path d="M14 2.5l-8.5 8.5-3.5-3.5-1.5 1.5 5 5 10-10z"></path>
				</svg></span></a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</div></div></li>';
            } elseif ($current_theme->get('Name') === 'Blocksy') {
                echo '<li class="product type-product post-' . esc_attr($product['ID']) . ' status-publish last instock product_cat-diabetic-wellness product_tag-accu-answer-4-in-1 product_tag-accurate-diabetes-care product_tag-diabetic-machine sale shipping-taxable purchasable product-type-simple">
	<figure>
	' . ($on_sale ? '<span class="onsale" data-shape="type-2">Sale!</span>' : '') . '
	<a class="ct-media-container" href="' . esc_url($product_link) . '" aria-label="' . esc_attr($product_title) . '">' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'woocommerce-placeholder wp-post-image')) . '</a></figure><h2 class="woocommerce-loop-product__title"><a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="' . esc_url($product_link) . '" target="_self">' . esc_attr($product_title) . '</a></h2>
	<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</bdi></span></span>
<ul class="entry-meta" data-type="simple:none" data-id="default">' . wp_kses_post($cata_output) . '</ul><div class="ct-woo-card-actions" data-add-to-cart="auto-hide" data-alignment="equal"><a href="?add-to-cart=' . esc_attr($product['ID']) . '" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow" data-success_message="“' . esc_attr($product_title) . '” has been added to your cart">Add to cart</a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</div></li>';
            } elseif ($current_theme->get('Name') === 'Woostify') {
                // $args = array(
                //     'product_link' => $product_link,
                //     'product_title' => $product_title,
                //     'product' => $product,
                // );

                // // Set up global product variable to ensure WooCommerce functions work
                // $GLOBALS['product'] = wc_get_product($product['ID']);
                // echo print_r($GLOBALS['product'], true);
                // // Load the WooCommerce product template part
                // wc_get_template_part('content', 'product', $args);

                echo '<li class="product type-product post-' . esc_attr($product['ID']) . ' status-publish last instock product_cat-diabetic-wellness product_tag-accu-answer-4-in-1 product_tag-accurate-diabetes-care product_tag-diabetic-machine sale shipping-taxable purchasable product-type-simple">
			<div class="product-loop-wrapper">
		<div class="product-loop-image-wrapper">' . ($on_sale ? '<span class="woostify-tag-on-sale onsale sale-left" data-shape="type-2">Sale!</span>' : '') . '
					<div class="product-loop-action"></div>
		<a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-loop-image')) . '</a></div><div class="product-loop-content text-center">		<h2 class="woocommerce-loop-product__title">
			<a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' . esc_attr($product_title) . '</a>		</h2>
		<div class="product-loop-meta "><div class="animated-meta">			<span class="price"><ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</span></ins><span class="screen-reader-text">Current price is: $' . esc_html($product_price) . '.</span></span>
			<a href="?add-to-cart=' . esc_attr($product['ID']) . '" data-quantity="1" class="loop-add-to-cart-btn button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" title="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow"><span class="woostify-svg-icon icon-shopping-cart-2"><svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" width="17" height="17" viewBox="0 0 17 17" id="svg50" sodipodi:docname="shopping-cart-2.svg" inkscape:version="1.0.2-2 (e86c870879, 2021-01-15)">
  <metadata id="metadata56">
    <rdf:rdf>
      <cc:work rdf:about="">
        <dc:format>image/svg+xml</dc:format>
        <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"></dc:type>
        <dc:title></dc:title>
      </cc:work>
    </rdf:rdf>
  </metadata>
  <defs id="defs54"></defs>
  <sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" inkscape:pageopacity="0" inkscape:pageshadow="2" inkscape:window-width="2400" inkscape:window-height="1271" id="namedview52" showgrid="false" inkscape:zoom="48.823529" inkscape:cx="8.5" inkscape:cy="8.5" inkscape:window-x="2391" inkscape:window-y="-9" inkscape:window-maximized="1" inkscape:current-layer="svg50"></sodipodi:namedview>
  <g id="g46" transform="matrix(-1,0,0,1,16.926,0)"></g>
  <path d="m 14.176,12.5 c 0.965,0 1.75,0.785 1.75,1.75 0,0.965 -0.785,1.75 -1.75,1.75 -0.965,0 -1.75,-0.785 -1.75,-1.75 0,-0.965 0.785,-1.75 1.75,-1.75 z m 0,2.5 c 0.414,0 0.75,-0.337 0.75,-0.75 0,-0.413 -0.336,-0.75 -0.75,-0.75 -0.414,0 -0.75,0.337 -0.75,0.75 0,0.413 0.336,0.75 0.75,0.75 z m -8.5,-2.5 c 0.965,0 1.75,0.785 1.75,1.75 0,0.965 -0.785,1.75 -1.75,1.75 -0.965,0 -1.75,-0.785 -1.75,-1.75 0,-0.965 0.785,-1.75 1.75,-1.75 z m 0,2.5 c 0.414,0 0.75,-0.337 0.75,-0.75 0,-0.413 -0.336,-0.75 -0.75,-0.75 -0.414,0 -0.75,0.337 -0.75,0.75 0,0.413 0.336,0.75 0.75,0.75 z M 3.555,2 3.857,4 H 17 l -1.118,8.036 H 3.969 L 2.931,4.573 2.695,3 H -0.074 V 2 Z M 4,5 4.139,6 H 15.713 L 15.852,5 Z M 15.012,11.036 15.573,7 H 4.278 l 0.561,4.036 z" fill="#000000" id="path48"></path>
</svg>
</span>Add to cart</a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</div></div></div>		</div>
		</li>';
            } else {
                if ($current_theme->get('Name') === 'Popularis eCommerce') {
                    echo '<style>span.ast-woo-product-category {display: none !important;}</style>';
                }
                echo '<li class="product type-product entry loop-entry content-bg" style="margin: 10px; padding: 0px;">
	<div class="astra-shop-thumbnail-wrap">
	<a href="' . esc_url($product_link) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
    ' . wp_get_attachment_image($attachment_id, 'full', false, array('alt' => esc_attr($product_title), 'class' => 'woocommerce-placeholder wp-post-image')) . '
        </a>
        ' . ($on_sale ? '<span class="ast-on-card-button ast-onsale-card" data-notification="default">Sale!</span>' : '') . '
        <a href="?add-to-cart="' . esc_attr($product['ID']) . '" data-quantity="1" class="ast-on-card-button ast-select-options-trigger product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_attr($product_title) . '”" rel="nofollow" style="display:none;"> <span class="ast-card-action-tooltip"> Add to cart </span> <span class="ahfb-svg-iconset"> <span class="ast-icon icon-bag"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="ast-bag-icon-svg" x="0px" y="0px" width="100" height="100" viewBox="826 826 140 140" enable-background="new 826 826 140 140" xml:space="preserve">
                    <path d="M960.758,934.509l2.632,23.541c0.15,1.403-0.25,2.657-1.203,3.761c-0.953,1.053-2.156,1.579-3.61,1.579H833.424  c-1.454,0-2.657-0.526-3.61-1.579c-0.952-1.104-1.354-2.357-1.203-3.761l2.632-23.541H960.758z M953.763,871.405l6.468,58.29H831.77  l6.468-58.29c0.15-1.203,0.677-2.218,1.58-3.045c0.903-0.827,1.981-1.241,3.234-1.241h19.254v9.627c0,2.658,0.94,4.927,2.82,6.807  s4.149,2.82,6.807,2.82c2.658,0,4.926-0.94,6.807-2.82s2.821-4.149,2.821-6.807v-9.627h28.882v9.627  c0,2.658,0.939,4.927,2.819,6.807c1.881,1.88,4.149,2.82,6.807,2.82s4.927-0.94,6.808-2.82c1.879-1.88,2.82-4.149,2.82-6.807v-9.627  h19.253c1.255,0,2.332,0.414,3.235,1.241C953.086,869.187,953.612,870.202,953.763,871.405z M924.881,857.492v19.254  c0,1.304-0.476,2.432-1.429,3.385s-2.08,1.429-3.385,1.429c-1.303,0-2.432-0.477-3.384-1.429c-0.953-0.953-1.43-2.081-1.43-3.385  v-19.254c0-5.315-1.881-9.853-5.641-13.613c-3.76-3.761-8.298-5.641-13.613-5.641s-9.853,1.88-13.613,5.641  c-3.761,3.76-5.641,8.298-5.641,13.613v19.254c0,1.304-0.476,2.432-1.429,3.385c-0.953,0.953-2.081,1.429-3.385,1.429  c-1.303,0-2.432-0.477-3.384-1.429c-0.953-0.953-1.429-2.081-1.429-3.385v-19.254c0-7.973,2.821-14.779,8.461-20.42  c5.641-5.641,12.448-8.461,20.42-8.461c7.973,0,14.779,2.82,20.42,8.461C922.062,842.712,924.881,849.519,924.881,857.492z"></path>
                    </svg></span> </span> </a></div><div class="astra-shop-summary-wrap">			<span class="ast-woo-product-category" style="
    font-size: 15px;
">
                    ' . wp_kses_post($cata_output) . '			</span>
                <a href="' . esc_url($product_link) . '" class="ast-loop-product__link"><h2 class="woocommerce-loop-product__title">' . esc_html($product_title) . '</h2></a>
            <div class="review-rating"><div class="star-rating"><span style="width:' . (esc_attr($rating) * 20) . '%">Rated <strong class="rating">' . esc_html($rating) . '</strong> out of 5</span></div></div>
    <span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' . esc_html($product_price) . '</bdi></span></span>
<a href="?add-to-cart=' . esc_attr($product['ID']) . '" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" data-quantity="1" class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr($product['ID']) . '" data-product_sku="" aria-label="Add to cart: “' . esc_html($product_title) . '”" rel="nofollow" data-success_message="“' . esc_html($product_title) . '” has been added to your cart">Add to cart</a>	<span id="woocommerce_loop_add_to_cart_link_describedby_' . esc_attr($product['ID']) . '" class="screen-reader-text">
			</span>
</div></li>';
            }
        }
    }
    // Function to generate pagination
    private function pagination($paged, $total_pages)
    {
        $big = 999999999; // an unlikely integer
        $paginationLinks = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, $paged),
            'total' => $total_pages,
            'prev_text' => __('« Prev', 'dynamic-ajax-product-filters-for-woocommerce'),
            'next_text' => __('Next »', 'dynamic-ajax-product-filters-for-woocommerce'),
            'type' => 'array', // This returns an array of pagination links
        ));

        if ($paginationLinks) {
            // Start building the pagination HTML
            $paginationHtml = '';
            foreach ($paginationLinks as $link) {
                // Wrap each link in an <a> tag
                $paginationHtml .= '<li>' . $link . '</li>';
            }
            return $paginationHtml; // Return the constructed HTML
        }
        return '';
    }
}
