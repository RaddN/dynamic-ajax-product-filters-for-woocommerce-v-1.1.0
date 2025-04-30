<?php

/**
 * Plugin Name: Dynamic AJAX Product Filters for WooCommerce
 * Plugin URI:  https://plugincy.com/
 * Description: A WooCommerce plugin to filter products by attributes, categories, and tags using AJAX for seamless user experience.
 * Version:     1.1.1
 * Author:      Plugincy
 * Author URI:  https://plugincy.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dynamic-ajax-product-filters-for-woocommerce
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load text domain for translations
add_action('plugins_loaded', 'dapfforwc_load_textdomain');
function dapfforwc_load_textdomain()
{
    load_plugin_textdomain('dynamic-ajax-product-filters-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Global Variables
global $allowed_tags, $dapfforwc_options, $dapfforwc_seo_permalinks_options, $dapfforwc_advance_settings, $dapfforwc_styleoptions, $dapfforwc_use_url_filter, $dapfforwc_auto_detect_pages_filters, $dapfforwc_slug, $dapfforwc_sub_options, $dapfforwc_front_page_slug;


$dapfforwc_options = get_option('dapfforwc_options') ?: [];
$dapfforwc_advance_settings = get_option('dapfforwc_advance_options') ?: [];
$dapfforwc_seo_permalinks_options = get_option('dapfforwc_seo_permalinks_options') ?: [];
$dapfforwc_styleoptions = get_option('dapfforwc_style_options') ?: [];

$dapfforwc_use_url_filter = isset($dapfforwc_options['use_url_filter']) ? $dapfforwc_options['use_url_filter'] : false;
$dapfforwc_auto_detect_pages_filters = isset($dapfforwc_options['pages_filter_auto']) ? $dapfforwc_options['pages_filter_auto'] : '';
$dapfforwc_slug = "";

// Get the ID of the front page

$dapfforwc_front_page_id = get_option('page_on_front') ?: null;

// Get the front page object
$dapfforwc_front_page = isset($dapfforwc_front_page_id) ? get_post($dapfforwc_front_page_id) : null;
// Get the slug of the front page
$dapfforwc_front_page_slug = isset($dapfforwc_front_page) ? $dapfforwc_front_page->post_name : "";


$allowed_tags = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'class' => array(),
        'target' => array(), // Allow target attribute for links
    ),
    'strong' => array(),
    'em' => array(),
    'li' => array(
        'class' => array(),
    ),
    'div' => array(
        'class' => array(),
        'id' => array(), // Allow id for divs
    ),
    'img' => array(
        'src' => array(),
        'alt' => array(),
        'class' => array(),
        'width' => array(), // Allow width attribute
        'height' => array(), // Allow height attribute
    ),
    'h1' => array('class' => array()), // Allow h1
    'h2' => array('class' => array()),
    'h3' => array('class' => array()), // Allow h3
    'h4' => array('class' => array()), // Allow h4
    'h5' => array('class' => array()), // Allow h5
    'h6' => array('class' => array()), // Allow h6
    'span' => array('class' => array()),
    'p' => array('class' => array()),
    'br' => array(), // Allow line breaks
    'blockquote' => array(
        'cite' => array(), // Allow cite attribute for blockquotes
        'class' => array(),
    ),
    'table' => array(
        'class' => array(),
        'style' => array(), // Allow inline styles
    ),
    'tr' => array(
        'class' => array(),
    ),
    'td' => array(
        'class' => array(),
        'colspan' => array(), // Allow colspan attribute
        'rowspan' => array(), // Allow rowspan attribute
    ),
    'th' => array(
        'class' => array(),
        'colspan' => array(),
        'rowspan' => array(),
    ),
    'ul' => array('class' => array()), // Allow unordered lists
    'ol' => array('class' => array()), // Allow ordered lists
    'script' => array(), // Be cautious with scripts
);


// Define sub-options
$dapfforwc_sub_options = [
    'checkbox' => [
        'checkbox' => 'Checkbox',
        'button_check' => 'Button Checkbox',
        'radio_check' => 'Radio Check',
        'radio' => 'Radio',
        'square_check' => 'Square Check',
        'square' => 'Square',
        'checkbox_hide' => 'Checkbox Hide',
    ],
    'color' => [
        'color' => 'Color',
        'color_no_border' => 'Color Without Border',
        'color_circle' => 'Color Circle',
        'color_value' => 'Color With Value',
    ],
    'image' => [
        'image' => 'Image',
        'image_no_border' => 'Image Without Border',
    ],
    'dropdown' => [
        'select' => 'Select',
        'select2' => 'Select 2',
        'select2_classic' => 'Select 2 Classic',
    ],
    'price' => [
        'price' => 'Price',
        'slider' => 'Slider',
        'input-price-range' => 'input price range',
    ],
    'rating' => [
        'rating' => 'Rating Star',
        'rating-text' => 'Rating Text',
        'dynamic-rating' => 'Dynamic Rating',
    ],
];



// Check if WooCommerce is active
add_action('plugins_loaded', 'dapfforwc_check_woocommerce');

function dapfforwc_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'dapfforwc_missing_woocommerce_notice');
    } else {
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'admin/admin-notice.php';
            require_once(plugin_dir_path(__FILE__) . 'includes/get_review.php');
            require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
        }
        require_once plugin_dir_path(__FILE__) . 'includes/filter-template.php';

        add_action('wp_enqueue_scripts', 'dapfforwc_enqueue_scripts');
        add_action('admin_enqueue_scripts', 'dapfforwc_admin_scripts');
        require_once plugin_dir_path(__FILE__) . 'includes/class-filter-functions.php';

        add_action('wp_ajax_dapfforwc_filter_products', 'dapfforwc_filter_products');
        add_action('wp_ajax_nopriv_dapfforwc_filter_products', 'dapfforwc_filter_products');

        register_setting('dapfforwc_options_group', 'dapfforwc_filters', 'sanitize_text_field');

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dapfforwc_add_settings_link');
        require_once plugin_dir_path(__FILE__) . 'includes/common-functions.php';
        require_once plugin_dir_path(__FILE__) . 'includes/new-method-ajax-handel.php';
    }
}

function dapfforwc_missing_woocommerce_notice()
{
    echo '<div class="notice notice-error"><p><strong>' . esc_html__('Filter Plugin', 'dynamic-ajax-product-filters-for-woocommerce') . '</strong> ' . esc_html__('requires WooCommerce to be installed and activated.', 'dynamic-ajax-product-filters-for-woocommerce') . '</p></div>';
}

// Enqueue scripts and styles
function dapfforwc_enqueue_scripts()
{
    global $dapfforwc_use_url_filter, $dapfforwc_options, $dapfforwc_seo_permalinks_options, $dapfforwc_slug, $dapfforwc_styleoptions, $dapfforwc_advance_settings, $dapfforwc_front_page_slug;

    // $script_handle = 'filter-ajax';
    // $script_path = 'assets/js/filter.min.js';

    // if ($dapfforwc_use_url_filter === 'query_string') {
    $script_handle = 'urlfilter-ajax';
    $script_path = 'assets/js/new-filter.js';
    // }

    wp_enqueue_script('jquery');
    wp_enqueue_script($script_handle, plugin_dir_url(__FILE__) . $script_path, ['jquery'], '1.1.1', true);
    wp_script_add_data($script_handle, 'async', true); // Load script asynchronously
    wp_localize_script($script_handle, 'dapfforwc_data', compact('dapfforwc_options', 'dapfforwc_seo_permalinks_options', 'dapfforwc_slug', 'dapfforwc_styleoptions', 'dapfforwc_advance_settings', 'dapfforwc_front_page_slug'));
    wp_localize_script($script_handle, 'dapfforwc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);

    wp_enqueue_style('filter-style', plugin_dir_url(__FILE__) . 'assets/css/style.min.css', [], '1.1.1');
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '1.1.1');
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], '1.1.1', true);
    $css = '';
    // Generate inline css for sidebartop in mobile
    if (isset($dapfforwc_advance_settings["sidebar_top"]) && $dapfforwc_advance_settings["sidebar_top"] === "on") {
        $css .= "@media (max-width: 768px) {
                    div#content>div {
                        flex-direction: column !important;
                    }
        }";
    }
    // Generate CSS for max-height

    $max_height = (is_array($dapfforwc_styleoptions) && isset($dapfforwc_styleoptions["max_height"])) ? $dapfforwc_styleoptions["max_height"] : [];
    foreach ($max_height as $key => $value) {
        // Sanitize the key to create a valid CSS class name
        if (is_numeric($value) && $value > 0) {
            $cssClass = strtolower($key); // Replace dashes with underscores
            $css .= "#{$cssClass} .items{\n";
            $css .= "    max-height: {$value}px;\n"; // Set max-height based on value
            $css .= "    overflow-y: scroll;\n";
            $css .= "    transition: max-height 0.3s ease;\n";
            $css .= "}\n";
        }
    }
    // Add the generated CSS as inline style
    wp_add_inline_style('filter-style', $css);
    wp_add_inline_script('select2-js', '
        jQuery(document).ready(function($) {
            
            $(".select2").select2({
                placeholder: "Select Options",
                allowClear: true
            });

           
            $("select.select2_classic").select2({
                placeholder: "Select Options",
                allowClear: true
            });
            if ($(window).width() > 768) {
    function initializeCollapsible() {
        $(".title").each(function () {
            const $this = $(this);
            const $items = $this.next(".items");

            // Hide items initially if the title has a specific class
            if ($this.hasClass("collapsable_minimize_initial")) {
                $items.hide();
            }

            // Clear any existing event handlers before adding new ones
            $this.off("click").on("click", function () {
                // Handle `.collapsable_arrow` class for rotating the SVG icon
                if ($this.hasClass("collapsable_arrow")) {
                    $this.find("svg").toggleClass("rotated");
                }
                // Toggle the visibility of the sibling `.items`
                $items.slideToggle(300);
            });
        });
    }

    // Initialize collapsible elements
    initializeCollapsible();

    // Reinitialize collapsibles after AJAX content is loaded
    $(document).ajaxComplete(function () {
        initializeCollapsible();
    });
}


        });
    ');
}

function dapfforwc_admin_scripts($hook)
{
    if ($hook !== 'toplevel_page_dapfforwc-admin') {
        return; // Load only on the plugin's admin page
    }
    global $dapfforwc_sub_options;
    wp_enqueue_style('dapfforwc-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.min.css', [], '1.1.1');
    wp_enqueue_code_editor(array('type' => 'text/html'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('dapfforwc-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.min.js', [], '1.1.1', true);
    wp_enqueue_media();
    wp_enqueue_script('dapfforwc-media-uploader', plugin_dir_url(__FILE__) . 'assets/js/media-uploader.min.js', ['jquery'], '1.0.0', true);

    $inline_script = 'document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("attribute-dropdown");

    if(dropdown){const firstAttribute = dropdown.value;

    document.querySelector(`#options-${firstAttribute}`).style.display = "block";}

    function toggleDisplay(selector, display) {
        document.querySelectorAll(selector).forEach(el => {
            el.style.display = display;
        });
    }

    if(dropdown)dropdown.addEventListener("change", function () {
    const selectedAttribute = this.value;

    toggleDisplay(".style-options", "none");

    if (selectedAttribute) {
        const selectedOptions = document.getElementById(`options-${selectedAttribute}`);
        if (selectedOptions) {
            selectedOptions.style.display = "block";
        }
    }

    if (selectedAttribute === "price") {
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".primary_options label.price", "block");
        toggleDisplay(".min-max-price-set", "block");
    }
    else if (selectedAttribute === "rating") {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".primary_options label.rating", "block");
    } else if(selectedAttribute === "category"){
        toggleDisplay(".hierarchical", "block");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
    }
    else {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
    }
});

    document.querySelectorAll(`.style-options .primary_options input[type="radio"][name^="dapfforwc_style_options"]`).forEach(function (radio) {
        radio.addEventListener("change", function () {
            const selectedType = this.value;
            const attributeName = this.name.match(/\[(.*?)\]/)[1];
            const subOptionsContainer = document.querySelector(`#options-${attributeName} .dynamic-sub-options`);
   
            document.querySelectorAll(".primary_options label").forEach(label => {
                label.classList.remove("active");
                const checkIcon = label.querySelector(".active");
                if (checkIcon) {
                    checkIcon.style.display = "none"; 
                }
            });
            const selectedLabel = radio.closest("label");
            selectedLabel.classList.add("active");

            const subOptions = ' . (isset($dapfforwc_sub_options) && is_array($dapfforwc_sub_options) ? wp_json_encode($dapfforwc_sub_options) : '[]') . '

            const currentOptions = subOptions[selectedType] || {};
            subOptionsContainer.innerHTML = "";

            const fragment = document.createDocumentFragment();
            for (const key in currentOptions) {
                const label = document.createElement("label");
                label.innerHTML = `
                    <span class="active" style="display:none;"><i class="fa fa-check"></i></span>
                    <input type="radio" class="optionselect" name="dapfforwc_style_options[${attributeName}][sub_option]" value="${key}">                    
                    <img src="/wp-content/plugins/dynamic-ajax-product-filters-for-woocommerce/assets/images/${key}.png" alt="${currentOptions[key]}">
                   
                `;
                fragment.appendChild(label);
            }
            subOptionsContainer.appendChild(fragment);

            attachSubOptionListeners();

           if(selectedType==="color" || selectedType==="image") {
            document.querySelector(`.advanced-options.${attributeName}`).style.display = "block";
            document.querySelector(`.advanced-options.${attributeName} .color`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .image`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .${selectedType}`).style.display = "block";

           }else {
            document.querySelectorAll(".advanced-options").forEach(advanceoptions =>{
                advanceoptions.style.display = "none";
            })
           }
        });
    });

    function attachSubOptionListeners() {
    const radioButtons = document.querySelectorAll(".optionselect");
    
    
    radioButtons.forEach(radio => {
        radio.addEventListener("change", function() {
            document.querySelectorAll(".dynamic-sub-options label").forEach(label => {
                label.classList.remove("active");
                const checkIcon = label.querySelector(".active");
                if (checkIcon) {
                    checkIcon.style.display = "none";
                }
            });

            const selectedLabel = this.closest("label");
            selectedLabel.classList.add("active");
            const checkIcon = selectedLabel.querySelector(".active");
            if (checkIcon) {
                checkIcon.style.display = "inline"; // Show check icon
            }

            // Managing single selection checkbox
            const singleSelectionCheckbox = this.closest(".style-options").querySelector(".setting-item.single-selection input");
            if (this.value === "select") {
                singleSelectionCheckbox.checked = true;
            } else {
                singleSelectionCheckbox.checked = false; // Uncheck if other options are selected
            }
        });
    });
}

// Call the function to attach listeners
attachSubOptionListeners();

});';
    wp_add_inline_script('dapfforwc-admin-script', $inline_script);
}

function dapfforwc_filter_products()
{
    if (class_exists('dapfforwc_Filter_Functions')) {
        $filter = new dapfforwc_Filter_Functions();
        $filter->process_filter();
    } else {
        wp_send_json_error('Filter class not found.');
    }
}


function dapfforwc_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=dapfforwc-admin">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

function dapfforwc_get_full_slug($post_id)
{
    if (empty($post_id)) {
        return ''; // Return an empty string if $post_id is not defined
    }
    $dapfforwc_slug_parts = [];
    $current_post_id = $post_id;

    while ($current_post_id) {
        $current_post = get_post($current_post_id);

        if (!$current_post) {
            break; // Exit if no post is found
        }

        // Prepend the current slug
        array_unshift($dapfforwc_slug_parts, $current_post->post_name);

        // Get the parent post ID
        $current_post_id = wp_get_post_parent_id($current_post_id);
    }

    return implode('/', $dapfforwc_slug_parts); // Combine slugs with '/'
}


require_once(plugin_dir_path(__FILE__) . 'includes/widget_design_template.php');
require_once(plugin_dir_path(__FILE__) . 'includes/blocks_widget_create.php');

// block editor script
function dapfforwc_enqueue_dynamic_ajax_filter_block_assets()
{
    wp_enqueue_script(
        'dynamic-ajax-filter-block',
        plugins_url('includes/block.min.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'includes/block.min.js'),
        true
    );

    wp_enqueue_style('custom-box-control-styles', plugin_dir_url(__FILE__) . 'assets/css/block-editor.min.css', [], '1.1.1');
}
add_action('enqueue_block_editor_assets', 'dapfforwc_enqueue_dynamic_ajax_filter_block_assets');




// filter error detector
add_action('admin_bar_menu', 'dapfforwc_add_debug_menu', 100);

function dapfforwc_add_debug_menu($wp_admin_bar)
{
    if (current_user_can('administrator')) {
        $args = [
            'id'    => 'dapfforwc_debug',
            'title' => '<span class="ab-icon dashicons dashicons-filter"></span><span id="dapfforwc_issue_count"></span> ' . __('Product Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'meta'  => [
                'class' => 'dapfforwc-debug-bar',
            ],
        ];
        $wp_admin_bar->add_node($args);

        $wp_admin_bar->add_node([
            'id'     => 'dapfforwc_debug_sub',
            'parent' => 'dapfforwc_debug',
            'title'  => '<span id="dapfforwc_debug_message">' . __('Checking...', 'dynamic-ajax-product-filters-for-woocommerce') . '</span>',
            'meta'   => [
                'class' => 'ab-sub-wrapper',
            ],
        ]);
    }
}

add_action('wp_footer', 'dapfforwc_check_elements');

function dapfforwc_check_elements()
{
    global $dapfforwc_advance_settings;
    if (current_user_can('administrator')) {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var debugMessage = document.getElementById('dapfforwc_debug_message');
                var issueCount = document.getElementById('dapfforwc_issue_count');
                if (!document.querySelector('#product-filter')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Filter is not added', 'dynamic-ajax-product-filters-for-woocommerce'); ?>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwc_advance_settings["product_selector"]) && !empty($dapfforwc_advance_settings["product_selector"]) ? $dapfforwc_advance_settings["product_selector"] : ''); ?>')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Products are not found. Add product or', 'dynamic-ajax-product-filters-for-woocommerce'); ?> <a href="#" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwc_advance_settings["pagination_selector"]) && !empty($dapfforwc_advance_settings["pagination_selector"]) ? $dapfforwc_advance_settings["pagination_selector"] : ''); ?>')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Pagination is not found', 'dynamic-ajax-product-filters-for-woocommerce'); ?> <a href="#" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else {
                    debugMessage.innerHTML = '<span style="color: green;">&#10003;</span> <?php echo esc_html__('Filter working fine', 'dynamic-ajax-product-filters-for-woocommerce'); ?>';

                }
            });
        </script>
        <style>
            ul#wp-admin-bar-dapfforwc_debug-default {
                padding: 0 !important;
                margin: 0 !important;
            }

            li#wp-admin-bar-dapfforwc_debug_sub {
                display: block !important;
                padding: 10px 5px !important;
                height: max-content;
            }
        </style>
<?php
    }
}



function dapfforwc_register_api_routes()
{
    register_rest_route('dynamic-ajax-product-filters-for-woocommerce/v1', '/attributes/', array(
        'methods' => 'GET',
        'callback' => 'dapfforwc_get_product_attributes',
        'permission_callback' => '__return_true', // Adjust permissions as needed
    ));
}
add_action('rest_api_init', 'dapfforwc_register_api_routes');

function dapfforwc_get_product_attributes()
{
    // Fetch WooCommerce attribute taxonomies
    $attributes = wc_get_attribute_taxonomies();
    $result = [];

    foreach ($attributes as $attribute) {
        $result[] = [
            'id' => $attribute->attribute_id,
            'name' => $attribute->attribute_label,
            'slug' => $attribute->attribute_name,
        ];
    }

    if (empty($result)) {
        return new WP_Error('no_attributes', __('No product attributes found', 'dynamic-ajax-product-filters-for-woocommerce'), array('status' => 404));
    }

    return rest_ensure_response($result);
}

/** * Set custom SEO meta tags based on URL parameters */
function dapfforwc_set_seo_meta_tags()
{
    global $dapfforwc_seo_permalinks_options;

    // Only proceed if SEO is enabled
    if (!isset($dapfforwc_seo_permalinks_options['enable_seo']) || $dapfforwc_seo_permalinks_options['enable_seo'] !== 'on') {
        return;
    }

    // Get sanitized URL parameters using the secure method
    $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
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
    $query_params = [];
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_params);
    }

    // Check if the URL has a fragment and join it with query_params
    if (isset($parsed_url['fragment']) && !empty($parsed_url['fragment'])) {
        $parsed_fragment = str_replace(['#038;', '038;'], '', $parsed_url['fragment']);
        parse_str($parsed_fragment, $fragment_params);
        $query_params = array_merge($query_params, $fragment_params);
    }

    // Check if we have filter parameters in the URL
    $has_filters = false;

    // Check format 1: filters=1&param=value
    if (isset($query_params['filters']) && $query_params['filters'] == '1') {
        $has_filters = true;
    }
    // Check format 2: filters=value1,value2
    elseif (isset($query_params['filters']) && !empty($query_params['filters']) && $query_params['filters'] != '1') {
        $has_filters = true;
    }

    if (!$has_filters) {
        return;
    }

    // Get base SEO settings
    $seo_title = $dapfforwc_seo_permalinks_options['seo_title'] ?? '{site_title} {page_title} {attribute_prefix} {value}';
    $seo_description = $dapfforwc_seo_permalinks_options['seo_description'] ?? '{site_title} {page_title} {attribute_prefix} {value}';
    $seo_keywords = $dapfforwc_seo_permalinks_options['seo_keywords'] ?? '{site_title} {page_title} {attribute_prefix} {value}';

    // Get site and page title
    $site_title = get_bloginfo('name');
    $page_title = '';

    // Get current category title if available
    if (is_product_category()) {
        $term = get_queried_object();
        if ($term) {
            $page_title = $term->name;
        }
    } else if (is_product_tag()) {
        $term = get_queried_object();
        if ($term) {
            $page_title = $term->name;
        }
    } else {
        $page_title = get_the_title();
    }

    $seo_title = str_replace(array_keys(dapfforwc_replacement($seo_title, $query_params, $site_title, $page_title)), array_values(dapfforwc_replacement($seo_title, $query_params, $site_title, $page_title)), $seo_title);
    $seo_description = str_replace(array_keys(dapfforwc_replacement($seo_description, $query_params, $site_title, $page_title)), array_values(dapfforwc_replacement($seo_description, $query_params, $site_title, $page_title)), $seo_description);
    $seo_keywords = str_replace(array_keys(dapfforwc_replacement($seo_keywords, $query_params, $site_title, $page_title)), array_values(dapfforwc_replacement($seo_keywords, $query_params, $site_title, $page_title)), $seo_keywords);

    // Clean up any extra spaces
    $seo_title = preg_replace('/\s+/', ' ', trim($seo_title));
    $seo_description = preg_replace('/\s+/', ' ', trim($seo_description));
    $seo_keywords = preg_replace('/\s+/', ' ', trim($seo_keywords));

    // Get canonical URL
    $canonical_url = home_url(add_query_arg([], $GLOBALS['wp']->request));

    // Output the meta tags
    echo '<meta name="title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<title>' . esc_html($seo_title) . '</title>' . "\n";
    echo '<meta name="description" content="' . esc_attr($seo_description) . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '">' . "\n";
    echo '<meta name="robots" content="' . esc_attr($dapfforwc_seo_permalinks_options['seo_meta_tag'] ?? 'index, follow') . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";

    // Open Graph meta tags
    echo '<meta property="og:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($seo_description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical_url) . '">' . "\n";

    // Twitter meta tags
    echo '<meta name="twitter:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($seo_description) . '">' . "\n";
}

