<?php

/**
 * Plugin Name: Dynamic AJAX Product Filters for WooCommerce
 * Plugin URI:  https://plugincy.com/
 * Description: A WooCommerce plugin to filter products by attributes, categories, and tags using AJAX for seamless user experience.
 * Version:     1.2.9.2
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

define('DAPFFORWC_VERSION', '1.2.9.2');

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
        // 'select2_classic' => 'Select 2 Classic',
    ],
    'price' => [
        'price' => 'Price',
        'slider' => 'Slider',
        'slider2' => 'Slider2',
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
        require_once plugin_dir_path(__FILE__) . 'admin/license-page.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-inject.php';
        require_once plugin_dir_path(__FILE__) . 'admin/elementor-category.php';


        add_action('wp_enqueue_scripts', 'dapfforwc_enqueue_scripts');
        add_action('admin_enqueue_scripts', 'dapfforwc_admin_scripts');
        require_once plugin_dir_path(__FILE__) . 'includes/class-filter-functions.php';

        // add_action('wp_ajax_dapfforwc_filter_products', 'dapfforwc_filter_products');
        // add_action('wp_ajax_nopriv_dapfforwc_filter_products', 'dapfforwc_filter_products');

        register_setting('dapfforwc_options_group', 'dapfforwc_filters', 'sanitize_text_field');

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dapfforwc_add_settings_link');
        require_once plugin_dir_path(__FILE__) . 'includes/common-functions.php';

        // filter error detector
        add_action('admin_bar_menu', 'dapfforwc_add_debug_menu', 100);
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

    $script_handle = 'urlfilter-ajax';
    $script_path = 'assets/js/filter.min.js';

    wp_enqueue_script('jquery');
    wp_enqueue_script($script_handle, plugin_dir_url(__FILE__) . $script_path, ['jquery'], '1.2.9.2', true);
    wp_script_add_data($script_handle, 'async', true); // Load script asynchronously
    wp_localize_script($script_handle, 'dapfforwc_data', compact('dapfforwc_options', 'dapfforwc_seo_permalinks_options', 'dapfforwc_slug', 'dapfforwc_styleoptions', 'dapfforwc_advance_settings', 'dapfforwc_front_page_slug'));
    wp_localize_script($script_handle, 'dapfforwc_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'shopPageUrl' => esc_url(get_permalink(get_option('woocommerce_shop_page_id'))),
        'isProductArchive' =>  is_shop() || is_product_category() || is_product_tag() || is_product(),
        'currencySymbol' => get_woocommerce_currency_symbol(),
        'isHomePage' => is_front_page()
    ]);

    wp_enqueue_style('filter-style', plugin_dir_url(__FILE__) . 'assets/css/style.min.css', [], '1.2.9.2');
    wp_enqueue_style('select2-css', plugin_dir_url(__FILE__) . 'assets/css/select2.min.css', [], '1.2.9.2');
    wp_enqueue_script('select2-js', plugin_dir_url(__FILE__) . 'assets/js/select2.min.js', ['jquery'], '1.2.9.2', true);
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
            
             function initializeSelect2() {
                $(".select2").select2({
                    placeholder: "Select Options",
                    allowClear: true
                });
                $("select.select2_classic").select2({
                    placeholder: "Select Options",
                    allowClear: true
                });
            }

            // Initial initialization
            initializeSelect2();

            $(document).ajaxComplete(function () {
                // Check if new options are added before reinitializing
                if ($(".select2").find("option").length > 0) {
                    initializeSelect2();
                }
            });

            if ($(window).width() > 768) {
    function initializeCollapsible() {
        $(".title").each(function () {
            const $this = $(this);
            const $items = $this.parent().children().not(".title");

            // Hide items initially if the title has a specific class
            if ($this.hasClass("plugincy_collapsable_minimize_initial")) {
                $items.addClass("dapfforwc-hidden-important");
                $this.off("click").on("click", function () {
                        $this.find("svg").toggleClass("rotated");
                        // $items.slideToggle(300);
                        $items.toggleClass("dapfforwc-hidden-important", 300);
                });
            }
            
            if($this.hasClass("plugincy_collapsable_no_arrow")){
                $this.off("click").on("click", function () {
                    $this.find("svg").toggleClass("rotated");
                    // $items.slideToggle(300);
                    $items.toggleClass("dapfforwc-hidden-important", 300);
                });
            }
            
            if($this.hasClass("plugincy_collapsable_arrow")){
                $this.find(".collaps").off("click").on("click", function () {
                        $this.find("svg").toggleClass("rotated");
                        // $items.slideToggle(300);
                        $items.toggleClass("dapfforwc-hidden-important", 300);
                });
            }

            // Mobile: collapse/expand on .title click if data-mobile-style="style_1"
        if ($(window).width() <= 768) {
            var $filter = $("#product-filter");
            if ($filter.length && $filter.data("mobile-style") === "style_1") {
            $filter.find(".title").each(function () {
                var $title = $(this);
                var $items = $title.parent().children().not(".title");
                // Hide items initially
                $items.addClass("dapfforwc-hidden-important");
                $title.off("click.dapfforwcMobile").on("click.dapfforwcMobile", function () {
                $title.find("svg").toggleClass("rotated");
                $items.toggleClass("dapfforwc-hidden-important", 300);
                });
            });
            }
        }
            
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
    wp_enqueue_style('dapfforwc-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.min.css', [], '1.2.9.2');
    wp_enqueue_code_editor(array('type' => 'text/html'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('dapfforwc-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.min.js', [], '1.2.9.2', true);
    wp_enqueue_media();
    wp_enqueue_script('dapfforwc-media-uploader', plugin_dir_url(__FILE__) . 'assets/js/media-uploader.min.js', ['jquery'], '1.0.0', true);

    $inline_script = 'document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("attribute-dropdown");

    const savedAttribute = localStorage.getItem("dapfforwc_selected_attribute");
    if (savedAttribute) {
        try {
            const parsed = JSON.parse(savedAttribute);
            if (parsed && parsed.attribute && dropdown) {
                dropdown.value = parsed.attribute;

                selectedAttribute = parsed.attribute;

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
                    toggleDisplay(".setting-item.single-selection", "block");
                    toggleDisplay(".setting-item.show-product-count", "block");
                }
                else if (selectedAttribute === "rating") {
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "none");
                    toggleDisplay(".primary_options label.rating", "block");
                    toggleDisplay(".setting-item.single-selection", "none");
                    toggleDisplay(".setting-item.show-product-count", "none");
                } else if(selectedAttribute === "product-category"){
                    toggleDisplay(".hierarchical", "block");
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.show-product-count", "block");
                    toggleDisplay(".primary_options label.color", "none");
                    toggleDisplay(".primary_options label.image", "none");
                }else if(selectedAttribute === "tag"){
                    toggleDisplay(".hierarchical", "none");
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.show-product-count", "block");
                    toggleDisplay(".primary_options label.color", "none");
                    toggleDisplay(".primary_options label.image", "none");
                }
                else {
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".hierarchical", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.single-selection", "block");
                    toggleDisplay(".setting-item.show-product-count", "block");
                }
                
            }
        } catch (e) {}
    }

    if(dropdown){
        const firstAttribute = dropdown.value;
        const firstOptions = document.querySelector(`#options-${firstAttribute}`);
        if (firstOptions) {
            firstOptions.style.display = "block";
        }
    }

    function toggleDisplay(selector, display) {
        document.querySelectorAll(selector).forEach(el => {
            el.style.display = display;
        });
    }

    if(dropdown)dropdown.addEventListener("change", function () {
    const selectedAttribute = this.value;
    
    localStorage.setItem("dapfforwc_selected_attribute", JSON.stringify({ "attribute": selectedAttribute }));

    toggleDisplay(".style-options", "none");

    if (selectedAttribute) {
        const selectedOptions = document.getElementById(`options-${selectedAttribute}`);
        if (selectedOptions) {
            selectedOptions.style.display = "block";
        }
    }

    if (selectedAttribute === "price") {
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label.price", "block");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".min-max-price-set", "block");
        toggleDisplay(".setting-item.single-selection", "none");
        toggleDisplay(".setting-item.show-product-count", "none");
    }
    else if (selectedAttribute === "rating") {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "block");
        toggleDisplay(".setting-item.single-selection", "none");
        toggleDisplay(".setting-item.show-product-count", "none");
    } else if(selectedAttribute === "product-category"){
        toggleDisplay(".hierarchical", "block");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.show-product-count", "block");
        toggleDisplay(".primary_options label.color", "none");
        toggleDisplay(".primary_options label.image", "none");
    }else if(selectedAttribute === "tag"){
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.show-product-count", "block");
        toggleDisplay(".primary_options label.color", "none");
        toggleDisplay(".primary_options label.image", "none");
    }
    else {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.single-selection", "block");
        toggleDisplay(".setting-item.show-product-count", "block");
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
                label.className = `${key}` + (key === "dynamic-rating" || key === "input-price-range" || key === "color_circle" || key === "color_value" || key === "button_check" ? " pro-only" : "");
                label.innerHTML = `
                    <span class="active" style="display:none;"><i class="fa fa-check"></i></span>
                    <input ${key === "dynamic-rating" || key === "input-price-range" || key === "color_circle" || key === "color_value" || key === "button_check" ? "disabled" : ""} type="radio" class="optionselect" name="${key === "dynamic-rating" || key === "input-price-range" || key === "color_circle" || key === "color_value" || key === "button_check" ? "_pro" : "dapfforwc_style_options"}[${attributeName}][sub_option]" value="${key}">                    
                    <img src="' . plugin_dir_url(__FILE__) . 'assets/images/${key}.png" alt="${currentOptions[key]}">
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
            document.querySelector(`.setting-item.single-selection`).style.display = "block";

           }else if(selectedType==="dropdown") {
            document.querySelector(`.setting-item.single-selection`).style.display = "none";
            document.querySelectorAll(".advanced-options").forEach(advanceoptions =>{
                advanceoptions.style.display = "none";
            })
           } else {
            document.querySelector(`.setting-item.single-selection`).style.display = "block";
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
            const singleSelectiondiv = this.closest(".style-options").querySelector(".setting-item.single-selection"); 
            if (this.value === "select") {
                singleSelectionCheckbox.checked = true;
                singleSelectiondiv.style.display = "none"; // Show the checkbox
                
            } else {
                singleSelectionCheckbox.checked = false; // Uncheck if other options are selected
                singleSelectiondiv.style.display = "block"; // Hide the checkbox
            }
        });
    });
}

// Call the function to attach listeners
attachSubOptionListeners();

});';
    wp_add_inline_script('dapfforwc-admin-script', $inline_script);
}

// function dapfforwc_filter_products()
// {
//     if (class_exists('dapfforwc_Filter_Functions')) {
//         $filter = new dapfforwc_Filter_Functions();
//         $filter->process_filter();
//     } else {
//         wp_send_json_error('Filter class not found.');
//     }
// }


function dapfforwc_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=dapfforwc-admin">Settings</a>';
    $get_pro_link = '<a href="https://plugincy.com/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" style="color:#d54e21;font-weight:bold;">Get Pro</a>';
    array_unshift($links, $settings_link);
    $links[] = $get_pro_link;
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

    wp_enqueue_style('custom-box-control-styles', plugin_dir_url(__FILE__) . 'assets/css/block-editor.min.css', [], '1.2.9.2');
}
add_action('enqueue_block_editor_assets', 'dapfforwc_enqueue_dynamic_ajax_filter_block_assets');






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

add_action('wp_footer', 'dapfforwc_check_elements', 100); // Ensure this runs after the DOM is fully loaded

function dapfforwc_check_elements()
{
    global $dapfforwc_advance_settings;
    if (current_user_can('administrator')) {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                var debugMessage = document.getElementById('dapfforwc_debug_message');
                var issueCount = document.getElementById('dapfforwc_issue_count');
                if (!document.querySelector('#product-filter')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Filter is not added', 'dynamic-ajax-product-filters-for-woocommerce'); ?>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else if (!window.getProductSelector) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Products are not found. Add product or', 'dynamic-ajax-product-filters-for-woocommerce'); ?> <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/#product-selector-configuration" target="_blank" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                    if (!document.getElementById('dapfforwc-popup-notification')) {
                        var popup = document.createElement('div');
                        popup.id = 'dapfforwc-popup-notification';
                        popup.innerHTML = `
                            <div style="
                                    display: flex;
                                    align-items: center;
                                    position: fixed;
                                    top: 123px;
                                    right: 10px;
                                    background: #fff;
                                    color: #222;
                                    border: 1px solid #d54e21;
                                    padding: 18px 28px;
                                    border-radius: 8px;
                                    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.13);
                                    z-index: 99999;
                                    font-size: 16px;
                                    min-width: 340px;
                                    max-width: 100%;
                                    gap: 14px;
                                    transition: opacity 0.3s;
                            ">
                                <span style="
                                    color: #d54e21;
                                    font-size: 26px;
                                    margin-right: 8px;
                                    flex-shrink: 0;
                                ">&#9888;</span>
                                <span style="flex:1;">
                                    <strong style="display:block;font-size:17px;margin-bottom:2px;">Product Selector Not Found</strong>
                                    <span>
                                        The product selector does not match any element on this page.<br>
                                        <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/#product-selector-configuration" target="_blank" style="color:#0073aa;text-decoration:underline;font-weight:500;display:inline-block;margin-top:6px;">View documentation</a>
                                    </span>
                                </span>
                                <span style="
                                    margin-left: 12px;
                                    cursor: pointer;
                                    color: #d54e21;
                                    font-weight: bold;
                                    font-size: 22px;
                                    line-height: 1;
                                    transition: color 0.2s;
                                " onclick="this.closest('#dapfforwc-popup-notification').style.display='none'" title="Dismiss">&times;</span>
                            </div>
                        `;
                        document.body.appendChild(popup);
                    }
                } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwc_advance_settings["pagination_selector"]) && !empty($dapfforwc_advance_settings["pagination_selector"]) ? $dapfforwc_advance_settings["pagination_selector"] : ''); ?>')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Pagination is not found', 'dynamic-ajax-product-filters-for-woocommerce'); ?> <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/#pagination-selector-configuration" target="_blank" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else {
                    debugMessage.innerHTML = '<span style="color: green;">&#10003;</span> <?php echo esc_html__('Filter working fine', 'dynamic-ajax-product-filters-for-woocommerce'); ?>';

                }
                }, 2000);
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

    // Replace placeholders in SEO settings
    $replacements = [
        '{site_title}' => $site_title,
        '{page_title}' => $page_title,
        '{attribute_prefix}' => '', // No longer needed as we format differently
        '{value}' => $formatted_string // Now contains "attribute - value" format
    ];

    return $replacements;
}


function dapfforwc_block_categories($categories, $post)
{
    // Create the new category array
    $new_category = array(
        'slug' => 'plugincy',
        'title' => __('Plugincy', 'one-page-quick-checkout-for-woocommerce'),
        'icon'  => 'plugincy',
    );

    // Add the new category to the beginning of the categories array
    array_unshift($categories, $new_category);

    return $categories;
}
add_filter('block_categories_all', 'dapfforwc_block_categories', 0, 2);


function dapfforwc_editor_script()
{
    if (wp_script_is('plugincy-custom-editor', 'enqueued')) {
        return;
    }
    wp_enqueue_script(
        'plugincy-custom-editor',
        plugin_dir_url(__FILE__) . 'includes/blocks/editor.js',
        array('wp-blocks', 'wp-element', 'wp-edit-post', 'wp-dom-ready', 'wp-plugins'),
        '1.0.3',
        true
    );
}
add_action('enqueue_block_editor_assets', 'dapfforwc_editor_script');



if (isset($dapfforwc_advance_settings["remove_outofStock"]) && $dapfforwc_advance_settings["remove_outofStock"] === 'on') {
    // Filter products to exclude out of stock items
    add_filter('woocommerce_product_query_meta_query', function ($meta_query) {
        $meta_query[] = array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        );
        return $meta_query;
    });

    // Or use the built-in WooCommerce option
    add_filter('pre_option_woocommerce_hide_out_of_stock_items', function () {
        return 'yes';
    });
}










require_once plugin_dir_path(__FILE__) . 'includes/analytics.php';

class dapfforwc_cart_analytics_main
{
    private $analytics;

    public function __construct()
    {
        global $dapfforwc_advance_settings;
        // Initialize analytics with the correct plugin file path
        $this->analytics = new dapfforwc_cart_anaylytics(
            '01',
            'https://plugincy.com/wp-json/product-analytics/v1',
            "1.2.9.2",
            'One Page Quick Checkout for WooCommerce',
            __FILE__ // Pass the main plugin file
        );

        add_action('admin_footer',  array($this->analytics, "add_deactivation_feedback_form"));

        // Plugin hooks
        add_action('init', array($this, 'init'));
        if (!isset($dapfforwc_advance_settings["allow_data_share"]) || (isset($dapfforwc_advance_settings["allow_data_share"])  && $dapfforwc_advance_settings["allow_data_share"] === 'on')) {
            add_action('admin_init', array($this, 'admin_init'));
        }

        // Handle deactivation feedback AJAX
        add_action('wp_ajax_send_deactivation_feedback', array($this, 'handle_deactivation_feedback'));
    }

    public function init()
    {
        // Any initialization code
    }

    public function admin_init()
    {
        // Send analytics data on first activation or weekly
        $this->maybe_send_analytics();
    }

    private function maybe_send_analytics()
    {
        $last_sent = get_option('onepaquc_analytics_last_sent', 0);
        $week_ago = strtotime('-1 week');

        if ($last_sent < $week_ago) {
            $this->analytics->send_tracking_data();
            update_option('onepaquc_analytics_last_sent', time());
        }
    }

    public function handle_deactivation_feedback()
    {
        check_ajax_referer('deactivation_feedback', 'nonce');

        $reason = sanitize_text_field($_POST['reason'] ?? '');
        $this->analytics->send_deactivation_data($reason);

        wp_die();
    }
}

new dapfforwc_cart_analytics_main();




function dapfforwc_sidebar_to_top_inline_scripts()
{
    ?>
    <style>
        /* Mobile-only Sidebar to Top CSS */
        @media (max-width: 767px) {
            .sidebar-moved-to-top {
                order: -1 !important;
                -webkit-box-ordinal-group: 0 !important;
                -ms-flex-order: -1 !important;
                width: 100% !important;
                margin-bottom: 20px !important;
                display: block !important;
            }

            /* Make parent container flex if it isn't already - mobile only */
            .sidebar-parent-flex {
                display: flex !important;
                flex-direction: column !important;
            }

            .sidebar-moved-to-top .widget {
                margin-bottom: 15px !important;
            }
        }

        /* Desktop - reset to normal positioning */
        @media (min-width: 768px) {
            .sidebar-moved-to-top {
                order: initial !important;
                -webkit-box-ordinal-group: initial !important;
                -ms-flex-order: initial !important;
                width: auto !important;
                margin-bottom: initial !important;
            }

            .sidebar-parent-flex {
                display: initial !important;
                flex-direction: initial !important;
            }
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            // Common sidebar selectors used across WordPress themes
            var sidebarSelectors = [
                '#sidebar',
                '.sidebar',
                '#secondary',
                '.secondary',
                '.widget-area',
                '#primary-sidebar',
                '.primary-sidebar',
                '#main-sidebar',
                '.main-sidebar',
                '.sidebar-primary',
                '.sidebar-secondary',
                '#complementary',
                '.complementary',
                '.aside',
                '#aside',
                '.sidebar-1',
                '.sidebar-2',
                '#sidebar-1',
                '#sidebar-2'
            ];

            // Function to move sidebar to top (mobile only)
            function moveSidebarToTop() {
                // Check if we're on mobile (767px or less)
                if ($(window).width() <= 767) {
                    var sidebarFound = false;

                    // Try each selector until we find a sidebar
                    $.each(sidebarSelectors, function(index, selector) {
                        var $sidebar = $(selector);

                        if ($sidebar.length > 0 && !sidebarFound && !$sidebar.hasClass('sidebar-moved-to-top')) {
                            sidebarFound = true;

                            // Find the main content area (common selectors)
                            var $mainContent = $sidebar.siblings().filter(function() {
                                return $(this).find('article, .post, .entry, .content').length > 0;
                            }).first();

                            // If no main content found, try common content selectors
                            if ($mainContent.length === 0) {
                                var contentSelectors = [
                                    '#main',
                                    '.main',
                                    '#content',
                                    '.content',
                                    '#primary',
                                    '.primary',
                                    '.site-content',
                                    '.entry-content',
                                    '.post-content',
                                    'main',
                                    'article'
                                ];

                                $.each(contentSelectors, function(i, contentSelector) {
                                    var $content = $(contentSelector);
                                    if ($content.length > 0 && $content.parent().is($sidebar.parent())) {
                                        $mainContent = $content;
                                        return false; // Break the loop
                                    }
                                });
                            }

                            // Move sidebar to top
                            if ($mainContent.length > 0) {
                                var $parent = $mainContent.parent();

                                // Make parent flex container
                                $parent.addClass('sidebar-parent-flex');

                                // Move sidebar before main content
                                $sidebar.addClass('sidebar-moved-to-top');
                                $mainContent.before($sidebar);
                            } else {
                                // Fallback: move to top of body or main container
                                var $container = $sidebar.closest('.container, .wrap, .site, #page, #wrapper, .main-container');
                                if ($container.length > 0) {
                                    $container.addClass('sidebar-parent-flex');
                                    $sidebar.addClass('sidebar-moved-to-top');
                                    $container.prepend($sidebar);
                                }
                            }

                            return false; // Break the loop
                        }
                    });

                    // If no sidebar found with common selectors, try a more generic approach
                    if (!sidebarFound) {
                        $('.widget').closest('div, aside, section').each(function() {
                            var $possibleSidebar = $(this);
                            if ($possibleSidebar.find('.widget').length >= 2 && !$possibleSidebar.hasClass('sidebar-moved-to-top')) {
                                var $parent = $possibleSidebar.parent();
                                $parent.addClass('sidebar-parent-flex');
                                $possibleSidebar.addClass('sidebar-moved-to-top');
                                $parent.prepend($possibleSidebar);
                                return false; // Break after first match
                            }
                        });
                    }
                } else {
                    // Desktop - restore original position if moved
                    $('.sidebar-moved-to-top').each(function() {
                        var $sidebar = $(this);
                        var $parent = $sidebar.parent();

                        // Remove mobile classes
                        $sidebar.removeClass('sidebar-moved-to-top');
                        $parent.removeClass('sidebar-parent-flex');

                        // Move back to original position (after main content)
                        var $mainContent = $parent.find('#main, .main, #content, .content, #primary, .primary, main, article').first();
                        if ($mainContent.length > 0) {
                            $mainContent.after($sidebar);
                        }
                    });
                }
            }

            // Execute on page load
            moveSidebarToTop();

            // Re-execute after AJAX calls (for dynamic content)
            $(document).ajaxComplete(function() {
                setTimeout(moveSidebarToTop, 100);
            });

            // Re-execute after window resize (for responsive themes)
            $(window).on('resize', function() {
                setTimeout(moveSidebarToTop, 100);
            });
        });
    </script>
