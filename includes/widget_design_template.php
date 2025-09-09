<?php

if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_filter_form($updated_filters, $default_filter, $use_anchor, $use_filters_word, $atts, $min_price, $max_price, $min_max_prices, $search_txt = '', $is_filters_in_url = true, $disable_unselected = false)
{
    global $dapfforwc_styleoptions, $post, $dapfforwc_options, $dapfforwc_advance_settings;
    $dapfforwc_product_count = [];

    // Extract category counts
    $dapfforwc_product_count['categories'] = [];
    if (isset($updated_filters['categories']) && is_array($updated_filters['categories'])) {
        foreach ($updated_filters['categories'] as $category) {
            // Ensure $category has the properties you're accessing
            if (isset($category->slug) && isset($category->count)) {
                $dapfforwc_product_count['categories'][$category->slug] = $category->count;
            }
        }
    }

    // Extract tag counts
    $dapfforwc_product_count['tags'] = [];

    // Check if 'tags' exists and is an array
    if (isset($updated_filters['tags']) && is_array($updated_filters['tags'])) {
        foreach ($updated_filters['tags'] as $tag) {
            // Ensure $tag has the properties you're accessing
            if (isset($tag->slug) && isset($tag->count)) {
                $dapfforwc_product_count['tags'][$tag->slug] = $tag->count;
            }
        }
    }

    // Extract brands counts
    $dapfforwc_product_count['brands'] = [];

    // Check if 'brands' exists and is an array
    if (isset($updated_filters['brands']) && is_array($updated_filters['brands'])) {
        foreach ($updated_filters['brands'] as $brand) {
            // Ensure $brand has the properties you're accessing
            if (isset($brand->slug) && isset($brand->count)) {
                $dapfforwc_product_count['brands'][$brand->slug] = $brand->count;
            }
        }
    }

    // Extract attribute counts
    $dapfforwc_product_count['attributes'] = [];

    // Check if 'attributes' exists and is an array
    if (isset($updated_filters['attributes']) && is_array($updated_filters['attributes'])) {
        foreach ($updated_filters['attributes'] as $key => $terms) {
            // Initialize the key in the attributes array
            $dapfforwc_product_count['attributes'][$key] = [];

            // Check if $terms is an array
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    // Ensure $term has the properties you're accessing
                    $slug = is_object($term) ? esc_attr($term->slug) : esc_attr($term['slug']);
                    $count = is_object($term) ? esc_attr($term->count) : esc_attr(isset($term['count']) ? $term['count'] : 0);
                    $dapfforwc_product_count['attributes'][$key][$slug] = $count;
                }
            }
        }
    }

    // Extract custom field counts
    $dapfforwc_product_count['custom_fields'] = [];

    // Check if 'custom_fields' exists and is an array
    if (isset($updated_filters['custom_fields']) && is_array($updated_filters['custom_fields'])) {
        foreach ($updated_filters['custom_fields'] as $key => $terms) {
            // Initialize the key in the custom_fields array
            $dapfforwc_product_count['custom_fields'][$key] = [];

            // Check if $terms is an array
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    // Ensure $term has the properties you're accessing
                    $slug = is_object($term) ? esc_attr($term->slug) : esc_attr($term['slug']);
                    $count = is_object($term) ? esc_attr($term->count) : esc_attr(isset($term['count']) ? $term['count'] : 0);
                    $dapfforwc_product_count['custom_fields'][$key][$slug] = $count;
                }
            }
        }
    }

    $formOutPut = "";