function dapfforwc_replacement($current_place, $query_params, $site_title, $page_title)
{
    // New approach: Extract attribute-value pairs directly from query_params
    $formatted_pairs = [];

    // Process each query parameter as an attribute-value pair
    foreach ($query_params as $param => $value) {
        // Skip special parameters

        if ($param === 'filters' && $value === '1') {
            continue; // Skip special parameters
        }
        // Process multi-value parameters (comma-separated)
        $values = explode(',', sanitize_text_field($value));
        $formatted_values = [];

        if (strpos($current_place, '{attribute_prefix}') !== false) {

            foreach ($values as $val) {
                $formatted_values[] = str_replace('-', ' ', $val);
            }

            // Format as "attribute seperator between {attribute_prefix} & {value} value1, value2"
            if (!empty($formatted_values)) {
                preg_match('/{attribute_prefix}(.*?)\{value\}/', $current_place, $matches);
                $separator = $matches[1] ?? '-';
                $formatted_pairs[] = $param . "{$separator}" . implode(', ', $formatted_values);
            }
        } elseif (strpos($current_place, '{value}') !== false) {
            // Format as "value1, value2"
            if (!empty($values)) {
                $formatted_pairs[] = implode(', ', $values);
            }
        }
    }

    // Combine all formatted pairs
    $formatted_string = implode(', ', $formatted_pairs);

    //  // Special handling for categories and tags from the old approach
    //  if (isset($query_params['cata']) && !empty($query_params['cata'])) {
    //      $categories = explode(',', sanitize_text_field($query_params['cata']));
    //      $formatted_categories = array_map(function ($cat) {
    //          return str_replace('-', ' ', $cat);
    //      }, $categories);

    //      if (!empty($formatted_categories)) {
    //          $formatted_string .= (!empty($formatted_string) ? ', ' : '') . 'category - ' . implode(', ', $formatted_categories);
    //      }
    //  }

    //  if (isset($query_params['tags']) && !empty($query_params['tags'])) {
    //      $tags = explode(',', sanitize_text_field($query_params['tags']));
    //      $formatted_tags = array_map(function ($tag) {
    //          return str_replace('-', ' ', $tag);
    //      }, $tags);

    //      if (!empty($formatted_tags)) {
    //          $formatted_string .= (!empty($formatted_string) ? ', ' : '') . 'tag - ' . implode(', ', $formatted_tags);
    //      }
    //  }

    // Replace placeholders in SEO settings
    $replacements = [
        '{site_title}' => $site_title,
        '{page_title}' => $page_title,
        '{attribute_prefix}' => '', // No longer needed as we format differently
        '{value}' => $formatted_string // Now contains "attribute - value" format
    ];

    return $replacements;
}

