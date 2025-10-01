<?php

/**
 * Plugin Name: Dynamic AJAX Product Filters for WooCommerce
 * Plugin URI:  https://plugincy.com/
 * Description: A WooCommerce plugin to filter products by attributes, categories, and tags using AJAX for seamless user experience.
 * Version:     1.4.0.30
 * Author:      Plugincy
 * Author URI:  https://plugincy.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dynamic-ajax-product-filters-for-woocommerce
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define DAY_IN_SECONDS if not already defined
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

define('DAPFFORWC_VERSION', '1.4.0.30');

// Global Variables
global $dapfforwc_allowed_tags, $template_options, $dapfforwc_options, $dapfforwc_seo_permalinks_options, $dapfforwc_advance_settings, $dapfforwc_styleoptions, $dapfforwc_use_url_filter, $dapfforwc_auto_detect_pages_filters, $dapfforwc_slug, $dapfforwc_sub_options, $dapfforwc_front_page_slug;

$template_options = get_option('dapfforwc_template_options') ?: [
    'active_template' => 'clean',
    'background_color' => '#ffffffb3',
    'primary_color' => '#432fb8',
    'secondary_color' => '#ff4d4d',
    'border_color' => '#eeeeee',
    'text_color' => '#000000',
];

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
    'pagination_selector' => '.woocommerce-pagination ul.page-numbers',
    'filters_word_in_permalinks' => 'filters',
];
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

$dapfforwc_allowed_tags = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'class' => array(),
        'target' => array(), // Allow target attribute for links
        'style' => array(),
        'id' => array(),
    ),
    'strong' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'em' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'li' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'div' => array(
        'class' => array(),
        'id' => array(), // Allow id for divs
        'style' => array(),
    ),
    'img' => array(
        'src' => array(),
        'alt' => array(),
        'class' => array(),
        'width' => array(), // Allow width attribute
        'height' => array(), // Allow height attribute
        'style' => array(),
        'id' => array(),
        'data-src' => array(),
    ),
    'h1' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow h1
    'h2' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'h3' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow h3
    'h4' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow h4
    'h5' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow h5
    'h6' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow h6
    'span' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'p' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'br' => array(
        'style' => array(),
        'class' => array(),
    ), // Allow line breaks
    'blockquote' => array(
        'cite' => array(), // Allow cite attribute for blockquotes
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'table' => array(
        'class' => array(),
        'style' => array(), // Allow inline styles
        'id' => array(),
    ),
    'tr' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'td' => array(
        'class' => array(),
        'colspan' => array(), // Allow colspan attribute
        'rowspan' => array(), // Allow rowspan attribute
        'style' => array(),
        'id' => array(),
    ),
    'th' => array(
        'class' => array(),
        'colspan' => array(),
        'rowspan' => array(),
        'style' => array(),
        'id' => array(),
    ),
    'ul' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow unordered lists
    'ol' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
    ), // Allow ordered lists
    'script' => array(
        'type' => array(),
        'src' => array(),
        'async' => array(),
        'defer' => array(),
        'charset' => array(),
    ), // Be cautious with scripts

    // Style and Meta Tags
    'style' => array(
        'type' => array(),
        'media' => array(),
        'scoped' => array(),
    ),
    'link' => array(
        'rel' => array(),
        'href' => array(),
        'type' => array(),
        'media' => array(),
        'sizes' => array(),
        'hreflang' => array(),
        'crossorigin' => array(),
    ),
    'meta' => array(
        'name' => array(),
        'content' => array(),
        'http-equiv' => array(),
        'charset' => array(),
        'property' => array(), // For Open Graph
    ),
    'title' => array(),
    'base' => array(
        'href' => array(),
        'target' => array(),
    ),

    // Document Structure
    'html' => array(
        'lang' => array(),
        'dir' => array(),
        'class' => array(),
        'style' => array(),
    ),
    'head' => array(),
    'body' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'onload' => array(),
    ),
    'header' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'role' => array(),
    ),
    'footer' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'role' => array(),
    ),
    'nav' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
        'role' => array(),
    ),
    'main' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
        'role' => array(),
    ),
    'section' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'role' => array(),
    ),
    'article' => array(
        'class' => array(),
        'style' => array(),
        'id' => array(),
        'role' => array(),
    ),
    'aside' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'role' => array(),
    ),

    // Form Elements
    'form' => array(
        'action' => array(),
        'method' => array(),
        'style' => array(),
        'enctype' => array(),
        'target' => array(),
        'name' => array(),
        'id' => array(),
        'class' => array(),
        'autocomplete' => array(),
        'novalidate' => array(),
        'data-mobile-style' => array(),
        'data-product_show_settings' => array(),
        'data-product_selector' => array(),
        'data-pagination_selector' => array(),
        'data-layout' => array(),
    ),
    'input' => array(
        'type' => array(),
        'name' => array(),
        'value' => array(),
        'style' => array(),
        'placeholder' => array(),
        'id' => array(),
        'class' => array(),
        'required' => array(),
        'disabled' => array(),
        'readonly' => array(),
        'checked' => array(),
        'selected' => array(),
        'multiple' => array(),
        'min' => array(),
        'max' => array(),
        'step' => array(),
        'pattern' => array(),
        'maxlength' => array(),
        'minlength' => array(),
        'size' => array(),
        'autocomplete' => array(),
        'autofocus' => array(),
        'form' => array(),
        'formaction' => array(),
        'formmethod' => array(),
        'formtarget' => array(),
        'formnovalidate' => array(),
        'accept' => array(),
        'alt' => array(),
        'src' => array(),
        'width' => array(),
        'height' => array(),
    ),
    'textarea' => array(
        'name' => array(),
        'id' => array(),
        'class' => array(),
        'placeholder' => array(),
        'rows' => array(),
        'style' => array(),
        'cols' => array(),
        'required' => array(),
        'disabled' => array(),
        'readonly' => array(),
        'maxlength' => array(),
        'minlength' => array(),
        'wrap' => array(),
        'autocomplete' => array(),
        'autofocus' => array(),
        'form' => array(),
    ),
    'select' => array(
        'name' => array(),
        'id' => array(),
        'class' => array(),
        'multiple' => array(),
        'size' => array(),
        'required' => array(),
        'style' => array(),
        'disabled' => array(),
        'autofocus' => array(),
        'form' => array(),
    ),
    'option' => array(
        'value' => array(),
        'selected' => array(),
        'style' => array(),
        'disabled' => array(),
        'label' => array(),
    ),
    'optgroup' => array(
        'label' => array(),
        'style' => array(),
        'disabled' => array(),
    ),
    'button' => array(
        'type' => array(),
        'name' => array(),
        'value' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
        'disabled' => array(),
        'form' => array(),
        'formaction' => array(),
        'formmethod' => array(),
        'formtarget' => array(),
        'formnovalidate' => array(),
        'autofocus' => array(),
    ),
    'label' => array(
        'for' => array(),
        'form' => array(),
        'id' => array(),
        'class' => array(),
        'style' => array(),
    ),
    'fieldset' => array(
        'disabled' => array(),
        'form' => array(),
        'style' => array(),
        'name' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'legend' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'datalist' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'output' => array(
        'for' => array(),
        'form' => array(),
        'name' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'plugrogress' => array(
        'value' => array(),
        'max' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'meter' => array(
        'value' => array(),
        'min' => array(),
        'max' => array(),
        'low' => array(),
        'style' => array(),
        'high' => array(),
        'optimum' => array(),
        'id' => array(),
        'class' => array(),
    ),

    // Media Elements
    'audio' => array(
        'src' => array(),
        'controls' => array(),
        'autoplay' => array(),
        'style' => array(),
        'loop' => array(),
        'muted' => array(),
        'preload' => array(),
        'crossorigin' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'video' => array(
        'src' => array(),
        'controls' => array(),
        'autoplay' => array(),
        'loop' => array(),
        'muted' => array(),
        'preload' => array(),
        'style' => array(),
        'poster' => array(),
        'width' => array(),
        'height' => array(),
        'crossorigin' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'source' => array(
        'src' => array(),
        'style' => array(),
        'type' => array(),
        'media' => array(),
        'sizes' => array(),
        'srcset' => array(),
    ),
    'track' => array(
        'kind' => array(),
        'src' => array(),
        'style' => array(),
        'srclang' => array(),
        'label' => array(),
        'default' => array(),
    ),
    'embed' => array(
        'src' => array(),
        'type' => array(),
        'width' => array(),
        'height' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'object' => array(
        'data' => array(),
        'type' => array(),
        'style' => array(),
        'name' => array(),
        'width' => array(),
        'height' => array(),
        'form' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'param' => array(
        'name' => array(),
        'value' => array(),
        'style' => array(),
    ),
    'iframe' => array(
        'src' => array(),
        'srcdoc' => array(),
        'name' => array(),
        'width' => array(),
        'style' => array(),
        'height' => array(),
        'sandbox' => array(),
        'allow' => array(),
        'allowfullscreen' => array(),
        'loading' => array(),
        'id' => array(),
        'class' => array(),
    ),

    // Interactive Elements
    'details' => array(
        'open' => array(),
        'id' => array(),
        'class' => array(),
        'style' => array(),
    ),
    'summary' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'dialog' => array(
        'open' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),

    // Text Content Elements
    'pre' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'code' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'kbd' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'samp' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'var' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'small' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'sub' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'sup' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'mark' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'del' => array(
        'datetime' => array(),
        'style' => array(),
        'cite' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'ins' => array(
        'datetime' => array(),
        'style' => array(),
        'cite' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'q' => array(
        'cite' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'cite' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'abbr' => array(
        'title' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'dfn' => array(
        'title' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'time' => array(
        'datetime' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'data' => array(
        'value' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'address' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Table Elements (Enhanced)
    'caption' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'thead' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'tbody' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'tfoot' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'colgroup' => array(
        'span' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),
    'col' => array(
        'span' => array(),
        'style' => array(),
        'id' => array(),
        'class' => array(),
    ),

    // Definition Lists
    'dl' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'dt' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'dd' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Ruby Annotations
    'ruby' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'rt' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'rp' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Bidirectional Text
    'bdi' => array(
        'dir' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'bdo' => array(
        'dir' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Web Components
    'template' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'slot' => array(
        'name' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Math and Science
    'math' => array(
        'display' => array(),
        'xmlns' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Canvas and Graphics
    'canvas' => array(
        'width' => array(),
        'height' => array(),
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),

    // Obsolete but sometimes needed
    'center' => array(
        'id' => array(),
        'style' => array(),
        'class' => array(),
    ),
    'font' => array(
        'size' => array(),
        'style' => array(),
        'color' => array(),
        'face' => array(),
        'id' => array(),
        'class' => array(),
    ),

    // SVG Tags
    'svg' => array(
        'xmlns' => array(),
        'viewbox' => array(), // lowercase
        'viewBox' => array(), // camelCase (standard)
        'width' => array(),
        'height' => array(),
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'preserveAspectRatio' => array(),
        'version' => array(),
        'x' => array(),
        'y' => array(),
        'fill' => array(),
    ),
    'g' => array(
        'class' => array(),
        'id' => array(),
        'transform' => array(),
        'style' => array(),
        'fill' => array(),
        'stroke' => array(),
        'opacity' => array(),
    ),
    'path' => array(
        'd' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'stroke-dasharray' => array(),
        'stroke-linecap' => array(),
        'stroke-linejoin' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'circle' => array(
        'cx' => array(),
        'cy' => array(),
        'r' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'ellipse' => array(
        'cx' => array(),
        'cy' => array(),
        'rx' => array(),
        'ry' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'rect' => array(
        'x' => array(),
        'y' => array(),
        'width' => array(),
        'height' => array(),
        'rx' => array(),
        'ry' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'line' => array(
        'x1' => array(),
        'y1' => array(),
        'x2' => array(),
        'y2' => array(),
        'class' => array(),
        'id' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'stroke-dasharray' => array(),
        'stroke-linecap' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'polyline' => array(
        'points' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'stroke-dasharray' => array(),
        'stroke-linecap' => array(),
        'stroke-linejoin' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'polygon' => array(
        'points' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
        'stroke-dasharray' => array(),
        'stroke-linecap' => array(),
        'stroke-linejoin' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'text' => array(
        'x' => array(),
        'y' => array(),
        'dx' => array(),
        'dy' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'font-family' => array(),
        'font-size' => array(),
        'font-weight' => array(),
        'text-anchor' => array(),
        'dominant-baseline' => array(),
        'opacity' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'tspan' => array(
        'x' => array(),
        'y' => array(),
        'dx' => array(),
        'dy' => array(),
        'class' => array(),
        'id' => array(),
        'fill' => array(),
        'stroke' => array(),
        'font-family' => array(),
        'font-size' => array(),
        'font-weight' => array(),
        'text-anchor' => array(),
        'dominant-baseline' => array(),
        'opacity' => array(),
        'style' => array(),
    ),
    'use' => array(
        'href' => array(),
        'xlink:href' => array(),
        'x' => array(),
        'y' => array(),
        'width' => array(),
        'height' => array(),
        'class' => array(),
        'id' => array(),
        'transform' => array(),
        'style' => array(),
    ),
    'defs' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
    ),
    'symbol' => array(
        'id' => array(),
        'viewBox' => array(),
        'class' => array(),
        'style' => array(),
        'preserveAspectRatio' => array(),
    ),
    'marker' => array(
        'id' => array(),
        'markerWidth' => array(),
        'markerHeight' => array(),
        'refX' => array(),
        'refY' => array(),
        'style' => array(),
        'orient' => array(),
        'markerUnits' => array(),
        'class' => array(),
    ),
    'linearGradient' => array(
        'id' => array(),
        'x1' => array(),
        'y1' => array(),
        'style' => array(),
        'x2' => array(),
        'y2' => array(),
        'gradientUnits' => array(),
        'gradientTransform' => array(),
        'class' => array(),
    ),
    'lineargradient' => array(
        'id' => array(),
        'x1' => array(),
        'y1' => array(),
        'style' => array(),
        'x2' => array(),
        'y2' => array(),
        'gradientUnits' => array(),
        'gradientTransform' => array(),
        'class' => array(),
    ),
    'radialGradient' => array(
        'id' => array(),
        'cx' => array(),
        'cy' => array(),
        'style' => array(),
        'r' => array(),
        'fx' => array(),
        'fy' => array(),
        'gradientUnits' => array(),
        'gradientTransform' => array(),
        'class' => array(),
    ),
    'radialgradient' => array(
        'id' => array(),
        'cx' => array(),
        'cy' => array(),
        'r' => array(),
        'style' => array(),
        'fx' => array(),
        'fy' => array(),
        'gradientUnits' => array(),
        'gradientTransform' => array(),
        'class' => array(),
    ),
    'stop' => array(
        'offset' => array(),
        'stop-color' => array(),
        'stop-opacity' => array(),
        'class' => array(),
        'style' => array(),
    ),
    'clipPath' => array(
        'id' => array(),
        'class' => array(),
        'style' => array(),
        'clipPathUnits' => array(),
    ),
    'mask' => array(
        'id' => array(),
        'class' => array(),
        'style' => array(),
        'maskUnits' => array(),
        'maskContentUnits' => array(),
        'x' => array(),
        'y' => array(),
        'width' => array(),
        'height' => array(),
    ),
    'pattern' => array(
        'id' => array(),
        'x' => array(),
        'y' => array(),
        'width' => array(),
        'style' => array(),
        'height' => array(),
        'patternUnits' => array(),
        'patternContentUnits' => array(),
        'patternTransform' => array(),
        'viewBox' => array(),
        'class' => array(),
    ),
    'filter' => array(
        'id' => array(),
        'x' => array(),
        'y' => array(),
        'style' => array(),
        'width' => array(),
        'height' => array(),
        'filterUnits' => array(),
        'primitiveUnits' => array(),
        'class' => array(),
    ),
    'feGaussianBlur' => array(
        'in' => array(),
        'style' => array(),
        'stdDeviation' => array(),
        'result' => array(),
    ),
    'feOffset' => array(
        'in' => array(),
        'dx' => array(),
        'style' => array(),
        'dy' => array(),
        'result' => array(),
    ),
    'feDropShadow' => array(
        'dx' => array(),
        'dy' => array(),
        'style' => array(),
        'stdDeviation' => array(),
        'flood-color' => array(),
        'flood-opacity' => array(),
    ),
    'image' => array(
        'x' => array(),
        'y' => array(),
        'width' => array(),
        'style' => array(),
        'height' => array(),
        'href' => array(),
        'xlink:href' => array(),
        'preserveAspectRatio' => array(),
        'class' => array(),
        'id' => array(),
        'opacity' => array(),
        'transform' => array(),
    ),
);


// Allow extensive CSS properties for WordPress
add_filter('safe_style_css', function ($styles) {
    return array_merge($styles, array(
        // Layout & Positioning
        'display',
        'visibility',
        'opacity',
        'position',
        'top',
        'right',
        'bottom',
        'left',
        'z-index',
        'float',
        'clear',
        'clip',
        'clip-path',

        // Box Model
        'width',
        'height',
        'max-width',
        'max-height',
        'min-width',
        'min-height',
        'box-sizing',
        'aspect-ratio',

        // Margins & Padding
        'margin',
        'margin-top',
        'margin-right',
        'margin-bottom',
        'margin-left',
        'margin-block',
        'margin-inline',
        'margin-block-start',
        'margin-block-end',
        'margin-inline-start',
        'margin-inline-end',
        'padding',
        'padding-top',
        'padding-right',
        'padding-bottom',
        'padding-left',
        'padding-block',
        'padding-inline',
        'padding-block-start',
        'padding-block-end',
        'padding-inline-start',
        'padding-inline-end',

        // Borders
        'border',
        'border-top',
        'border-right',
        'border-bottom',
        'border-left',
        'border-width',
        'border-top-width',
        'border-right-width',
        'border-bottom-width',
        'border-left-width',
        'border-style',
        'border-top-style',
        'border-right-style',
        'border-bottom-style',
        'border-left-style',
        'border-color',
        'border-top-color',
        'border-right-color',
        'border-bottom-color',
        'border-left-color',
        'border-radius',
        'border-top-left-radius',
        'border-top-right-radius',
        'border-bottom-left-radius',
        'border-bottom-right-radius',
        'border-image',
        'border-image-source',
        'border-image-slice',
        'border-image-width',
        'border-image-outset',
        'border-image-repeat',
        'border-collapse',
        'border-spacing',

        // Background
        'background',
        'background-color',
        'background-image',
        'background-position',
        'background-position-x',
        'background-position-y',
        'background-repeat',
        'background-size',
        'background-attachment',
        'background-origin',
        'background-clip',
        'background-blend-mode',

        // Typography
        'color',
        'font',
        'font-family',
        'font-size',
        'font-weight',
        'font-style',
        'font-variant',
        'font-stretch',
        'font-display',
        'font-feature-settings',
        'font-variation-settings',
        'line-height',
        'letter-spacing',
        'word-spacing',
        'text-align',
        'text-align-last',
        'text-decoration',
        'text-decoration-line',
        'text-decoration-color',
        'text-decoration-style',
        'text-decoration-thickness',
        'text-transform',
        'text-indent',
        'text-shadow',
        'text-overflow',
        'text-rendering',
        'white-space',
        'word-wrap',
        'word-break',
        'overflow-wrap',
        'hyphens',
        'writing-mode',
        'text-orientation',
        'direction',
        'unicode-bidi',

        // List Styles
        'list-style',
        'list-style-type',
        'list-style-position',
        'list-style-image',

        // Table Styles
        'table-layout',
        'caption-side',
        'empty-cells',

        // Positioning & Alignment
        'vertical-align',
        'object-fit',
        'object-position',

        // Overflow & Scrolling
        'overflow',
        'overflow-x',
        'overflow-y',
        'overflow-anchor',
        'overscroll-behavior',
        'overscroll-behavior-x',
        'overscroll-behavior-y',
        'scroll-behavior',
        'scroll-margin',
        'scroll-padding',
        'scroll-snap-type',
        'scroll-snap-align',

        // Flexbox
        'flex',
        'flex-direction',
        'flex-wrap',
        'flex-flow',
        'justify-content',
        'align-items',
        'align-content',
        'align-self',
        'order',
        'flex-grow',
        'flex-shrink',
        'flex-basis',

        // Grid
        'grid',
        'grid-template',
        'grid-template-columns',
        'grid-template-rows',
        'grid-template-areas',
        'grid-auto-columns',
        'grid-auto-rows',
        'grid-auto-flow',
        'grid-column',
        'grid-column-start',
        'grid-column-end',
        'grid-row',
        'grid-row-start',
        'grid-row-end',
        'grid-area',
        'gap',
        'row-gap',
        'column-gap',
        'grid-gap',
        'grid-row-gap',
        'grid-column-gap',
        'justify-items',
        'justify-self',
        'place-items',
        'place-self',
        'place-content',

        // Transforms & Animations
        'transform',
        'transform-origin',
        'transform-style',
        'transform-box',
        'perspective',
        'perspective-origin',
        'backface-visibility',
        'transition',
        'transition-property',
        'transition-duration',
        'transition-timing-function',
        'transition-delay',
        'animation',
        'animation-name',
        'animation-duration',
        'animation-timing-function',
        'animation-delay',
        'animation-iteration-count',
        'animation-direction',
        'animation-fill-mode',
        'animation-play-state',

        // Visual Effects
        'box-shadow',
        'filter',
        'backdrop-filter',
        'mix-blend-mode',
        'isolation',
        'outline',
        'outline-color',
        'outline-style',
        'outline-width',
        'outline-offset',
        'resize',
        'cursor',
        'pointer-events',
        'user-select',
        'touch-action',

        // Print Styles
        'page-break-before',
        'page-break-after',
        'page-break-inside',
        'break-before',
        'break-after',
        'break-inside',

        // Logical Properties (modern CSS)
        'block-size',
        'inline-size',
        'min-block-size',
        'min-inline-size',
        'max-block-size',
        'max-inline-size',
        'inset',
        'inset-block',
        'inset-inline',
        'inset-block-start',
        'inset-block-end',
        'inset-inline-start',
        'inset-inline-end',

        // Container Queries
        'container-type',
        'container-name',
        'container',

        // Content & Generated Content
        'content',
        'quotes',
        'counter-reset',
        'counter-increment',

        // Miscellaneous
        'all',
        'contain',
        'will-change',
        'appearance',
        'caret-color',
        'tab-size',
        'column-count',
        'column-width',
        'column-gap',
        'column-rule',
        'column-rule-color',
        'column-rule-style',
        'column-rule-width',
        'column-span',
        'column-fill',
        'columns',

        // svg style
        'stop-color',
        'stop-opacity',
        'fill'

    ));
});


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
    'plugincy_color' => [
        'plugincy_color' => 'Color',
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
    "plugincy_search" => [
        "plugincy_search" => 'Search with Button',
        "icon_search" => 'Search with Icon'
    ]
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
    wp_enqueue_script($script_handle, plugin_dir_url(__FILE__) . $script_path, ['jquery'], '1.4.0.30', true);
    wp_script_add_data($script_handle, 'async', true); // Load script asynchronously
    wp_localize_script($script_handle, 'dapfforwc_data', compact('dapfforwc_options', 'dapfforwc_seo_permalinks_options', 'dapfforwc_slug', 'dapfforwc_styleoptions', 'dapfforwc_advance_settings', 'dapfforwc_front_page_slug'));
    wp_localize_script($script_handle, 'dapfforwc_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'shopPageUrl' => esc_url(get_permalink(get_option('woocommerce_shop_page_id'))),
        'isProductArchive' =>  is_shop() || is_product_category() || is_product_tag() || is_product(),
        'currencySymbol' => get_woocommerce_currency_symbol(),
        'isHomePage' => is_front_page()
    ]);

    wp_enqueue_style('filter-style', plugin_dir_url(__FILE__) . 'assets/css/style.min.css', [], '1.4.0.30');
    wp_enqueue_style('select2-css', plugin_dir_url(__FILE__) . 'assets/css/select2.min.css', [], '1.4.0.30');
    wp_enqueue_script('select2-js', plugin_dir_url(__FILE__) . 'assets/js/select2.min.js', ['jquery'], '1.4.0.30', true);
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
            $css .= "    overflow-y: auto;\n";
            $css .= "    scrollbar-width: thin;\n";
            $css .= "    transition: max-height 0.3s ease;\n";
            $css .= "}\n";
        }
    }
    // Add the generated CSS as inline style
    wp_add_inline_style('filter-style', $css);
    wp_add_inline_script('select2-js', '
        
    ');
}

function dapfforwc_admin_scripts($hook)
{
    if ($hook !== 'toplevel_page_dapfforwc-admin') {
        return; // Load only on the plugin's admin page
    }
    global $dapfforwc_sub_options;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('dapfforwc-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.min.css', [], '1.4.0.30');
    wp_enqueue_code_editor(array('type' => 'text/html'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('dapfforwc-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.min.js', [], '1.4.0.30', true);
    wp_enqueue_media();
    wp_enqueue_script('dapfforwc-media-uploader', plugin_dir_url(__FILE__) . 'assets/js/media-uploader.min.js', ['jquery'], '1.0.0', true);

    $inline_script = 'document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("attribute-dropdown");
    const dropdown_main = document.getElementById("main-texonomy-dropdown");
    const dropdown_attr = document.getElementById("child-attr-dropdown");
    const dropdown_custom = document.getElementById("child-custom-dropdown");

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
        toggleDisplay(".primary_options label.plugincy_color", "none");
        toggleDisplay(".primary_options label.image", "none");
    }else if(selectedAttribute === "tag"){
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.show-product-count", "block");
        toggleDisplay(".primary_options label.plugincy_color", "none");
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

    const savedAttribute = localStorage.getItem("dapfforwc_selected_attribute");
    if (savedAttribute) {
        try {
            const parsed = JSON.parse(savedAttribute);
            if (parsed && parsed.attribute && dropdown) {
                dropdown.value = parsed.attribute;
                dropdown_main.value = parsed.attribute;
                dropdown_attr.value = parsed.attribute;
                dropdown_custom.value = parsed.attribute;
                if (dropdown_attr.value) {
                    dropdown_main.value = "attributes";
                }else if (dropdown_custom.value){
                    dropdown_main.value = "custom_fields";
                }
                // Trigger a change event on the attribute-dropdown
                const event = new Event("change", {
                    bubbles: true
                });

                toggleDisplay(".style-options", "none");
                
                dropdown_main.dispatchEvent(event);
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

           if(selectedType==="plugincy_color" || selectedType==="image") {
            document.querySelector(`.advanced-options.${attributeName}`).style.display = "block";
            document.querySelector(`.advanced-options.${attributeName} .plugincy_color`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .image`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .${selectedType}`).style.display = "block";
            document.querySelector(`.setting-item.single-selection`).style.display = "block";

           }else if(selectedType==="dropdown") {
            radio.closest(".style-options").querySelector(`.setting-item.single-selection`).style.display = "none";
            document.querySelectorAll(".advanced-options").forEach(advanceoptions =>{
                advanceoptions.style.display = "none";
            })
           } else {
            radio.closest(".style-options").querySelector(`.setting-item.single-selection`).style.display = "block";
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
            const selectedType = this.value;
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

            if(selectedType==="icon_search") {
                document.querySelector(`#options-search .optional_settings .btn_text`).style.display = "none";
            }else{
                document.querySelector(`#options-search .optional_settings .btn_text`).style.display = "block";
            }

            // Managing single selection checkbox
            const singleSelectionCheckbox = this.closest(".style-options").querySelector(".setting-item.single-selection input");
            const singleSelectiondiv = this.closest(".style-options").querySelector(".setting-item.single-selection"); 
            if(singleSelectionCheckbox){
                if (this.value === "select" || selectedType==="select2") {
                    singleSelectionCheckbox.checked = true;
                    singleSelectiondiv.style.display = "none"; // Show the checkbox
                    
                } else {
                    singleSelectionCheckbox.checked = false; // Uncheck if other options are selected
                    singleSelectiondiv.style.display = "block"; // Hide the checkbox
                }
            }
        });

        const selectedType = radio.value;
        if(selectedType==="icon_search") {
            document.querySelector(`#options-search .optional_settings .btn_text`).style.display = "none";
        }else{
            document.querySelector(`#options-search .optional_settings .btn_text`).style.display = "block";
        }

        if(selectedType==="select" || selectedType==="select2"){
               document.querySelector(`.single-selection`).style.display = "none";
            }
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
    $get_pro_link = '<a href="https://plugincy.com/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" style="color:#d54e21;font-weight:bold;">' . esc_html__('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce') . '</a>';
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

    wp_enqueue_style('custom-box-control-styles', plugin_dir_url(__FILE__) . 'assets/css/block-editor.min.css', [], '1.4.0.30');
}
add_action('enqueue_block_editor_assets', 'dapfforwc_enqueue_dynamic_ajax_filter_block_assets');






function dapfforwc_add_debug_menu($wp_admin_bar)
{
    if (current_user_can('administrator')) {
        $args = [
            'id'    => 'dapfforwc_debug',
            'title' => '<span class="ab-icon dashicons dashicons-filter"></span><span id="dapfforwc_issue_count"></span> ' . esc_html__('Product Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            'meta'  => [
                'class' => 'dapfforwc-debug-bar',
            ],
        ];
        $wp_admin_bar->add_node($args);

        $wp_admin_bar->add_node([
            'id'     => 'dapfforwc_debug_sub',
            'parent' => 'dapfforwc_debug',
            'title'  => '<span id="dapfforwc_debug_message">' . esc_html__('Checking...', 'dynamic-ajax-product-filters-for-woocommerce') . '</span>',
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
                                        <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/#product-selector-configuration" target="_blank" style="color:#432fb8;text-decoration:underline;font-weight:500;display:inline-block;margin-top:6px;"><?php echo esc_html__('View documentation', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>
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
                    } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwc_advance_settings["pagination_selector"]) && !empty($dapfforwc_advance_settings["pagination_selector"]) ? $dapfforwc_advance_settings["pagination_selector"] : ''); ?>') && !document.querySelector('.plugincy-filter-pagination')) {
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
    global $dapfforwc_advance_settings;
    // Fetch WooCommerce attribute taxonomies
    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $all_attributes = isset($all_data['attributes']) ? $all_data['attributes'] : [];
    $exclude_attributes = isset($dapfforwc_advance_settings['exclude_attributes']) ? explode(',', $dapfforwc_advance_settings['exclude_attributes']) : [];
    $exclude_custom_fields = isset($dapfforwc_advance_settings['exclude_custom_fields']) ? explode(',', $dapfforwc_advance_settings['exclude_custom_fields']) : [];
    $custom_fields = isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [];
    $result = [];

    foreach ($all_attributes as $attribute) {
        if (in_array($attribute['attribute_name'], $exclude_attributes)) {
            continue;
        }
        $result[] = [
            'name' => $attribute['attribute_label'],
            'slug' => $attribute['attribute_name'],
        ];
    }
    foreach ($custom_fields as $attribute) {
        if (in_array($attribute['name'], $exclude_custom_fields)) {
            continue;
        }
        $result[] = [
            'name' => $attribute['label'],
            'slug' => $attribute['name'],
        ];
    }

    if (empty($result)) {
        return new WP_Error('no_attributes', esc_html__('No product attributes found', 'dynamic-ajax-product-filters-for-woocommerce'), array('status' => 404));
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
        $values = explode(',', sanitize_text_field(wp_unslash($value)));
        $formatted_values = [];

        if (strpos($current_place, '{attribute_prefix}') !== false) {

            foreach ($values as $val) {
                $formatted_values[] = str_replace('-', ' ', $val);
            }

            // Format as "attribute seperator between {attribute_prefix} & {value} value1, value2"
            if (!empty($formatted_values)) {
                preg_match('/{attribute_prefix}(.*?)\{value\}/', $current_place, $matches);
                $separator = isset($matches[1]) ? $matches[1] : '-';
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
        'title' => esc_html__('Plugincy', 'dynamic-ajax-product-filters-for-woocommerce'),
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
            "1.4.0.30",
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

        $reason = sanitize_text_field(wp_unslash($_POST['reason'] ?? ''));
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

            var $filterForm = $('form#product-filter');
            // Ensure jQuery is properly loaded
            if (typeof $ === 'undefined' || !$) {
                console.warn('jQuery is not properly loaded');
                return;
            }

            if ($filterForm.data("mobile-style") !== "style_1" && $filterForm.data("mobile-style") !== "style_2") {
                return;
            }

            setTimeout(function() {

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

                // Store original sidebar data
                var sidebarOriginalData = null;
                var currentlyMovedSidebar = null;
                var resizeTimer = null;

                // Safe element checking function
                function isValidElement($element) {
                    return $element && $element.length > 0 && $element.get(0) && $element.get(0).nodeType === 1;
                }

                // Function to check if form#product-filter is already at the top of .products
                function isFilterFormAlreadyAtTop() {
                    try {
                        var $products = $(window.getProductSelectorString ? window.getProductSelectorString : '.products');

                        if (!isValidElement($filterForm) || !isValidElement($products)) {
                            return false;
                        }

                        // Check if elements are visible and have layout properties
                        if (!$filterForm.is(':visible') || !$products.is(':visible')) {
                            return false;
                        }

                        // Get the position of both elements with null checks
                        var formOffset = $filterForm.offset();
                        var productsOffset = $products.offset();

                        // Check if offset() returned valid objects
                        if (!formOffset || !productsOffset ||
                            typeof formOffset.top === 'undefined' ||
                            typeof productsOffset.top === 'undefined') {
                            return false;
                        }

                        var formTop = formOffset.top;
                        var productsTop = productsOffset.top;

                        // Check if form is already above or at the same level as products
                        // Adding a small tolerance (10px) for any margin/spacing
                        return formTop <= (productsTop + 10);
                    } catch (error) {
                        console.warn('Error checking filter form position:', error);
                        return false;
                    }
                }

                // Function to find and store original sidebar position
                function findAndStoreSidebarData() {
                    try {
                        if (sidebarOriginalData) return true; // Already found and stored

                        var sidebarFound = false;

                        // Look for sidebar containing form#product-filter
                        $.each(sidebarSelectors, function(index, selector) {
                            try {
                                var $sidebar = $(selector);

                                if (isValidElement($sidebar) && !sidebarFound) {
                                    // Check if this sidebar contains the product filter form
                                    if ($sidebar.find('form#product-filter').length > 0) {
                                        sidebarFound = true;
                                        storeSidebarOriginalData($sidebar);
                                        return false; // Break the loop
                                    }
                                }
                            } catch (error) {
                                console.warn('Error checking sidebar selector:', selector, error);
                            }
                        });

                        // If no sidebar with common selectors contains form#product-filter, 
                        // check for any element containing form#product-filter
                        if (!sidebarFound) {
                            var $filterForm = $('form#product-filter');
                            if (isValidElement($filterForm)) {
                                var $possibleSidebar = $filterForm.closest('div, aside, section');
                                if (isValidElement($possibleSidebar)) {
                                    storeSidebarOriginalData($possibleSidebar);
                                    sidebarFound = true;
                                }
                            }
                        }

                        return sidebarFound;
                    } catch (error) {
                        console.warn('Error finding sidebar data:', error);
                        return false;
                    }
                }

                // Store the original sidebar data
                function storeSidebarOriginalData($sidebar) {
                    try {
                        if (!isValidElement($sidebar)) return;

                        var $parent = $sidebar.parent();
                        if (!isValidElement($parent)) return;

                        var $nextSibling = $sidebar.next();
                        var $prevSibling = $sidebar.prev();

                        sidebarOriginalData = {
                            sidebar: $sidebar,
                            parent: $parent,
                            nextSibling: isValidElement($nextSibling) ? $nextSibling : null,
                            prevSibling: isValidElement($prevSibling) ? $prevSibling : null,
                            index: $parent.children().index($sidebar)
                        };
                    } catch (error) {
                        console.warn('Error storing sidebar data:', error);
                    }
                }

                // Function to move sidebar to top (mobile only)
                function moveSidebarToTop() {
                    try {
                        // Check if we're on mobile (767px or less)
                        if ($(window).width() <= 767) {
                            // Check if form#product-filter is already at the top of .products
                            if (isFilterFormAlreadyAtTop()) {
                                return; // Don't move if already positioned correctly
                            }

                            if (!findAndStoreSidebarData()) return;

                            var $sidebar = sidebarOriginalData.sidebar;
                            if (!isValidElement($sidebar)) return;

                            // Don't move if already moved
                            if ($sidebar.hasClass('sidebar-moved-to-top')) return;

                            // Try to find .products container first as the preferred target
                            var $products = $(window.getProductSelectorString ? window.getProductSelectorString : '.products');
                            var $targetParent = null;

                            if (isValidElement($products)) {
                                // Move sidebar before .products
                                $targetParent = $products.parent();
                                if (isValidElement($targetParent)) {
                                    $targetParent.addClass('sidebar-parent-flex');
                                    $sidebar.addClass('sidebar-moved-to-top');

                                    // Safe DOM manipulation
                                    if ($sidebar.get(0) && $products.get(0)) {
                                        $products.before($sidebar);
                                        currentlyMovedSidebar = $sidebar;
                                    }
                                }
                            } else {
                                // Fallback to original logic if .products not found
                                var $mainContent = findMainContent($sidebar);

                                if (isValidElement($mainContent)) {
                                    $targetParent = $mainContent.parent();
                                    if (isValidElement($targetParent)) {
                                        $targetParent.addClass('sidebar-parent-flex');
                                        $sidebar.addClass('sidebar-moved-to-top');

                                        // Safe DOM manipulation
                                        if ($sidebar.get(0) && $mainContent.get(0)) {
                                            $mainContent.before($sidebar);
                                            currentlyMovedSidebar = $sidebar;
                                        }
                                    }
                                } else {
                                    // Final fallback: move to top of container
                                    var $container = $sidebar.closest('.container, .wrap, .site, #page, #wrapper, .main-container');
                                    if (isValidElement($container)) {
                                        $container.addClass('sidebar-parent-flex');
                                        $sidebar.addClass('sidebar-moved-to-top');

                                        // Safe DOM manipulation
                                        if ($sidebar.get(0) && $container.get(0)) {
                                            $container.prepend($sidebar);
                                            currentlyMovedSidebar = $sidebar;
                                        }
                                    }
                                }
                            }
                        } else {
                            // Desktop - restore original position
                            restoreOriginalPosition();
                        }
                    } catch (error) {
                        console.warn('Error moving sidebar to top:', error);
                    }
                }

                // Function to find main content
                function findMainContent($sidebar) {
                    try {
                        if (!isValidElement($sidebar)) return null;

                        // First, try to find main content as a sibling
                        var $mainContent = $sidebar.siblings().filter(function() {
                            var $this = $(this);
                            return $this.find('article, .post, .entry, .content').length > 0;
                        }).first();

                        // If no main content found as sibling, try common content selectors within the same parent
                        if (!isValidElement($mainContent)) {
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

                            var $parent = $sidebar.parent();
                            if (isValidElement($parent)) {
                                $.each(contentSelectors, function(i, contentSelector) {
                                    var $content = $parent.find(contentSelector).first();
                                    if (isValidElement($content) && !$content.is($sidebar) && !$sidebar.find($content).length) {
                                        $mainContent = $content;
                                        return false; // Break the loop
                                    }
                                });
                            }
                        }

                        return isValidElement($mainContent) ? $mainContent : null;
                    } catch (error) {
                        console.warn('Error finding main content:', error);
                        return null;
                    }
                }

                // Function to restore original position
                function restoreOriginalPosition() {
                    try {
                        if (!sidebarOriginalData || !currentlyMovedSidebar) return;

                        var $sidebar = currentlyMovedSidebar;
                        var originalData = sidebarOriginalData;

                        if (!isValidElement($sidebar)) return;

                        // Remove mobile classes
                        $sidebar.removeClass('sidebar-moved-to-top');

                        // Remove flex class from any parents that might have it
                        $('.sidebar-parent-flex').removeClass('sidebar-parent-flex');

                        // Restore to original position
                        if (originalData.nextSibling && isValidElement(originalData.nextSibling)) {
                            // Insert before the next sibling
                            if ($sidebar.get(0) && originalData.nextSibling.get(0)) {
                                originalData.nextSibling.before($sidebar);
                            }
                        } else if (originalData.prevSibling && isValidElement(originalData.prevSibling)) {
                            // Insert after the previous sibling
                            if ($sidebar.get(0) && originalData.prevSibling.get(0)) {
                                originalData.prevSibling.after($sidebar);
                            }
                        } else if (isValidElement(originalData.parent)) {
                            // Append to original parent if no siblings
                            if ($sidebar.get(0) && originalData.parent.get(0)) {
                                originalData.parent.append($sidebar);
                            }
                        }

                        currentlyMovedSidebar = null;
                    } catch (error) {
                        console.warn('Error restoring original position:', error);
                    }
                }

                // Debounced resize handler
                function handleResize() {
                    try {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function() {
                            moveSidebarToTop();
                        }, 150); // Debounce resize events
                    } catch (error) {
                        console.warn('Error handling resize:', error);
                    }
                }

                // Execute on page load with delay to ensure DOM is ready
                setTimeout(function() {
                    try {
                        moveSidebarToTop();
                    } catch (error) {
                        console.warn('Error on initial load:', error);
                    }
                }, 100);

                // Re-execute after AJAX calls (for dynamic content)
                $(document).ajaxComplete(function() {
                    setTimeout(function() {
                        try {
                            moveSidebarToTop();
                        } catch (error) {
                            console.warn('Error after AJAX:', error);
                        }
                    }, 200);
                });

                // Re-execute after window resize (debounced)
                $(window).on('resize', handleResize);
            }, 2000);
        });
    </script>
    <?php
}

if (!isset($dapfforwc_advance_settings["sidebar_on_top"]) || (isset($dapfforwc_advance_settings["sidebar_on_top"])  && $dapfforwc_advance_settings["sidebar_on_top"] === 'on')) {
    add_action('template_redirect', function () {
        // Check if WooCommerce is active and functions exist
        if (
            class_exists('WooCommerce') &&
            function_exists('is_shop') &&
            function_exists('is_product_category') &&
            function_exists('is_product_tag')
        ) {

            if (is_shop() || is_product_category() || is_product_tag()) {
                add_action('wp_head', 'dapfforwc_sidebar_to_top_inline_scripts');
            }
        }
    });
}



/**
 * Create widget area for WooCommerce archives
 */
