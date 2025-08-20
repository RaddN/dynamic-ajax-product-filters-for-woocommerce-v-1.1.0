<?php
if (!defined('ABSPATH')) {
    exit;
}
// Render the "Product Selector" field
function dapfforwc_product_selector_callback()
{
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
    $product_selector = isset($dapfforwc_options['product_selector']) ? esc_attr($dapfforwc_options['product_selector']) : '.products';
?>
    <input type="text" name="dapfforwc_advance_options[product_selector]" value="<?php echo esc_attr($product_selector); ?>" placeholder=".products">
    <p class="description">
        <?php esc_html_e('Enter the CSS selector for the product container. Default is .products.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
    </p>
<?php
}

// Render the "Pagination Selector" field
function dapfforwc_pagination_selector_callback()
{
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
    $pagination_selector = isset($dapfforwc_options['pagination_selector']) ? esc_attr($dapfforwc_options['pagination_selector']) : '.woocommerce-pagination ul.page-numbers';
?>
    <input type="text" name="dapfforwc_advance_options[pagination_selector]" value="<?php echo esc_attr($pagination_selector); ?>" placeholder=".woocommerce-pagination ul.page-numbers">
    <p class="description">
        <?php esc_html_e('Enter the CSS selector for the pagination container. Default is .woocommerce-pagination ul.page-numbers.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
    </p>
<?php
}
// Render the "Product Shortcode Selector" field
function dapfforwc_product_shortcode_callback()
{
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
    $product_shortcode = isset($dapfforwc_options['product_shortcode']) ? esc_attr($dapfforwc_options['product_shortcode']) : 'products';
?>
    <input type="text" name="dapfforwc_advance_options[product_shortcode]" value="<?php echo esc_attr($product_shortcode); ?>" placeholder="products">
    <p class="description">
        <?php esc_html_e('Enter the selector for the products shortcode. Default is products', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
    </p>
<?php
}

function dapfforwc_remove_outofStock_render()
{
    dapfforwc_render_advance_checkbox('remove_outofStock', esc_html__('Enable this option to remove out-of-stock products from the filter results.', 'dynamic-ajax-product-filters-for-woocommerce'));
}
function dapfforwc_allow_data_share_render()
{
    dapfforwc_render_advance_checkbox('allow_data_share', esc_html__('We collect non-sensitive technical details from your website, like the PHP version and features usage, to help us troubleshoot issues faster, make informed development decisions, and build features that truly benefit you.', 'dynamic-ajax-product-filters-for-woocommerce') . " <a href='https://plugincy.com/usage-tracking/' target='_blank'>". esc_html__('Learn more…', 'dynamic-ajax-product-filters-for-woocommerce')."</a>");
}
function dapfforwc_side_bar_top_render()
{
    dapfforwc_render_advance_checkbox('sidebar_on_top', esc_html__('For mobile move the sidebar to top position.', 'dynamic-ajax-product-filters-for-woocommerce'));
}
function dapfforwc_default_value_selected_render()
{
    dapfforwc_render_advance_checkbox('default_value_selected', esc_html__('Make default value from shortcode & pages selected/checked', 'dynamic-ajax-product-filters-for-woocommerce'));
}


function dapfforwc_render_advance_checkbox($key, $message = null)
{
    global $allowed_tags;
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
?>
    <label class="switch <?php echo esc_attr($key); ?>">
        <input type='checkbox' name='dapfforwc_advance_options[<?php echo esc_attr($key); ?>]' <?php checked(isset($dapfforwc_options[$key]) && $dapfforwc_options[$key] === "on"); ?>>
        <span class="slider round"></span>
        <span class="switch-on">On</span>
        <span class="switch-off">Off</span>
    </label>
    <?php
    if (isset($message) && !empty($message)) {
        echo "<p class='description' style='max-width: 800px;'>" . wp_kses($message, $allowed_tags) . "</p>";
    }
}

function dapfforwc_exclude_attributes_render()
{
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
    $exclude_attributes = isset($dapfforwc_options['exclude_attributes']) ? explode(',', $dapfforwc_options['exclude_attributes']) : []; // Convert string to array
    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $all_attributes = $all_data['attributes'] ?? [];
    $dapfforwc_attributes = [];
    
    foreach ($all_attributes as $attribute) {
        $dapfforwc_attributes[] = (object) [
            'attribute_name' => $attribute['attribute_name'],
            'attribute_label' => $attribute['attribute_label'],
        ];
    }
    ?>
    <select id="exclude-attributes" multiple style="scrollbar-width: thin;min-width: 141px;">
        <?php foreach ($dapfforwc_attributes as $option) : ?>
            <option value="<?php echo esc_attr($option->attribute_name); ?>" 
                <?php echo in_array($option->attribute_name, $exclude_attributes) ? 'selected' : ''; ?>>
                <?php echo esc_html($option->attribute_label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="hidden" name="dapfforwc_advance_options[exclude_attributes]" id="exclude-attributes-string" value="<?php echo esc_attr(implode(',', $exclude_attributes)); ?>">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('exclude-attributes');
            const hiddenInput = document.getElementById('exclude-attributes-string');

            function updateHiddenInput() {
                const selectedOptions = Array.from(selectElement.selectedOptions);
                const selectedValues = selectedOptions.map(option => option.value);
                hiddenInput.value = selectedValues.join(',');
            }

            selectElement.addEventListener('change', updateHiddenInput);
            updateHiddenInput(); // Initial update
        });
    </script>
    <p class="description" style="max-width: 800px;"><?php esc_html_e('Use Ctrl + Click to select/remove multiple options.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
    <?php
}
function dapfforwc_exclude_custom_fields_render()
{
    $dapfforwc_options = get_option('dapfforwc_advance_options') ?: [];
    $exclude_custom_fields = isset($dapfforwc_options['exclude_custom_fields']) ? explode(',', $dapfforwc_options['exclude_custom_fields']) : []; // Convert string to array
    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $custom_fields = $all_data['custom_fields'] ?? [];
    $dapfforwc_attributes = [];
    
    foreach ($custom_fields as $attribute) {
        $dapfforwc_attributes[] = (object) [
            'attribute_name' => $attribute['name'],
            'attribute_label' => $attribute['label'],
        ];
    }
    ?>
    <select disabled class="pro-only" id="exclude_custom_fields" multiple style="scrollbar-width: thin;min-width: 141px;">
        <?php foreach ($dapfforwc_attributes as $option) : ?>
            <option value="<?php echo esc_attr($option->attribute_name); ?>" 
                <?php echo in_array($option->attribute_name, $exclude_custom_fields) ? 'selected' : ''; ?>>
                <?php echo esc_html($option->attribute_label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input disabled type="hidden" name="dapfforwc_advance_options[exclude_custom_fields]" id="exclude_custom_fields-string" value="<?php echo esc_attr(implode(',', $exclude_custom_fields)); ?>">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('exclude_custom_fields');
            const hiddenInput = document.getElementById('exclude_custom_fields-string');

            function updateHiddenInput() {
                const selectedOptions = Array.from(selectElement.selectedOptions);
                const selectedValues = selectedOptions.map(option => option.value);
                hiddenInput.value = selectedValues.join(',');
            }

            selectElement.addEventListener('change', updateHiddenInput);
            updateHiddenInput(); // Initial update
        });
    </script>
    <p class="description" style="max-width: 800px;"><?php esc_html_e('Use Ctrl + Click to select/remove multiple options.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
    <?php
}