?>
    
    <?php
    // display search
    // Initialize variables with default values
    $sub_option = "";
    $minimizable = "arrow";

    $formOutPut .= '<div id="search_text" class="filter-group search_text" style="display: ' . (isset($dapfforwc_options['show_search']) && !empty($dapfforwc_options['show_search']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["search"]) && $dapfforwc_styleoptions["widget_title"]["search"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["search"]) : esc_html__('Search Product', 'dynamic-ajax-product-filters-for-woocommerce')) . ($minimizable === "arrow" || $minimizable === "minimize_initial"  ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
    $formOutPut .= '<div class="items"><div class="search-container">';
    $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="search" id="plugincy-search-field" class="search-field" placeholder="' . ((isset($dapfforwc_styleoptions["placeholder"]) && isset($dapfforwc_styleoptions["placeholder"]["search"]) && $dapfforwc_styleoptions["placeholder"]["search"] !== "") ? esc_html($dapfforwc_styleoptions["placeholder"]["search"]) : esc_html__('Search Product', 'dynamic-ajax-product-filters-for-woocommerce')) . '&hellip;" value="' . ($search_txt !== '' ? $search_txt : (isset($default_filter["plugincy_search"]) ? $default_filter["plugincy_search"] : '')) . '" name="plugincy_search" />';
    $formOutPut .= ' <button class="plugincy-search-submit">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["search"]) && $dapfforwc_styleoptions["btntext"]["search"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["search"]) : esc_html__('Search', 'dynamic-ajax-product-filters-for-woocommerce')) . '</button>';
    $formOutPut .= '</div></div>';
    $formOutPut .= '</div>';
    // search ends

    // Initialize variables with default values
    $sub_option = "price";
    $minimizable_price = "arrow";
    $sub_option_rating = "rating-text";
    $minimizable_rating = "arrow";

    // Check if 'price' key exists in the style options
    if (isset($dapfforwc_styleoptions['price'])) {
        // Fetch the sub_option value safely
        $sub_option = isset($dapfforwc_styleoptions['price']['sub_option']) ? $dapfforwc_styleoptions['price']['sub_option'] : $sub_option;

        // Check if 'minimize' key exists and fetch its type
        if (isset($dapfforwc_styleoptions['price']['minimize'])) {
            $minimizable_price = isset($dapfforwc_styleoptions['price']['minimize']['type']) ? $dapfforwc_styleoptions['price']['minimize']['type'] : $minimizable_price;
        }
    }

    // Check if 'rating' key exists in the style options
    if (isset($dapfforwc_styleoptions['rating'])) {
        // Fetch the sub_option value safely
        $sub_option_rating = isset($dapfforwc_styleoptions['rating']['sub_option']) ? $dapfforwc_styleoptions['rating']['sub_option'] : $sub_option_rating;
        if ($sub_option_rating === 'dynamic-rating') {
            $sub_option_rating = 'rating';
        }

        // Check if 'minimize' key exists and fetch its type
        if (isset($dapfforwc_styleoptions['rating']['minimize'])) {
            $minimizable_rating = isset($dapfforwc_styleoptions['rating']['minimize']['type']) ? $dapfforwc_styleoptions['rating']['minimize']['type'] : $minimizable_rating;
        }
    }
    ?>
      
<?php $formOutPut .= '<div id="rating" class="filter-group rating" style="display: ' . (isset($dapfforwc_options['show_rating']) && !empty($dapfforwc_options['show_rating']) ? 'block' : 'none !important') . ';">'; ?>
 <?php $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable_rating) . '"><div> ' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["rating"]) && $dapfforwc_styleoptions["widget_title"]["rating"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["rating"]) : esc_html__('Rating', 'dynamic-ajax-product-filters-for-woocommerce')) . ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span></div>' . ($minimizable_rating === "arrow" || $minimizable_rating === "minimize_initial"  ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
    $formOutPut .= '<div class="items rating ' . esc_attr($sub_option_rating) . '"><div> '; ?>
        <?php if ($sub_option_rating) {
            $formOutPut .=  dapfforwc_render_filter_option($sub_option_rating, "", "", $checked = isset($default_filter['rating[]']) ? $default_filter['rating[]'] : [], $dapfforwc_styleoptions, "", "", "", "", 0, null, [], $disable_unselected);
        } else {
            $formOutPut .= 'Choose style from product filters->form style -> rating';
        }
        $formOutPut .= '</div></div></div>'; ?>

   <?php $formOutPut .= '<div id="price-range" class="filter-group price-range" style="display: ' . (isset($dapfforwc_options['show_price_range']) && !empty($dapfforwc_options['show_price_range']) ? 'block' : 'none !important') . ';">'; ?>
 <?php $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable_price) . '">' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["price"]) && $dapfforwc_styleoptions["widget_title"]["price"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["price"]) : esc_html__('Price Range', 'dynamic-ajax-product-filters-for-woocommerce')) . ($minimizable_price === "arrow" || $minimizable_price === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
    $formOutPut .= '<div class="items ' . ($sub_option !== "slider" ? $sub_option : '') . '">'; ?>
        <?php if ($sub_option) {
            if ($sub_option === 'input-price-range') {
                $sub_option = 'slider';
            }
            $formOutPut .=  dapfforwc_render_filter_option($sub_option, "", "", "", $dapfforwc_styleoptions, "", "", "", "", $min_price, $max_price, $min_max_prices, $disable_unselected);
        } else {
            $formOutPut .= 'Choose style from product filters->form style -> price';
        }
        $formOutPut .= '</div></div>';
        // Fetch global options and style configurations

        $sub_option = '';
        $minimizable = 'arrow';
        $show_count = '';
        $singlevaluecataSelect = '';
        $hierarchical = '';

        // Additional checks to ensure the structure exists before accessing
        if (isset($dapfforwc_styleoptions["product-category"])) {
            $sub_option = isset($dapfforwc_styleoptions["product-category"]['sub_option']) ? $dapfforwc_styleoptions["product-category"]['sub_option'] : '';

            if (isset($dapfforwc_styleoptions["product-category"]['minimize'])) {
                $minimizable = isset($dapfforwc_styleoptions["product-category"]['minimize']['type']) ? $dapfforwc_styleoptions["product-category"]['minimize']['type'] : '';
            } else {
                $minimizable = '';
            }

            $show_count = isset($dapfforwc_styleoptions["product-category"]['show_product_count']) ? $dapfforwc_styleoptions["product-category"]['show_product_count'] : '';
            $singlevaluecataSelect = isset($dapfforwc_styleoptions["product-category"]['single_selection']) ? $dapfforwc_styleoptions["product-category"]['single_selection'] : '';

            if (isset($dapfforwc_styleoptions["product-category"]['hierarchical'])) {
                $hierarchical = isset($dapfforwc_styleoptions["product-category"]['hierarchical']['type']) ? $dapfforwc_styleoptions["product-category"]['hierarchical']['type'] : '';
            } else {
                $hierarchical = '';
            }
        }
        $selected_categories = !empty($default_filter) && isset($default_filter["product-category[]"]) ? $default_filter["product-category[]"] : [];

        // Fetch categories

        // Render categories based on hierarchical mode
        if ($hierarchical !== 'enable_separate' && !empty($updated_filters["categories"])) {
            $formOutPut .= '<div id="product-category" class="filter-group category" style="display: ' . (isset($dapfforwc_options['show_categories']) && !empty($dapfforwc_options['show_categories']) ? 'block' : 'none !important') . ';">';
            $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["product-category"]) && $dapfforwc_styleoptions["widget_title"]["product-category"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["product-category"]) : esc_html__('Category', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevaluecataSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === 'arrow' || $minimizable === 'minimize_initial' ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
            if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
                $sub_option = 'plugincy_color';
            } elseif ($sub_option === 'button_check') {
                $sub_option = '';
            }
            $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

            if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                $formOutPut .= '<select name="product-category[]" class="select filter-select" ' . ($singlevaluecataSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
                $formOutPut .= '<option class="filter-checkbox" > Any </option>';
            }
        }

        if ($hierarchical === 'enable' || $hierarchical === 'enable_hide_child') {
            $parent_categories = [];
            $child_category = [];
            if (isset($updated_filters["categories"]) && is_array($updated_filters["categories"])) {
                foreach ($updated_filters["categories"] as $category) {
                    // Check if the category is an instance of WP_Term or stdClass
                    if (is_object($category) && ($category instanceof WP_Term || $category instanceof stdClass)) {
                        if ($category->parent == "0") { // Ensure comparison matches data type
                            $parent_categories[] = $category;
                        } else {
                            $child_category[] = $category;
                        }
                    }
                }
            }

            if ($parent_categories) {
                $formOutPut .= dapfforwc_render_category_hierarchy($parent_categories, $selected_categories, $sub_option, $dapfforwc_styleoptions, $singlevaluecataSelect, $show_count, $use_anchor, $use_filters_word, $hierarchical, $child_category);
            }
        } elseif ($hierarchical === 'enable_separate') {

            // Render parent categories in a unified section
            $parent_categories = [];
            $child_category = [];
            if (isset($updated_filters["categories"]) && is_array($updated_filters["categories"])) {
                foreach ($updated_filters["categories"] as $category) {
                    // Check if the category is an instance of WP_Term or stdClass
                    if (is_object($category) && ($category instanceof WP_Term || $category instanceof stdClass)) {
                        if ($category->parent == "0") { // Ensure comparison matches data type
                            $parent_categories[] = $category;
                        } else {
                            $child_category[] = $category;
                        }
                    }
                }
            }

            $formOutPut .= '<div id="product-category" class="filter-group category" style="display: ' . (isset($dapfforwc_options['show_categories']) && !empty($dapfforwc_options['show_categories']) ? 'block' : 'none !important') . ';">';
            $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["product-category"]) && $dapfforwc_styleoptions["widget_title"]["product-category"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["product-category"]) : esc_html__('Category', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevaluecataSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === 'arrow' || $minimizable === 'minimize_initial' ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
            if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
                $sub_option = 'plugincy_color';
            } elseif ($sub_option === 'button_check') {
                $sub_option = '';
            }

            $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

            if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                $formOutPut .= '<select name="product-category[]" class="select ' . esc_attr($sub_option) . ' filter-select" ' . ($singlevaluecataSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
                $formOutPut .= '<option class="filter-checkbox" > Any </option>';
            }

            foreach (isset($parent_categories) ? $parent_categories : [] as $parent_category) {
                $value = is_object($parent_category) ? esc_attr($parent_category->slug) : esc_attr($parent_category['slug']);
                $title = is_object($parent_category) ? esc_html($parent_category->name) : esc_html($parent_category['name']);
                $count = $show_count === 'yes' ? (is_object($parent_category) ? esc_attr($parent_category->count) : esc_attr(isset($parent_category['count']) ? $parent_category['count'] : 0)) : 0;
                $checked = in_array($value, $selected_categories) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === 'on'   && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic")
                    ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count, 0, null, [], $disable_unselected) . '</a>'
                    : dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count, 0, null, [], $disable_unselected);
            }

            if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                $formOutPut .= '</select></div></div>';
            } else {
                $formOutPut .= '</div></div>';
            }

            // Render child categories grouped by parent
            foreach ($parent_categories as $parent_category) {
                $child_categories = dapfforwc_get_child_categories($child_category, $parent_category->term_id) ?: [];

                if (!empty($child_categories)) {
                    $formOutPut .= '<div id="category-with-child" class="filter-group category with-child" style="display: ' . (isset($dapfforwc_options['show_categories']) && !empty($dapfforwc_options['show_categories']) ? 'block' : 'none !important') . ';">';
                    $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">' . esc_html($parent_category->name) . ' ' . ($minimizable === 'arrow' || $minimizable === 'minimize_initial' ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
                    if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
                        $sub_option = 'plugincy_color';
                    } elseif ($sub_option === 'button_check') {
                        $sub_option = '';
                    }

                    $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

                    if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                        $formOutPut .= '<select name="product-category[]" class="select ' . esc_attr($sub_option) . ' filter-select" ' . ($singlevaluecataSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
                        $formOutPut .= '<option class="filter-checkbox" > Any </option>';
                    }

                    $formOutPut .= dapfforwc_render_category_hierarchy($child_categories, $selected_categories, $sub_option, $dapfforwc_styleoptions, $singlevaluecataSelect, $show_count, $use_anchor, $use_filters_word, $hierarchical, $child_categories);

                    if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                        $formOutPut .= '</select></div></div>';
                    } else {
                        $formOutPut .= '</div></div>';
                    }
                }
            }
        } else {
            // Render categories non-hierarchically
            foreach (isset($updated_filters["categories"]) ? $updated_filters["categories"] : [] as $category) {
                $value = is_object($category) ? esc_attr($category->slug) : esc_attr($category['slug']);
                $title = is_object($category) ? esc_html($category->name) : esc_html($category['name']);
                $count = $show_count === 'yes' ? $dapfforwc_product_count['categories'][$value] : 0;

                $checked = in_array($value, $selected_categories) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === 'on'   && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic")
                    ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count, 0, null, [], $disable_unselected) . '</a>'
                    : dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "product-category", "product-category", $singlevaluecataSelect, $count, 0, null, [], $disable_unselected);
            }
        }

        if ($hierarchical !== 'enable_separate' && !empty($updated_filters["categories"])) {
            if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
                $formOutPut .= '</select></div></div>';
            } else {
                $formOutPut .= '</div></div>';
            }
        }
        ?>