function dapfforwc_register_sidebar()
{
    register_sidebar(array(
        'name' => esc_html__('WooCommerce Archive Content', 'dynamic-ajax-product-filters-for-woocommerce'),
        'id' => 'wc-archive-content',
        'description' => esc_html__('Content displayed after WooCommerce archive titles and before products', 'dynamic-ajax-product-filters-for-woocommerce'),
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
function dapfforwc_display_archive_content()
{
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
function dapfforwc_hook_archive_content()
{
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
function dapfforwc_activate()
{
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'dapfforwc_activate');

/**
 * Plugin deactivation hook
 */
function dapfforwc_deactivate()
{
    // Clean up if needed
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'dapfforwc_deactivate');


// Handle AJAX template activation
function dapfforwc_ajax_activate_template()
{
    check_ajax_referer('dapfforwc_template_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(esc_html__('You do not have permission to manage templates.', 'dynamic-ajax-product-filters-for-woocommerce'));
    }

    $template_id = isset($_POST['template_id']) ? sanitize_text_field(wp_unslash($_POST['template_id'])) : '';

    if (empty($template_id)) {
        wp_send_json_error(esc_html__('Invalid template ID.', 'dynamic-ajax-product-filters-for-woocommerce'));
    }

    // Validate template ID
    $valid_templates = ['clean', 'shadow', 'modern', 'basic', 'basic_bordered'];
    if (!in_array($template_id, $valid_templates, true)) {
        wp_send_json_error(esc_html__('Invalid template selected.', 'dynamic-ajax-product-filters-for-woocommerce'));
    }

    global $template_options;
    $template_options['active_template'] = $template_id;

    $updated = update_option('dapfforwc_template_options', $template_options);

    if ($updated !== false) {
        // Get template name for success message
        $template_names = [
            'clean'  => esc_html__('Minimal Template', 'dynamic-ajax-product-filters-for-woocommerce'),
            'shadow' => esc_html__('Elevated Template', 'dynamic-ajax-product-filters-for-woocommerce'),
            'modern' => esc_html__('Modern Template', 'dynamic-ajax-product-filters-for-woocommerce'),
            'basic' => esc_html__('Basic Template', 'dynamic-ajax-product-filters-for-woocommerce'),
            'basic_bordered' => esc_html__('Basic Bordered Template', 'dynamic-ajax-product-filters-for-woocommerce'),
        ];

        // translators: %s: Template name.
        wp_send_json_success(sprintf(esc_html__('%s activated successfully!', 'dynamic-ajax-product-filters-for-woocommerce'), $template_names[$template_id]));
    } else {
        wp_send_json_error(esc_html__('Failed to save template settings.', 'dynamic-ajax-product-filters-for-woocommerce'));
    }
}
add_action('wp_ajax_dapfforwc_activate_template', 'dapfforwc_ajax_activate_template');


/**
 * Register the widget
 */
function dapfforwc_register_widget()
{
    register_widget('dapfforwc_Widget_filters');
}
add_action('widgets_init', 'dapfforwc_register_widget');

/**
 * Plugincy Widget Filter Class
 */
class dapfforwc_Widget_filters extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'plugincy_widget',
            esc_html__('Dynamic Ajax Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            array(
                'description' => esc_html__('Display content after WooCommerce archive titles', 'dynamic-ajax-product-filters-for-woocommerce'),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance)
    {
        global $dapfforwc_allowed_tags;
        // Only display on WooCommerce archive pages
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
            return;
        }

        $content = '[plugincy_filters]';

        echo wp_kses($args['before_widget'], $dapfforwc_allowed_tags);

        // Process shortcodes and display content
        echo do_shortcode(wpautop($content));

        echo wp_kses($args['after_widget'], $dapfforwc_allowed_tags);
    }

    /**
     * Back-end widget form
     */
    public function form($instance)
    {
        $content = '[plugincy_filters]';
    ?>
        <p> <?php echo esc_attr($content); ?></p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['content'] = '[plugincy_filters]';

        return $instance;
    }
}




// Register the widget for [plugincy_filters_single name="selector_here"]

function dapfforwc_register_single_filter_widget()
{
    register_widget('dapfforwc_Widget_single_filter');
}
add_action('widgets_init', 'dapfforwc_register_single_filter_widget');

// dapfforwc_Widget_single_filter
class dapfforwc_Widget_single_filter extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'plugincy_widget_single',
            esc_html__('Dynamic Ajax Single Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            array(
                'description' => esc_html__('Display a single filter for WooCommerce products', 'dynamic-ajax-product-filters-for-woocommerce'),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance)
    {
        global $dapfforwc_allowed_tags;
        // Only display on WooCommerce archive pages
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
            return;
        }

        $selector = !empty($instance['selector']) ? $instance['selector'] : 'selector_here';
        $content = '[plugincy_filters_single name="' . esc_attr($selector) . '"]';

        echo wp_kses($args['before_widget'], $dapfforwc_allowed_tags);
        echo do_shortcode(wpautop($content));
        echo wp_kses($args['after_widget'], $dapfforwc_allowed_tags);
    }

    /**
     * Back-end widget form
     */
    public function form($instance)
    {
        $selector = !empty($instance['selector']) ? $instance['selector'] : '';
    ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('selector')); ?>">
                <?php esc_html_e('Selector Name:', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('selector')); ?>" name="<?php echo esc_attr($this->get_field_name('selector')); ?>" type="text" value="<?php echo esc_attr($selector); ?>" placeholder="selector_here" />
        </p>
        <p>
            <small><?php esc_html_e('Enter the selector name for the single filter shortcode.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></small>
        </p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['selector'] = !empty($new_instance['selector']) ? sanitize_text_field($new_instance['selector']) : '';
        return $instance;
    }
}

// Register the widget for [plugincy_filters_selected]

function dapfforwc_register_selected_filter_widget()
{
    register_widget('dapfforwc_Widget_selected_filter');
}
add_action('widgets_init', 'dapfforwc_register_selected_filter_widget');
// dapfforwc_Widget_selected_filter
class dapfforwc_Widget_selected_filter extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'plugincy_widget_selected',
            esc_html__('Dynamic Ajax Selected Filter', 'dynamic-ajax-product-filters-for-woocommerce'),
            array(
                'description' => esc_html__('Display a selected filter for WooCommerce products', 'dynamic-ajax-product-filters-for-woocommerce'),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance)
    {
        global $dapfforwc_allowed_tags;
        // Only display on WooCommerce archive pages
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
            return;
        }
        $content = '[plugincy_filters_selected]';

        echo wp_kses($args['before_widget'], $dapfforwc_allowed_tags);
        // No dynamic content required, just output shortcode
        echo do_shortcode($content);
        echo wp_kses($args['after_widget'], $dapfforwc_allowed_tags);
    }

    /**
     * Back-end widget form
     */
    public function form($instance)
    {
        $content = '[plugincy_filters_selected]';
    ?>
        <p> <?php echo esc_attr($content); ?></p>
<?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();

        return $instance;
    }
}


