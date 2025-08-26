<?php
if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_render_checkbox($key, $settings = "dapfforwc_options")
{
    global $$settings;
?>
    <label class="switch <?php echo esc_attr($key); echo $key === "use_anchor" || $key === "show_custom_fields"  ? ' pro-only' : ''; ?>">
        <input <?php echo $key === "use_anchor" || $key === "show_custom_fields" ? ' disabled' : ''; ?> type='checkbox' name='<?php echo $key === "use_anchor" || $key === "show_custom_fields" ? '_pro' : esc_attr($settings); ?>[<?php echo esc_attr($key); ?>]' <?php $key === "use_anchor" || $key === "show_custom_fields" ? '' : checked(isset($$settings[$key]) && $$settings[$key] === "on"); ?>>
        <span class="slider round"></span>
        <span class="switch-on">On</span>
        <span class="switch-off">Off</span>
    </label>
    <?php
    if ($key === "use_filters_word_in_permalinks") {
        echo "<p>if you want to use permalinks filter in your front page & archive page turn it on.</p>";
    } 
    elseif ($key === "use_attribute_type_in_permalinks") {
        echo "<p>".esc_html__('Enable this setting if you want permalinks Like', 'dynamic-ajax-product-filters-for-woocommerce') ."<code>?filters=1&product_cat=compact-design&pa-size=large&product-tag=tag1</code></p>";
    } 
    elseif ($key === "show_loader") {
        echo "<p><a href='#' id='customize_loader'>".esc_html__('Customize', 'dynamic-ajax-product-filters-for-woocommerce') ."</a> ".esc_html__('loading effect', 'dynamic-ajax-product-filters-for-woocommerce') ."</p>";
    }
    elseif ($key === "enable_seo") {
        echo "<p>".esc_html__('Add "robots" meta tag in head tag of HTML page if filters have been activated.', 'dynamic-ajax-product-filters-for-woocommerce') ."</p>";

        ?>
        <p>
            <h4 for="seo_meta_tag" style="margin: 20px 0 10px;"><?php esc_html_e('Choose Meta Tag:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
            <select name="dapfforwc_seo_permalinks_options[seo_meta_tag]" id="seo_meta_tag">
                <?php
                $meta_tag_options = [
                    'noindex,nofollow' => 'noindex, nofollow',
                    'noindex,follow' => 'noindex, follow',
                    'index,nofollow' => 'index, nofollow',
                    'index,follow' => 'index, follow',
                ];
                foreach ($meta_tag_options as $value => $label) {
                    echo '<option value="' . esc_attr($value) . '" ' . selected(isset($$settings['seo_meta_tag']) ? $$settings['seo_meta_tag'] : '', $value, false) . '>' . esc_html($label) . '</option>';
                }
                ?>
            </select>
            <p class="description"><?php esc_html_e('Select the meta tag to be added in the head section of the page.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
        </p>
        <?php
    }
}

function dapfforwc_show_categories_render()
{
    dapfforwc_render_checkbox('show_categories');
}
function dapfforwc_show_attributes_render()
{
    dapfforwc_render_checkbox('show_attributes');
}
function dapfforwc_show_tags_render()
{
    dapfforwc_render_checkbox('show_tags');
}
function dapfforwc_show_price_range_render()
{
    dapfforwc_render_checkbox('show_price_range');
}
function dapfforwc_show_rating_render()
{
    dapfforwc_render_checkbox('show_rating');
}
function dapfforwc_show_search_render()
{
    dapfforwc_render_checkbox('show_search');
}
function dapfforwc_show_brand_render()
{
    dapfforwc_render_checkbox('show_brand');
}
function dapfforwc_show_author_render()
{
    dapfforwc_render_checkbox('show_author');
}
function dapfforwc_show_status_render()
{
    dapfforwc_render_checkbox('show_status');
}
function dapfforwc_show_onsale_render()
{
    dapfforwc_render_checkbox('show_onsale');
}
function dapfforwc_show_dimension_render()
{
    dapfforwc_render_checkbox('show_dimension');
}
function dapfforwc_show_sku_render()
{
    dapfforwc_render_checkbox('show_sku');
}
function dapfforwc_show_discount_render()
{
    dapfforwc_render_checkbox('show_discount');
}
function dapfforwc_show_date_filter_render()
{
    dapfforwc_render_checkbox('show_date_filter');
}
function dapfforwc_show_custom_fields_render()
{
    dapfforwc_render_checkbox('show_custom_fields');
}
function dapfforwc_use_filters_word_in_permalinks_render()
{
    dapfforwc_render_checkbox('use_filters_word_in_permalinks', 'dapfforwc_seo_permalinks_options');
}

function dapfforwc_update_filter_options_render()
{
    dapfforwc_render_checkbox('update_filter_options');
}
function dapfforwc_show_loader_render()
{
    dapfforwc_render_checkbox('show_loader');
}
function dapfforwc_use_custom_template_render()
{
    dapfforwc_render_checkbox('use_custom_template');
}




function dapfforwc_custom_template_code_render()
{
    global $dapfforwc_options;
    echo '    
    <div class="custom_template_code" >';
    ?>
    <!-- Placeholder List -->
    <div id="placeholder-list" style="margin-bottom: 10px;">
        <?php
        $placeholders = [
            '{{product_link}}' => 'Product Link',
            '{{product_title}}' => 'Product Title',
            '{{product_image}}' => 'Product Image',
            '{{product_price}}' => 'Product Price',
            '{{product_excerpt}}' => 'Product Excerpt',
            '{{product_category}}' => 'Product Category',
            '{{product_sku}}' => 'Product SKU',
            '{{product_stock}}' => 'Product Stock',
            '{{add_to_cart_url}}' => 'Add to Cart URL',
            '{{product_id}}' => 'Product ID'
        ];
        foreach ($placeholders as $placeholder => $label) {
            echo "<span class='placeholder' onclick=\"insertPlaceholder('" . esc_html($placeholder) . "')\">" . esc_html($placeholder) . "</span>";
        }
        ?>
    </div>
    <textarea style="display:none;" id="custom_template_input" name="dapfforwc_options[custom_template_code]" rows="10" cols="50" class="large-text"><?php if (isset($dapfforwc_options['custom_template_code'])) {
                                                                                                                                                            echo esc_textarea($dapfforwc_options['custom_template_code']);
                                                                                                                                                        } ?></textarea>
    <div id="code-editor"></div>
    <p class="description"><?php esc_html_e('Enter your custom template code here.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
    </div>


<?php
}

function dapfforwc_use_url_filter_render()
{
    global $dapfforwc_options;
?>
    <fieldset>
        <legend><?php esc_html_e('Select URL Filter Type', 'dynamic-ajax-product-filters-for-woocommerce'); ?></legend>
        <?php
        $types = [
            'query_string' => esc_html__('With Query String (e.g., ?filters)', 'dynamic-ajax-product-filters-for-woocommerce'),
            'pro_only' => esc_html__('With Permalinks (e.g., brand/size/color)', 'dynamic-ajax-product-filters-for-woocommerce'),
            'ajax' => esc_html__('With Ajax', 'dynamic-ajax-product-filters-for-woocommerce'),
        ];
        foreach ($types as $value => $label) {
            // Check if the current value is "pro_only"
            $disabled = $value === 'pro_only' ? 'disabled' : '';
            $label_class = $value === 'pro_only' ? 'pro-only' : '';

            echo '<label class="' . esc_attr($label_class) . '">
                    <input type="radio" name="dapfforwc_options[use_url_filter]" value="' . esc_attr($value) . '" ' . checked(isset($dapfforwc_options['use_url_filter'])?$dapfforwc_options['use_url_filter']:"", $value, false) . ' ' . esc_attr($disabled) . '> 
                    ' . esc_html($label) . '
                  </label><br>';
        }
        ?>
    </fieldset>
<?php
}
