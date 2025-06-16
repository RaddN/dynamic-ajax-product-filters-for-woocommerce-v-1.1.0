<?php
if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_use_attribute_type_in_permalinks_render()
{
    dapfforwc_render_checkbox('use_attribute_type_in_permalinks',"dapfforwc_seo_permalinks_options");
}

function dapfforwc_enable_seo_render()
{
    dapfforwc_render_checkbox('enable_seo', "dapfforwc_seo_permalinks_options");
}

function dapfforwc_use_anchor_render() { dapfforwc_render_checkbox('use_anchor','dapfforwc_seo_permalinks_options'); }

function dapfforwc_permalinks_prefix_render()
{
    global $dapfforwc_seo_permalinks_options;
    $options = isset($dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options']) ? $dapfforwc_seo_permalinks_options['dapfforwc_permalinks_prefix_options'] : [
        "product-category" => 'cata',
        'tag' => 'tags',
        'price' => 'price',
        'rating' => 'rating',
        'attribute' => [
            'color' => 'color',
            'size' => 'size',
            'brand' => 'brand',
            'material' => 'material',
            'style' => 'style',
        ],
    ];
    $attributes = wc_get_attribute_taxonomies(); // Get WooCommerce attributes
?>
    <style>
        .attribute_prefix_list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .dapfforwc-form-group {
            margin-bottom: 15px;
        }

        .dapfforwc-form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .dapfforwc-form-group input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .dapfforwc-form-group input[type="text"] {
                max-width: 100%;
            }
        }

        .dapfforwc-info-message {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const useAttributeTypeCheckbox = document.querySelector('input[name="dapfforwc_seo_permalinks_options[use_attribute_type_in_permalinks]"]');
            const attributePrefixList = document.querySelector('.attribute_prefix_list');
            const infoMessage = document.querySelector('.dapfforwc-info-message');

            function toggleAttributePrefixList() {
                if (useAttributeTypeCheckbox.checked) {
                    attributePrefixList.style.display = 'grid';
                    infoMessage.style.display = 'none';
                } else {
                    attributePrefixList.style.display = 'none';
                    infoMessage.style.display = 'block';
                }
            }

            if (useAttributeTypeCheckbox) {
                toggleAttributePrefixList(); // Initialize on page load
            }
            useAttributeTypeCheckbox.addEventListener('change', function () {
                toggleAttributePrefixList(); // Toggle on change
            });
        });
    </script>
    <div class="attribute_prefix_list" style="margin-bottom: 10px;">
        <div class="dapfforwc-form-group">
            <label for="dapfforwc_category_prefix"><?php esc_html_e("product-category", 'dynamic-ajax-product-filters-for-woocommerce'); ?></label>
            <input type="text" id="dapfforwc_category_prefix" name="dapfforwc_seo_permalinks_options[dapfforwc_permalinks_prefix_options][product-category]"
                value="<?php echo isset($options["product-category"]) ? esc_attr($options["product-category"]) : ''; ?>"
                placeholder="<?php esc_attr_e("product-category", 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        </div>
        <div class="dapfforwc-form-group">
            <label for="dapfforwc_tag_prefix"><?php esc_html_e('Tags', 'dynamic-ajax-product-filters-for-woocommerce'); ?></label>
            <input type="text" id="dapfforwc_tag_prefix" name="dapfforwc_seo_permalinks_options[dapfforwc_permalinks_prefix_options][tag]"
                value="<?php echo isset($options['tag']) ? esc_attr($options['tag']) : ''; ?>"
                placeholder="<?php esc_attr_e('Tags', 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        </div>
        <div class="dapfforwc-form-group">
            <label for="dapfforwc_price_prefix"><?php esc_html_e('Price', 'dynamic-ajax-product-filters-for-woocommerce'); ?></label>
            <input type="text" id="dapfforwc_price_prefix" name="dapfforwc_seo_permalinks_options[dapfforwc_permalinks_prefix_options][price]"
                value="<?php echo isset($options['price']) ? esc_attr($options['price']) : ''; ?>"
                placeholder="<?php esc_attr_e('Price', 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        </div>
        <div class="dapfforwc-form-group">
            <label for="dapfforwc_rating_prefix"><?php esc_html_e('Rating', 'dynamic-ajax-product-filters-for-woocommerce'); ?></label>
            <input type="text" id="dapfforwc_rating_prefix" name="dapfforwc_seo_permalinks_options[dapfforwc_permalinks_prefix_options][rating]"
                value="<?php echo isset($options['rating']) ? esc_attr($options['rating']) : ''; ?>"
                placeholder="<?php esc_attr_e('Rating', 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        </div>
        <?php if (!empty($attributes)) : ?>
            <?php foreach ($attributes as $attribute) : ?>
                <div class="dapfforwc-form-group">
                    <label for="dapfforwc_attribute_prefix_<?php echo esc_attr($attribute->attribute_name); ?>">
                        <?php 
                        // translators: %s is replaced with the attribute label.
                        printf(esc_html__('Attribute (%s)', 'dynamic-ajax-product-filters-for-woocommerce'), esc_html($attribute->attribute_label)); 
                        ?>
                    </label>
                    <input type="text" id="dapfforwc_attribute_prefix_<?php echo esc_attr($attribute->attribute_name); ?>"
                        name="dapfforwc_seo_permalinks_options[dapfforwc_permalinks_prefix_options][attribute][<?php echo esc_attr($attribute->attribute_name); ?>]"
                        value="<?php echo isset($options['attribute'][$attribute->attribute_name]) ? esc_attr($options['attribute'][$attribute->attribute_name]) : ''; ?>"
                        <?php 
                        // translators: %s is replaced with the attribute label.
                        $placeholder_text = sprintf(esc_attr__('Attribute (%s)', 'dynamic-ajax-product-filters-for-woocommerce'), esc_html($attribute->attribute_label)); 
                        ?>
                        placeholder="<?php echo esc_html($placeholder_text); ?>" />
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="dapfforwc-info-message" style="display: none;">
        Enable <b>"Use Attribute Type in Permalinks"</b> to manage the prefix. Currently your URL format is: <code>?filters=compact-design,large,tag1</code>
    </div>