add_action('widgets_init', function () {
    if (function_exists('wp_use_widgets_block_editor') && wp_use_widgets_block_editor()) {
        unregister_widget('dapfforwc_Widget_filters');
        unregister_widget('dapfforwc_Widget_single_filter');
        unregister_widget('dapfforwc_Widget_selected_filter');
    }
});


// Enqueue your script in WordPress
add_action('wp_enqueue_scripts', function () {
    wp_add_inline_script('jquery-core', "
        (function() {
            function isDebugMode() {
                return new URLSearchParams(window.location.search).get('plugincydebug') === 'true';
            }
            
            window.plugincydebugLog = function() {
                if (isDebugMode() && console && console.log) {
                    console.log.apply(console, arguments);
                }
            };
        })();
    ");
});

// Function to clear all cache files
function dapfforwc_clear_woocommerce_caches()
{
    $cache_files = [
        plugin_dir_path(__FILE__) . 'includes/woocommerce_attributes_cache.json',
        plugin_dir_path(__FILE__) . 'includes/woocommerce_product_details.json',
        plugin_dir_path(__FILE__) . 'includes/min_max_prices_cache.json',
    ];

    foreach ($cache_files as $cache_file) {
        if (file_exists($cache_file)) {
            wp_delete_file($cache_file);
        }
    }
}


register_activation_hook(__FILE__, 'dapfforwc_clear_woocommerce_caches');



/**
 * Ultra-light fragment mode for gm-product-filter AJAX.
 * Goal: Emit only the needed HTML markup, nothing else.
 */

if (!function_exists('gm_pf_is_fragment_request')) {
    function gm_pf_is_fragment_request(): bool {
        // Cheap, safe guard
        if (empty($_GET['gm-product-filter-nonce'])) return false;
        $nonce = sanitize_text_field(wp_unslash($_GET['gm-product-filter-nonce']));
        return (bool) wp_verify_nonce($nonce, 'gm-product-filter-action');
    }
}

/** ------------------------------------------------------------------------
 * Constants & helpers
 * --------------------------------------------------------------------- */
if (!defined('GM_PF_PLACEHOLDER')) {
    // 1×1 transparent GIF (43 bytes) — avoids browser quirks with src=""
    define('GM_PF_PLACEHOLDER', '');
}

/**
 * Rewrite an <img ...> tag to lazy placeholder.
 */
if (!function_exists('gm_pf_rewrite_img_html')) {
    function gm_pf_rewrite_img_html(string $html): string {
        if (!gm_pf_is_fragment_request() || strpos($html, '<img') === false) return $html;

        // Remove eager hints that can trigger loads
        $html = preg_replace('/\s(srcset|sizes|loading|decoding|fetchpriority)="[^"]*"/i', '', $html);

        // Replace src with placeholder, move real URL to data-src, ensure class exists
        $html = preg_replace_callback(
            '/<img\b([^>]*?)\ssrc="([^"]+)"([^>]*)>/i',
            static function ($m) {
                $pre  = $m[1];
                $src  = $m[2];
                $post = $m[3];

                if (preg_match('/\bclass="([^"]*)"/i', $pre . $post, $cm)) {
                    $cls = trim($cm[1] . ' gm-pf-lazy');
                    $pre = preg_replace('/\bclass="[^"]*"/i', ' class="' . esc_attr($cls) . '" ', $pre);
                } else {
                    $pre .= ' class="gm-pf-lazy" ';
                }

                return '<img' . $pre . ' src="' . GM_PF_PLACEHOLDER . '" data-src="' . esc_url($src) . '"' . $post . '>';
            },
            $html
        );

        return $html;
    }
}

/** ------------------------------------------------------------------------
 * HEAD/SCRIPT/CSS suppression (works even if theme hard-codes tags)
 * --------------------------------------------------------------------- */
add_action('init', function () {
    if (!gm_pf_is_fragment_request()) return;

    // Stop resource hints (dns-prefetch, preconnect, font/style preloads)
    remove_action('wp_head', 'wp_resource_hints', 2);
    add_filter('wp_resource_hints', static fn() => [], 999);

    // Block core printers (prevents many default tags)
    remove_action('wp_head', 'wp_print_styles', 8);
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_footer', 'wp_print_footer_scripts', 20);

    // Block block-theme "Global Styles" and SVG filters
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

    // Trim misc head noise
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);

    // Emojis + oEmbed + REST links
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11);
}, 1);

