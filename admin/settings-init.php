<?php
// settings-init.php

if (!defined('ABSPATH')) {
    exit;
}
function dapfforwc_settings_init()
{
    $dapfforwc_options = get_option('dapfforwc_options') ?: [
        'show_categories' => "on",
        'show_attributes' => "on",
        'show_tags' => "on",
        'show_price_range' => "on",
        'show_rating' => "on",
        'show_search' => "on",
        'show_brand' => "",
        'show_author' => "",
        'show_status' => "",
        'show_onsale' => "",
        'show_dimension' => "",
        'show_sku' => "",
        'show_discount' => "",
        'show_date_filter' => "",
        'use_url_filter' => 'query_string',
        'update_filter_options' => "on",
        'show_loader' => "on",
        'pages' => [],
        'loader_html' => '<div id="loader" style="display:none;"></div>',
        'loader_css' => '#loader { width: 56px; height: 56px; border-radius: 50%; -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0); animation: spinner-zp9dbg 1s infinite linear; } @keyframes spinner-zp9dbg { to { transform: rotate(1turn); } }',
        'use_custom_template' => 0,
        'custom_template_code' => '',
        'product_selector' => '.products',
        'pagination_selector' => '.woocommerce-pagination',
        'filters_word_in_permalinks' => 'filters',
    ];
    update_option('dapfforwc_options', $dapfforwc_options);

    register_setting(
        'dapfforwc_options_group',
        'dapfforwc_options',
        'dapfforwc_sanitize_options'
    );

    add_settings_section('dapfforwc_section', '', null, 'dapfforwc-admin');

    $fields = [
        'show_categories' => [
            'label' => esc_html__('Show Categories', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show categories in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_attributes' => [
            'label' => esc_html__('Show Attributes', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show attributes in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_tags' => [
            'label' => esc_html__('Show Tags', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show tags in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_price_range' => [
            'label' => esc_html__('Show Price Range', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show price range filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_rating' => [
            'label' => esc_html__('Show Rating', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show product rating filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_search' => [
            'label' => esc_html__('Show Search', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show search box for products.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_brand' => [
            'label' => esc_html__('Show Brand', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show brand filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_author' => [
            'label' => esc_html__('Show Authors', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show authors filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_status' => [
            'label' => esc_html__('Show Stock Status', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show status filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_onsale' => [
            'label' => esc_html__('Show Sale Status', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show on sale filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_dimension' => [
            'label' => esc_html__('Show Dimensions', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show dimensions filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_sku' => [
            'label' => esc_html__('Show SKU', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show sku filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_discount' => [
            'label' => esc_html__('Show Discount', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show discount filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_date_filter' => [
            'label' => esc_html__('Show Date Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show date filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_custom_fields' => [
            'label' => esc_html__('Show Custom Fields Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show custom fields filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'use_url_filter' => [
            'label' => esc_html__('Use URL-Based Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Choose Filter Method', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'update_filter_options' => [
            'label' => esc_html__('Update filter options', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to dynamically update filter options.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_loader' => [
            'label' => esc_html__('Show Loader', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => esc_html__('Enable this option to show a loading indicator during AJAX requests.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        // 'use_custom_template' => [
        //     'label' => esc_html__('Use Custom Product Template', 'dynamic-ajax-product-filters-for-woocommerce'),
        //     'description' => esc_html__('Enable this option to use a custom product template.', 'dynamic-ajax-product-filters-for-woocommerce')
        // ],
    ];

    foreach ($fields as $key => $field) {
        add_settings_field($key, '<p>' . $field['label'] . '</p><p class="admin-description">' . $field['description'] . '</p>', "dapfforwc_{$key}_render", 'dapfforwc-admin', 'dapfforwc_section');
    }

    // custom code template
    // add_settings_field('custom_template_code', esc_html__('product custom template code', 'dynamic-ajax-product-filters-for-woocommerce'), 'dapfforwc_custom_template_code_render', 'dapfforwc-admin', 'dapfforwc_section');

    $default_style = get_option('dapfforwc_style_options') ?: [
        'price' => ['type' => 'price', 'sub_option' => 'price', 'auto_price' => "on"],
        'rating' => ['type' => 'rating', 'sub_option' => 'rating'],
        'brands' => ['type' => 'brands', 'sub_option' => 'image'],
        'max_height' => ['product-category' => '400', "tag" => '400'],
    ];
    update_option('dapfforwc_style_options', $default_style);
    // form style register
    register_setting(
        'dapfforwc_style_options_group',
        'dapfforwc_style_options',
        'dapfforwc_sanitize_style_options'
    );

    // Add Form Style section
    add_settings_section(
        'dapfforwc_style_section',
        '<p class="page-title">' . esc_html__('Form Style Configuration', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px;">' . esc_html__('Customize the appearance and behavior of your filter components with our comprehensive styling options', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-style'
    );

    //   advance settings register
    $Advance_options = get_option('dapfforwc_advance_options') ?: [
        'product_selector' => 'ul.products',
        'pagination_selector' => '.woocommerce-pagination',
        'product_shortcode' => 'products',
        'remove_outofStock' => 0,
        'allow_data_share' => "on",
        'sidebar_on_top' => "on",
        'mobile_breakpoint' => 768,
        'default_value_selected' => 0,
        'exclude_attributes' => "",
        'exclude_custom_fields' => "",
        'no_products_text' => 'No products were found matching your selection.',
        'select2_placeholder' => 'Select Options',
    ];
    update_option('dapfforwc_advance_options', $Advance_options);
    register_setting(
        'dapfforwc_advance_settings',
        'dapfforwc_advance_options',
        'dapfforwc_sanitize_options'
    );
    // Add the "Advance Settings" section
    add_settings_section(
        'dapfforwc_advance_settings_section',
        '<p class="page-title">' . esc_html__('Advanced Settings', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;">' . esc_html__('You can handle advanced settings for your product filters here.', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-advance-settings'
    );

    // Add the "Product Selector" field
    add_settings_field(
        'product_selector',
        esc_html__('Product Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_product_selector_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );
    // Add the "Pagination Selector" field
    add_settings_field(
        'pagination_selector',
        esc_html__('Pagination Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_pagination_selector_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );
    // Add the "Product shotcode Selector" field
    add_settings_field(
        'product_shortcode',
        esc_html__('Product Shortcode Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_product_shortcode_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );

    // Add the "Text Manage" field
    add_settings_field(
        'text_manage',
        esc_html__('Text Manage', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwcpro_text_manage_render',
        'dapfforwcpro-advance-settings',
        'dapfforwcpro_advance_settings_section'
    );

    add_settings_field('remove_outofStock', esc_html__('Remove out of stock product', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_remove_outofStock_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('allow_data_share', esc_html__('Contribute to Plugincy', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_allow_data_share_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('sidebar_on_top', esc_html__('Sidebar Top (Mobile only)', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_side_bar_top_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('mobile_breakpoint', esc_html__('Mobile Breakpoint', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_mobile_breakpoint_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('default_value_selected', esc_html__('Make Default Options Selected', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_default_value_selected_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('exclude_attributes', esc_html__('Exclude Attribute', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_exclude_attributes_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('exclude_custom_fields', esc_html__('Exclude Custom Fields', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_exclude_custom_fields_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');

    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $all_attributes = isset($all_data['attributes']) ? $all_data['attributes'] : [];
    $exclude_attributes = isset($dapfforwc_advance_settings['exclude_attributes']) ? explode(',', $dapfforwc_advance_settings['exclude_attributes']) : [];
    $custom_fields = isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [];
    $exclude_custom_fields = isset($dapfforwc_advance_settings['exclude_custom_fields']) ? explode(',', $dapfforwc_advance_settings['exclude_custom_fields']) : [];
    $attributes = [];
    foreach ($all_attributes as $attribute) {
        if (in_array($attribute['attribute_name'], $exclude_attributes)) {
            continue;
        }
        $attributes[] = (object) [
            'attribute_name' => $attribute['attribute_name'],
            'attribute_label' => $attribute['attribute_label'],
        ];
    }
    $all_custom_fields = [];
    foreach ($custom_fields as $custom_field) {
        if (in_array($custom_field['name'], $exclude_custom_fields)) {
            continue;
        }
        $all_custom_fields[] = (object) [
            'attribute_name' => $custom_field['name'],
            'attribute_label' => $custom_field['label'],
        ];
    }

    register_setting(
        'dapfforwc_template_options_group',
        'dapfforwc_template_options',
        'dapfforwc_sanitize_template_options'
    );

    global $template_options;

    update_option('dapfforwc_template_options', $template_options);

    // seo-permalinks settings register
    $seo_permalinks_options = get_option('dapfforwc_seo_permalinks_options') ?: [
        'use_attribute_type_in_permalinks' => "on",
        'dapfforwc_permalinks_prefix_options' => [
            "product-category" => 'cata',
            'tag' => 'tags',
            'attribute' => !empty($attributes) ? array_reduce($attributes, function ($carry, $attr) {
                $carry[$attr->attribute_name] = $attr->attribute_name;
                return $carry;
            }, []) : [
                'color' => 'color',
                'size' => 'size',
                'brand' => 'brand',
                'material' => 'material',
                'style' => 'style',
            ],
            'custom' => !empty($all_custom_fields) ? array_reduce($all_custom_fields, function ($carry, $attr) {
                $carry[$attr->attribute_name] = $attr->attribute_name;
                return $carry;
            }, []) : [],
            'price' => 'price',
            'rating' => 'rating',
            'brand' => 'brand',
            'author' => 'author',
            'stock_status' => 'stockStatus',
            'sale_status' => 'saleStatus',
            'sale_status' => 'saleStatus',
            'width' => 'width',
            'min_width' => 'min_width',
            'max_width' => 'max_width',
            'length' => 'length',
            'height' => 'height',
            'min_height' => 'min_height',
            'max_height' => 'max_height',
            'weight' => 'weight',
            'min_weight' => 'min_weight',
            'max_weight' => 'max_weight',
            'sku' => 'sku',
            'discount' => 'discount',
            'date_filter' => 'date',
            'plugincy_search' => 'title',
        ],
        'filters_word_in_permalinks' => 'filters',
        'use_filters_word_in_permalinks' => '',
        'use_anchor' => 0,
    ];

    update_option('dapfforwc_seo_permalinks_options', $seo_permalinks_options);
    register_setting(
        'dapfforwc_seo_permalinks_settings',
        'dapfforwc_seo_permalinks_options',
        'dapfforwc_sanitize_options'
    );

    add_settings_section(
        'dapfforwc_seo_permalinks_section',
        '<p class="page-title">' . esc_html__('SEO Configuration', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;">' . esc_html__('Configure SEO options for your product filters to improve search engine visibility and customize permalinks structure.', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-seo-permalinks'
    );

    // add_settings_field('use_filters_word_in_permalinks', esc_html__('Use Filters Word in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_filters_word_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
    if (isset($dapfforwc_options["use_url_filter"]) && $dapfforwc_options["use_url_filter"] !== "ajax") {
        add_settings_field('use_attribute_type_in_permalinks', esc_html__('Use Attribute Type in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_attribute_type_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
        // add_settings_field('filters_word_in_permalinks', esc_html__('Filters Word in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_filters_word_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
        add_settings_field('permalinks_prefix', esc_html__('Permalinks Prefix', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_permalinks_prefix_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
    }
    // Add the "SEO Setup" section
    add_settings_section(
        'dapfforwc_seo_section',
        '<p class="h3title">' . esc_html__('SEO Setup', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        null,
        'dapfforwc-seo-permalinks'
    );

    if (isset($dapfforwc_options["use_url_filter"]) && $dapfforwc_options["use_url_filter"] !== "ajax") {
        // add Enable SEO option
        add_settings_field('enable_seo', esc_html__('Enable SEO', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_enable_seo_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_section');
    }
    add_settings_field('use_anchor', esc_html__('Make filter link indexable for best SEO', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_anchor_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_section');
    if (isset($dapfforwc_options["use_url_filter"]) && $dapfforwc_options["use_url_filter"] !== "ajax") {
        // Add the "SEO Title" field
        add_settings_field(
            'seo_title',
            esc_html__('SEO Title', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_title_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );
        // Add the "SEO Description" field

        add_settings_field(
            'seo_description',
            esc_html__('SEO Description', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_description_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );

        // Add the "SEO Keywords" field

        add_settings_field(
            'seo_keywords',
            esc_html__('SEO Keywords', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_keywords_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );
    }
    if (isset($dapfforwc_options["use_url_filter"]) && $dapfforwc_options["use_url_filter"] === "ajax") {
        add_settings_field(
            'use_url_filter_notice',
            '',
            function () {
                echo '<div style="margin:10px 0 20px 0;padding:10px 15px;background:#f1f5f9;border-left:4px solid #2271b1;">' .
                    esc_html__('To access more SEO and permalinks settings, please change the "Use URL-Based Filter" option from "ajax" to another method.', 'dynamic-ajax-product-filters-for-woocommerce') .
                    '</div>';
            },
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );
    }
}

add_action('admin_init', 'dapfforwc_settings_init');

// Sanitize template options
function dapfforwc_sanitize_template_options($input)
{
    $sanitized = array();

    if (isset($input['active_template'])) {
        $sanitized['active_template'] = sanitize_text_field(wp_unslash($input['active_template']));
    }

    if (isset($input['background_color'])) {
        $sanitized['background_color'] = sanitize_hex_color(wp_unslash($input['background_color']));
    }

    if (isset($input['primary_color'])) {
        $sanitized['primary_color'] = sanitize_hex_color(wp_unslash($input['primary_color']));
    }

    if (isset($input['secondary_color'])) {
        $sanitized['secondary_color'] = sanitize_hex_color(wp_unslash($input['secondary_color']));
    }

    if (isset($input['border_color'])) {
        $sanitized['border_color'] = sanitize_hex_color(wp_unslash($input['border_color']));
    }

    if (isset($input['text_color'])) {
        $sanitized['text_color'] = sanitize_hex_color(wp_unslash($input['text_color']));
    }

    return $sanitized;
}


function dapfforwc_sanitize_options($input)
{
    if (isset($input["product_selector"]) && !isset($input["allow_data_share"])) {
        $input["allow_data_share"] = "off";
    }
    if (isset($input["product_selector"]) && !isset($input["sidebar_on_top"])) {
        $input["sidebar_on_top"] = "off";
    }
    // If input is not an array, make it one
    if (!is_array($input)) {
        return array();
    }

    $sanitized = array();

    // Loop through each element of the array
    foreach ($input as $key => $value) {

        if($value === '#000000' || $value === "" || $value === 0 || $value === "0" ){
            continue; // Skip if the value is the default color
        }
        // Sanitize based on what type of data this is
        if (is_array($value)) {
            // Recursively sanitize nested arrays
            $sanitized[$key] = dapfforwc_sanitize_options($value);
        } else {
            // Determine the right sanitization based on the key or value type
            switch ($key) {
                // Examples for different types of fields
                case 'custom_template_code':
                    $sanitized[$key] = wp_kses_post($value);
                    break;

                case 'url_field':
                    $sanitized[$key] = esc_url_raw($value);
                    break;

                case 'email_field':
                    $sanitized[$key] = sanitize_email($value);
                    break;

                case 'number_field':
                    $sanitized[$key] = intval($value);
                    break;

                case 'mobile_breakpoint':
                    $bp = absint($value);
                    $sanitized[$key] = $bp > 0 ? $bp : 768;
                    break;

                default:
                    // Default sanitization for text fields
                    $sanitized[$key] = sanitize_text_field(wp_unslash($value));
                    if ($key === 'use_attribute_type_in_permalinks' && $value === 'on') {
                        update_option('woocommerce_slug_check_dismissed_time', time() + (100 * DAY_IN_SECONDS));
                        break;
                    }
            }
        }
    }

    if (isset($input['seo_title']) && isset($input['seo_description']) && !isset($input['use_attribute_type_in_permalinks'])) {
        update_option('woocommerce_slug_check_dismissed_time', false);
    }

    return $sanitized;
}

function dapfforwc_sanitize_style_options($input)
{
    if (!is_array($input)) {
        return [];
    }

    $sanitized = [];

    foreach ($input as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = dapfforwc_sanitize_style_options($value);
            continue;
        }

        $sanitized[$key] = sanitize_text_field(wp_unslash($value));
    }

    return $sanitized;
}
