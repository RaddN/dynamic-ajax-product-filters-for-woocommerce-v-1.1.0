<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="post" action="options.php">
    <?php
    settings_fields('dapfforwc_style_options_group');
    do_settings_sections('dapfforwc-style');

    // Fetch WooCommerce attributes
    $dapfforwc_attributes = wc_get_attribute_taxonomies();
    $dapfforwc_form_styles = get_option('dapfforwc_style_options') ?: [];

    // Define extra options
    $dapfforwc_extra_options = [
        (object) ['attribute_name' => "product-category", 'attribute_label' => __('Category Options', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'tag', 'attribute_label' => __('Tag Options', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'price', 'attribute_label' => __('Price', 'dynamic-ajax-product-filters-for-woocommerce')],
        (object) ['attribute_name' => 'rating', 'attribute_label' => __('Rating', 'dynamic-ajax-product-filters-for-woocommerce')],
    ];

    // Combine attributes and extra options
    $dapfforwc_all_options = array_merge($dapfforwc_attributes, $dapfforwc_extra_options);

    // Get the first attribute for default display
    $dapfforwc_first_attribute = !empty($dapfforwc_all_options) ? $dapfforwc_all_options[0]->attribute_name : '';
    ?>

    <?php if (!empty($dapfforwc_all_options)) : ?>
        <div class="attribute-selection">
            <label for="attribute-dropdown">
                <strong><?php esc_html_e('Configure Style for:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></strong>
            </label>
            <select id="attribute-dropdown" style="margin-bottom: 20px;">
                <?php foreach ($dapfforwc_all_options as $option) : ?>
                    <option value="<?php echo esc_attr($option->attribute_name); ?>">
                        <?php echo esc_html($option->attribute_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Style Options Container -->
        <div id="style-options-container">
            <?php foreach ($dapfforwc_all_options as $option) :
                $dapfforwc_attribute_name = $option->attribute_name;
                $dapfforwc_selected_style = $dapfforwc_form_styles[$dapfforwc_attribute_name]['type'] ?? 'dropdown';
                $dapfforwc_sub_option = $dapfforwc_form_styles[$dapfforwc_attribute_name]['sub_option'] ?? ''; // current stored in database
                global $dapfforwc_sub_options; //get from root page

            ?>
                <div class="style-options" id="options-<?php echo esc_attr($dapfforwc_attribute_name); ?>" style="display: <?php echo $dapfforwc_attribute_name === $dapfforwc_first_attribute && $dapfforwc_attribute_name !== "product-category" ? 'block' : 'none'; ?>;">
                    <h3 class="section-title"><?php echo esc_html($option->attribute_label); ?> Filter Style</h3>

                    <!-- Primary Options -->
                    <div class="primary_options">
                        <?php foreach ($dapfforwc_sub_options as $key => $label) : ?>
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
                        <p class="sub-options-title"><strong>Choose Display Style <span class="required">*</span></strong></p>
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
                        $dapfforwc_terms = [];
                        if ($dapfforwc_attribute_name === "product-category" || $dapfforwc_attribute_name === "tag" || $dapfforwc_attribute_name === "price" || $dapfforwc_attribute_name === "rating") {
                            $dapfforwc_terms = [];
                        } else $dapfforwc_terms = get_terms(['taxonomy' => 'pa_' . $dapfforwc_attribute_name, 'hide_empty' => false]);
                        if ($dapfforwc_attribute_name !== "product-category" || $dapfforwc_attribute_name !== "tag") {
                        ?>
                            <div class="advanced-options <?php echo esc_attr($dapfforwc_attribute_name); ?>" style="display: <?php echo $dapfforwc_selected_style === 'color' || $dapfforwc_selected_style === 'image' ? 'block' : 'none'; ?>;">
                                <h4 class="advanced-title" style="margin: 0;"><?php esc_html_e('Advanced Options for Terms', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                                <?php if (!empty($dapfforwc_terms)) : ?>

                                    <!-- Color Options -->
                                    <div class="color" style="display: <?php echo $dapfforwc_selected_style === 'color' ? 'block' : 'none'; ?>;">
                                        <h5><?php esc_html_e('Set Colors for Terms', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h5>
                                        <div class="color-options">
                                            <?php foreach ($dapfforwc_terms as $term) :
                                                if (is_object($term) && property_exists($term, 'slug')) {
                                                    $dapfforwc_color_value = $dapfforwc_form_styles[$dapfforwc_attribute_name]['colors'][$term->slug]
                                                        ?? dapfforwc_color_name_to_hex(esc_attr($term->slug)); // Fetch stored color or default
                                                } else {
                                                    // Handle the case where $term is not an object or does not have 'slug'
                                                    $dapfforwc_color_value = '#000000'; // Default color or some fallback
                                                }
                                            ?>
                                                <div class="term-option">
                                                    <label for="color-<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                            echo esc_attr($term->slug);
                                                                        } ?>">
                                                        <strong><?php if (is_object($term) && property_exists($term, 'name')) {
                                                                    echo esc_html($term->name);
                                                                } ?></strong>
                                                    </label>
                                                    <input type="color" id="color-<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                                        echo esc_attr($term->slug);
                                                                                    } ?>" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][colors][<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                                                                                                                                    echo esc_attr($term->slug);
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
                                                if (is_object($term) && property_exists($term, 'slug')) {
                                                    $dapfforwc_image_value = $dapfforwc_form_styles[$dapfforwc_attribute_name]['images'][$term->slug] ?? ''; // Fetch stored image URL
                                                } else {
                                                    $dapfforwc_image_value = '';
                                                }

                                            ?>
                                                <div class="term-option">
                                                    <img src="<?php echo esc_attr(isset($dapfforwc_image_value) && !empty($dapfforwc_image_value)  ? $dapfforwc_image_value : plugin_dir_url(__FILE__) . '../assets/images/upload.png'); ?>">
                                                    <label for="image-<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                            echo esc_attr($term->slug);
                                                                        } ?>">
                                                        <strong><?php if (is_object($term) && property_exists($term, 'name')) {
                                                                    echo esc_html($term->name);
                                                                } ?></strong>
                                                    </label>
                                                    <input type="hidden" id="image-<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                                        echo esc_attr($term->slug);
                                                                                    } ?>" name="dapfforwc_style_options[<?php echo esc_attr($dapfforwc_attribute_name); ?>][images][<?php if (is_object($term) && property_exists($term, 'slug')) {
                                                                                                                                                                                    echo esc_attr($term->slug);
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
                                            $cache_file = __DIR__ . '/../includes/min_max_prices_cache.json';
                                            if (file_exists($cache_file)) {
                                                $min_max_prices = json_decode(file_get_contents($cache_file), true);
                                            } else {
                                                $min_max_prices = [];
                                            }
                                            $product_min = isset($dapfforwc_form_styles[$dapfforwc_attribute_name]["min_price"]) ? esc_attr($dapfforwc_form_styles[$dapfforwc_attribute_name]["min_price"]) : 0;
                                            $product_max = isset($dapfforwc_form_styles[$dapfforwc_attribute_name]["max_price"]) ? esc_attr($dapfforwc_form_styles[$dapfforwc_attribute_name]["max_price"]) : $min_max_prices['max'] ?? 0;
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
                                        <div class="setting-item single-selection" style="display: <?php echo $dapfforwc_sub_option === 'select' ? 'none':'block';?> ;">
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