/**
 * Dequeue/deregister anything that still gets enqueued.
 * Also neuter tag writers in case something slips through late.
 */
add_action('wp_enqueue_scripts', function () {
    if (!gm_pf_is_fragment_request()) return;

    global $wp_styles, $wp_scripts;

    if ($wp_styles && !empty($wp_styles->queue)) {
        foreach ((array) $wp_styles->queue as $h) {
            wp_dequeue_style($h);
            wp_deregister_style($h);
        }
    }
    if ($wp_scripts && !empty($wp_scripts->queue)) {
        foreach ((array) $wp_scripts->queue as $h) {
            wp_dequeue_script($h);
            wp_deregister_script($h);
        }
    }

    // Known inline handles (WP 5.9+)
    wp_dequeue_style('global-styles');
    wp_deregister_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
    wp_deregister_style('classic-theme-styles');

    add_filter('style_loader_tag',  '__return_empty_string', 9999);
    add_filter('script_loader_tag', '__return_empty_string', 9999, 3);
}, 9999);

/**
 * Output buffer (final guard) — strips hard-coded tags in header.php.
 * Only runs in fragment mode; safe for all themes.
 */
add_action('template_redirect', function () {
    if (!gm_pf_is_fragment_request()) return;

    // Remove heavy footer actions (trackers, etc.)
    remove_all_actions('wp_footer');

    ob_start(static function ($html) {
        // Kill stylesheets and style/font preloads
        $html = preg_replace('#<link[^>]+rel=["\']stylesheet["\'][^>]*>#i', '', $html);
        $html = preg_replace('#<link[^>]+rel=["\']preload["\'][^>]+as=["\'](?:style|font)["\'][^>]*>#i', '', $html);
        // Kill inline <style> and all <script> blocks
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
        // Optionally strip <head> content entirely for max speed
        $html = preg_replace('#<head\b[^>]*>.*?</head>#is', '', $html);
        // Optional: remove theme header/footer wrappers from fragments
        $html = preg_replace('#<header\b[^>]*>.*?</header>#is', '', $html);
        $html = preg_replace('#<footer\b[^>]*>.*?</footer>#is', '', $html);
        return $html;
    });
}, 0);