<?php
}
if (!isset($dapfforwc_advance_settings["sidebar_on_top"]) || (isset($dapfforwc_advance_settings["sidebar_on_top"])  && $dapfforwc_advance_settings["sidebar_on_top"] === 'on')) {
    add_action('wp_head', 'dapfforwc_sidebar_to_top_inline_scripts');
}







/**
 * Plugincy Widget Class
 */
class dapfforwc_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'plugincy_widget',
            __('Plugincy Widget', 'plugincy'),
            array(
                'description' => __('Display content after WooCommerce archive titles', 'plugincy'),
                'customize_selective_refresh' => true,
            )
        );
    }
    
    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        // Only display on WooCommerce archive pages
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
            return;
        }
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $content = !empty($instance['content']) ? $instance['content'] : '[plugincy_filters_selected]';
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        // Process shortcodes and display content
        echo '<div class="plugincy-widget-content">';
        echo do_shortcode(wpautop($content));
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $content = !empty($instance['content']) ? $instance['content'] : '[plugincy_filters_selected]';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'plugincy'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content:', 'plugincy'); ?></label>
            <textarea class="widefat" rows="8" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>"><?php echo esc_textarea($content); ?></textarea>
            <small><?php _e('You can use HTML and shortcodes. Default: [plugincy_filters_selected]', 'plugincy'); ?></small>
        </p>
        <?php
    }
    
    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['content'] = (!empty($new_instance['content'])) ? wp_kses_post($new_instance['content']) : '[plugincy_filters_selected]';
        
        return $instance;
    }
}