// Hook into wp_head with a high priority to ensure our tags are output correctly
add_action('wp_head', 'dapfforwc_set_seo_meta_tags', 0);














/**
 * Advanced Product Filter Functions
 * Add this to your theme's functions.php or to a plugin file
 */

/**
 * Initialize the product filter functionality
 */
function wpc_filter_init()
{
    // Only run on frontend
    if (is_admin() && !wp_doing_ajax()) {
        return;
    }

    error_log("hello init done");

    // Filter the main query using pre_get_posts
    add_action('pre_get_posts', 'wpc_filter_products_query');

    // Add scripts and styles
    // add_action('wp_enqueue_scripts', 'wpc_filter_enqueue_scripts');

    // Intercept and handle filter requests
    add_action('parse_request', 'wpc_parse_filter_request');
}
add_action('init', 'wpc_filter_init');

/**
 * Parse filter parameters from the request
 * 
 * @param WP $wp Current WordPress environment instance
 * @return WP
 */
function wpc_parse_filter_request($wp)
{
    // Only process product filter requests
    if (!isset($wp->query_vars['post_type']) || $wp->query_vars['post_type'] !== 'product') {
        return $wp;
    }

    // Parse all the filter parameters
    $filter_params = wpc_get_filter_params();

    // Store filter parameters in a global variable for later use
    $GLOBALS['wpc_filter_params'] = $filter_params;

    return $wp;
}