add_action('shutdown', function () {
    if (!gm_pf_is_fragment_request()) return;
    if (ob_get_level()) { @ob_end_flush(); }
}, PHP_INT_MAX);

/** ------------------------------------------------------------------------
 * Image rewriting (attachments, thumbnails, avatars, content)
 * --------------------------------------------------------------------- */
add_filter('wp_get_attachment_image_attributes', function ($attr) {
    if (!gm_pf_is_fragment_request()) return $attr;

    if (!empty($attr['src'])) {
        $attr['data-src'] = $attr['src'];
        $attr['src']      = GM_PF_PLACEHOLDER;
    }

    // Avoid any eager loads
    unset($attr['srcset'], $attr['sizes'], $attr['loading'], $attr['decoding'], $attr['fetchpriority']);

    $attr['class'] = trim(($attr['class'] ?? '') . ' gm-pf-lazy');

    // Less layout work, smaller markup
    unset($attr['width'], $attr['height']);

    return $attr;
}, 10, 1);

add_filter('post_thumbnail_html', function ($html) {
    return gm_pf_rewrite_img_html($html);
}, 10, 1);

add_filter('get_avatar', function ($html) {
    return gm_pf_rewrite_img_html($html);
}, 10, 1);

add_filter('the_content', function ($content) {
    return gm_pf_rewrite_img_html($content);
}, 9);

/** ------------------------------------------------------------------------
 * WooCommerce & Query trimming
 * --------------------------------------------------------------------- */
add_filter('woocommerce_enable_cart_session', function ($enabled) {
    return gm_pf_is_fragment_request() ? false : $enabled;
}, 10, 1);

add_filter('woocommerce_use_cart_session', function ($use) {
    return gm_pf_is_fragment_request() ? false : $use;
}, 10, 1);

add_filter('woocommerce_cart_hash', function ($hash) {
    return gm_pf_is_fragment_request() ? '' : $hash;
}, 10, 1);


/** ------------------------------------------------------------------------
 * Headers & cosmetics
 * --------------------------------------------------------------------- */
add_action('init', function () {
    if (!gm_pf_is_fragment_request()) return;
    add_filter('show_admin_bar', '__return_false', 999);
});

add_action('send_headers', function () {
    if (!gm_pf_is_fragment_request()) return;
    header('X-GM-PF-Fragment: 1');   // debug flag
    header('Connection: keep-alive');
    header('Cache-Control: no-store, max-age=0'); // personalized, keep off
});
