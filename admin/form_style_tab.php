<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="post" action="options.php">
    <?php
    settings_fields('dapfforwc_style_options_group');
    do_settings_sections('dapfforwc-style');
    global $dapfforwc_advance_settings;

    // Fetch WooCommerce attributes
    $all_data = dapfforwc_get_woocommerce_attributes_with_terms();
    $all_cata = isset($all_data['categories']) ? $all_data['categories'] : [];
    $all_tags = isset($all_data['tags']) ? $all_data['tags'] : [];
    $all_attributes = isset($all_data['attributes']) ? $all_data['attributes'] : [];
    $all_brands = isset($all_data['brands']) ? $all_data['brands'] : [];
    $exclude_attributes = isset($dapfforwc_advance_settings['exclude_attributes']) ? explode(',', $dapfforwc_advance_settings['exclude_attributes']) : [];
    $exclude_custom_fields = isset($dapfforwc_advance_settings['exclude_custom_fields']) ? explode(',', $dapfforwc_advance_settings['exclude_custom_fields']) : [];
    $dapfforwc_attributes = [];
    foreach ($all_attributes as $attribute) {
        if (in_array($attribute['attribute_name'], $exclude_attributes)) {
            continue;
        }
        $dapfforwc_attributes[] = (object) [
            'attribute_name' => $attribute['attribute_name'],
            'attribute_label' => $attribute['attribute_label'],
        ];
    }
    $custom_fields = isset($all_data['custom_fields']) ? $all_data['custom_fields'] : [];
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

    $dapfforwc_form_styles = get_option('dapfforwc_style_options') ?: [];

    // Define extra options
    $dapfforwc_extra_options = [
        (object) ['attribute_name' => "product-category", 'attribute_label' => esc_html__('Category Options', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'tag', 'attribute_label' => esc_html__('Tag Options', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'price', 'attribute_label' => esc_html__('Price', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'rating', 'attribute_label' => esc_html__('Rating', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'brands', 'attribute_label' => esc_html__('Brands', 'dynamic-ajax-product-filters-for-woocommerce')],

        (object) ['attribute_name' => 'authors', 'attribute_label' => esc_html__('Authors', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'status', 'attribute_label' => esc_html__('Stock Status', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'sale_status', 'attribute_label' => esc_html__('Sale Status', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'dimensions', 'attribute_label' => esc_html__('Dimensions', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'sku', 'attribute_label' => esc_html__('SKU', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'discount', 'attribute_label' => esc_html__('Discount', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'date_filter', 'attribute_label' => esc_html__('Post Date', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'search', 'attribute_label' => esc_html__('Search Product', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'reset_btn', 'attribute_label' => esc_html__('Reset Button', 'dynamic-ajax-product-filters-for-woocommerce')],
    ];
    $dapfforwc_extra_main_options = [
        (object) ['attribute_name' => "attributes", 'attribute_label' => esc_html__('Attributes', 'dynamic-ajax-product-filters-for-woocommerce')],
        // (object) ['attribute_name' => "custom_fields", 'attribute_label' => esc_html__('Custom Fields', 'dynamic-ajax-product-filters-for-woocommerce')],
    ];



    // Combine attributes and extra options
    $dapfforwc_all_options = array_merge($dapfforwc_attributes, $dapfforwc_extra_options);
    $main_texonomy = array_merge($dapfforwc_extra_options, $dapfforwc_extra_main_options);

    // Get the first attribute for default display
    $dapfforwc_first_attribute = !empty($dapfforwc_all_options) ? $dapfforwc_all_options[0]->attribute_name : '';
    ?>

    <?php if (!empty($dapfforwc_all_options)) : ?>
        <div class="attribute-selection">
            <label for="attribute-dropdown">
                <strong><?php esc_html_e('Configure Style for:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong>
            </label>
            <div style="display: flex; gap:10px">
                <select id="main-texonomy-dropdown" style="margin-bottom: 20px;">
                    <?php foreach ($main_texonomy as $option) : ?>
                        <option value="<?php echo esc_attr($option->attribute_name); ?>">
                            <?php echo esc_html($option->attribute_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="child-attr-dropdown" style="margin-bottom: 20px; display:none;">
                    <?php foreach ($dapfforwc_attributes as $option) : ?>
                        <option value="<?php echo esc_attr($option->attribute_name); ?>">
                            <?php echo esc_html($option->attribute_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="child-custom-dropdown" style="margin-bottom: 20px; display:none;">
                    <?php foreach ($all_custom_fields as $option) : ?>
                        <option value="<?php echo esc_attr($option->attribute_name); ?>">
                            <?php echo esc_html($option->attribute_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <select hidden id="attribute-dropdown" style="margin-bottom: 20px;">
                <?php foreach ($dapfforwc_all_options as $option) : ?>
                    <option value="<?php echo esc_attr($option->attribute_name); ?>">
                        <?php echo esc_html($option->attribute_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mainTaxonomyDropdown = document.getElementById('main-texonomy-dropdown');
                const childAttrDropdown = document.getElementById('child-attr-dropdown');
                const childCustomDropdown = document.getElementById('child-custom-dropdown');
                const attributeDropdown = document.getElementById('attribute-dropdown');
                const style_container = document.getElementById('style-options-container');

                mainTaxonomyDropdown.addEventListener('change', function() {
                    const selectedValue = this.value;

                    // Hide both child dropdowns initially
                    childAttrDropdown.style.display = 'none';
                    childCustomDropdown.style.display = 'none';

                    if (selectedValue === 'attributes') {
                        childAttrDropdown.style.display = 'block';
                        let selectedattr = childAttrDropdown.value;
                        if (!selectedattr) {
                            style_container.style.display = 'none';
                        } else {
                            updateAttributeDropdown(selectedattr);
                        }
                    } else if (selectedValue === 'custom_fields') {
                        childCustomDropdown.style.display = 'block';
                        let selectedcus = childCustomDropdown.value;
                        if (!selectedcus) {
                            style_container.style.display = 'none';
                        } else {
                            updateAttributeDropdown(selectedcus);
                        }
                    } else {
                        // Trigger changes in attribute-dropdown
                        updateAttributeDropdown(selectedValue);
                    }
                });

                childAttrDropdown.addEventListener('change', function() {
                    style_container.style.display = 'block';
                    updateAttributeDropdown(this.value);
                });

                childCustomDropdown.addEventListener('change', function() {
                    style_container.style.display = 'block';
                    updateAttributeDropdown(this.value);
                });

                function updateAttributeDropdown(selectedValue) {
                    // Set the attribute-dropdown to the selected value
                    attributeDropdown.value = selectedValue;

                    // Trigger a change event on the attribute-dropdown
                    const event = new Event('change', {
                        bubbles: true
                    });
                    attributeDropdown.dispatchEvent(event);
                }
            });
        </script>

        <!-- Style Options Container -->
        <div id="style-options-container">
            <?php foreach ($dapfforwc_all_options as $option) :
                $dapfforwc_attribute_name = $option->attribute_name;
                if (
                    $dapfforwc_attribute_name === 'authors' ||
                    $dapfforwc_attribute_name === 'status' ||
                    $dapfforwc_attribute_name === 'sale_status' ||
                    $dapfforwc_attribute_name === 'dimensions' ||
                    $dapfforwc_attribute_name === 'sku' ||
                    $dapfforwc_attribute_name === 'discount' ||
                    $dapfforwc_attribute_name === 'date_filter' ||
                    $dapfforwc_attribute_name === 'search' ||
                    $dapfforwc_attribute_name === 'reset_btn'
                ) { ?>
                    <div class="style-options" id="options-<?php echo esc_attr($dapfforwc_attribute_name); ?>" style="display: <?php echo $dapfforwc_attribute_name === $dapfforwc_first_attribute && $dapfforwc_attribute_name !== "product-category" ? 'block' : 'none'; ?>;">
                        <div class="optional_settings">
                            <h4><?php esc_html_e('Optional Settings:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                            <?php if ($dapfforwc_attribute_name !== 'reset_btn') { ?>
                                <div class="row" style="padding-top: 16px;">
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <p><strong><?php esc_html_e('Widget Title:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <?php $widget_title = isset($dapfforwc_form_styles["widget_title"][$dapfforwc_attribute_name]) ? esc_attr($dapfforwc_form_styles["widget_title"][$dapfforwc_attribute_name]) : ''; ?>
                                                <input type="text" name="dapfforwc_style_options[widget_title][<?php echo esc_attr($dapfforwc_attribute_name); ?>]" value="<?php echo esc_attr($widget_title); ?>">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            if ($dapfforwc_attribute_name === 'search') { ?>
                                <div class="row" style="padding-top: 16px;">
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <p><strong><?php esc_html_e('Placeholder:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <?php $placeholder = isset($dapfforwc_form_styles["placeholder"][$dapfforwc_attribute_name]) ? esc_attr($dapfforwc_form_styles["placeholder"][$dapfforwc_attribute_name]) : ''; ?>
                                                <input type="text" name="dapfforwc_style_options[placeholder][<?php echo esc_attr($dapfforwc_attribute_name); ?>]" value="<?php echo esc_attr($placeholder); ?>">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            if ($dapfforwc_attribute_name === 'search' || $dapfforwc_attribute_name === 'reset_btn') { ?>
                                <div class="row" style="padding-top: 16px;">
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <p><strong><?php esc_html_e('Button Text:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <?php $btntext = isset($dapfforwc_form_styles["btntext"][$dapfforwc_attribute_name]) ? esc_attr($dapfforwc_form_styles["btntext"][$dapfforwc_attribute_name]) : ''; ?>
                                                <input type="text" name="dapfforwc_style_options[btntext][<?php echo esc_attr($dapfforwc_attribute_name); ?>]" value="<?php echo esc_attr($btntext); ?>">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                <?php
                    continue; // Skip custom fields in this loop
                }
                $dapfforwc_selected_style = $dapfforwc_form_styles[$dapfforwc_attribute_name]['type'] ?? 'dropdown';
                $dapfforwc_sub_option = $dapfforwc_form_styles[$dapfforwc_attribute_name]['sub_option'] ?? ''; // current stored in database
                global $dapfforwc_sub_options; //get from root page

                ?>
                <div class="style-options" id="options-<?php echo esc_attr($dapfforwc_attribute_name); ?>" style="display: <?php echo $dapfforwc_attribute_name === $dapfforwc_first_attribute && $dapfforwc_attribute_name !== "product-category" ? 'block' : 'none'; ?>;">
                    <h3 class="section-title"><?php echo esc_html($option->attribute_label); ?> <?php echo esc_html__('Filter Style', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>

                    <!-- Primary Options -->
                    <div class="primary_options">
                        <?php foreach ($dapfforwc_sub_options as $key => $label) :
                            if ($dapfforwc_attribute_name === 'brands' && ($key === "plugincy_color" || $key === "price" || $key === "rating")) {
                                continue;
                            }
                        ?>
                            <label class="<?php echo esc_attr($key);
                                            echo $dapfforwc_selected_style === $key ? ' active' : ''; ?>" style="display:<?php echo $key === 'price' || $key === 'rating' ? 'none' : 'block'; ?>;">
                                <span class="active" style="display:none;">
                                </span>
                                <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][type]" value="<?php echo esc_html($key); ?>" <?php checked($dapfforwc_selected_style, $key); ?> data-type="<?php echo esc_html($key); ?>">
                                <img src="<?php echo esc_url(plugins_url('../assets/images/' . $key . '.png', __FILE__)); ?>" alt="<?php echo esc_attr($key); ?>">
                                <!-- <div class="title"> -->
                                <?php
                                // echo esc_html($key); 
                                ?>
                                <!-- </div> -->
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Sub-Options -->
                    <div class="sub-options">
                        <p class="sub-options-title"><strong><?php echo esc_html__('Choose Display Style', 'dynamic-ajax-product-filters-for-woocommerce'); ?> <span class="required">*</span></strong></p>
                        <div class="dynamic-sub-options">
                            <?php foreach ($dapfforwc_sub_options[$dapfforwc_selected_style] as $key => $label) : ?>

                                <label class="<?php echo $dapfforwc_sub_option === $key ? 'active ' : '';
                                                echo esc_attr($key); ?> <?php if ($key === "dynamic-rating" || $key === "input-price-range" || $key === "color_circle" || $key === "color_value" || $key === "button_check") {
                                                                            echo "pro-only";
                                                                        } ?>">
                                    <span class="active" style="display:none;">
                                    </span>
                                    <input <?php if ($key === "dynamic-rating" || $key === "input-price-range" || $key === "color_circle" || $key === "color_value" || $key === "button_check") {
                                                echo "disabled";
                                            } ?> type="radio" class="optionselect" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][sub_option]" value="<?php echo esc_attr($key); ?>" <?php checked($dapfforwc_sub_option, $key); ?>>
                                    <img src="<?php echo esc_url(plugins_url('../assets/images/' . $key . '.png', __FILE__)); ?>" alt="<?php echo esc_attr($label); ?>">
                                    <!-- <div class="title"> -->
                                    <?php
                                    // echo esc_html($label); 
                                    ?>
                                    <!-- </div> -->
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Advanced Options for Color/Image -->
                    <div style="margin-bottom: 16px;">
                        <?php
                        // Determine terms based on attribute type
                        $dapfforwc_terms = [];
                        if ($dapfforwc_attribute_name === "price" || $dapfforwc_attribute_name === "rating") {
                            $dapfforwc_terms = [];
                        } elseif ($dapfforwc_attribute_name === "product-category") {
                            $dapfforwc_terms = $all_cata;
                        } elseif ($dapfforwc_attribute_name === "tag") {
                            $dapfforwc_terms = $all_tags;
                        } elseif (isset($all_attributes[$dapfforwc_attribute_name]["terms"])) {
                            // For WooCommerce attributes
                            $dapfforwc_terms = $all_attributes[$dapfforwc_attribute_name]["terms"];
                        } elseif (isset($custom_fields[$dapfforwc_attribute_name])) {
                            // For custom fields - you might need to define how custom field terms are structured
                            $dapfforwc_terms = $custom_fields[$dapfforwc_attribute_name]["terms"] ?? [];
                        } elseif ($dapfforwc_attribute_name === "brands") {
                            foreach ($all_brands as $brand) {
                                $dapfforwc_terms[] = $brand;
                            }
                        } else {
                            $dapfforwc_terms = [];
                        }
                        if ($dapfforwc_attribute_name !== "product-category" && $dapfforwc_attribute_name !== "tag") {
                        ?>
                            <div class="advanced-options <?php echo esc_attr($dapfforwc_attribute_name); ?>" style="display: <?php echo $dapfforwc_selected_style === 'plugincy_color' || $dapfforwc_selected_style === 'image' ? 'block' : 'none'; ?>;">
                                <h4 class="advanced-title" style="margin: 0;"><?php esc_html_e('Advanced Options for Terms', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                                <?php if (!empty($dapfforwc_terms)) : ?>

                                    <!-- Color Options -->
                                    <div class="plugincy_color" style="display: <?php echo $dapfforwc_selected_style === 'plugincy_color' ? 'block' : 'none'; ?>;">
                                        <h5><?php esc_html_e('Set Colors for Terms', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h5>
                                        <div class="color-options">
                                            <?php foreach ($dapfforwc_terms as $term) :
                                                if (isset($term['slug'])) {
                                                    $dapfforwc_color_value = $dapfforwc_form_styles[$dapfforwc_attribute_name]['colors'][$term["slug"]]
                                                        ?? dapfforwc_color_name_to_hex(esc_attr($term["slug"])); // Fetch stored color or default
                                                } else {
                                                    // Handle the case where $term is not an object or does not have 'slug'
                                                    $dapfforwc_color_value = '#000000'; // Default color or some fallback
                                                }
                                            ?>
                                                <div class="term-option">
                                                    <label for="color-<?php if (isset($term['slug'])) {
                                                                            echo esc_attr($term['slug']);
                                                                        } ?>">
                                                        <strong><?php if (isset($term['name'])) {
                                                                    echo esc_html($term['name']);
                                                                } ?></strong>
                                                    </label>
                                                    <input type="color" id="color-<?php if (isset($term['slug'])) {
                                                                                        echo esc_attr($term['slug']);
                                                                                    } ?>" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][colors][<?php if (isset($term['slug'])) {
                                                                                                                                                                                        echo esc_attr($term['slug']);
                                                                                                                                                                                    } ?>]" value="<?php echo esc_attr($dapfforwc_color_value); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- Image Options -->
                                    <div class="image" style="display: <?php echo $dapfforwc_selected_style === 'image' ? 'block' : 'none'; ?>;">
                                        <h5><?php esc_html_e('Set Images for Terms', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h5>
                                        <div class="image-options">
                                            <?php foreach ($dapfforwc_terms as $term) :
                                                if (isset($term['slug'])) {
                                                    $brand_image_url = dapfforwc_get_wc_brand_image_by_slug($term['slug']);
                                                    $dapfforwc_image_value = isset($brand_image_url) && !empty($brand_image_url) ? $brand_image_url : $dapfforwc_form_styles[$dapfforwc_attribute_name]['images'][$term['slug']] ?? ''; // Fetch stored image URL
                                                } else {
                                                    $dapfforwc_image_value = '';
                                                }

                                            ?>
                                                <div class="term-option">
                                                    <img src="<?php echo esc_attr(isset($dapfforwc_image_value) && !empty($dapfforwc_image_value)  ? $dapfforwc_image_value : plugin_dir_url(__FILE__) . '../assets/images/upload.png'); ?>" style=" max-width: 170px; ">
                                                    <label for="image-<?php if (isset($term['slug'])) {
                                                                            echo esc_attr($term['slug']);
                                                                        } ?>">
                                                        <strong><?php if (isset($term['name'])) {
                                                                    echo esc_html($term['name']);
                                                                } ?></strong>
                                                    </label>
                                                    <input type="hidden" id="image-<?php if (isset($term['slug'])) {
                                                                                        echo esc_attr($term['slug']);
                                                                                    } ?>" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][images][<?php if (isset($term['slug'])) {
                                                                                                                                                                                        echo esc_attr($term['slug']);
                                                                                                                                                                                    } ?>]" value="<?php echo esc_attr($dapfforwc_image_value); ?>" placeholder="<?php esc_attr_e('Image URL', 'dynamic-ajax-product-filters-for-woocommerce'); ?>">
                                                    <button type="button" class="upload-image-button">
                                                        <svg class="edit-icon" viewBox="0 0 24 24">
                                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                <?php else : ?>
                                    <p><?php esc_html_e('No terms found. Please create terms for this attribute first.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php } ?>

                        <!-- Advanced Options for Color/Image Ends -->

                        <!-- Optional Settings -->
                        <div class="optional_settings">
                            <h4><?php esc_html_e('Optional Settings:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>

                            <div class="row" style="padding-top: 16px;">
                                <div class="col-6">
                                    <!-- Hierarchical -->
                                    <div class="setting-item hierarchical" style="display:none;">
                                        <p><strong><?php esc_html_e('Enable Hierarchical:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                        <label>
                                            <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][hierarchical][type]" value="disabled"
                                                <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['hierarchical']['type'] ?? '', 'disabled'); ?>>
                                            <?php esc_html_e('Disabled', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][hierarchical][type]" value="enable"
                                                <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['hierarchical']['type'] ?? '', 'enable'); ?>>
                                            <?php esc_html_e('Enabled', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </label>
                                        <label class="pro-only">
                                            <input disabled type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][hierarchical][type]" value="enable_separate"
                                                <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['hierarchical']['type'] ?? '', 'enable_separate'); ?>>
                                            <?php esc_html_e('Enabled & Seperate', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][hierarchical][type]" value="enable_hide_child"
                                                <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['hierarchical']['type'] ?? '', 'enable_hide_child'); ?>>
                                            <?php esc_html_e('Enabled & hide child', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </label>
                                    </div>
                                    <?php if ($dapfforwc_attribute_name === "price") { ?>
                                        <div class="setting-item min-max-price-set" style="display:none;">
                                            <?php
                                            $product_min = isset($dapfforwc_form_styles[$dapfforwc_attribute_name]["min_price"]) ? esc_attr($dapfforwc_form_styles[$dapfforwc_attribute_name]["min_price"]) : 0;
                                            $product_max = isset($dapfforwc_form_styles[$dapfforwc_attribute_name]["max_price"]) ? esc_attr($dapfforwc_form_styles[$dapfforwc_attribute_name]["max_price"]) : 100000;
                                            ?>
                                            <p><strong><?php esc_html_e('Set Min & Max Price:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <p>Auto Set <label id="auto_price" class="switch auto_price">
                                                    <input type="checkbox" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][auto_price]" <?php echo isset($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['auto_price']) && $dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['auto_price'] == "on" ? 'checked' : ''; ?>>
                                                    <span class="slider round"></span>
                                                </label></p>
                                            <div id="price_set" style="display:<?php echo isset($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['auto_price']) && $dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['auto_price'] === "on" ? 'none' : 'block'; ?>;">
                                                <label for="min_price"> Min Price </label>
                                                <input disabled type="number" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][min_price]" value="0">
                                                <label for="max_price"> Max Price </label>
                                                <input type="number" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][max_price]" value="<?php echo esc_attr($product_max); ?>">
                                            </div>
                                        </div>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Find all auto_price checkboxes in the current attribute section
                                                document.querySelectorAll('.auto_price input[type="checkbox"]').forEach(function(checkbox) {
                                                    checkbox.addEventListener('change', function() {
                                                        var priceSet = this.closest('.min-max-price-set').querySelector('#price_set');
                                                        if (this.checked) {
                                                            priceSet.style.display = 'none';
                                                        } else {
                                                            priceSet.style.display = 'block';
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                    <?php } ?>

                                    <!-- Enable Minimization Option -->
                                    <?php if ($dapfforwc_attribute_name !== "price") { ?>
                                        <div class="setting-item">
                                            <p><strong><?php esc_html_e('Enable Minimization Option:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][minimize][type]" value="disabled"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['minimize']['type'] ?? 'arrow', 'disabled'); ?>>
                                                <?php esc_html_e('Disabled', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][minimize][type]" value="arrow"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['minimize']['type'] ?? 'arrow', 'arrow'); ?>>
                                                <?php esc_html_e('Enabled with Arrow', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][minimize][type]" value="no_arrow"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['minimize']['type'] ?? 'arrow', 'no_arrow'); ?>>
                                                <?php esc_html_e('Enabled without Arrow', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][minimize][type]" value="minimize_initial"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['minimize']['type'] ?? 'arrow', 'minimize_initial'); ?>>
                                                <?php esc_html_e('Initially Minimized', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                        </div>


                                        <!-- Single Selection Option -->
                                        <div class="setting-item single-selection" style="display: <?php echo $dapfforwc_sub_option === 'select' ? 'none' : 'block'; ?> ;">
                                            <p><strong><?php esc_html_e('Single Selection:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <input type="checkbox" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][single_selection]" value="yes"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['single_selection'] ?? '', 'yes'); ?>>
                                                <?php esc_html_e('Only one value can be selected at a time', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                        </div>

                                        <!-- Show/Hide Number of Products -->
                                        <div class="setting-item show-product-count">
                                            <p><strong><?php esc_html_e('Show/Hide Number of Products:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <input type="checkbox" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][show_product_count]" value="yes"
                                                    <?php checked($dapfforwc_form_styles[esc_attr($dapfforwc_attribute_name)]['show_product_count'] ?? '', 'yes'); ?>>
                                                <?php esc_html_e('Show number of products', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <!-- Max Height -->
                                    <?php if ($dapfforwc_attribute_name !== "price") { ?>
                                        <div class="setting-item">
                                            <p><strong><?php esc_html_e('Max Height:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                            <label>
                                                <?php $max_height = isset($dapfforwc_form_styles["max_height"][$dapfforwc_attribute_name]) ? esc_attr($dapfforwc_form_styles["max_height"][$dapfforwc_attribute_name]) : 0; ?>
                                                <input type="number" name="dapfforwc_style_options[max_height][<?php echo esc_attr($dapfforwc_attribute_name); ?>]" value="<?php echo esc_attr($max_height); ?>">
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <div class="setting-item">
                                        <p><strong><?php esc_html_e('Widget Title:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong></p>
                                        <label>
                                            <?php $widget_title = isset($dapfforwc_form_styles["widget_title"][$dapfforwc_attribute_name]) ? esc_attr($dapfforwc_form_styles["widget_title"][$dapfforwc_attribute_name]) : ''; ?>
                                            <input type="text" name="dapfforwc_style_options[widget_title][<?php echo esc_attr($dapfforwc_attribute_name); ?>]" value="<?php echo esc_attr($widget_title); ?>">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- optional ends -->
                    </div>


                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p><?php esc_html_e('No attributes found. Please create attributes in WooCommerce first.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
    <?php endif; ?>
    <?php submit_button(); ?>
</form>