<?php
    // category ends


    // display attributes
    $attributes = isset($updated_filters['attributes']) && is_array($updated_filters['attributes']) ? $updated_filters["attributes"] : [];
    $exclude_attributes = isset($dapfforwc_advance_settings['exclude_attributes']) ? explode(',', $dapfforwc_advance_settings['exclude_attributes']) : [];

    if ($attributes) {
        foreach ($attributes as $attribute_name => $attribute_terms) {
            if (in_array($attribute_name, $exclude_attributes)) {
                continue;
            }
            $terms = $attribute_terms; // Directly use the terms from the array
            $sub_optionattr = isset($dapfforwc_styleoptions[$attribute_name]["sub_option"]) ? $dapfforwc_styleoptions[$attribute_name]["sub_option"] : "";
            $minimizable = isset($dapfforwc_styleoptions[$attribute_name]["minimize"]["type"]) ? $dapfforwc_styleoptions[$attribute_name]["minimize"]["type"] : "arrow";
            $show_count = isset($dapfforwc_styleoptions[$attribute_name]["show_product_count"]) ? $dapfforwc_styleoptions[$attribute_name]["show_product_count"] : "";
            $singlevalueattrSelect = isset($dapfforwc_styleoptions[$attribute_name]["single_selection"]) ? $dapfforwc_styleoptions[$attribute_name]["single_selection"] : "";

            if ($terms) {
                usort($terms, function ($a, $b) {
                    return dapfforwc_customSort(
                        is_object($a) ? $a->name : $a['name'],
                        is_object($b) ? $b->name : $b['name']
                    );
                });
                $formOutPut .= '<div id="' . esc_attr($attribute_name) . '" class="filter-group ' . esc_attr($attribute_name) . '" style="display: ' . (isset($dapfforwc_options['show_attributes']) && !empty($dapfforwc_options['show_attributes']) ? 'block' : 'none !important') . ';">
                            <div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"][$attribute_name]) && $dapfforwc_styleoptions["widget_title"][$attribute_name] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"][$attribute_name]) : esc_html($attribute_name)) . ' ' . ($singlevalueattrSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' .
                    ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') .
                    '</div>';

                if ($sub_optionattr === 'color_circle' || $sub_optionattr === 'color_value') {
                    $sub_optionattr = 'plugincy_color';
                } elseif ($sub_optionattr === 'button_check') {
                    $sub_optionattr = '';
                }

                $formOutPut .= '<div class="items ' . esc_attr($sub_optionattr) . '">';

                if ($sub_optionattr === "select" || $sub_optionattr === "select2" || $sub_optionattr === "select2_classic") {
                    $formOutPut .= '<select name="attribute[' . esc_attr($attribute_name) . '][]" class="' . esc_attr($sub_optionattr) . ' filter-select" ' . ($singlevalueattrSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
                    $formOutPut .= '<option class="filter-checkbox" > Any </option>';
                }

                $selected_terms = isset($default_filter["attribute[$attribute_name][]"]) ? $default_filter["attribute[$attribute_name][]"] : [];
                foreach ($terms as $term) {

                    $name = is_object($term) ? esc_html($term->name) : esc_html($term['name']);
                    $slug = is_object($term) ? esc_attr($term->slug) : esc_attr($term['slug']);
                    $checked = in_array($slug, $selected_terms) ? ($sub_optionattr === 'select' || str_contains($sub_optionattr, 'select2') ? ' selected' : ' checked') : '';
                    $count = $show_count === "yes" ? (is_object($term) ? esc_attr($term->count ?? 0) : esc_attr(isset($term['count']) ? $term['count'] : 0)) : 0; // Use term count directly
                    $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? esc_attr($slug) : "filters/" . esc_attr($slug)) : '?filters=' . esc_attr($slug);
                    $term_label = is_object($term) ? esc_html($term->attribute_label) : esc_attr($term['attribute_label']);
                    $formOutPut .= $use_anchor === "on" && $sub_optionattr !== "select" && $sub_optionattr !== "select2" && $sub_optionattr !== "select2_classic" ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_optionattr, $term_label, esc_attr($slug), $checked, $dapfforwc_styleoptions, "attribute[$attribute_name]", $attribute_name, $singlevalueattrSelect, $count, 0, null, [], $disable_unselected) . '</a>' : dapfforwc_render_filter_option($sub_optionattr, esc_html($name), esc_attr($slug), $checked, $dapfforwc_styleoptions, "attribute[$attribute_name]", $attribute_name, $singlevalueattrSelect, $count, 0, null, [], $disable_unselected);
                }

                if ($sub_optionattr === "select" || $sub_optionattr === "select2" || $sub_optionattr === "select2_classic") {
                    $formOutPut .= '</select>';
                }
                $formOutPut .= '</div></div>';
            }
        }
    }

    // display tags
    $tags = isset($updated_filters['tags']) && is_array($updated_filters['tags']) ? $updated_filters["tags"] : [];
    if (!empty($tags)) {
        $selected_tags = !empty($default_filter) && isset($default_filter["tag[]"]) ? $default_filter["tag[]"] : [];
        $sub_option = isset($dapfforwc_styleoptions["tag"]["sub_option"]) ? $dapfforwc_styleoptions["tag"]["sub_option"] : ""; // Fetch the sub_option value
        $minimizable = isset($dapfforwc_styleoptions["tag"]["minimize"]["type"]) ? $dapfforwc_styleoptions["tag"]["minimize"]["type"] : "arrow";
        $show_count = isset($dapfforwc_styleoptions["tag"]["show_product_count"]) ? $dapfforwc_styleoptions["tag"]["show_product_count"] : "";
        $singlevalueSelect = isset($dapfforwc_styleoptions["tag"]["single_selection"]) ? $dapfforwc_styleoptions["tag"]["single_selection"] : "";
        $formOutPut .= '<div id="tag" class="filter-group tag" style="display: ' . (isset($dapfforwc_options['show_tags']) && !empty($dapfforwc_options['show_tags']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["tag"]) && $dapfforwc_styleoptions["widget_title"]["tag"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["tag"]) : esc_html__('Tags', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevalueSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
        if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
            $sub_option = 'plugincy_color';
        } elseif ($sub_option === 'button_check') {
            $sub_option = '';
        }

        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '<select name="tags[]" class="' . esc_attr($sub_option) . ' filter-select" ' . ($singlevalueSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
            $formOutPut .= '<option class="filter-checkbox" > Any </option>';
        }
        if ($tags) {
            foreach ($tags as $tag) {
                $value = is_object($tag) ? esc_attr($tag->slug) : esc_attr($tag['slug']);
                $title = is_object($tag) ? esc_html($tag->name) : esc_html($tag['name']);
                $checked = in_array($value, $selected_tags) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $count = $show_count === "yes" ? $dapfforwc_product_count["tags"][$value] : 0;
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === "on"  && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic") ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "tags", $attribute = "tags", $singlevalueSelect, $count, 0, null, [], $disable_unselected) . '</a>' :  dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "tags", $attribute = "tags", $singlevalueSelect, $count, 0, null, [], $disable_unselected);
            }
        }
        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '</select>';
        }
        $formOutPut .= '</div></div>';
    }
    // tags ends
    // display brands
    $brands = isset($updated_filters['brands']) && is_array($updated_filters['brands']) ? $updated_filters["brands"] : [];
    if (!empty($brands)) {
        $selected_brands = !empty($default_filter) && isset($default_filter["rplurand[]"]) ? $default_filter["rplurand[]"] : [];
        $sub_option = isset($dapfforwc_styleoptions["brands"]["sub_option"]) ? $dapfforwc_styleoptions["brands"]["sub_option"] : ""; // Fetch the sub_option value
        $minimizable = isset($dapfforwc_styleoptions["brands"]["minimize"]["type"]) ? $dapfforwc_styleoptions["brands"]["minimize"]["type"] : "arrow";
        $show_count = isset($dapfforwc_styleoptions["brands"]["show_product_count"]) ? $dapfforwc_styleoptions["brands"]["show_product_count"] : "";
        $singlevalueSelect = isset($dapfforwc_styleoptions["brands"]["single_selection"]) ? $dapfforwc_styleoptions["brands"]["single_selection"] : "";
        $formOutPut .= '<div id="brands" class="filter-group brands" style="display: ' . (isset($dapfforwc_options['show_brand']) && !empty($dapfforwc_options['show_brand']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["brands"]) && $dapfforwc_styleoptions["widget_title"]["brands"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["brands"]) : esc_html__('Brands', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevalueSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
        if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
            $sub_option = 'plugincy_color';
        } elseif ($sub_option === 'button_check') {
            $sub_option = '';
        }

        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '<select name="rplurand[]" class="' . esc_attr($sub_option) . ' filter-select" ' . ($singlevalueSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
            $formOutPut .= '<option class="filter-checkbox" > Any </option>';
        }
        if ($brands) {
            foreach ($brands as $brand) {
                $value = is_object($brand) ? esc_attr($brand->slug) : esc_attr($brand['slug']);
                $title = is_object($brand) ? esc_html($brand->name) : esc_html($brand['name']);
                $checked = in_array($value, $selected_brands) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';

                $count = $show_count === "yes" ? $dapfforwc_product_count["brands"][$value] : 0;
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === "on"  && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic") ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rplurand", $attribute = "brands", $singlevalueSelect, $count, 0, null, [], $disable_unselected) . '</a>' :  dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rplurand", $attribute = "brands", $singlevalueSelect, $count, 0, null, [], $disable_unselected);
            }
        }

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '</select>';
        }
        $formOutPut .= '</div></div>';
    }
    // brands ends
    // display Authors
    $authors = isset($updated_filters['authors']) && is_array($updated_filters['authors']) ? $updated_filters["authors"] : [];
    if (!empty($authors)) {
        $selected_authors = !empty($default_filter) && isset($default_filter["rpluthor[]"]) ? $default_filter["rpluthor[]"] : [];
        $sub_option = isset($dapfforwc_styleoptions["authors"]["sub_option"]) ? $dapfforwc_styleoptions["authors"]["sub_option"] : ""; // Fetch the sub_option value
        $minimizable = isset($dapfforwc_styleoptions["authors"]["minimize"]["type"]) ? $dapfforwc_styleoptions["authors"]["minimize"]["type"] : "arrow";
        $show_count = isset($dapfforwc_styleoptions["authors"]["show_product_count"]) ? $dapfforwc_styleoptions["authors"]["show_product_count"] : "";
        $singlevalueSelect = isset($dapfforwc_styleoptions["authors"]["single_selection"]) ? $dapfforwc_styleoptions["authors"]["single_selection"] : "";
        $formOutPut .= '<div id="authors" class="filter-group authors" style="display: ' . (isset($dapfforwc_options['show_author']) && !empty($dapfforwc_options['show_author']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["authors"]) && $dapfforwc_styleoptions["widget_title"]["authors"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["authors"]) : esc_html__('Authors', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevalueSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
        if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
            $sub_option = 'plugincy_color';
        } elseif ($sub_option === 'button_check') {
            $sub_option = '';
        }

        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '<select name="rpluthor[]" class="' . esc_attr($sub_option) . ' filter-select" ' . ($singlevalueSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
            $formOutPut .= '<option class="filter-checkbox" > Any </option>';
        }
        if ($authors) {
            foreach ($authors as $author) {
                $value = is_object($author) ? esc_attr($author->slug) : esc_attr($author['slug']);
                $title = is_object($author) ? esc_html($author->name) : esc_html($author['name']);
                $checked = in_array($value, $selected_authors) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $count = $show_count === "yes" ? $dapfforwc_product_count["authors"][$value] : 0;
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === "on"  && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic") ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rpluthor", $attribute = "rpluthor", $singlevalueSelect, $count, 0, null, [], $disable_unselected) . '</a>' :  dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rpluthor", $attribute = "rpluthor", $singlevalueSelect, $count, 0, null, [], $disable_unselected);
            }
        }
        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '</select>';
        }
        $formOutPut .= '</div></div>';
    }
    // authors ends
    // display Stock Status
    $status = isset($updated_filters['stock_status']) && is_array($updated_filters['stock_status']) ? $updated_filters["stock_status"] : [];
    if (!empty($status)) {
        $selected_status = !empty($default_filter) && isset($default_filter["rplutock_status[]"]) ? $default_filter["rplutock_status[]"] : [];
        $sub_option = isset($dapfforwc_styleoptions["status"]["sub_option"]) ? $dapfforwc_styleoptions["status"]["sub_option"] : ""; // Fetch the sub_option value
        $minimizable = isset($dapfforwc_styleoptions["status"]["minimize"]["type"]) ? $dapfforwc_styleoptions["status"]["minimize"]["type"] : "arrow";
        $show_count = isset($dapfforwc_styleoptions["status"]["show_product_count"]) ? $dapfforwc_styleoptions["status"]["show_product_count"] : "";
        $singlevalueSelect = isset($dapfforwc_styleoptions["status"]["single_selection"]) ? $dapfforwc_styleoptions["status"]["single_selection"] : "";
        $formOutPut .= '<div id="status" class="filter-group status" style="display: ' . (isset($dapfforwc_options['show_status']) && !empty($dapfforwc_options['show_status']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["status"]) && $dapfforwc_styleoptions["widget_title"]["status"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["status"]) : esc_html__('Stock Status', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevalueSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
        if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
            $sub_option = 'plugincy_color';
        } elseif ($sub_option === 'button_check') {
            $sub_option = '';
        }

        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '<select name="rpluthor[]" class="' . esc_attr($sub_option) . ' filter-select" ' . ($singlevalueSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
            $formOutPut .= '<option class="filter-checkbox" > Any </option>';
        }

        if ($status) {
            foreach ($status as $stat) {
                $value = is_object($stat) ? esc_attr($stat->slug) : esc_attr($stat['slug']);
                $title = is_object($stat) ? esc_html($stat->name) : esc_html($stat['name']);
                $checked = in_array($value, $selected_status) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $count = $show_count === "yes" ? $dapfforwc_product_count["status"][$value] : 0;
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === "on"  && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic") ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rplutock_status", $attribute = "rplutock_status", $singlevalueSelect, $count, 0, null, [], $disable_unselected) . '</a>' :  dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rplutock_status", $attribute = "rplutock_status", $singlevalueSelect, $count, 0, null, [], $disable_unselected);
            }
        }
        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '</select>';
        }
        $formOutPut .= '</div></div>';
    }
    // Stock Status ends
    // display Stock Status
    $sale_status = isset($updated_filters['sale_status']) && is_array($updated_filters['sale_status']) ? $updated_filters["sale_status"] : [];
    if (!empty($sale_status)) {
        $selected_sale_status = !empty($default_filter) && isset($default_filter["rpn_sale[]"]) ? $default_filter["rpn_sale[]"] : [];
        $sub_option = isset($dapfforwc_styleoptions["sale_status"]["sub_option"]) ? $dapfforwc_styleoptions["sale_status"]["sub_option"] : ""; // Fetch the sub_option value
        $minimizable = isset($dapfforwc_styleoptions["sale_status"]["minimize"]["type"]) ? $dapfforwc_styleoptions["sale_status"]["minimize"]["type"] : "arrow";
        $show_count = isset($dapfforwc_styleoptions["sale_status"]["show_product_count"]) ? $dapfforwc_styleoptions["sale_status"]["show_product_count"] : "";
        $singlevalueSelect = isset($dapfforwc_styleoptions["sale_status"]["single_selection"]) ? $dapfforwc_styleoptions["sale_status"]["single_selection"] : "";
        $formOutPut .= '<div id="sale_status" class="filter-group sale_status" style="display: ' . (isset($dapfforwc_options['show_onsale']) && !empty($dapfforwc_options['show_onsale']) ? 'block' : 'none !important') . ';"><div class="title plugincy_collapsable_' . esc_attr($minimizable) . '"><span>' . ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["sale_status"]) && $dapfforwc_styleoptions["widget_title"]["sale_status"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["sale_status"]) : esc_html__('Sale Status', 'dynamic-ajax-product-filters-for-woocommerce')) . ($singlevalueSelect === "yes" ? ' <span class="reset-value">' . ((isset($dapfforwc_styleoptions["btntext"]) && isset($dapfforwc_styleoptions["btntext"]["reset_btn"]) && $dapfforwc_styleoptions["btntext"]["reset_btn"] !== "") ? esc_html($dapfforwc_styleoptions["btntext"]["reset_btn"]) : esc_html__('Reset', 'dynamic-ajax-product-filters-for-woocommerce')) . '</span>' : '') . '</span>' . ($minimizable === "arrow" || $minimizable === "minimize_initial" ? '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>' : '') . '</div>';
        if ($sub_option === 'color_circle' || $sub_option === 'color_value') {
            $sub_option = 'plugincy_color';
        } elseif ($sub_option === 'button_check') {
            $sub_option = '';
        }

        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '<select name="rpluthor[]" class="' . esc_attr($sub_option) . ' filter-select" ' . ($singlevalueSelect !== "yes" ? 'multiple="multiple"' : '') . '>';
            $formOutPut .= '<option class="filter-checkbox" > Any </option>';
        }
        if ($sale_status) {
            foreach ($sale_status as $stat) {
                $value = is_object($stat) ? esc_attr($stat->slug) : esc_attr($stat['slug']);
                $title = is_object($stat) ? esc_html($stat->name) : esc_html($stat['name']);
                $checked = in_array($value, $selected_sale_status) ? ($sub_option === 'select' || str_contains($sub_option, 'select2') ? ' selected' : ' checked') : '';
                $count = $show_count === "yes" ? $dapfforwc_product_count["sale_status"][$value] : 0;
                $anchorlink = $use_filters_word === 'on' ? ($is_filters_in_url ? "$value" : "filters/$value") : '?filters=' . $value;
                $formOutPut .= $use_anchor === "on"  && ($sub_option !== "select" && $sub_option !== "select2" && $sub_option !== "select2_classic") ? '<a href="' . esc_attr($anchorlink) . '">' . dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rpn_sale", $attribute = "rpn_sale", $singlevalueSelect, $count, 0, null, [], $disable_unselected) . '</a>' :  dapfforwc_render_filter_option($sub_option, $title, $value, $checked, $dapfforwc_styleoptions, "rpn_sale", $attribute = "rpn_sale", $singlevalueSelect, $count, 0, null, [], $disable_unselected);
            }
        }
        if ($sub_option === "select" || $sub_option === "select2" || $sub_option === "select2_classic") {
            $formOutPut .= '</select>';
        }
        $formOutPut .= '</div></div>';
    }
    // Stock Status ends

    // Unified Dimension Filter
    $dimensions = [
        'length' => ['label' => 'Length', 'unit' => 'cm'],
        'width' => ['label' => 'Width', 'unit' => 'cm'],
        'height' => ['label' => 'Height', 'unit' => 'cm'],
        'weight' => ['label' => 'Weight', 'unit' => 'kg']
    ];

    // Check if any dimension is enabled
    $show_any_dimension = false;
    if (isset($dapfforwc_options["show_dimension"]) && !empty($dapfforwc_options["show_dimension"])) {
        $show_any_dimension = true;
    }

    if ($show_any_dimension) {
        // Get common settings from the first dimension (or you can make this configurable)
        $sub_option = isset($dapfforwc_styleoptions["dimensions"]["sub_option"]) ? $dapfforwc_styleoptions["dimensions"]["sub_option"] : "";
        $minimizable = isset($dapfforwc_styleoptions["dimensions"]["minimize"]["type"]) ? $dapfforwc_styleoptions["dimensions"]["minimize"]["type"] : "arrow";

        $formOutPut .= '<div id="dimensions" class="filter-group dimensions" style="display: block;">';
        $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">';
        $formOutPut .= ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["dimensions"]) && $dapfforwc_styleoptions["widget_title"]["dimensions"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["dimensions"]) : esc_html__('Dimensions', 'dynamic-ajax-product-filters-for-woocommerce'));

        if ($minimizable === "arrow" || $minimizable === "minimize_initial") {
            $formOutPut .= '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>';
        }

        $formOutPut .= '</div>';
        $formOutPut .= '<div class="items ' . esc_attr($sub_option) . '">';

        if ($sub_option === 'slider' || $sub_option === 'input-price-range') {
            // Unified Slider implementation
            $formOutPut .= '<div class="unified-dimension-slider-container">';

            foreach ($dimensions as $dimension => $config) {
                // Get current values from default filter
                $min_value = $default_filter["min_{$dimension}"] ?? '';
                $max_value = $default_filter["max_{$dimension}"] ?? '';

                $formOutPut .= '<div class="dimension-row">';
                $formOutPut .= '<div class="dimension-label">' . esc_html($config['label']) . ' (' . esc_html($config['unit']) . '):</div>';
                $formOutPut .= '<div class="dimension-values">';
                $formOutPut .= '<span class="min-value">' . esc_html($min_value) . '</span>';
                $formOutPut .= ' - ';
                $formOutPut .= '<span class="max-value">' . esc_html($max_value) . '</span>';
                $formOutPut .= ' ' . esc_html($config['unit']);
                $formOutPut .= '</div>';
                $formOutPut .= '<div class="dimension-slider" data-dimension="' . esc_attr($dimension) . '"></div>';
                $formOutPut .= '<input type="hidden" name="min_' . esc_attr($dimension) . '" value="' . esc_attr($min_value) . '" />';
                $formOutPut .= '<input type="hidden" name="max_' . esc_attr($dimension) . '" value="' . esc_attr($max_value) . '" />';
                $formOutPut .= '</div>';
            }

            $formOutPut .= '</div>';
        } else {
            // Unified Input fields implementation
            $formOutPut .= '<div class="unified-dimension-input-container">';

            foreach ($dimensions as $dimension => $config) {
                // Get current values from default filter
                $min_value = $default_filter["min_{$dimension}"] ?? '';
                $max_value = $default_filter["max_{$dimension}"] ?? '';

                $formOutPut .= '<div class="dimension-row">';
                $formOutPut .= '<div class="dimension-label" style=" margin-bottom: 5px; ">' . esc_html($config['label']) . ' (' . esc_html($config['unit']) . '):</div>';
                $formOutPut .= '<div class="dimension-inputs" style=" display: flex; gap: 10px; margin-bottom: 10px; ">';
                $formOutPut .= '<div class="dimension-input-group">';
                $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="number" name="min_' . esc_attr($dimension) . '" value="' . esc_attr($min_value) . '" placeholder="Min" step="0.1" min="0" />';
                $formOutPut .= '</div>';
                $formOutPut .= '<div class="dimension-input-group">';
                $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="number" name="max_' . esc_attr($dimension) . '" value="' . esc_attr($max_value) . '" placeholder="Max" step="0.1" min="0" />';
                $formOutPut .= '</div>';
                $formOutPut .= '</div>';
                $formOutPut .= '</div>';
            }

            $formOutPut .= '</div>';
        }

        $formOutPut .= '</div>';
        $formOutPut .= '</div>';
    }
    // Unified Dimension Filter end

    // SKU Filter
    $sub_option = isset($dapfforwc_styleoptions["sku"]["sub_option"]) ? $dapfforwc_styleoptions["sku"]["sub_option"] : "input";
    $minimizable = isset($dapfforwc_styleoptions["sku"]["minimize"]["type"]) ? $dapfforwc_styleoptions["sku"]["minimize"]["type"] : "arrow";
    $show_sku = isset($dapfforwc_options["show_sku"]) ? $dapfforwc_options["show_sku"] : false;

    if (!empty($show_sku)) {
        $formOutPut .= '<div id="sku" class="filter-group sku" style="display: block;">';
        $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">';
        $formOutPut .= ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["sku"]) && $dapfforwc_styleoptions["widget_title"]["sku"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["sku"]) : esc_html__('SKU', 'dynamic-ajax-product-filters-for-woocommerce'));

        if ($minimizable === "arrow" || $minimizable === "minimize_initial") {
            $formOutPut .= '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>';
        }

        $formOutPut .= '</div>';
        $formOutPut .= '<div class="items sku-container">';

        // Get current SKU value from default filter
        $sku_value = isset($default_filter["sku"]) ? $default_filter["sku"] : '';

        $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="text" name="sku" class="sku-field" placeholder="Enter SKU..." value="' . esc_attr($sku_value) . '" />';

        $formOutPut .= '</div>';
        $formOutPut .= '</div>';
    }
    // SKU Filter end

    // Discount Filter
    $sub_option = isset($dapfforwc_styleoptions["discount"]["sub_option"]) ? $dapfforwc_styleoptions["discount"]["sub_option"] : "input";
    $minimizable = isset($dapfforwc_styleoptions["discount"]["minimize"]["type"]) ? $dapfforwc_styleoptions["discount"]["minimize"]["type"] : "arrow";
    $show_discount = isset($dapfforwc_options["show_discount"]) ? $dapfforwc_options["show_discount"] : false;

    if (!empty($show_discount)) {
        $formOutPut .= '<div id="discount" class="filter-group discount" style="display: block;">';
        $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">';
        $formOutPut .= ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["discount"]) && $dapfforwc_styleoptions["widget_title"]["discount"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["discount"]) : esc_html__('Minimum Discount (%)', 'dynamic-ajax-product-filters-for-woocommerce'));

        if ($minimizable === "arrow" || $minimizable === "minimize_initial") {
            $formOutPut .= '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>';
        }

        $formOutPut .= '</div>';
        $formOutPut .= '<div class="items discount-container">';

        // Get current discount value from default filter
        $discount_value = isset($default_filter["discount"]) ? $default_filter["discount"] : '';

        $formOutPut .= '<div class="discount-input-group">';
        $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="number" name="discount" class="discount-field" placeholder="20" value="' . esc_attr($discount_value) . '" min="0" max="100" step="1" />';
        $formOutPut .= '<span class="discount-unit">% or more</span>';
        $formOutPut .= '</div>';

        $formOutPut .= '</div>';
        $formOutPut .= '</div>';
    }
    // Discount Filter end

    // Date Filter
    $sub_option = isset($dapfforwc_styleoptions["date_filter"]["sub_option"]) ? $dapfforwc_styleoptions["date_filter"]["sub_option"] : "select";
    $minimizable = isset($dapfforwc_styleoptions["date_filter"]["minimize"]["type"]) ? $dapfforwc_styleoptions["date_filter"]["minimize"]["type"] : "arrow";
    $show_date_filter = isset($dapfforwc_options["show_date_filter"]) ? $dapfforwc_options["show_date_filter"] : false;

    if (!empty($show_date_filter)) {
        $formOutPut .= '<div id="date_filter" class="filter-group date_filter" style="display: block;">';
        $formOutPut .= '<div class="title plugincy_collapsable_' . esc_attr($minimizable) . '">';
        $formOutPut .= ((isset($dapfforwc_styleoptions["widget_title"]) && isset($dapfforwc_styleoptions["widget_title"]["date_filter"]) && $dapfforwc_styleoptions["widget_title"]["date_filter"] !== "") ? esc_html($dapfforwc_styleoptions["widget_title"]["date_filter"]) : esc_html__('Date Filter', 'dynamic-ajax-product-filters-for-woocommerce'));

        if ($minimizable === "arrow" || $minimizable === "minimize_initial") {
            $formOutPut .= '<div class="collaps"><svg class="rotatable" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512" role="graphics-symbol" aria-hidden="false" aria-label=""><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"></path></svg></div>';
        }

        $formOutPut .= '</div>';
        $formOutPut .= '<div class="items date-filter-container">';

        // Get current values from default filter
        $date_filter_value = isset($default_filter["date_filter"]) ? $default_filter["date_filter"] : '';
        $date_from_value = isset($default_filter["date_from"]) ? $default_filter["date_from"] : '';
        $date_to_value = isset($default_filter["date_to"]) ? $default_filter["date_to"] : '';

        // Date filter options
        $date_options = [
            '' => 'All Time',
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_year' => 'This Year',
            // 'custom' => 'Custom Range'
        ];

        $formOutPut .= '<div class="date-filter-select-group">';
        $formOutPut .= '<select ' . ($disable_unselected ? "disabled" : "") . ' name="date_filter" class="date-filter-select">';

        foreach ($date_options as $value => $label) {
            $selected = ($date_filter_value === $value) ? ' selected' : '';
            $formOutPut .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
        }

        $formOutPut .= '</select>';
        $formOutPut .= '</div>';

        // Custom date range inputs (hidden by default, shown when custom is selected)
        $custom_display = ($date_filter_value === 'custom') ? 'block' : 'none !important';
        $formOutPut .= '<div class="custom-date-range" style="display: ' . $custom_display . ';">';
        $formOutPut .= '<div class="date-input-group">';
        $formOutPut .= '<label>From:</label>';
        $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="date" name="date_from" value="' . esc_attr($date_from_value) . '" class="date-from-field" />';
        $formOutPut .= '</div>';
        $formOutPut .= '<div class="date-input-group">';
        $formOutPut .= '<label>To:</label>';
        $formOutPut .= '<input ' . ($disable_unselected ? "disabled" : "") . ' type="date" name="date_to" value="' . esc_attr($date_to_value) . '" class="date-to-field" />';
        $formOutPut .= '</div>';
        $formOutPut .= '</div>';

        $formOutPut .= '</div>';
        $formOutPut .= '</div>';
    }
    // Date Filter end

    return $formOutPut;
} //function ends
?>
