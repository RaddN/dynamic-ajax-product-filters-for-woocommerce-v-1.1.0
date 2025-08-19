<?php

// form_template_tab.php

if (!defined('ABSPATH')) {
    exit;
}


global $template_options, $allowed_tags;
?>

<div class="dapfforwc-template-container">
    <div class="dapfforwc-template-header">
        <h2><?php echo esc_html__('Form Templates', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
        <p class="description"><?php echo esc_html__('Choose a template design and customize colors to match your store\'s branding.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
    </div>

    <!-- Loading overlay -->
    <div class="dapfforwc-loading-overlay" style="display: none;">
        <div class="dapfforwc-spinner"></div>
        <p>Activating template...</p>
    </div>

    <div class="dapfforwc-templates-grid">
        <?php
        // Define available templates with enhanced SVG previews
        $templates = [
            [
                'id' => 'clean',
                'name' => esc_html__('Minimal Template', 'dynamic-ajax-product-filters-for-woocommerce'),
                'type' => 'free',
                'image' => '<svg width="200" height="150" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="150" fill="#fff" stroke="#e2e8f0"/>
            <text x="20" y="30" font-family="Arial" font-size="12" font-weight="600" fill="#2d3748">Category</text>
            <line x1="20" y1="38" x2="180" y2="38" stroke="#e2e8f0"/>
            <rect x="20" y="50" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="38" y="59" font-family="Arial" font-size="11" fill="#4a5568">Laptop</text>
            <rect x="20" y="70" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="38" y="79" font-family="Arial" font-size="11" fill="#4a5568">MacBook</text>
            <rect x="20" y="90" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="38" y="99" font-family="Arial" font-size="11" fill="#4a5568">Desktop</text>
            <rect x="20" y="110" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="38" y="119" font-family="Arial" font-size="11" fill="#4a5568">Linux</text>
        </svg>',
                'description' => esc_html__('Simple, No border, no shadow.', 'dynamic-ajax-product-filters-for-woocommerce'),
                'features' => ['No box shadow', 'No borders', 'Lightweight']
            ],
            [
                'id' => 'shadow',
                'name' => esc_html__('Elevated Template', 'dynamic-ajax-product-filters-for-woocommerce'),
                'type' => 'free',
                'image' => '<svg width="200" height="150" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="ds"><feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000" flood-opacity="0.15"/></filter>
            </defs>
            <rect x="14" y="12" width="172" height="126" rx="8" fill="#fff" stroke="#e2e8f0" filter="url(#ds)"/>
            <text x="30" y="42" font-family="Arial" font-size="12" font-weight="600" fill="#2d3748">Category</text>
            <line x1="30" y1="48" x2="170" y2="48" stroke="#e2e8f0"/>
            <rect x="30" y="62" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="48" y="71" font-family="Arial" font-size="11" fill="#4a5568">Laptop</text>
            <rect x="30" y="82" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="48" y="91" font-family="Arial" font-size="11" fill="#4a5568">MacBook</text>
            <rect x="30" y="102" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="48" y="111" font-family="Arial" font-size="11" fill="#4a5568">Desktop</text>
            <rect x="30" y="122" width="10" height="10" fill="none" stroke="#cbd5e0"/>
            <text x="48" y="131" font-family="Arial" font-size="11" fill="#4a5568">Linux</text>
        </svg>',
                'description' => esc_html__('Card UI with subtle box shadow (matches provided reference).', 'dynamic-ajax-product-filters-for-woocommerce'),
                'features' => ['Box shadow card', 'Soft border', 'Compact spacing']
            ]
        ];

        foreach ($templates as $template) {
            $is_active = ($template['id'] === $template_options['active_template']);
            $template_type = isset($template['type']) ? $template['type'] : 'free';
        ?>
            <div class="dapfforwc-template-card <?php echo $is_active ? 'active' : ''; ?>" data-template-id="<?php echo esc_attr($template['id']); ?>">
                <div class="dapfforwc-template-image">
                    <?php echo wp_kses($template['image'], $allowed_tags); ?>
                    <div class="dapfforwc-template-status">
                        <?php if ($is_active) : ?>
                            <span class="active-badge"><?php esc_html_e('Active', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        <?php else : ?>
                            <span class="inactive-badge"><?php esc_html_e('Inactive', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="dapfforwc-template-type">
                        <?php if ($template_type === 'free') : ?>
                            <span class="free-badge"><?php esc_html_e('Free', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        <?php else : ?>
                            <span class="paid-badge"><?php esc_html_e('Premium', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="dapfforwc-template-overlay">
                        <button class="preview-template-btn" data-template="<?php echo esc_attr($template['id']); ?>">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e('Quick Preview', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </button>
                    </div>
                </div>
                <div class="dapfforwc-template-content">
                    <h3><?php echo esc_html($template['name']); ?></h3>
                    <p><?php echo esc_html($template['description']); ?></p>

                    <?php if (!empty($template['features'])) : ?>
                        <ul class="template-features">
                            <?php foreach ($template['features'] as $feature) : ?>
                                <li><span class="dashicons dashicons-yes-alt"></span> <?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="dapfforwc-template-actions">
                        <?php if (!$is_active) : ?>
                            <button class="button button-primary activate-template" data-template="<?php echo esc_attr($template['id']); ?>">
                                <span class="button-text"><?php esc_html_e('Activate', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                                <span class="button-spinner" style="display: none;">
                                    <span class="spinner is-active"></span>
                                </span>
                            </button>
                        <?php else : ?>
                            <span class="current-template">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php esc_html_e('Activated', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </span>
                        <?php endif; ?>
                        <button class="button preview-template" data-template="<?php echo esc_attr($template['id']); ?>">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e('Preview', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <!-- Coming soon card -->
        <div class="dapfforwc-template-card coming-soon" aria-hidden="true">
            <div class="dapfforwc-template-image" style="background:#f8f9fa;">
                <div style="text-align:center;">
                    <svg width="200" height="120" xmlns="http://www.w3.org/2000/svg">
                        <rect x="10" y="20" width="180" height="90" rx="10" fill="#fff" stroke="#e2e8f0" stroke-dasharray="6,6" />
                        <text x="100" y="70" text-anchor="middle" font-family="Arial" font-size="12" fill="#a0aec0">
                            <?php echo esc_html__('More templates coming soon', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </text>
                    </svg>
                </div>
            </div>
            <div class="dapfforwc-template-content">
                <h3><?php echo esc_html__('More coming soon', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                <p><?php echo esc_html__('We’re crafting additional styles and layouts. Stay tuned!', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
            </div>
        </div>
    </div>

    <div class="dapfforwc-color-settings">
        <h2><?php esc_html_e('Color Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('dapfforwc_template_options_group'); ?>
            <input type="hidden" name="dapfforwc_template_options[active_template]" value="<?php echo esc_attr($template_options['active_template']); ?>">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Background Color', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="text"
                                name="dapfforwc_template_options[background_color]"
                                value="<?php echo esc_attr($template_options['background_color'] ?? '#ffffffb3'); ?>"
                                class="dapfforwc-color-picker"
                                id="dapfforwc-bg-color"
                                data-alpha="true"
                                data-default-color="#ffffffb3"
                                style="width:160px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Primary Color', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                    <td>
                        <input type="text"
                            name="dapfforwc_template_options[primary_color]"
                            value="<?php echo esc_attr($template_options['primary_color'] ?? '#432fb8'); ?>"
                            class="dapfforwc-color-picker"
                            data-alpha="true"
                            data-default-color="#432fb8"
                            style="width:160px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Secondary Color', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                    <td>
                        <input type="text"
                            name="dapfforwc_template_options[secondary_color]"
                            value="<?php echo esc_attr($template_options['secondary_color'] ?? '#ff4d4d'); ?>"
                            class="dapfforwc-color-picker"
                            data-alpha="true"
                            data-default-color="#ff4d4d"
                            style="width:160px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Border Color', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                    <td>
                        <input type="text"
                            name="dapfforwc_template_options[border_color]"
                            value="<?php echo esc_attr($template_options['border_color'] ?? '#eeeeee'); ?>"
                            class="dapfforwc-color-picker"
                            data-alpha="true"
                            data-default-color="#eeeeee"
                            style="width:160px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Text Color', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                    <td>
                        <input type="text"
                            name="dapfforwc_template_options[text_color]"
                            value="<?php echo esc_attr($template_options['text_color'] ?? '#000000'); ?>"
                            class="dapfforwc-color-picker"
                            data-alpha="true"
                            data-default-color="#000000"
                            style="width:160px;">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <script>
            jQuery(document).ready(function($) {
                // Initialize color pickers with alpha support
                $('.dapfforwc-color-picker').each(function() {
                    var $input = $(this);
                    var defaultColor = $input.data('default-color');

                    $input.wpColorPicker({
                        defaultColor: defaultColor,
                        change: function(event, ui) {
                            // Update value when color changes
                            $input.val(ui.color.toString());
                        },
                        clear: function() {
                            // Reset to default color
                            $input.val(defaultColor);
                        },
                        palettes: true
                    });
                });
            });
        </script>
    </div>

</div>

<!-- Preview Modal -->
<div id="dapfforwc-preview-modal" class="dapfforwc-modal" style="display: none;">
    <div class="dapfforwc-modal-backdrop"></div>
    <div class="dapfforwc-modal-content">
        <div class="dapfforwc-modal-header">
            <h3><?php esc_html_e('Template Preview', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
            <button class="dapfforwc-modal-close">&times;</button>
        </div>
        <div class="dapfforwc-modal-body">
            <div class="preview-container">
                <div class="preview-description">
                    <h4 id="preview-template-name"></h4>
                    <p id="preview-template-description"></p>
                    <div class="preview-features" id="preview-features"></div>
                </div>
                <div class="preview-demo">
                    <div class="demo-filter-widget" id="demo-widget">
                        <!-- Dynamic content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="dapfforwc-modal-footer">
            <button class="button button-primary" id="activate-from-preview"><?php esc_html_e('Activate This Template', 'dynamic-ajax-product-filters-for-woocommerce'); ?></button>
            <button class="button" id="close-preview"><?php esc_html_e('Close Preview', 'dynamic-ajax-product-filters-for-woocommerce'); ?></button>
        </div>
    </div>
</div>

<style>
    .dapfforwc-template-container {
        padding: 20px 0;
        position: relative;
    }

    .dapfforwc-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        z-index: 10000;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .dapfforwc-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 10px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .dapfforwc-templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        padding: 30px 0;
    }

    .dapfforwc-template-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .dapfforwc-template-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .dapfforwc-template-card.active {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    .dapfforwc-template-image {
        position: relative;
        height: 180px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dapfforwc-template-image svg {
        transition: transform 0.3s ease;
    }

    .dapfforwc-template-card:hover .dapfforwc-template-image svg {
        transform: scale(1.05);
    }

    .dapfforwc-template-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .dapfforwc-template-card:hover .dapfforwc-template-overlay {
        opacity: 1;
    }

    .preview-template-btn {
        background: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        color: #333;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }

    .preview-template-btn:hover {
        background: #667eea;
        color: #fff;
        transform: scale(1.05);
    }

    .dapfforwc-template-status {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }

    .active-badge,
    .inactive-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .active-badge {
        background: #667eea;
        color: #fff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .inactive-badge {
        background: rgba(255, 255, 255, 0.9);
        color: #718096;
        border: 1px solid #e2e8f0;
    }

    .dapfforwc-template-type {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 2;
    }

    .free-badge,
    .paid-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .free-badge {
        background: #48bb78;
        color: #fff;
        box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
    }

    .paid-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }


    .dapfforwc-template-content {
        padding: 25px;
    }

    .dapfforwc-template-content h3 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #2d3748;
        font-weight: 600;
    }

    .dapfforwc-template-content p {
        margin: 0 0 15px;
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
    }

    .template-features {
        margin: 15px 0;
        padding: 0;
        list-style: none;
    }

    .template-features li {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
        font-size: 13px;
        color: #4a5568;
    }

    .template-features .dashicons {
        color: #667eea;
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    .dapfforwc-template-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .dapfforwc-template-actions .button {
        flex: 1;
        text-align: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .current-template {
        flex: 1;
        padding: 8px 12px;
        background: #f0fff4;
        border: 1px solid #9ae6b4;
        border-radius: 6px;
        color: #276749;
        text-align: center;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .current-template .dashicons {
        color: #48bb78;
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    /* Modal Styles */
    .dapfforwc-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 100000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dapfforwc-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .dapfforwc-modal-content {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .dapfforwc-modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dapfforwc-modal-header h3 {
        margin: 0;
        font-size: 20px;
        color: #2d3748;
    }

    .dapfforwc-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #718096;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .dapfforwc-modal-close:hover {
        background: #f7fafc;
        color: #2d3748;
    }

    .dapfforwc-modal-body {
        padding: 25px;
        max-height: 60vh;
        overflow-y: auto;
    }

    .preview-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: start;
    }

    .preview-description h4 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #2d3748;
    }

    .preview-description p {
        margin: 0 0 20px;
        color: #718096;
        line-height: 1.6;
    }

    .preview-features {
        background: #f7fafc;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .preview-features h5 {
        margin: 0 0 10px;
        font-size: 14px;
        color: #2d3748;
        font-weight: 600;
    }

    .preview-features ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .preview-features li {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 13px;
        color: #4a5568;
    }

    .preview-features .dashicons {
        color: #667eea;
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    .demo-filter-widget {
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        min-height: 300px;
    }

    .dapfforwc-modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f7fafc;
    }

    .dapfforwc-color-settings {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .dapfforwc-color-settings h2 {
        margin-top: 0;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #2d3748;
    }

    .dapfforwc-color-picker {
        width: 60px;
        height: 40px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
    }

    /* Loading states */
    .activate-template.loading .button-text {
        opacity: 0;
    }

    .activate-template.loading .button-spinner {
        display: block !important;
    }

    .activate-template.loading {
        pointer-events: none;
    }

    /* Success animation */
    @keyframes successPulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .dapfforwc-template-card.just-activated {
        animation: successPulse 0.6s ease-in-out;
    }

    .dapfforwc-template-card.coming-soon .dapfforwc-template-image {
        opacity: .85;
    }

    .dapfforwc-template-card.coming-soon .dapfforwc-template-actions {
        display: none;
    }


    /* Responsive */
    @media (max-width: 768px) {
        .dapfforwc-templates-grid {
            grid-template-columns: 1fr;
        }

        .preview-container {
            grid-template-columns: 1fr;
        }

        .dapfforwc-modal-content {
            width: 95%;
            margin: 10px;
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Template data for previews
        const templateData = <?php echo json_encode($templates); ?>;

        // Handle template activation
        $('.activate-template').on('click', function() {
            var $button = $(this);
            var $card = $button.closest('.dapfforwc-template-card');
            var templateId = $button.data('template');

            // Show loading state
            $button.addClass('loading');
            $('.dapfforwc-loading-overlay').fadeIn(200);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dapfforwc_activate_template',
                    template_id: templateId,
                    nonce: '<?php echo esc_js(wp_create_nonce("dapfforwc_template_nonce")); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI immediately without page reload
                        updateActiveTemplate(templateId);

                        // Show success message
                        showNotification('success', response.data);

                        // Add success animation
                        $card.addClass('just-activated');
                        setTimeout(() => $card.removeClass('just-activated'), 600);

                    } else {
                        showNotification('error', response.data || '<?php esc_html_e("Error activating template. Please try again.", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
                    }
                },
                error: function() {
                    showNotification('error', '<?php esc_html_e("Network error. Please try again.", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
                },
                complete: function() {
                    $button.removeClass('loading');
                    $('.dapfforwc-loading-overlay').fadeOut(200);
                }
            });
        });

        // Update Activated in UI
        // In form_template_tab.php, update the updateActiveTemplate function
        function updateActiveTemplate(templateId) {
            // Remove active state from all cards
            $('.dapfforwc-template-card').removeClass('active');
            $('.active-badge').removeClass('active-badge').addClass('inactive-badge').text('<?php esc_html_e("Inactive", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
            $('.current-template').replaceWith('<button class="button button-primary activate-template" data-template="' + $('.current-template').closest('[data-template-id]').data('template-id') + '"><span class="button-text"><?php esc_html_e("Activate", "dynamic-ajax-product-filters-for-woocommerce"); ?></span><span class="button-spinner" style="display: none;"><span class="spinner is-active"></span></span></button>');

            // Set new Activated
            var $newActiveCard = $('[data-template-id="' + templateId + '"]');
            $newActiveCard.addClass('active');
            $newActiveCard.find('.inactive-badge').removeClass('inactive-badge').addClass('active-badge').text('<?php esc_html_e("Active", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
            $newActiveCard.find('.activate-template').replaceWith('<span class="current-template"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e("Activated", "dynamic-ajax-product-filters-for-woocommerce"); ?></span>');

            // Update the hidden form field to preserve the Activated
            $('input[name="dapfforwc_template_options[active_template]"]').val(templateId);

            // Re-bind event handlers for new activate buttons
            $('.activate-template').off('click').on('click', function() {
                var $button = $(this);
                var $card = $button.closest('.dapfforwc-template-card');
                var templateId = $button.data('template');

                // Show loading state
                $button.addClass('loading');
                $('.dapfforwc-loading-overlay').fadeIn(200);

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dapfforwc_activate_template',
                        template_id: templateId,
                        nonce: '<?php echo esc_js(wp_create_nonce("dapfforwc_template_nonce")); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI immediately without page reload
                            updateActiveTemplate(templateId);

                            // Show success message
                            showNotification('success', response.data);

                            // Add success animation
                            $card.addClass('just-activated');
                            setTimeout(() => $card.removeClass('just-activated'), 600);

                        } else {
                            showNotification('error', response.data || '<?php esc_html_e("Error activating template. Please try again.", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
                        }
                    },
                    error: function() {
                        showNotification('error', '<?php esc_html_e("Network error. Please try again.", "dynamic-ajax-product-filters-for-woocommerce"); ?>');
                    },
                    complete: function() {
                        $button.removeClass('loading');
                        $('.dapfforwc-loading-overlay').fadeOut(200);
                    }
                });
            });
        }

        // Handle template preview
        $('.preview-template, .preview-template-btn').on('click', function() {
            var templateId = $(this).data('template');
            var template = templateData.find(t => t.id === templateId);

            if (template) {
                showPreviewModal(template);
            }
        });

        // Show preview modal
        // In the document ready function, update the showPreviewModal function to check if the template is active
        function showPreviewModal(template) {
            $('#preview-template-name').text(template.name);
            $('#preview-template-description').text(template.description);

            // Check if this template is currently active
            var isActive = template.id === '<?php echo esc_attr($template_options['active_template']); ?>';

            // Build features list
            var featuresHtml = '<h5><?php esc_html_e("Key Features:", "dynamic-ajax-product-filters-for-woocommerce"); ?></h5><ul>';
            template.features.forEach(function(feature) {
                featuresHtml += '<li><span class="dashicons dashicons-yes-alt"></span>' + feature + '</li>';
            });
            featuresHtml += '</ul>';
            $('#preview-features').html(featuresHtml);

            // Create demo widget based on template
            createDemoWidget(template);

            // Set up activate button
            $('#activate-from-preview').data('template', template.id);

            // Update button text based on active state
            if (isActive) {
                $('#activate-from-preview')
                    .html('<span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e("Activated", "dynamic-ajax-product-filters-for-woocommerce"); ?>')
                    .prop('disabled', true)
                    .removeClass('button-primary')
                    .addClass('button-secondary');
            } else {
                $('#activate-from-preview')
                    .html('<?php esc_html_e("Activate This Template", "dynamic-ajax-product-filters-for-woocommerce"); ?>')
                    .prop('disabled', false)
                    .removeClass('button-secondary')
                    .addClass('button-primary');
            }

            // Show modal
            $('#dapfforwc-preview-modal').fadeIn(300);
            $('body').addClass('modal-open');
        }

        // Create demo widget
        function createDemoWidget(template) {
            var demoHtml = '';

            switch (template.id) {
                case 'clean':
                    demoHtml = `
      <div style="border:1px solid #e2e8f0;border-radius:8px;background:#fff;">
        <div style="padding:14px;border-bottom:1px solid #e2e8f0;">
          <h4 style="margin:0;font-size:14px;font-weight:600;color:#2d3748;">Category</h4>
        </div>
        <div style="padding:14px;">
          ${['Laptop','MacBook','Desktop','Linux','Tablet'].map(l => `
            <label style="display:flex;align-items:center;gap:8px;margin:8px 0;cursor:pointer;">
              <input type="checkbox" style="accent-color:#667eea;">
              <span style="font-size:13px;color:#4a5568;">${l}</span>
            </label>
          `).join('')}
        </div>
      </div>`;
                    break;

                case 'shadow':
                    demoHtml = `
      <div style="border:1px solid #e2e8f0;border-radius:12px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.08);">
        <div style="padding:16px;border-bottom:1px solid #e2e8f0;">
          <h4 style="margin:0;font-size:14px;font-weight:600;color:#2d3748;">Category</h4>
        </div>
        <div style="padding:16px;">
          ${['Laptop','MacBook','Desktop','Linux','Tablet'].map(l => `
            <label style="display:flex;align-items:center;gap:8px;margin:10px 0;cursor:pointer;">
              <input type="checkbox" style="accent-color:#667eea;">
              <span style="font-size:14px;color:#2d3748;">${l}</span>
            </label>
          `).join('')}
        </div>
      </div>`;
                    break;
            }


            $('#demo-widget').html(demoHtml);
        }

        // Handle activate from preview
        $('#activate-from-preview').on('click', function() {
            var templateId = $(this).data('template');
            $('#dapfforwc-preview-modal').fadeOut(300);
            $('body').removeClass('modal-open');

            // Trigger activation
            $('[data-template="' + templateId + '"].activate-template').trigger('click');
        });

        // Close modal
        $('.dapfforwc-modal-close, #close-preview, .dapfforwc-modal-backdrop').on('click', function() {
            $('#dapfforwc-preview-modal').fadeOut(300);
            $('body').removeClass('modal-open');
        });

        // Show notification
        function showNotification(type, message) {
            var notificationHtml = `
                <div class="notice notice-${type} is-dismissible dapfforwc-notification" style="position: fixed; top: 32px; right: 20px; z-index: 100001; max-width: 350px; animation: slideInRight 0.3s ease;">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `;

            $('body').append(notificationHtml);

            // Auto dismiss after 4 seconds
            setTimeout(function() {
                $('.dapfforwc-notification').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);

            // Manual dismiss
            $('.dapfforwc-notification .notice-dismiss').on('click', function() {
                $(this).closest('.dapfforwc-notification').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }

        // Prevent modal close on content click
        $('.dapfforwc-modal-content').on('click', function(e) {
            e.stopPropagation();
        });

        // Add CSS for animations
        $('<style>').prop('type', 'text/css').html(`
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .modal-open { overflow: hidden; }
        `).appendTo('head');
    });
</script>

<?php



// Helper function to convert hex to RGB
function hex2rgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }

    return "$r, $g, $b";
}
?>