/**
 * Get all filter parameters from request
 * 
 * @return array
 */
function wpc_get_filter_params()
{
    global $dapfforwc_seo_permalinks_options;
    $isattrinurl = $dapfforwc_seo_permalinks_options && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on";
    $attrprefix = isset($dapfforwc_seo_permalinks_options["dapfforwc_permalinks_prefix_options"]) ? $dapfforwc_seo_permalinks_options["dapfforwc_permalinks_prefix_options"] : [];
    $params = array();

    // Get category filter
    if ($isattrinurl &&  $attrprefix["category"] && isset($_GET[$attrprefix["category"]]) && !empty($_GET[$attrprefix["category"]])) {
        $params['category'] = wpc_sanitize_array($_GET[$attrprefix["category"]]);
        error_log(json_encode($params['category']));
    } else {
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $params['category'] = wpc_sanitize_array($_GET['category']);
        }
    }

    // Get tag filter
    if ($isattrinurl &&  $attrprefix["tag"] && isset($_GET[$attrprefix["tag"]]) && !empty($_GET[$attrprefix["tag"]])) {
        $params['tag'] = wpc_sanitize_array($_GET[$attrprefix["tag"]]);
        error_log(json_encode($params['tag']));
    } else {
        if (isset($_GET['tags']) && !empty($_GET['tags'])) {
            $params['tag'] = wpc_sanitize_array($_GET['tags']);
            error_log(json_encode($_GET['tags']));
        }
    }

    // Get attribute filters (dynamic)
    if (isset($_GET['attribute']) && is_array($_GET['attribute'])) {
        foreach ($_GET['attribute'] as $attribute_name => $value) {
            error_log('your key: ' . json_encode($attribute_name) . ' & value: ' . json_encode($value));

            if (!empty($value)) {
                $params['attributes'][$attribute_name] = wpc_sanitize_array($value);
            }
        }
    }

    // Get rating filter
    if ($isattrinurl &&  $attrprefix["rating"] && isset($_GET[$attrprefix["rating"]]) && !empty($_GET[$attrprefix["rating"]])) {
        $params['rating'] = wpc_sanitize_array($_GET[$attrprefix["rating"]]);
        error_log(json_encode($params['rating']));
    } else {
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $params['rating'] = wpc_sanitize_array($_GET['rating']);
        }
    }

    // Get price range filter
    if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
        $params['min_price'] = floatval($_GET['min_price']);
    }

    if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
        $params['max_price'] = floatval($_GET['max_price']);
    }

    // Get search query
    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $params['search'] = sanitize_text_field($_GET['s']);
    }

    error_log('sir your params : ' . json_encode($params));

    return $params;
}

