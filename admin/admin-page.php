<?php
if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_admin_menu()
{
    add_menu_page(
        'WooCommerce Product Filters',
        'Product Filters',
        'manage_options',
        'dapfforwc-admin',
        'dapfforwc_admin_page_content',
        'dashicons-filter',
        '55.50' // Priority set to place after WooCommerce
    );
    add_submenu_page(
        'dapfforwc-admin',
        __('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'),
        __('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'),
        'manage_options',
        'dapfforwc-get-pro',
        function () {
?>
        <div class="wrap dapfforwc-get-pro-page">
            <div class="dapfforwc-pro-header">
                <h1><?php echo esc_html__('Upgrade to Dynamic AJAX Product Filters Pro', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h1>
                <p class="dapfforwc-pro-subtitle"><?php echo esc_html__('Take your WooCommerce store to the next level with advanced filtering capabilities and premium features.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
            </div>

            <div class="dapfforwc-pro-content">
                <div class="dapfforwc-features-grid">
                    <h2><?php echo esc_html__('Premium Features', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>

                    <div class="dapfforwc-feature-list">
                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <div>
                                <h3><?php echo esc_html__('Separate Child Values for Hierarchical Filters', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Display child categories and attributes separately for better organization and user experience.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-admin-links"></span>
                            <div>
                                <h3><?php echo esc_html__('SEO Friendly URLs', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Generate clean, SEO-optimized URLs for filtered pages to improve search engine rankings.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-star-filled"></span>
                            <div>
                                <h3><?php echo esc_html__('Filter by Dynamic Rating', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Allow customers to filter products by star ratings for better product discovery.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-database-import"></span>
                            <div>
                                <h3><?php echo esc_html__('Import & Export Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Easily backup and transfer your filter configurations between sites.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-image-rotate"></span>
                            <div>
                                <h3><?php echo esc_html__('Customize Loading Effects', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Choose from multiple loading animations to match your site\'s design and branding.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-layout"></span>
                            <div>
                                <h3><?php echo esc_html__('Custom Product Template Design', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Create unique product layouts with advanced template customization options.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-editor-code"></span>
                            <div>
                                <h3><?php echo esc_html__('Custom CSS Editor', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Fine-tune the appearance with built-in CSS editor for pixel-perfect customization.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-sos"></span>
                            <div>
                                <h3><?php echo esc_html__('Premium Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Get priority email support from our expert team with faster response times.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>

                        <div class="dapfforwc-feature-item">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <div>
                                <h3><?php echo esc_html__('Many More Features', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                                <p><?php echo esc_html__('Regular updates with new features, improvements, and compatibility enhancements.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dapfforwc-pro-cta">
                    <div class="dapfforwc-cta-content">
                        <h2><?php echo esc_html__('Ready to Upgrade?', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
                        <p><?php echo esc_html__('Join thousands of store owners who have enhanced their WooCommerce filtering experience with our Pro version.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>

                        <div class="dapfforwc-action-buttons">
                            <a href="https://plugincy.com/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" class="btn btn-primary" style="background: #ff5a36; color: #fff;">
                                <span class="dashicons dashicons-star-filled"></span>
                                <?php echo esc_html__('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                            <a href="https://plugincy.com/product-filters-for-woocommerce/" target="_blank" class="btn btn-accent">
                                <span class="dashicons dashicons-visibility"></span>
                                <?php echo esc_html__('View Demo', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                            <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" class="btn btn-primary">
                                <span class="dashicons dashicons-book"></span>
                                <?php echo esc_html__('View Documentation', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                            <a href="https://www.plugincy.com/support/" target="_blank" class="btn btn-secondary">
                                <span class="dashicons dashicons-sos"></span>
                                <?php echo esc_html__('Get Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                        </div>

                        <div class="dapfforwc-guarantees">
                            <div class="dapfforwc-guarantee-item">
                                <span class="dashicons dashicons-clock"></span>
                                <span><?php echo esc_html__('24/7 Premium Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            </div>
                            <div class="dapfforwc-guarantee-item">
                                <span class="dashicons dashicons-update"></span>
                                <span><?php echo esc_html__('Regular Updates', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            </div>
                            <div class="dapfforwc-guarantee-item">
                                <span class="dashicons dashicons-money-alt"></span>
                                <span><?php echo esc_html__('30-Day Money Back', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .notice {
                display: none;
            }

            .dapfforwc-get-pro-page {
                max-width: 1200px;
                margin: 0 auto;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .dapfforwc-pro-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 40px;
                text-align: center;
                border-radius: 8px 8px 0 0;
            }

            .dapfforwc-pro-header h1 {
                font-size: 2.5em;
                margin: 0 0 15px 0;
                font-weight: 600;
                color: #fff;
            }

            .dapfforwc-pro-subtitle {
                font-size: 1.2em;
                margin: 0;
                opacity: 0.9;
            }

            .dapfforwc-pro-content {
                padding: 40px;
            }

            .dapfforwc-features-grid h2 {
                text-align: center;
                font-size: 2em;
                margin-bottom: 30px;
                color: #333;
            }

            .dapfforwc-feature-list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 25px;
                margin-bottom: 50px;
            }

            .dapfforwc-feature-item {
                display: flex;
                align-items: flex-start;
                padding: 20px;
                border: 1px solid #e1e5e9;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .dapfforwc-feature-item:hover {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                border-color: #667eea;
            }

            .dapfforwc-feature-item .dashicons {
                color: #667eea;
                font-size: 24px;
                margin-right: 15px;
                margin-top: 5px;
                flex-shrink: 0;
            }

            .dapfforwc-feature-item h3 {
                margin: 0 0 10px 0;
                font-size: 1.1em;
                color: #333;
            }

            .dapfforwc-feature-item p {
                margin: 0;
                color: #666;
                line-height: 1.5;
            }

            .dapfforwc-pro-cta {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 10px;
                padding: 40px;
                text-align: center;
                color: white;
            }

            .dapfforwc-cta-content h2 {
                font-size: 2em;
                margin: 0 0 15px 0;
                color: #fff;
            }

            .dapfforwc-cta-content p {
                font-size: 1.1em;
                margin: 0 0 30px 0;
                opacity: 0.9;
            }

            .dapfforwc-action-buttons {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
                margin-bottom: 30px;
            }

            .btn {
                padding: 12px 20px !important;
                font-size: 1em !important;
                font-weight: 600 !important;
                border-radius: 5px !important;
                text-decoration: none !important;
                transition: all 0.3s ease !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 8px !important;
                border: 2px solid transparent !important;
                min-width: 150px !important;
                justify-content: center !important;
            }

            .btn-primary {
                background: #667eea;
                color: #fff !important;
                box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3) !important;
            }

            .btn-primary:hover {
                background: #5a6fd8 !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4) !important;
                color: #fff !important;
            }

            .btn-accent {
                background: #fff !important;
                color: #667eea !important;
                box-shadow: 0 3px 10px rgba(118, 75, 162, 0.3) !important;
                border: 1px solid #5a6fd8 !important;
            }

            .btn-accent:hover {
                background: #fff !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 5px 20px rgba(118, 75, 162, 0.4) !important;
                color: #5a6fd8 !important;

            }

            .btn-secondary {
                background: transparent !important;
                color: #fff !important;
                border-color: rgba(255, 255, 255, 0.3) !important;
            }

            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                border-color: rgba(255, 255, 255, 0.6) !important;
                color: #fff !important;
            }

            .btn .dashicons {
                font-size: 16px !important;
                width: 16px !important;
                height: 16px !important;
            }

            .dapfforwc-guarantees {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 30px;
                margin-top: 20px;
            }

            .dapfforwc-guarantee-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.95em;
                opacity: 0.9;
            }

            .dapfforwc-guarantee-item .dashicons {
                font-size: 18px;
            }

            @media (max-width: 768px) {
                .dapfforwc-feature-list {
                    grid-template-columns: 1fr;
                }

                .dapfforwc-pro-header,
                .dapfforwc-pro-content,
                .dapfforwc-pro-cta {
                    padding: 20px;
                }

                .dapfforwc-pro-header h1 {
                    font-size: 2em;
                }

                .dapfforwc-action-buttons {
                    flex-direction: column;
                    align-items: center;
                }

                .btn {
                    min-width: 200px !important;
                }

                .dapfforwc-guarantees {
                    flex-direction: column;
                    gap: 15px;
                }
            }
        </style>
    <?php
        }
    );
}
add_action('admin_menu', 'dapfforwc_admin_menu');

function dapfforwc_get_loading_effects()
{
    $loading_effects = [
        [
            'name' => 'Basic',
            'value' => 'basic',
            'html' => '<div class="basic" style="display:none;" id="loader"></div>',
            'css' => '.basic {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 9px solid;
    border-color: #dbdcef;
    border-right-color: #474bff;
    animation: basic-d3wgkg 1s infinite linear;
 }
 
 @keyframes basic-d3wgkg {
    to {
       transform: rotate(1turn);
    }
 }'
        ],
        [
            'name' => 'Comet',
            'value' => 'comet',
            'html' => '<div class="comet" style="display:none;" id="loader"></div>',
            'css' => ' .comet {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: conic-gradient(#0000 10%,#474bff);
    -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0);
    animation: spinner-zp9dbg 1s infinite linear;
 }
 
 @keyframes spinner-zp9dbg {
    to {
       transform: rotate(1turn);
    }
 }'
        ],
        [
            'name' => 'Counter Arcs',
            'value' => 'counter_arcs',
            'html' => '<div class="counter_arcs" style="display:none;" id="loader"></div>',
            'css' => '.counter_arcs {
    width: 56px;
    height: 56px;
    display: grid;
    animation: spinner-plncf9 4s infinite;
 }
 
 .counter_arcs::before,
 .counter_arcs::after {
    content: "";
    grid-area: 1/1;
    border: 9px solid;
    border-radius: 50%;
    border-color: #474bff #474bff #0000 #0000;
    mix-blend-mode: darken;
    animation: counter_arcs-plncf9 1s infinite linear;
 }
 
 .counter_arcs::after {
    border-color: #0000 #0000 #dbdcef #dbdcef;
    animation-direction: reverse;
 }
 
 @keyframes counter_arcs-plncf9 {
    100% {
       transform: rotate(1turn);
    }
 }'
        ],
        [
            'name' => 'Dot Ring',
            'value' => 'dot_ring',
            'html' => '<div class="dot_ring" style="display:none;" id="loader"></div>',
            'css' => '.dot_ring {
   width: 11.2px;
   height: 11.2px;
   animation: dot_ring-z355kx 1s infinite linear;
   border-radius: 11.2px;
   box-shadow: 28px 0px 0 0 #474bff, 17.4px 21.8px 0 0 #474bff, -6.2px 27.2px 0 0 #474bff, -25.2px 12px 0 0 #474bff, -25.2px -12px 0 0 #474bff, -6.2px -27.2px 0 0 #474bff, 17.4px -21.8px 0 0 #474bff;
}

@keyframes dot_ring-z355kx {
   to {
      transform: rotate(360deg);
   }
}'
        ],
        [
            'name' => 'Half Ring',
            'value' => 'half_ring',
            'html' => '<div class="half_ring" style="display:none;" id="loader"></div>',
            'css' => '.half_ring {
   width: 11.2px;
   height: 11.2px;
   border-radius: 11.2px;
   box-shadow: 28px 0px 0 0 rgba(71,75,255,0.2), 22.7px 16.5px 0 0 rgba(71,75,255,0.4), 8.68px 26.6px 0 0 rgba(71,75,255,0.6), -8.68px 26.6px 0 0 rgba(71,75,255,0.8), -22.7px 16.5px 0 0 #474bff;
   animation: half_ring-b87k6z 1s infinite linear;
}

@keyframes half_ring-b87k6z {
   to {
      transform: rotate(360deg);
   }
}'
        ],
        [
            'name' => 'Chase',
            'value' => 'chase',
            'html' => '<div class="chase" style="display:none;" id="loader"></div>',
            'css' => '.chase {
   position: relative;
   width: 22.4px;
   height: 22.4px;
}

.chase::before,
.chase::after {
   content: "";
   width: 100%;
   height: 100%;
   display: block;
   animation: chase-b4c8mmmd 0.5s backwards, chase-49opz7md 1.25s 0.5s infinite ease;
   border: 5.6px solid #474bff;
   border-radius: 50%;
   box-shadow: 0 -33.6px 0 -5.6px #474bff;
   position: absolute;
}

.chase::after {
   animation-delay: 0s, 1.25s;
}

@keyframes chase-b4c8mmmd {
   from {
      box-shadow: 0 0 0 -5.6px #474bff;
   }
}

@keyframes chase-49opz7md {
   to {
      transform: rotate(360deg);
   }
}'
        ]
    ];

    return json_encode($loading_effects);
}

function dapfforwc_admin_page_content()
{
    global $dapfforwc_options;
    ?>
    <div class="wrap wcapf_admin plugincyajaxfilters_admin_settings">
        <!-- welcome box here -->
        <div class="plugincy-filter-welcome-container">
            <div class="welcome-header">
                <div class="plugincy-plugin-icon">
                    <span class="dashicons dashicons-filter"></span>
                </div>
                <div class="header-content">
                    <div>Dynamic AJAX Product Filters for WooCommerce</div>
                    <p class="tagline">Transform your store with lightning-fast, user-friendly product filtering</p>
                </div>
                <div class="version-badge">
                    <span>Version 1.2.9</span>
                </div>
            </div>

            <div class="welcome-content">
                <div class="quick-actions">
                    <h3>Quick Start Guide:</h3>
                    <div class="action-steps">
                        <div class="step">
                            <span class="step-number">1</span>
                            <div class="step-content">
                                <h4>Configure Filters</h4>
                                <p>Set up your product attributes, categories, and price ranges</p>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-number">2</span>
                            <div class="step-content">
                                <h4>Customize Appearance</h4>
                                <p>Choose colors, layouts, and animations that match your theme</p>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-number">3</span>
                            <div class="step-content">
                                <h4>Deploy & Monitor</h4>
                                <p>Add filters to your shop pages and track performance improvements</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cta-section">
                    <div class="cta-buttons">
                        <a href="https://plugincy.com/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" class="btn btn-primary" style="background: #ff5a36; color: #fff;">
                            <span class="dashicons dashicons-star-filled"></span>
                            Get Pro
                        </a>
                        <a href="https://plugincy.com/product-filters-for-woocommerce/" target="_blank" class="btn btn-accent">
                            <span class="dashicons dashicons-visibility"></span>
                            View Demo
                        </a>
                        <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/"
                            target="_blank" class="btn btn-primary">
                            <span class="dashicons dashicons-book"></span>
                            View Documentation
                        </a>
                        <a href="https://www.plugincy.com/support/"
                            target="_blank" class="btn btn-secondary">
                            <span class="dashicons dashicons-sos"></span>
                            Get Support
                        </a>
                    </div>

                    <div class="support-info">
                        <div class="support-item">
                            <span class="dashicons dashicons-format-chat"></span>
                            <span>24/7 Premium Support</span>
                        </div>
                        <div class="support-item">
                            <span class="dashicons dashicons-update"></span>
                            <span>Regular Updates</span>
                        </div>
                        <div class="support-item">
                            <span class="dashicons dashicons-shield"></span>
                            <span>30-Day Money Back</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .plugincy-filter-welcome-container {
                margin: 20px auto;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                animation: slideIn 0.6s ease-out;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .plugincy-filter-welcome-container .welcome-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 25px 20px;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }

            .plugincy-filter-welcome-container .welcome-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
                opacity: 0.3;
            }

            .plugincy-filter-welcome-container .plugincy-plugin-icon {
                font-size: 48px;
                margin-right: 20px;
                opacity: 0.9;
                position: relative;
                z-index: 2;
            }

            .plugincy-filter-welcome-container .plugincy-plugin-icon .dashicons {
                font-size: 48px;
                width: 48px;
                height: 48px;
            }

            .plugincy-filter-welcome-container .header-content {
                flex: 1;
                position: relative;
                z-index: 2;
            }

            .plugincy-filter-welcome-container .header-content div {
                font-size: 28px;
                font-weight: 700;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                color: #fff;
                padding: 0 0 10px;
                line-height: 1.2;
            }

            .plugincy-filter-welcome-container .tagline {
                font-size: 16px;
                opacity: 0.9;
                font-weight: 400;
                margin: 0;
            }

            .plugincy-filter-welcome-container .version-badge {
                position: relative;
                z-index: 2;
            }

            .plugincy-filter-welcome-container .version-badge span {
                background: rgba(255, 255, 255, 0.2);
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.5px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .plugincy-filter-welcome-container .welcome-content {
                padding: 20px;
            }

            .plugincy-filter-welcome-container .quick-actions {
                background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 20px;
            }

            .plugincy-filter-welcome-container .quick-actions h3 {
                color: #2d3748;
                margin-bottom: 20px;
                font-size: 20px;
                margin-top: 0;
            }

            .plugincy-filter-welcome-container .quick-actions h3 .dashicons {
                color: #667eea;
                font-size: 20px;
                width: 20px;
                height: 20px;
            }

            .plugincy-filter-welcome-container .action-steps {
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
            }

            .plugincy-filter-welcome-container .step {
                flex: 1;
                min-width: 200px;
                display: flex;
                align-items: flex-start;
                gap: 15px;
            }

            .plugincy-filter-welcome-container .step-number {
                background: #667eea;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 14px;
                flex-shrink: 0;
            }

            .plugincy-filter-welcome-container .step-content h4 {
                color: #2d3748;
                margin-bottom: 4px;
                font-size: 16px;
                margin: 0;
            }

            .plugincy-filter-welcome-container .step-content p {
                color: #718096;
                font-size: 14px;
            }

            .plugincy-filter-welcome-container .cta-section {
                text-align: center;
                border-top: 1px solid #e2e8f0;
                padding-top: 20px;
            }

            .plugincy-filter-welcome-container .cta-buttons {
                display: flex;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
                margin-bottom: 20px;
            }

            .plugincy-filter-welcome-container .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                cursor: pointer;
                border: none;
                box-sizing: border-box;
            }

            .plugincy-filter-welcome-container .btn-primary {
                background: #667eea;
                color: white;
            }

            .plugincy-filter-welcome-container .btn-primary:hover {
                background: #5a67d8;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            .plugincy-filter-welcome-container .btn-secondary {
                background: #48bb78;
                color: white;
            }

            .plugincy-filter-welcome-container .btn-secondary:hover {
                background: #38a169;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
            }

            .plugincy-filter-welcome-container .btn-accent {
                background: #ed8936;
                color: white;
            }

            .plugincy-filter-welcome-container .btn-accent:hover {
                background: #dd6b20;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(237, 137, 54, 0.4);
            }

            .plugincy-filter-welcome-container .btn .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }

            .plugincy-filter-welcome-container .support-info {
                display: flex;
                justify-content: center;
                gap: 30px;
                flex-wrap: wrap;
            }

            .plugincy-filter-welcome-container .support-item {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #718096;
                font-size: 14px;
            }

            .plugincy-filter-welcome-container .support-item .dashicons {
                color: #48bb78;
                font-size: 16px;
                width: 16px;
                height: 16px;
            }

            @media (max-width: 768px) {
                .plugincy-filter-welcome-container .welcome-header {
                    flex-direction: column;
                    text-align: center;
                    padding: 20px;
                }

                .plugincy-filter-welcome-container .plugincy-plugin-icon {
                    margin-right: 0;
                    margin-bottom: 15px;
                }

                .plugincy-filter-welcome-container .header-content div {
                    font-size: 24px;
                }

                .plugincy-filter-welcome-container .welcome-content {
                    padding: 20px;
                }

                .plugincy-filter-welcome-container .action-steps {
                    flex-direction: column;
                }

                .plugincy-filter-welcome-container .cta-buttons {
                    flex-direction: column;
                    align-items: center;
                }

                .plugincy-filter-welcome-container .btn {
                    width: 100%;
                    max-width: 300px;
                    justify-content: center;
                }

                .plugincy-filter-welcome-container .support-info {
                    flex-direction: column;
                    align-items: center;
                    gap: 15px;
                }

                .version-badge {
                    margin-top: 15px;
                }
            }
        </style>
        <h1 style="margin-bottom: 20px;">Manage WooCommerce Product Filters</h1>
        <?php settings_errors(); // Displays success or error notices
        $nonce = wp_create_nonce('dapfforwc_tab_nonce');
        $active_tab = 'form_manage'; // Default tab
        if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'dapfforwc_tab_nonce')) {
            $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'form_manage';
        }

        ?>
        <div class="wcapf_admin_page">
            <h2 class="nav-tab-wrapper">
                <a href="?page=dapfforwc-admin&tab=form_manage&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'form_manage' ? 'nav-tab-active' : (!isset($_GET['tab']) ? 'nav-tab-active' : ''); ?>"><span class="dashicons dashicons-forms"></span><span class="nav-title">Form Manage</span></a>
                <a href="?page=dapfforwc-admin&tab=form_style&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'form_style' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-customizer"></span><span class="nav-title">Form Style</span></a>
                <a href="?page=dapfforwc-admin&tab=seo_permalinks&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'seo_permalinks' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-links"></span><span class="nav-title">SEO & Permalinks Setup</span></a>
                <a href="?page=dapfforwc-admin&tab=advance_settings&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'advance_settings' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-generic"></span><span class="nav-title">Advance Settings</span></a>
                <a href="?page=dapfforwc-admin&tab=license_settings&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'license_settings' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-network"></span><span class="nav-title">Plugin License</span></a>
            </h2>
            <div class="tab-content">
                <?php
                if ($active_tab == 'form_manage') {
                ?>
                    <form method="post" action="options.php">
                        <div id="custom-loading-popup" style="display:none;">
                            <div class="popup-content pro-only-2 pro-overlay customizer-pro-overlay">
                                <span class="close-popup">&times;</span>
                                <h2>Customize Loading Effect</h2>
                                <label for="loader_html">HTML</label>
                                <p style="text-align:left;"><b>Note:</b> style="display:none;" id="loader" is required </p>
                                <textarea disabled id="loader_html" placeholder="html values will be here" name="dapfforwc_options[loader_html_pro_only]"><?php
                                                                                                                                                            if (isset($dapfforwc_options["loader_html"])) {
                                                                                                                                                                echo esc_html($dapfforwc_options["loader_html"]);
                                                                                                                                                            } else {
                                                                                                                                                                // Optionally, handle the case where it's not set
                                                                                                                                                                echo '<div id="loader" style="display:none;"></div>';
                                                                                                                                                            }
                                                                                                                                                            ?></textarea>
                                <label for="loader_css">CSS</label>
                                <textarea disabled id="loader_css" placeholder="CSS values will be here" name="dapfforwc_options[loader_css_pro_only]"><?php
                                                                                                                                                        if (isset($dapfforwc_options["loader_css"])) {
                                                                                                                                                            echo esc_html($dapfforwc_options["loader_css"]);
                                                                                                                                                        } else {
                                                                                                                                                            // Optionally, handle the case where it's not set
                                                                                                                                                            echo '#loader { width: 56px; height: 56px; border-radius: 50%; background: conic-gradient(#0000 10%,#474bff); -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0); animation: spinner-zp9dbg 1s infinite linear; } @keyframes spinner-zp9dbg { to { transform: rotate(1turn); } }';
                                                                                                                                                        }
                                                                                                                                                        ?></textarea>
                                <p>Select a loading effect (or get html & css code from anywhere paste & save): </p>
                                <div class="loading-options">
                                    <?php
                                    $loading_effects_json = dapfforwc_get_loading_effects();
                                    $loading_effects = json_decode($loading_effects_json, true);

                                    foreach ($loading_effects as $effect) {
                                    ?>
                                        <div class="loading-option" data-value="<?php echo esc_attr($effect['value']); ?>"
                                            data-html="<?php echo esc_html($effect['html']); ?>"
                                            data-css="<?php echo esc_html($effect['css']); ?>">
                                            <style>
                                                <?php echo esc_attr($effect['css']); ?>
                                            </style>
                                            <div class="<?php echo esc_attr($effect['value']); ?>"></div>
                                            <span class="effect-name"><?php echo esc_html($effect['name']); ?></span>
                                            <div class="checkmark" style="display:none;">✓</div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <button id="save-effect" class="button button-primary">Save</button>
                            </div>

                        </div>
                        <?php
                        settings_fields('dapfforwc_options_group');
                        do_settings_sections('dapfforwc-admin');
                        submit_button();
                        ?>
                        <p style="margin-bottom: 10px;">Use shortcode to show filter: <b>[plugincy_filters]</b></p>
                        <p style="margin-bottom: 10px;">For button style filter use this shortcode: <b>[plugincy_filters_single name="conference-by-month"]</b></p>
                        <p>For show currently selected filter above product: <b>[plugincy_filters_selected]</b></p>
                    </form>
                <?php
                } elseif ($active_tab == 'form_style') {
                    require_once(plugin_dir_path(__FILE__) . 'form_style_tab.php');
                } elseif ($active_tab == 'advance_settings') {
                ?>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('dapfforwc_advance_settings');
                        do_settings_sections('dapfforwc-advance-settings');
                        submit_button();
                        ?>
                    </form>
                    <h2>Import &amp; Export Settings</h2>
                    <div class="pro-only-2 pro-overlay">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row">Import Settings</th>
                                    <td>
                                        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                            <?php wp_nonce_field('dapfforwc_import_settings_nonce'); ?>
                                            <input type="hidden" name="action" value="dapfforwc_import_settings">
                                            <input type="file" name="dapfforwc_import_file" accept=".json" required>
                                            <button type="submit" name="wcapf_import_button" id="wcapf_import_button" class="button button-primary">Import Settings</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Export Settings</th>
                                    <td>
                                        <form method="post" action="admin-post.php">
                                            <input type="hidden" name="action" value="dapfforwc_export_settings">
                                            <button type="submit" name="wcapf_export_button" id="wcapf_export_button" class="button button-primary">Export Settings</button>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?php dapfforwc_reset_settings_form() ?>
                    </div>
                <?php
                } elseif ($active_tab == 'seo_permalinks') {
                ?>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('dapfforwc_seo_permalinks_settings');
                        do_settings_sections('dapfforwc-seo-permalinks');
                        submit_button();
                        ?>
                    </form>
                <?php
                } elseif ($active_tab == 'license_settings') {
                    $license_manager = new DAPFFORWC_License_Manager();
                    $license_manager->render_license_form();
                }
                ?>
                <style>
                    .header {
                        margin-bottom: 28px;
                    }

                    .header h3 {
                        font-size: 1.8em;
                        color: #2d3748;
                        margin-bottom: 8px;
                    }

                    .header p {
                        color: #718096;
                        font-size: 0.95em;
                    }

                    .steps {
                        display: flex;
                        flex-direction: column;
                        gap: 16px;
                    }
                </style>
                <div class="plugincy-notice professional-notice">
                    <div class="header">
                        <h3>Filters Not Working?</h3>
                        <p>Quick troubleshooting steps</p>
                    </div>

                    <div class="steps">
                        <div class="step">
                            1. <b> Filters not responding?</b> Adjust the selectors in the Advanced Settings to match your theme.
                            <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/" target="_blank" class="step-link">View docs</a>
                        </div>

                        <div class="step">
                            2. <b> Filters behaving unexpectedly?</b> Temporarily deactivate other filter plugins to check for conflicts.
                        </div>

                        <div class="step">
                            3. Still facing issues? Reach out to Plugincy Support as we’re always here to help.
                            <a href="https://plugincy.com/support/" target="_blank" class="support-link">Plugincy Support</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php
}
// init settings first
require_once(plugin_dir_path(__FILE__) . 'settings-init.php');
// include form_manage content
require_once(plugin_dir_path(__FILE__) . 'form-manage.php');
// color converter include
require_once(plugin_dir_path(__FILE__) . 'color_name_to_hex.php');
// before save image & color
function dapfforwc_save_style_options($input)
{
    foreach ($input as $attribute => $data) {
        // Handle color data
        if (isset($data['colors'])) {
            foreach ($data['colors'] as $term_slug => $value) {
                $input[$attribute]['colors'][$term_slug] = sanitize_hex_color($value); // Sanitize color
            }
        }

        // Handle image data
        if (isset($data['images'])) {
            foreach ($data['images'] as $term_slug => $value) {
                $input[$attribute]['images'][$term_slug] = esc_url_raw($value); // Sanitize URL
            }
        }
    }
    return $input;
}
add_filter('pre_update_option_dapfforwc_style_options', 'dapfforwc_save_style_options');


// include advance settings

require_once(plugin_dir_path(__FILE__) . 'advance_settings.php');

require_once(plugin_dir_path(__FILE__) . 'page-seo-permalinks.php');








/**
 * Handle settings reset functionality
 */
function dapfforwc_reset_settings()
{
    // Check if reset form was submitted
    if (isset($_POST['reset_settings']) && $_POST['reset_settings'] == '1') {

        // Verify nonce for security
        if (
            !isset($_POST['reset_settings_nonce']) ||
            !wp_verify_nonce($_POST['reset_settings_nonce'], 'reset_settings_nonce_action')
        ) {
            add_settings_error(
                'dapfforwc_messages',
                'dapfforwc_message',
                __('Security check failed. Settings were not reset.', 'dapfforwc'),
                'error'
            );
            return;
        }

        // Delete all plugin settings
        delete_option('dapfforwc_filters');
        delete_option('dapfforwc_options');
        delete_option('dapfforwc_style_options');
        delete_option('dapfforwc_advance_options');
        delete_option('dapfforwc_seo_permalinks_options');
        delete_option('woocommerce_slug_check_dismissed_time');
        delete_option('dapfforwc_install_time');
        delete_option('dapfforwc_review_already_done');
        delete_option('dapfforwc_remind_me_later');

        // Add success message
        add_settings_error(
            'dapfforwc_messages',
            'dapfforwc_message',
            __('Dynamic AJAX Product Filters for WooCommerce settings have been reset to defaults.', 'dapfforwc'),
            'updated'
        );

        // Redirect to prevent form resubmission
        $redirect_url = add_query_arg(array(
            'page' => isset($_GET['page']) ? $_GET['page'] : 'dapfforwc-settings',
            'settings-reset' => 'true'
        ), admin_url('admin.php'));

        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'dapfforwc_reset_settings');

/**
 * Create a reset settings form
 */
function dapfforwc_reset_settings_form()
{
?>
    <div class="dapfforwc-reset-settings-wrapper">
        <h2><?php _e('Reset Settings', 'dapfforwc'); ?></h2>
        <p class="description"><?php _e('Click the button below to reset all plugin settings to their default values. This action cannot be undone.', 'dapfforwc'); ?></p>

        <form method="post">
            <?php wp_nonce_field('reset_settings_nonce_action', 'reset_settings_nonce'); ?>
            <input type="hidden" name="reset_settings" value="1">
            <button type="submit" class="button button-secondary button-danger"
                onclick="return confirm('<?php _e('Are you sure you want to reset all settings? This action cannot be undone.', 'dapfforwc'); ?>');">
                <?php _e('Reset All Settings', 'dapfforwc'); ?>
            </button>
        </form>
    </div>
    <?php
}

/**
 * Display admin notices for settings reset
 */
function dapfforwc_admin_notices()
{
    if (isset($_GET['settings-reset']) && $_GET['settings-reset'] == 'true') {
    ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('All plugin settings have been reset to defaults.', 'dapfforwc'); ?></p>
        </div>
    <?php
    }
}
add_action('admin_notices', 'dapfforwc_admin_notices');

/**
 * Add custom styling for the reset button
 */
function dapfforwc_admin_styles()
{
    ?>
    <style>
        .button-danger {
            color: #fff !important;
            background: #dc3545 !important;
            border-color: #dc3232 !important;
        }

        .button-danger:hover,
        .button-danger:focus {
            background: #c82333 !important;
            border-color: #bd2130 !important;
        }

        .dapfforwc-reset-settings-wrapper {
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
<?php
}
add_action('admin_head', 'dapfforwc_admin_styles');

?>