/**
 * Register the widget
 */
function dapfforwc_register_widget() {
    register_widget('dapfforwc_Widget');
}
add_action('widgets_init', 'dapfforwc_register_widget');

/**
 * Create widget area for WooCommerce archives
 */
function dapfforwc_register_sidebar() {
    register_sidebar(array(
        'name' => __('WooCommerce Archive Content', 'plugincy'),
        'id' => 'wc-archive-content',
        'description' => __('Content displayed after WooCommerce archive titles and before products', 'plugincy'),
        'before_widget' => '<div id="%1$s" class="widget %2$s plugincy-archive-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'dapfforwc_register_sidebar');

/**
 * Display widget content after archive title and before products
 */
function dapfforwc_display_archive_content() {
    // Only on WooCommerce archive pages
    if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
        return;
    }
    
    if (is_active_sidebar('wc-archive-content')) {
        echo '<div class="plugincy-archive-content-wrapper">';
        dynamic_sidebar('wc-archive-content');
        echo '</div>';
    }
}

/**
 * Hook into WooCommerce template to display content
 */
function dapfforwc_hook_archive_content() {
    // Remove default WooCommerce hooks temporarily to insert our content
    remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);
    
    // Add our custom content
    add_action('woocommerce_archive_description', 'dapfforwc_display_archive_content', 15);
    
    // Re-add the default WooCommerce hooks after our content
    add_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 20);
    add_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 20);
}
add_action('init', 'dapfforwc_hook_archive_content');

/**
 * Plugin activation hook
 */
function dapfforwc_activate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'dapfforwc_activate');

/**
 * Plugin deactivation hook
 */
function dapfforwc_deactivate() {
    // Clean up if needed
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'dapfforwc_deactivate');