/**
 * Helper function to sanitize array values
 * 
 * @param mixed $input Input to sanitize
 * @return array|string Sanitized input
 */
function wpc_sanitize_array($input)
{
    if (is_array($input)) {
        return array_map('sanitize_text_field', $input);
    }
    return sanitize_text_field($input);
}

/**
 * Modify the main products query to apply filters
 * 
 * @param WP_Query $query The WordPress query object
 * @return void
 */
function wpc_filter_products_query($query)
{
    // error_log("sir your query is : " . json_encode($query));



    // Only modify main query on frontend for product queries
    if (!$query->is_main_query() || is_admin() && !$query->is_tax(get_object_taxonomies('product'))) {
        return;
    }
    // Get filter parameters
    $filter_params = isset($GLOBALS['wpc_filter_params']) ? $GLOBALS['wpc_filter_params'] : wpc_get_filter_params();

    error_log("sir your params : " . json_encode($filter_params));

    // Apply filters to query
    wpc_apply_filters_to_query($query, $filter_params);
}

/**
 * Apply filter parameters to WP_Query
 * 
 * @param WP_Query $query The WordPress query object
 * @param array $params Filter parameters
 * @return void
 */
function wpc_apply_filters_to_query($query, $params)
{
    // Initialize tax_query and meta_query arrays
    $tax_query = $query->get('tax_query', array());
    $meta_query = $query->get('meta_query', array());

    // Ensure proper structure
    if (!is_array($tax_query)) {
        $tax_query = array();
    }

    if (!is_array($meta_query)) {
        $meta_query = array();
    }

    // Add relation if we have multiple conditions
    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    if (count($meta_query) > 1) {
        $meta_query['relation'] = 'AND';
    }

    // Apply category filter
    if (isset($params['category']) && !empty($params['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $params['category'],
            'operator' => 'IN',
        );
    }

    // Apply tag filter
    if (isset($params['tag']) && !empty($params['tag'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $params['tag'],
            'operator' => 'IN',
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
                    'operator' => 'IN',
                );
            }
        }
    }

    // Apply price range filter
    if ((isset($params['min_price']) && $params['min_price'] > 0) ||
        (isset($params['max_price']) && $params['max_price'] > 0)
    ) {

        $min = isset($params['min_price']) ? floatval($params['min_price']) : 0;
        $max = isset($params['max_price']) ? floatval($params['max_price']) : PHP_INT_MAX;

        $meta_query[] = array(
            'key'     => '_price',
            'value'   => array($min, $max),
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
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

    // Apply search filter
    if (isset($params['search']) && !empty($params['search'])) {
        $query->set('s', $params['search']);
    }

    // Update the query with our modified parameters
    if (!empty($tax_query)) {
        $query->set('tax_query', $tax_query);
    }

    if (!empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }
}

/**
 * Template redirect hook for handling AJAX requests
 * This allows us to return partial content for AJAX filtering
 */
function wpc_template_redirect_filter()
{
    // Check if this is an AJAX request
    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

    // Only process on shop and archive pages
    if (!$is_ajax) {
        return;
    }

    // Output only the product content for AJAX requests
    add_filter('template_include', function ($template) {
        // Start output buffering
        ob_start();

        // Set up global WooCommerce loop variables
        global $woocommerce_loop;
        $woocommerce_loop['columns'] = wc_get_default_products_per_row();

        // Output products
        // woocommerce_product_loop_start();

        if (have_posts()) {
            while (have_posts()) {
                the_post();
                wc_get_template_part('content', 'product');
            }
        } else {
            echo '<p class="woocommerce-info">' . esc_html__('No products found matching your selection.', 'woocommerce') . '</p>';
        }

        // woocommerce_product_loop_end();


        // Get the buffered content
        $content = ob_get_clean();

        // Create the response
        $response = array(
            'success' => true,
            'data'    => array(
                'html'  => $content,
                'pagination' => woocommerce_pagination(),
                'found' => $GLOBALS['wp_query']->found_posts,
                'url'   => wpc_get_current_url_with_query(),
            ),
        );

        // Output as JSON and exit
        wp_send_json($response);
        exit;
    });
}
add_action('template_redirect', 'wpc_template_redirect_filter');

/**
 * Helper function to get current URL with query parameters
 * 
 * @return string Full current URL with query parameters
 */
function wpc_get_current_url_with_query()
{
    global $wp;
    return add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
}

/**
 * Add hidden fields for the filter form
 * Call this function inside your filter form
 */
function wpc_add_hidden_filter_fields()
{
    echo '<input type="hidden" id="wpc-filter-nonce" name="wpc-filter-nonce" value="' . wp_create_nonce('wpc_filter_nonce') . '">';
}
