<?php
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
        'use_url_filter' => 'query_string',
        'update_filter_options' => "on",
        'show_loader' => "on",
        'pages' => [],
        'loader_html' => '<div id="loader" style="display:none;"></div>',
        'loader_css' => '#loader { width: 56px; height: 56px; border-radius: 50%; background: conic-gradient(#0000 10%,#474bff); -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0); animation: spinner-zp9dbg 1s infinite linear; } @keyframes spinner-zp9dbg { to { transform: rotate(1turn); } }',
        'use_custom_template' => 0,
        'custom_template_code' => '',
        'product_selector' => '.products',
        'pagination_selector' => '.woocommerce-pagination ul.page-numbers',
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
            'label' => __('Show Categories', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show categories in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_attributes' => [
            'label' => __('Show Attributes', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show attributes in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_tags' => [
            'label' => __('Show Tags', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show tags in the filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_price_range' => [
            'label' => __('Show Price Range', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show price range filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_rating' => [
            'label' => __('Show Rating', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show product rating filter.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_search' => [
            'label' => __('Show Search', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show search box for products.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'use_url_filter' => [
            'label' => __('Use URL-Based Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Choose Filter Method', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'update_filter_options' => [
            'label' => __('Update filter options', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to dynamically update filter options.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        'show_loader' => [
            'label' => __('Show Loader', 'dynamic-ajax-product-filters-for-woocommerce'),
            'description' => __('Enable this option to show a loading indicator during AJAX requests.', 'dynamic-ajax-product-filters-for-woocommerce')
        ],
        // 'use_custom_template' => [
        //     'label' => __('Use Custom Product Template', 'dynamic-ajax-product-filters-for-woocommerce'),
        //     'description' => __('Enable this option to use a custom product template.', 'dynamic-ajax-product-filters-for-woocommerce')
        // ],
    ];

    foreach ($fields as $key => $field) {
        add_settings_field($key, '<p>' . $field['label'] . '</p><p class="admin-description">' . $field['description'] . '</p>', "dapfforwc_{$key}_render", 'dapfforwc-admin', 'dapfforwc_section');
    }

    // custom code template
    // add_settings_field('custom_template_code', __('product custom template code', 'dynamic-ajax-product-filters-for-woocommerce'), 'dapfforwc_custom_template_code_render', 'dapfforwc-admin', 'dapfforwc_section');

    $default_style = get_option('dapfforwc_style_options') ?: [
        'price' => ['type' => 'price', 'sub_option' => 'price', 'auto_price' => "on"],
        'rating' => ['type' => 'rating', 'sub_option' => 'rating'],
    ];
    update_option('dapfforwc_style_options', $default_style);
    // form style register
    register_setting(
        'dapfforwc_style_options_group',
        'dapfforwc_style_options',
        'dapfforwc_sanitize_options'
    );

    // Add Form Style section
    add_settings_section(
        'dapfforwc_style_section',
        '<p class="page-title">' . __('Form Style Configuration', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;">' . esc_html__('Customize the appearance and behavior of your filter components with our comprehensive styling options', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-style'
    );

    //   advance settings register
    $Advance_options = get_option('dapfforwc_advance_options') ?: [
        'product_selector' => 'ul.products',
        'pagination_selector' => '.woocommerce-pagination ul.page-numbers',
        'product_shortcode' => 'products',
        'remove_outofStock' => 0,
        'allow_data_share' => "on",
        'sidebar_on_top' => "on",
        'default_value_selected' => 0,
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
        '<p class="page-title">' . __('Advanced Settings', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;">' . esc_html__('You can handle advanced settings for your product filters here.', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-advance-settings'
    );

    // Add the "Product Selector" field
    add_settings_field(
        'product_selector',
        __('Product Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_product_selector_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );
    // Add the "Pagination Selector" field
    add_settings_field(
        'pagination_selector',
        __('Pagination Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_pagination_selector_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );
    // Add the "Product shotcode Selector" field
    add_settings_field(
        'product_shortcode',
        __('Product Shortcode Selector', 'dynamic-ajax-product-filters-for-woocommerce'),
        'dapfforwc_product_shortcode_callback',
        'dapfforwc-advance-settings',
        'dapfforwc_advance_settings_section'
    );

    add_settings_field('remove_outofStock', __('Remove out of stock product', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_remove_outofStock_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('allow_data_share', __('Contribute to Plugincy', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_allow_data_share_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('sidebar_on_top', __('Sidebar Top (Mobile only)', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_side_bar_top_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');
    add_settings_field('default_value_selected', __('Make Default Options Selected', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_default_value_selected_render", 'dapfforwc-advance-settings', 'dapfforwc_advance_settings_section');

    $attributes = wc_get_attribute_taxonomies(); // Get WooCommerce attributes

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
            'price' => 'price',
            'rating' => 'rating',
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
        '<p class="page-title">' . __('SEO Configuration', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        function () {
            echo '<p style="padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;">' . esc_html__('Configure SEO options for your product filters to improve search engine visibility and customize permalinks structure.', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>';
        },
        'dapfforwc-seo-permalinks'
    );

    // add_settings_field('use_filters_word_in_permalinks', __('Use Filters Word in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_filters_word_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
    if ($dapfforwc_options["use_url_filter"] !== "ajax") {
        add_settings_field('use_attribute_type_in_permalinks', __('Use Attribute Type in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_attribute_type_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
        // add_settings_field('filters_word_in_permalinks', __('Filters Word in Permalinks', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_filters_word_in_permalinks_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
        add_settings_field('permalinks_prefix', __('Permalinks Prefix', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_permalinks_prefix_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_permalinks_section');
    }
    // Add the "SEO Setup" section
    add_settings_section(
        'dapfforwc_seo_section',
        '<p class="h3title">' . __('SEO Setup', 'dynamic-ajax-product-filters-for-woocommerce') . '</p>',
        null,
        'dapfforwc-seo-permalinks'
    );

    // add Enable SEO option
    add_settings_field('enable_seo', __('Enable SEO', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_enable_seo_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_section');

    add_settings_field('use_anchor', __('Make filter link indexable for best SEO', 'dynamic-ajax-product-filters-for-woocommerce'), "dapfforwc_use_anchor_render", 'dapfforwc-seo-permalinks', 'dapfforwc_seo_section');
    if ($dapfforwc_options["use_url_filter"] !== "ajax") {
        // Add the "SEO Title" field
        add_settings_field(
            'seo_title',
            __('SEO Title', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_title_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );
        // Add the "SEO Description" field

        add_settings_field(
            'seo_description',
            __('SEO Description', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_description_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );

        // Add the "SEO Keywords" field

        add_settings_field(
            'seo_keywords',
            __('SEO Keywords', 'dynamic-ajax-product-filters-for-woocommerce'),
            'dapfforwc_seo_keywords_callback',
            'dapfforwc-seo-permalinks',
            'dapfforwc_seo_section'
        );
    }
    if ($dapfforwc_options["use_url_filter"] === "ajax") {
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

function dapfforwc_sanitize_options($input)
{
    if(isset($input["product_selector"]) && !isset($input["allow_data_share"])){
        $input["allow_data_share"] = "off";
    }
    if(isset($input["product_selector"]) && !isset($input["sidebar_on_top"])){
        $input["sidebar_on_top"] = "off";
    }
    // If input is not an array, make it one
    if (!is_array($input)) {
        return array();
    }

    $sanitized = array();

    // Loop through each element of the array
    foreach ($input as $key => $value) {
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

                default:
                    // Default sanitization for text fields
                    $sanitized[$key] = sanitize_text_field($value);
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