<?php
}

function dapfforwc_seo_title_callback()
{
    ?>
    <div class="dapfforwc-form-group pro-only">
        <input disabled type="text" id="dapfforwc_seo_title" name="_pro[seo_title]"
            placeholder="<?php esc_attr_e('Enter SEO Title', 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        <p class="description">Set a custom SEO title for your permalinks. <code>{site_title} {page_title} {attribute_prefix} {value}</code></p>
    </div>
    <?php
}

function dapfforwc_seo_description_callback()
{
    ?>
    <div class="dapfforwc-form-group pro-only">
        <textarea disabled id="dapfforwc_seo_description" name="_pro[seo_description]" 
            rows="4" 
            style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
            placeholder="<?php esc_attr_e('Enter SEO Description', 'dynamic-ajax-product-filters-for-woocommerce'); ?>"></textarea>
        <p class="description">Set a custom SEO description for your permalinks. <code>{site_title} {page_title} {attribute_prefix} {value}</code></p>
    </div>
    <?php
}

function dapfforwc_seo_keywords_callback()
{
    ?>
    <div class="dapfforwc-form-group pro-only">
        <input type="text" id="dapfforwc_seo_keywords" name="_pro[seo_keywords]"
            placeholder="<?php esc_attr_e('Enter SEO Keywords', 'dynamic-ajax-product-filters-for-woocommerce'); ?>" />
        <p class="description">Set custom SEO keywords for your permalinks. <code>{site_title} {page_title} {attribute_prefix} {value}</code></p>
    </div>
    <?php
}