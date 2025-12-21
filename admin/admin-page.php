<?php

// admin-page.php

if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_admin_menu()
{
    add_menu_page(
        'WooCommerce Product Filters',
        esc_html__('Product Filters', 'dynamic-ajax-product-filters-for-woocommerce'),
        'manage_options',
        'dapfforwc-admin',
        'dapfforwc_admin_page_content',
        'dashicons-filter',
        '55.50' // Priority set to place after WooCommerce
    );
    add_submenu_page(
        'dapfforwc-admin',
        esc_html__('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'),
        esc_html__('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'),
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
            'name' => 'Basic ',
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
    global $dapfforwc_options, $dapfforwc_allowed_tags;
    ?>
    <div class="wrap wcapf_admin plugincyajaxfilters_admin_settings">
        <!-- welcome box here -->
        <div class="plugincy-filter-welcome-container">
            <div class="welcome-header">
                <div class="plugincy-plugin-icon">
                    <span class="dashicons dashicons-filter"></span>
                </div>
                <div class="header-content">
                    <div><?php echo esc_html__('Dynamic AJAX Product Filters for WooCommerce', 'dynamic-ajax-product-filters-for-woocommerce'); ?></div>
                    <p class="tagline"><?php echo esc_html__('Transform your store with lightning-fast, user-friendly product filtering', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                </div>
                <div class="version-badge">
                    <span><?php echo esc_html__('Version', 'dynamic-ajax-product-filters-for-woocommerce'); ?> 1.4.6.10</span>
                </div>
            </div>

            <div class="welcome-content">
                <div class="quick-actions">
                    <h3><?php echo esc_html__('Quick Start Guide:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                    <div class="action-steps">
                        <div class="step">
                            <span class="step-number"><?php echo esc_html__('1', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            <div class="step-content">
                                <h4><?php echo esc_html__('Configure Filters', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                                <p><?php echo esc_html__('Set up your product attributes, categories, and price ranges', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-number"><?php echo esc_html__('2', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            <div class="step-content">
                                <h4><?php echo esc_html__('Customize Appearance', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                                <p><?php echo esc_html__('Choose colors, layouts, and animations that match your theme', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-number"><?php echo esc_html__('3', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                            <div class="step-content">
                                <h4><?php echo esc_html__('Deploy & Monitor', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h4>
                                <p><?php echo esc_html__('Add filters to your shop pages and track performance improvements', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cta-section">
                    <div class="cta-buttons">
                        <a href="https://plugincy.com/dynamic-ajax-product-filters-for-woocommerce/" target="_blank" class="btn btn-primary" style="background: #ff5a36; color: #fff;">
                            <span class="dashicons dashicons-star-filled"></span>
                            <?php echo esc_html__('Get Pro', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </a>
                        <a href="https://plugincy.com/product-filters-for-woocommerce/" target="_blank" class="btn btn-accent">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php echo esc_html__('View Demo', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </a>
                        <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/"
                            target="_blank" class="btn btn-primary">
                            <span class="dashicons dashicons-book"></span>
                            <?php echo esc_html__('View Documentation', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </a>
                        <a href="https://www.plugincy.com/support/"
                            target="_blank" class="btn btn-secondary">
                            <span class="dashicons dashicons-sos"></span>
                            <?php echo esc_html__('Get Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </a>
                    </div>

                    <div class="support-info">
                        <div class="support-item">
                            <span class="dashicons dashicons-format-chat"></span>
                            <span><?php echo esc_html__('24/7 Premium Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        </div>
                        <div class="support-item">
                            <span class="dashicons dashicons-update"></span>
                            <span><?php echo esc_html__('Regular Updates', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        </div>
                        <div class="support-item">
                            <span class="dashicons dashicons-shield"></span>
                            <span><?php echo esc_html__('30-Day Money Back', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
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
        <h1 style="margin-bottom: 20px;">
            <div class="plugincy-dapfforwc-card-header">
                <div class="plugincy-dapfforwc-card-header-icon">
                    <svg fill="#fff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" xml:space="preserve" width="16" height="16">
                        <path d="m.661 4.683.737.345a5.3 5.3 0 0 0-.099 2.154l-.763.276a.557.557 0 0 0-.332.71l.293.802a.557.557 0 0 0 .71.332l.763-.276a5.2 5.2 0 0 0 1.457 1.589l-.345.737a.56.56 0 0 0 .266.737l.776.362c.276.128.605.01.737-.266l.345-.74c.51.128 1.043.184 1.582.151l.01-.214c.013-.303.145-.582.368-.786.201-.184.457-.289.73-.296q.07-.172.158-.339a1.1 1.1 0 0 1-.168-.303 3.588 3.588 0 0 1-4.937-2.9 3.59 3.59 0 0 1 3.147-3.983A3.588 3.588 0 0 1 9.77 7.849q.192.08.345.224.17-.07.352-.128a1.134 1.134 0 0 1 1.174-.997l.079.003a5.2 5.2 0 0 0-.033-1.45l.796-.289a.557.557 0 0 0 .332-.71l-.293-.802a.557.557 0 0 0-.71-.332l-.796.289a5.2 5.2 0 0 0-1.437-1.572l.358-.763a.56.56 0 0 0-.266-.737L8.895.223a.56.56 0 0 0-.737.266l-.355.76a5.2 5.2 0 0 0-2.134-.102L5.386.364a.557.557 0 0 0-.71-.332l-.802.293a.557.557 0 0 0-.332.71l.283.776a5.2 5.2 0 0 0-1.589 1.444l-.74-.345a.56.56 0 0 0-.737.266l-.362.776a.54.54 0 0 0 .263.73" />
                        <path d="M12.474 8.116V8.1a.35.35 0 0 0-.332-.349l-.539-.023a.35.35 0 0 0-.365.332l-.023.513a3.3 3.3 0 0 0-1.276.46l-.345-.378a.35.35 0 0 0-.493-.023l-.398.365a.35.35 0 0 0-.02.494l.345.378c-.28.372-.47.793-.572 1.23l-.513-.023a.35.35 0 0 0-.365.332l-.023.539v.016a.35.35 0 0 0 .332.349l.516.023c.066.444.22.878.467 1.269l-.385.352a.35.35 0 0 0-.02.494l.365.398a.35.35 0 0 0 .493.023l.388-.355c.372.276.789.464 1.223.566l-.023.53v.016a.35.35 0 0 0 .332.349l.539.023a.35.35 0 0 0 .365-.332l.023-.53a3.25 3.25 0 0 0 1.26-.46l.358.395a.35.35 0 0 0 .493.023l.398-.365a.35.35 0 0 0 .02-.494l-.358-.395c.276-.368.467-.783.569-1.217l.53.023a.35.35 0 0 0 .365-.332l.023-.539v-.016a.35.35 0 0 0-.332-.349l-.53-.023a3.3 3.3 0 0 0-.457-1.266l.388-.355a.35.35 0 0 0 .02-.494l-.365-.398a.35.35 0 0 0-.493-.023l-.385.352a3.3 3.3 0 0 0-1.223-.576zm.543 2.536c.316.345.47.779.47 1.214a1.798 1.798 0 0 1-3.124 1.213 1.8 1.8 0 0 1-.47-1.214c0-.487.197-.973.585-1.325a1.795 1.795 0 0 1 2.539.112M6.508 3.673c-1.47 0-2.664 1.194-2.664 2.664s1.194 2.664 2.664 2.664 2.664-1.194 2.664-2.664-1.194-2.664-2.664-2.664m0 1.796a.867.867 0 0 0-.868.868.398.398 0 1 1-.796 0c0-.918.747-1.664 1.664-1.664a.398.398 0 1 1 0 .796" />
                    </svg>
                </div>
                <?php echo esc_html__('Manage WooCommerce Product Filters', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
            </div>
        </h1>
        <?php settings_errors(); // Displays success or error notices
        $nonce = esc_js(wp_create_nonce('dapfforwc_tab_nonce'));
        $active_tab = 'form_manage'; // Default tab
        if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'dapfforwc_tab_nonce')) {
            $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'form_manage';
        }

        ?>
        <div class="wcapf_admin_page row" style="justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div class="col-md-7">
                <h2 class="nav-tab-wrapper">
                    <a href="?page=dapfforwc-admin&tab=form_manage&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'form_manage' ? 'nav-tab-active' : (!isset($_GET['tab']) ? 'nav-tab-active' : ''); ?>"><span class="dashicons dashicons-forms"></span><span class="nav-title"><?php echo esc_html__('Form Manage', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
                    <a href="?page=dapfforwc-admin&tab=form_style&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'form_style' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-customizer"></span><span class="nav-title"><?php echo esc_html__('Form Style', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
                    <a href="?page=dapfforwc-admin&tab=form_template&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'form_template' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-welcome-widgets-menus"></span><span class="nav-title"><?php echo esc_html__('Form Template', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
                    <a href="?page=dapfforwc-admin&tab=seo_permalinks&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'seo_permalinks' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-links"></span><span class="nav-title"><?php echo esc_html__('SEO & Permalinks Setup', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
                    <a href="?page=dapfforwc-admin&tab=advance_settings&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'advance_settings' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-generic"></span><span class="nav-title"><?php echo esc_html__('Advance Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
                    <a href="?page=dapfforwc-admin&tab=license_settings&_wpnonce=<?php echo esc_attr($nonce); ?>" class="nav-tab <?php echo isset($_GET['tab']) && $active_tab == 'license_settings' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-network"></span><span class="nav-title"><?php echo esc_html__('Plugin License', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span></a>
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
                                                                                                                                                                echo '#loader { width: 56px; height: 56px; border-radius: 50%; -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0); animation: spinner-zp9dbg 1s infinite linear; } @keyframes spinner-zp9dbg { to { transform: rotate(1turn); } }';
                                                                                                                                                            }
                                                                                                                                                            ?></textarea>
                                    <p><?php echo esc_html__('Select a loading effect (or get html & css code from anywhere paste & save):', 'dynamic-ajax-product-filters-for-woocommerce'); ?> </p>
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
                                    <button id="save-effect" class="button button-primary"><?php echo esc_html__('Save', 'dynamic-ajax-product-filters-for-woocommerce'); ?></button>
                                </div>

                            </div>
                            <?php
                            settings_fields('dapfforwc_options_group');
                            do_settings_sections('dapfforwc-admin');
                            submit_button();
                            ?>
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

                        <h2><?php echo esc_html__('Cache Management', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row"><?php echo esc_html__('Clear Cache', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                                    <td>
                                        <p class="description" style="margin-bottom: 10px;"><?php echo esc_html__('Clear WooCommerce and object caches to ensure filters work properly after configuration changes.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                            <?php wp_nonce_field('dapfforwc_clear_cache_nonce', 'dapfforwc_clear_cache_nonce'); ?>
                                            <input type="hidden" name="action" value="dapfforwc_clear_cache">
                                            <button type="submit" name="dapfforwc_clear_cache_button" id="dapfforwc_clear_cache_button" class="button button-primary">
                                                <span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px;"></span>
                                                <?php echo esc_html__('Clear Cache', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="pro-only-2 pro-overlay">
                            <h2><?php echo esc_html__('Import &amp; Export Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('Import Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                                        <td>
                                            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                                <?php wp_nonce_field('dapfforwc_import_settings_nonce'); ?>
                                                <input type="hidden" name="action" value="dapfforwc_import_settings">
                                                <input type="file" name="dapfforwc_import_file" accept=".json" required>
                                                <button type="submit" name="wcapf_import_button" id="wcapf_import_button" class="button button-primary"><?php echo esc_html__('Import', 'dynamic-ajax-product-filters-for-woocommerce'); ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('Export Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></th>
                                        <td>
                                            <form method="post" action="admin-post.php">
                                                <input type="hidden" name="action" value="dapfforwc_export_settings">
                                                <button type="submit" name="wcapf_export_button" id="wcapf_export_button" class="button button-primary"><?php echo esc_html__('Export', 'dynamic-ajax-product-filters-for-woocommerce'); ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php dapfforwc_reset_settings_form() ?>
                        </div>
                    <?php
                    } elseif ($active_tab == 'form_template') {
                        require_once(plugin_dir_path(__FILE__) . 'form_template_tab.php');
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
                </div>

            </div>
            <div class="col-md-5">
                <div class="plugincy-dapfforwc-card" style="margin: 0;">
                    <div class="plugincy-dapfforwc-card-header">
                        <div class="plugincy-dapfforwc-card-header-icon"><svg fill="#ffffff" width="16" height="16" viewBox="0 0 0.96 0.96" xmlns="http://www.w3.org/2000/svg">
                                <g data-name="Layer 2">
                                    <path fill="none" data-name="invisible box" d="M0 0h0.96v0.96H0z" />
                                    <path d="M0.4 0.8H0.39a0.04 0.04 0 0 1 -0.028 -0.05L0.522 0.192a0.04 0.04 0 0 1 0.076 0.02l-0.16 0.56A0.037 0.037 0 0 1 0.4 0.8m-0.12 -0.1a0.04 0.04 0 0 0 0.026 -0.07L0.14 0.48 0.306 0.33A0.04 0.04 0 0 0 0.254 0.27L0.054 0.45a0.04 0.04 0 0 0 0 0.06l0.2 0.18a0.037 0.037 0 0 0 0.026 0.01m0.4 0a0.04 0.04 0 0 1 -0.026 -0.07L0.82 0.48 0.654 0.33a0.04 0.04 0 1 1 0.052 -0.06l0.2 0.18a0.04 0.04 0 0 1 0 0.06l-0.2 0.18a0.037 0.037 0 0 1 -0.026 0.01" data-name="icons Q2" />
                                </g>
                            </svg></div>
                        <h3 style="margin: 0;">Shortcodes for Displaying Filters</h3>
                    </div>
                    <div class="plugincy-dapfforwc-card-body">
                        <p><?php echo esc_html__('Use these shortcodes to display filters on your site.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                        <code class="plugincy-code-box">[plugincy_filters]</code>
                        <p><?php echo esc_html__('Display as topview', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                        <code class="plugincy-code-box">[plugincy_filters layout="top_view"]</code>
                        <p class="plugincy-code-description" style="margin-bottom: 10px;"><?php echo esc_html__('Displays the full filter form.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                        <code class="plugincy-code-box">[plugincy_filters_single name="attribute_selector"]</code>
                        <p class="plugincy-code-description" style="margin-bottom: 10px;"><?php echo esc_html__('Shows a single filter button.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                        <code class="plugincy-code-box">[plugincy_filters_selected]</code>
                        <p class="plugincy-code-description"><?php echo esc_html__('Displays selected filters.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
                        <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/display-filter-widgets/using-shortcodes-for-dynamic-ajax-product-filters/" target="_blank" class="plugincy-learn-more-link"><?php echo esc_html__('Learn more about using shortcodes', 'dynamic-ajax-product-filters-for-woocommerce'); ?></a>
                        <div class="plugincy-shortcode-note" style="margin-top:15px; padding:10px; border:1px solid #e2e2e2; border-radius:6px; background:#f9f9f9; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-filter" style="color:#ff6b35; font-size:18px;"></span>
                            <p style="margin:0; font-size:13px; line-height:1.5;">
                                <?php echo esc_html__('Tips: Instead of shortcodes, we recommend using our Elementor widget & Gutenberg block for easier management.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                <br>
                                <b><?php echo esc_html__('Dynamic Ajax Filter (Block/Widget)', 'dynamic-ajax-product-filters-for-woocommerce'); ?></b>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="plugincy-notice professional-notice">
                    <div class="plugincy-dapfforwc-card-header">
                        <div class="plugincy-dapfforwc-card-header-icon" style="background: #ff6b35;">
                            <svg width="16" height="16" viewBox="0 0 0.48 0.48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M.435.318.307.088Q.28.041.24.04C.2.039.19.057.173.088l-.128.23Q.021.363.04.398c.019.035.039.034.072.034h.256Q.42.432.44.398C.46.364.451.347.435.318M.225.18Q.227.166.24.165C.253.164.255.172.255.18v.1Q.253.294.24.295C.227.296.225.288.225.28zm.029.174L.251.356.247.358.243.359H.235L.231.358.227.356.224.354A.02.02 0 0 1 .218.34.02.02 0 0 1 .224.326L.227.324.231.322.235.321h.008l.004.001.004.002.003.002Q.26.332.26.34C.26.348.258.35.254.354" fill="#fff" />
                            </svg>
                        </div>
                        <h3><?php echo esc_html__('Filters Not Working?', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
                    </div>
                    <?php $checkmark = '<svg width="20" height="20" viewBox="0 0 0.6 0.6" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.227 0.083a0.333 0.333 0 0 1 0.149 0 0.189 0.189 0 0 1 0.142 0.142 0.333 0.333 0 0 1 0 0.149 0.189 0.189 0 0 1 -0.143 0.143 0.333 0.333 0 0 1 -0.149 0A0.189 0.189 0 0 1 0.083 0.374a0.333 0.333 0 0 1 0 -0.149 0.189 0.189 0 0 1 0.142 -0.142m0.15 0.179A0.013 0.013 0 1 0 0.354 0.242L0.281 0.321 0.243 0.283a0.013 0.013 0 0 0 -0.02 0.02l0.048 0.048a0.012 0.012 0 0 0 0.021 0z" fill="#4caf50"/></svg>'; ?>
                    <div class="plugincy-dapfforwc-card-body steps" style="padding-top:10px;">

                        <div class="step">
                            <?php echo wp_kses($checkmark, $dapfforwc_allowed_tags); ?>
                            <b><?php echo esc_html__('Filters not updating?', 'dynamic-ajax-product-filters-for-woocommerce'); ?> </b>
                            <?php echo esc_html__('If you are using a cache plugin, clear the cache. If the issue still persists, go to the Advanced Settings tab and click "Clear Cache".', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </div>

                        <div class="step">
                            <?php echo wp_kses($checkmark, $dapfforwc_allowed_tags); ?>
                            <b><?php echo esc_html__('Filters not responding?', 'dynamic-ajax-product-filters-for-woocommerce'); ?> </b>
                            <?php echo esc_html__('Adjust the selectors in the Advanced Settings to match your theme.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            <a href="https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/filters-setup/managing-selectors-in-product-filters/" target="_blank" class="step-link">
                                <?php echo esc_html__('View docs', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                        </div>

                        <div class="step">
                            <?php echo wp_kses($checkmark, $dapfforwc_allowed_tags); ?>
                            <b><?php echo esc_html__('Filters behaving unexpectedly?', 'dynamic-ajax-product-filters-for-woocommerce'); ?></b>
                            <?php echo esc_html__('Temporarily deactivate other filter plugins to check for conflicts.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                        </div>

                        <div class="step">
                            <?php echo wp_kses($checkmark, $dapfforwc_allowed_tags); ?>
                            <b><?php echo esc_html__('Still facing issues?', 'dynamic-ajax-product-filters-for-woocommerce'); ?></b>
                            <?php echo esc_html__('Reach out to Plugincy Support as we’re always here to help.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            <a href="https://plugincy.com/support/" target="_blank" class="support-link">
                                <?php echo esc_html__('Plugincy Support', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </a>
                        </div>

                    </div>

                </div>
                <?php dapfforwc_render_admin_faq(); ?>

            </div>
        </div>
        <!-- Tutorial section -->
            <?php
            // Tutorial Section with Professional UI and Relevant Content
            ?>
            <div class="dapfforwc-tutorial-section">
                <?php

                $tutorial_videos = [
                    [
                        'title' => __('Full tutorial', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'description' => __('Learn how to install the plugin and configure basic settings. This tutorial covers the initial setup process and activation.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'url' => 'https://www.youtube.com/embed/QndYhhLWaaM',
                        'difficulty' => 'beginner',
                        'topics' => ['Full overview', 'Initial Setup', 'Basic Configuration', 'Filter display'],
                        'doc_url' => 'https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/getting-started/installation-procedure/',
                    ],
                    [
                        'title' => __('Add product filter in any pages with elementor', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'description' => __('Learn how to add product filters to any page using Elementor.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'url' => 'https://www.youtube.com/embed/844dLfvZzZA',
                        'difficulty' => 'beginner',
                        'shortcode' => '[plugincy_filters layout="sidebar"]',
                        'topics' => ['Elementor Integration', 'Filter Widget', 'Page Customization'],
                        'doc_url' => 'https://plugincy.com/documentations/dynamic-ajax-product-filters-for-woocommerce/display-filter-widgets/displaying-filters-using-elementor-with-dynamic-ajax-product-filter-for-woocommerce/',
                    ],
                    [
                        'title' => __(' How to reorder/rearrange filter widget with elementor & gutenburg.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'description' => __('Learn how to reorder and rearrange filter widgets using Elementor and Gutenberg.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'url' => 'https://www.youtube.com/embed/VU5GAgyU0xY',
                        'difficulty' => 'beginner',
                        'topics' => ['Elementor', 'Gutenberg', 'Widget Management', 'Drag-and-Drop'],
                    ],
                    [
                        'title' => __('How to remove a filter widget (cover form manage from global setting, exclude attribute, custom fields)', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'description' => __('Learn how to remove a filter widget from the global settings, exclude attributes, and manage custom fields.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'url' => 'https://www.youtube.com/embed/KxgrBJm91Cs',
                        'difficulty' => 'intermediate',
                        'topics' => ['Widget Removal', 'Global Settings', 'Attribute Exclusion', 'Custom Fields'],
                    ],
                    [
                        'title' => __('Best Seo configuration with dynamic ajax product filter', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'description' => __('Learn how to configure SEO settings for the Dynamic Ajax Product Filter plugin.', 'dynamic-ajax-product-filters-for-woocommerce'),
                        'url' => 'https://www.youtube.com/embed/hmBXO2wC378',
                        'difficulty' => 'intermediate',
                        'topics' => ['SEO Configuration', 'Meta Tags', 'URL Structure', 'Robots.txt'],
                    ],
                    // [
                    //     'number' => 9,
                    //     'title' => __('Location Information Display & Maps', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_8',
                    //     'duration' => '7:45',
                    //     'difficulty' => 'advanced',
                    //     'shortcode' => '[mulopimfwc_location_info layout="tabs" search="yes"]',
                    //     'parameters' => [
                    //         [
                    //             'key' => 'id',
                    //             'desc' => __('Specific location ID(s)', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'id="123" or id="123,456,789"'
                    //         ],
                    //         [
                    //             'key' => 'slug',
                    //             'desc' => __('Location slug(s)', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'slug="downtown,uptown"'
                    //         ],
                    //         [
                    //             'key' => 'layout',
                    //             'desc' => __('Display layout style', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'auto | tabs | compact | grid'
                    //         ],
                    //         [
                    //             'key' => 'search',
                    //             'desc' => __('Enable location search', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ],
                    //         [
                    //             'key' => 'compact',
                    //             'desc' => __('Compact view for single location', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ],
                    //         [
                    //             'key' => 'limit',
                    //             'desc' => __('Limit number of locations', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'limit="5"'
                    //         ],
                    //         [
                    //             'key' => 'orderby',
                    //             'desc' => __('Sort locations by', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'name | id | count'
                    //         ],
                    //         [
                    //             'key' => 'order',
                    //             'desc' => __('Sort order', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'ASC | DESC'
                    //         ]
                    //     ],
                    // ],
                    // [
                    //     'number' => 10,
                    //     'title' => __('Location-Based Product Recommendations', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'description' => __('Display popular products for each location using customer insights and analytics. Learn to track customer preferences, show location-specific recommendations, and boost sales with personalized product suggestions.', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_10',
                    //     'duration' => '6:30',
                    //     'difficulty' => 'intermediate',
                    //     'shortcode' => '[mulopimfwc_location_recommendations limit="8" columns="4" title="Popular at {location}"]',
                    //     'parameters' => [
                    //         [
                    //             'key' => 'limit',
                    //             'desc' => __('Number of products to display', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'limit="8"'
                    //         ],
                    //         [
                    //             'key' => 'columns',
                    //             'desc' => __('Grid columns layout', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'columns="4"'
                    //         ],
                    //         [
                    //             'key' => 'title',
                    //             'desc' => __('Section title (use {location} placeholder)', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'title="Popular at {location}"'
                    //         ],
                    //         [
                    //             'key' => 'show_title',
                    //             'desc' => __('Display section title', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ],
                    //         [
                    //             'key' => 'show_badge',
                    //             'desc' => __('Show popularity badge', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ],
                    //         [
                    //             'key' => 'orderby',
                    //             'desc' => __('Sort products by', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'popularity'
                    //         ]
                    //     ],
                    // ],
                    // [
                    //     'number' => 11,
                    //     'title' => __('Location Selector for specific Product', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'description' => __('Customize the location selector display on product pages and throughout your store. Learn to use shortcodes, and enhance customer experience with multiple layout options.', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_6',
                    //     'duration' => '6:50',
                    //     'difficulty' => 'intermediate',
                    //     'shortcode' => '[mulopimfwc_location_selector product_id="123" layout="buttons" label="Select Location:"]',
                    //     'topics' => ['Location Selector', 'Shortcodes', 'Customization'],
                    //     'parameters' => [
                    //         [
                    //             'key' => 'product_id',
                    //             'desc' => __('Specific product ID (auto-detects if not provided)', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'product_id="123"'
                    //         ],
                    //         [
                    //             'key' => 'layout',
                    //             'desc' => __('Selector layout style', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'list | buttons | select'
                    //         ],
                    //         [
                    //             'key' => 'label',
                    //             'desc' => __('Custom label text', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'label="Choose Store:"'
                    //         ]
                    //     ],
                    //     'additional_shortcode' => '[mulopimfwc_store_location_selector enable_user_locations="on"]'
                    // ],
                    // [
                    //     'number' => 12,
                    //     'title' => __('AJAX Product Filter by Location & Stock', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'description' => __('Learn how to use the AJAX-powered product filter to allow customers to filter products by location and stock status without page reload. Includes caching, pagination support, and shortcode usage.', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_12',
                    //     'duration' => '7:30',
                    //     'difficulty' => 'intermediate',
                    //     'shortcode' => '[mulopimfwc_product_filter]',
                    //     'topics' => ['Product Filter', 'AJAX Filtering', 'Location Filtering', 'Stock Filtering', 'Shortcodes'],
                    //     'parameters' => [
                    //         [
                    //             'key' => 'location',
                    //             'desc' => __('Show location filter dropdown', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ],
                    //         [
                    //             'key' => 'stock',
                    //             'desc' => __('Show stock status filter dropdown', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'value' => 'yes | no'
                    //         ]
                    //     ]
                    // ],
                    // [
                    //     'number' => 13,
                    //     'title' => __('Social Notifications: Microsoft Teams Webhook', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'description' => __('Configure social alerts (orders, stock, digests) using a Microsoft Teams incoming webhook. Covers creating the webhook in Teams and pasting it into this plugin.', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_TEAMS',
                    //     'duration' => '4:10',
                    //     'difficulty' => 'intermediate',
                    //     'topics' => ['Teams', 'Webhooks', 'Notifications', 'Slack/Discord/Teams'],
                    // ],
                    // [
                    //     'number' => 14,
                    //     'title' => __('API & Webhook Integration: Bulk Inventory Sync', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'description' => __('Complete guide to integrating your WMS, POS, or ERP systems with the plugin using REST API endpoints and webhooks. Learn how to sync inventory in bulk via CSV or API, set up real-time webhook updates, generate API keys, configure authentication, and automate inventory management across multiple locations. This tutorial covers bulk operations, single product updates, CSV import/export, webhook configuration, and best practices for external system integration.', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //     'url' => 'https://www.youtube.com/embed/VIDEO_ID_API_WEBHOOK',
                    //     'duration' => '12:45',
                    //     'difficulty' => 'advanced',
                    //     'topics' => ['REST API', 'Webhooks', 'WMS Integration', 'POS Integration', 'Bulk Sync', 'CSV Import', 'Inventory Automation', 'API Authentication'],
                    //     'example_requests' => [
                    //         'get_api_keys' => [
                    //             'title' => __('How to Get API Key & Webhook Secret', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'GET',
                    //             'url' => 'WordPress Admin → Multi Location Products → API & Webhooks',
                    //             'headers' => null,
                    //             'body' => [
                    //                 'step_1' => 'Navigate to WordPress Admin Dashboard',
                    //                 'step_2' => 'Go to: Multi Location Products → API & Webhooks',
                    //                 'step_3' => 'Scroll to "API Authentication" section',
                    //                 'step_4' => 'Click "Generate API Key" button (if no key exists)',
                    //                 'step_5' => 'Copy the generated API key immediately',
                    //                 'step_6' => 'Click "Generate Webhook Secret" button (if no secret exists)',
                    //                 'step_7' => 'Copy the generated webhook secret immediately',
                    //                 'step_8' => 'Store both keys securely (they are shown only once)',
                    //                 'important_note' => 'If you lose your keys, you must generate new ones. Old keys will no longer work.',
                    //             ],
                    //             'response' => [
                    //                 'api_key_location' => 'Displayed in "API Key" field after generation',
                    //                 'webhook_secret_location' => 'Displayed in "Webhook Secret" field after generation',
                    //                 'security_warning' => 'Keys are displayed only once for security. Copy them immediately.',
                    //                 'usage_api_key' => 'Use in X-API-Key header for REST API requests',
                    //                 'usage_webhook_secret' => 'Use in X-Webhook-Secret header for webhook requests',
                    //             ]
                    //         ],
                    //         'bulk_sync' => [
                    //             'title' => __('Bulk Sync Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'POST',
                    //             'url' => '/mulopimfwc/v1/inventory/bulk-sync',
                    //             'headers' => [
                    //                 'Content-Type: application/json',
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //             'body' => [
                    //                 'items' => [
                    //                     [
                    //                         'sku' => 'PROD-001',
                    //                         'location_slug' => 'main-store',
                    //                         'stock' => 100,
                    //                         'regular_price' => '29.99',
                    //                         'sale_price' => '24.99'
                    //                     ],
                    //                     [
                    //                         'product_id' => 123,
                    //                         'location_id' => 5,
                    //                         'stock' => 50,
                    //                         'regular_price' => '19.99'
                    //                     ]
                    //                 ]
                    //             ],
                    //             'response' => [
                    //                 'success' => true,
                    //                 'message' => 'Processed 2 items. 2 succeeded, 0 failed.',
                    //                 'results' => [
                    //                     'success' => 2,
                    //                     'failed' => 0,
                    //                     'errors' => []
                    //                 ]
                    //             ]
                    //         ],
                    //         'single_update' => [
                    //             'title' => __('Single Product Update Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'POST',
                    //             'url' => '/mulopimfwc/v1/inventory/update',
                    //             'headers' => [
                    //                 'Content-Type: application/json',
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //             'body' => [
                    //                 'product_id' => 456,
                    //                 'location_id' => 3,
                    //                 'stock' => 25,
                    //                 'regular_price' => '39.99',
                    //                 'sale_price' => '34.99',
                    //                 'backorders' => 'no',
                    //                 'disabled' => false
                    //             ],
                    //             'response' => [
                    //                 'success' => true,
                    //                 'message' => 'Inventory updated successfully.',
                    //                 'data' => [
                    //                     'product_id' => 456,
                    //                     'location_id' => 3,
                    //                     'sku' => 'PROD-456'
                    //                 ]
                    //             ]
                    //         ],
                    //         'export_csv' => [
                    //             'title' => __('Export Inventory CSV Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'GET',
                    //             'url' => '/mulopimfwc/v1/inventory/export?format=csv&location_id=5',
                    //             'headers' => [
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //             'body' => null,
                    //             'response' => [
                    //                 'note' => 'Returns CSV file download with headers: product_id, sku, product_name, location_id, location_slug, location_name, stock, regular_price, sale_price, backorders, disabled'
                    //             ]
                    //         ],
                    //         'export_json' => [
                    //             'title' => __('Export Inventory JSON Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'GET',
                    //             'url' => '/mulopimfwc/v1/inventory/export?format=json',
                    //             'headers' => [
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //             'body' => null,
                    //             'response' => [
                    //                 'success' => true,
                    //                 'count' => 150,
                    //                 'data' => [
                    //                     [
                    //                         'product_id' => 123,
                    //                         'sku' => 'PROD-001',
                    //                         'product_name' => 'Sample Product',
                    //                         'location_id' => 5,
                    //                         'location_slug' => 'main-store',
                    //                         'location_name' => 'Main Store',
                    //                         'stock' => 100,
                    //                         'regular_price' => '29.99',
                    //                         'sale_price' => '24.99',
                    //                         'backorders' => 'no',
                    //                         'disabled' => false
                    //                     ]
                    //                 ]
                    //             ]
                    //         ],
                    //         'webhook' => [
                    //             'title' => __('Webhook Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'POST',
                    //             'url' => '/mulopimfwc/v1/webhook/inventory-update',
                    //             'headers' => [
                    //                 'Content-Type: application/json',
                    //                 'X-Webhook-Secret: your-webhook-secret-here'
                    //             ],
                    //             'body' => [
                    //                 'sku' => 'PROD-001',
                    //                 'location_slug' => 'main-store',
                    //                 'stock' => 75
                    //             ],
                    //             'response' => [
                    //                 'success' => true,
                    //                 'message' => 'Inventory updated via webhook.',
                    //                 'data' => [
                    //                     'product_id' => 123,
                    //                     'location_id' => 5,
                    //                     'sku' => 'PROD-001'
                    //                 ]
                    //             ]
                    //         ],
                    //         'get_locations' => [
                    //             'title' => __('Get Locations Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'GET',
                    //             'url' => '/mulopimfwc/v1/locations',
                    //             'headers' => [
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //             'body' => null,
                    //             'response' => [
                    //                 'success' => true,
                    //                 'count' => 3,
                    //                 'data' => [
                    //                     [
                    //                         'id' => 5,
                    //                         'slug' => 'main-store',
                    //                         'name' => 'Main Store',
                    //                         'description' => 'Our primary retail location'
                    //                     ],
                    //                     [
                    //                         'id' => 6,
                    //                         'slug' => 'warehouse',
                    //                         'name' => 'Warehouse',
                    //                         'description' => 'Distribution center'
                    //                     ]
                    //                 ]
                    //             ]
                    //         ],
                    //         'products' => [
                    //             'title' => __('Get Products Example', 'dynamic-ajax-product-filters-for-woocommerce'),
                    //             'method' => 'GET',
                    //             'url' => '/mulopimfwc/v1/products',
                    //             'headers' => [
                    //                 'X-API-Key: your-api-key-here'
                    //             ],
                    //         ],
                    //     ],
                    // ],
                ];

                ?>
                <!-- Header -->
                <div class="dapfforwc-tutorial-header">
                    <div class="dapfforwc-tutorial-header-content">
                        <svg class="dapfforwc-tutorial-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                        </svg>
                        <div>
                            <h2 class="dapfforwc-tutorial-title">
                                <?php echo esc_html__('Video Tutorials & Documentation', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </h2>
                            <p class="dapfforwc-tutorial-subtitle">
                                <?php echo esc_html__('Step-by-step guides to master location-based inventory management', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="dapfforwc-tutorial-stats">
                        <div class="dapfforwc-stat-item">
                            <span class="dapfforwc-stat-number"><?php echo esc_html(isset($tutorial_videos) && is_array($tutorial_videos) ? count($tutorial_videos) : 0); ?></span>
                            <span class="dapfforwc-stat-label"><?php echo esc_html__('Tutorials', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Tutorials Grid -->
                <div class="dapfforwc-tutorials-container" id="dapfforwc-tutorials-container">
                    <?php

                    foreach ($tutorial_videos as $tutorial) {
                        $difficulty_class = isset($tutorial['difficulty']) ? 'dapfforwc-difficulty-' . $tutorial['difficulty'] : 'dapfforwc-difficulty-beginner';
                    ?>
                        <div class="dapfforwc-tutorial-card">
                            <!-- Video Container -->
                            <div class="dapfforwc-tutorial-video-wrapper">
                                <iframe
                                    class="dapfforwc-tutorial-iframe"
                                    src="<?php echo esc_url($tutorial['url']); ?>"
                                    title="<?php echo esc_attr($tutorial['title']); ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                                <div class="dapfforwc-tutorial-difficulty <?php echo esc_attr($difficulty_class); ?>">
                                    <?php echo esc_html(ucfirst($tutorial['difficulty'])); ?>
                                </div>
                            </div>

                            <!-- Content Container -->
                            <div class="dapfforwc-tutorial-content">
                                <!-- Title -->
                                <h3 class="dapfforwc-tutorial-card-title"><?php echo esc_html($tutorial['title'] ?? ''); ?></h3>

                                <!-- Description -->
                                <p class="dapfforwc-tutorial-description"><?php echo esc_html($tutorial['description'] ?? ''); ?></p>

                                <?php if (isset($tutorial['doc_url']) && !empty($tutorial['doc_url'])) { ?>
                                    <div class="dapfforwc-tutorial-doc-link">
                                        <a href="<?php echo esc_url($tutorial['doc_url']); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html__('Read Full Documentation', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <!-- Topics Covered -->
                                <?php if (isset($tutorial['topics']) && !empty($tutorial['topics'])): ?>
                                    <div class="dapfforwc-tutorial-topics-section">
                                        <div class="dapfforwc-tutorial-section-label">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="currentColor" />
                                            </svg>
                                            <?php echo esc_html__('Topics Covered', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </div>
                                        <div class="dapfforwc-topics-tags">
                                            <?php foreach ($tutorial['topics'] as $topic): ?>
                                                <span class="dapfforwc-topic-tag"><?php echo esc_html($topic ?? ''); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Shortcode Section -->
                                <?php if (isset($tutorial['shortcode']) && !empty($tutorial['shortcode'])): ?>
                                    <div class="dapfforwc-tutorial-shortcode-section">
                                        <div class="dapfforwc-tutorial-section-label">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14">
                                                <path d="M9.4 16.6L4.8 12l4.6-4.6M14.6 16.6l4.6-4.6-4.6-4.6" fill="none" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            <?php echo esc_html__('Related Shortcode', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </div>
                                        <div class="dapfforwc-shortcode-block">
                                            <code><?php echo esc_html($tutorial['shortcode']); ?></code>
                                            <button class="dapfforwc-copy-btn" onclick="copyShortcode(this, '<?php echo esc_attr($tutorial['shortcode']); ?>')" title="<?php echo esc_attr__('Copy shortcode', 'dynamic-ajax-product-filters-for-woocommerce'); ?>">
                                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14">
                                                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z" fill="currentColor" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Parameters Section -->
                                <?php if (isset($tutorial['parameters']) && !empty($tutorial['parameters'])) { ?>
                                    <div class="dapfforwc-tutorial-params-section">
                                        <div class="dapfforwc-tutorial-section-label">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h3.02l4.25-5.7h-3.02l2.75-3.54h-3.02l-4.25 5.7h3.02z" />
                                            </svg>
                                            <?php echo esc_html__('Parameters', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </div>
                                        <div class="dapfforwc-params-list">
                                            <?php foreach ($tutorial['parameters'] as $param) { ?>
                                                <div class="dapfforwc-param-item">
                                                    <div class="dapfforwc-param-key"><?php echo esc_html($param['key']); ?></div>
                                                    <div class="dapfforwc-param-desc"><?php echo esc_html($param['desc']); ?></div>
                                                    <div class="dapfforwc-param-value"><?php echo esc_html($param['value']); ?></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- Usage Examples Section -->
                                <?php if (isset($tutorial['example_requests']) && !empty($tutorial['example_requests'])) { ?>
                                    <div class="dapfforwc-tutorial-examples-section">
                                        <div class="dapfforwc-tutorial-section-label">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14">
                                                <path d="M9.4 16.6L4.8 12l4.6-4.6M14.6 16.6l4.6-4.6-4.6-4.6" fill="none" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            <?php echo esc_html__('Usage Examples', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
                                        </div>
                                        <?php foreach ($tutorial['example_requests'] as $example_key => $example) { ?>
                                            <div class="dapfforwc-example-item">
                                                <div class="dapfforwc-example-header" onclick="toggleExample(this)">
                                                    <div class="dapfforwc-example-title">
                                                        <strong><?php echo esc_html($example['title'] ?? ''); ?></strong>
                                                        <span class="dapfforwc-example-method"><?php echo esc_html($example['method'] ?? 'POST'); ?></span>
                                                    </div>
                                                    <div class="dapfforwc-example-toggle">
                                                        <svg class="dapfforwc-example-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="dapfforwc-example-content">
                                                    <div class="dapfforwc-example-url">
                                                        <span class="dapfforwc-example-label"><?php echo esc_html__('Endpoint:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                                                        <code><?php echo esc_html($example['url'] ?? ''); ?></code>
                                                    </div>
                                                    <?php if (isset($example['headers']) && !empty($example['headers'])) { ?>
                                                        <div class="dapfforwc-example-headers">
                                                            <span class="dapfforwc-example-label"><?php echo esc_html__('Headers:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                                                            <pre><code><?php echo esc_html(implode("\n", $example['headers'])); ?></code></pre>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (isset($example['body']) && !empty($example['body']) && $example['body'] !== null) { ?>
                                                        <div class="dapfforwc-example-body">
                                                            <span class="dapfforwc-example-label"><?php echo esc_html__('Steps / Request Body:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                                                            <?php 
                                                            if (is_array($example['body']) && isset($example['body']['step_1'])) { 
                                                                echo '<div class="dapfforwc-example-steps">';
                                                                foreach ($example['body'] as $key => $value) {
                                                                    if (strpos($key, 'step_') === 0 || strpos($key, 'important_') === 0) {
                                                                        echo '<div class="dapfforwc-example-step-item">';
                                                                        echo '<strong>' . esc_html(ucfirst(str_replace('_', ' ', $key))) . ':</strong>';
                                                                        echo '<span>' . esc_html($value) . '</span>';
                                                                        echo '</div>';
                                                                    }
                                                                }
                                                                echo '</div>';
                                                            } else {
                                                                echo '<pre><code>' . esc_html(json_encode($example['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</code></pre>';
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (isset($example['response']) && !empty($example['response'])) { ?>
                                                        <div class="dapfforwc-example-response">
                                                            <span class="dapfforwc-example-label"><?php echo esc_html__('Response:', 'dynamic-ajax-product-filters-for-woocommerce'); ?></span>
                                                            <pre><code><?php echo esc_html(json_encode($example['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></code></pre>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Enhanced Styles -->
            <style>
                /* Tutorial Section */
                .dapfforwc-tutorial-section {
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    padding: 40px 20px;
                    border-radius: 12px;
                    margin-top: 40px;
                }

                /* Header */
                .dapfforwc-tutorial-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 32px;
                    gap: 20px;
                    flex-wrap: wrap;
                }

                .dapfforwc-tutorial-header-content {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    flex: 1;
                    min-width: 300px;
                }

                .dapfforwc-tutorial-icon {
                    width: 48px;
                    height: 48px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 8px;
                    fill: white;
                    border-radius: 8px;
                    color: white;
                    flex-shrink: 0;
                }

                .dapfforwc-tutorial-title {
                    font-size: 24px;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0;
                    line-height: 1.2;
                }

                .dapfforwc-tutorial-subtitle {
                    font-size: 14px;
                    color: #64748b;
                    margin: 4px 0 0 0;
                }

                /* Stats */
                .dapfforwc-tutorial-stats {
                    display: flex;
                    gap: 24px;
                }

                .dapfforwc-stat-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 4px;
                    padding: 12px 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                }

                .dapfforwc-stat-number {
                    font-size: 24px;
                    font-weight: 700;
                    color: #667eea;
                    line-height: 1;
                }

                .dapfforwc-stat-label {
                    font-size: 12px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                /* Container */
                .dapfforwc-tutorials-container {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                    gap: 24px;
                    margin-bottom: 32px;
                }

                /* Card */
                .dapfforwc-tutorial-card {
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e2e8f0;
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                }

                .dapfforwc-tutorial-card:hover {
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                    border-color: #cbd5e1;
                    transform: translateY(-4px);
                }

                /* Video Wrapper */
                .dapfforwc-tutorial-video-wrapper {
                    position: relative;
                    width: 100%;
                    padding-bottom: 56.25%;
                    background: #1a1a1a;
                    overflow: hidden;
                }

                .dapfforwc-tutorial-iframe {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border: none;
                }

                .dapfforwc-tutorial-badge {
                    position: absolute;
                    top: 12px;
                    left: 12px;
                    background: #2563eb;
                    color: white;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 700;
                    font-size: 14px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }

                .dapfforwc-tutorial-duration {
                    position: absolute;
                    bottom: 12px;
                    right: 12px;
                    background: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }

                .dapfforwc-tutorial-difficulty {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 11px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .dapfforwc-difficulty-beginner {
                    background: rgba(16, 185, 129, 0.9);
                    color: white;
                }

                .dapfforwc-difficulty-intermediate {
                    background: rgba(59, 130, 246, 0.9);
                    color: white;
                }

                .dapfforwc-difficulty-advanced {
                    background: rgba(245, 158, 11, 0.9);
                    color: white;
                }

                /* Content */
                .dapfforwc-tutorial-content {
                    padding: 20px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }

                .dapfforwc-tutorial-card-title {
                    font-size: 16px;
                    font-weight: 600;
                    color: #0f172a;
                    margin: 0;
                    line-height: 1.4;
                }

                .dapfforwc-tutorial-description {
                    font-size: 13px;
                    color: #64748b;
                    line-height: 1.6;
                    margin: 0;
                }

                /* Section Label */
                .dapfforwc-tutorial-section-label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 11px;
                    font-weight: 600;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 8px;
                }

                .dapfforwc-tutorial-section-label svg {
                    fill: #64748b;
                    width: 16px;
                    height: 16px;
                }

                /* Topics */
                .dapfforwc-tutorial-topics-section {
                    margin-top: auto;
                }

                .dapfforwc-topics-tags {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .dapfforwc-topic-tag {
                    display: inline-block;
                    background: #f1f5f9;
                    color: #475569;
                    padding: 4px 10px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: 500;
                    border: 1px solid #e2e8f0;
                }

                /* Shortcode */
                .dapfforwc-tutorial-shortcode-section {
                    margin-top: auto;
                }

                .dapfforwc-shortcode-block {
                    background: #f1f5f9;
                    border: 1px solid #e2e8f0;
                    border-radius: 6px;
                    padding: 10px 12px;
                    font-family: 'Monaco', 'Courier New', monospace;
                    font-size: 11px;
                    color: #0f172a;
                    position: relative;
                    word-break: break-all;
                    line-height: 1.4;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                }

                .dapfforwc-copy-btn {
                    background: #2563eb;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    width: 24px;
                    height: 24px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s;
                    flex-shrink: 0;
                }

                .dapfforwc-copy-btn:hover {
                    background: #1d4ed8;
                }

                /* Parameters */
                .dapfforwc-tutorial-params-section {
                    margin-top: auto;
                }

                .dapfforwc-params-list {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 8px;
                }

                .dapfforwc-param-item {
                    background: #f8fafc;
                    border-left: 3px solid #2563eb;
                    padding: 8px 10px;
                    border-radius: 4px;
                }

                .dapfforwc-param-key {
                    font-weight: 600;
                    color: #0f172a;
                    font-family: 'Monaco', 'Courier New', monospace;
                    font-size: 11px;
                }

                .dapfforwc-param-desc {
                    font-size: 12px;
                    color: #64748b;
                    margin-top: 2px;
                }

                .dapfforwc-param-value {
                    font-size: 11px;
                    color: #475569;
                    font-family: 'Monaco', 'Courier New', monospace;
                    margin-top: 2px;
                    background: white;
                    padding: 2px 4px;
                    border-radius: 3px;
                    display: inline-block;
                }

                /* CTA Section */
                .dapfforwc-tutorial-cta {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 10px;
                    padding: 32px;
                    color: white;
                }

                .dapfforwc-tutorial-cta-content {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    margin-bottom: 24px;
                }

                .dapfforwc-cta-icon {
                    width: 64px;
                    height: 64px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                }

                .dapfforwc-cta-icon svg {
                    fill: white;
                }

                .dapfforwc-tutorial-cta-content h3 {
                    font-size: 22px;
                    font-weight: 700;
                    margin: 0 0 8px 0;
                }

                .dapfforwc-tutorial-cta-content p {
                    font-size: 14px;
                    opacity: 0.95;
                    margin: 0;
                }

                .dapfforwc-tutorial-cta-buttons {
                    display: flex;
                    gap: 12px;
                    flex-wrap: wrap;
                    margin-bottom: 24px;
                }

                .dapfforwc-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 12px 24px;
                    border-radius: 6px;
                    font-weight: 600;
                    font-size: 14px;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .dapfforwc-btn-primary {
                    background: white;
                    color: #667eea;
                }

                .dapfforwc-btn-primary:hover {
                    background: #f8fafc;
                    transform: translateY(-1px);
                }

                .dapfforwc-btn-secondary {
                    background: rgba(255, 255, 255, 0.2);
                    color: white;
                    border: 1px solid rgba(255, 255, 255, 0.3);
                }

                .dapfforwc-btn-secondary:hover {
                    background: rgba(255, 255, 255, 0.3);
                    border-color: rgba(255, 255, 255, 0.5);
                    transform: translateY(-1px);
                }

                .dapfforwc-btn-accent {
                    background: #f59e0b;
                    color: white;
                }

                .dapfforwc-btn-accent:hover {
                    background: #d97706;
                    transform: translateY(-1px);
                }

                /* Support Features */
                .dapfforwc-support-features {
                    display: flex;
                    gap: 24px;
                    flex-wrap: wrap;
                }

                .dapfforwc-support-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                }

                .dapfforwc-support-item svg {
                    flex-shrink: 0;
                }

                /* Usage Examples Section */
                .dapfforwc-tutorial-examples-section {
                    margin-top: 20px;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 20px;
                }

                .dapfforwc-example-item {
                    margin-bottom: 16px;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    overflow: hidden;
                    background: #fff;
                    transition: all 0.3s ease;
                }

                .dapfforwc-example-item:hover {
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }

                .dapfforwc-example-header {
                    padding: 14px 16px;
                    cursor: pointer;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    background: #f8fafc;
                    transition: background 0.2s ease;
                }

                .dapfforwc-example-header:hover {
                    background: #f1f5f9;
                }

                .dapfforwc-example-header.active {
                    background: #e0f2fe;
                }

                .dapfforwc-example-title {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    flex: 1;
                }

                .dapfforwc-example-method {
                    padding: 4px 8px;
                    background: #3b82f6;
                    color: #fff;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: 600;
                    text-transform: uppercase;
                }

                .dapfforwc-example-toggle {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    color: #64748b;
                    font-size: 13px;
                }

                .dapfforwc-example-preview {
                    font-family: monospace;
                    color: #64748b;
                }

                .dapfforwc-example-arrow {
                    width: 16px;
                    height: 16px;
                    transition: transform 0.3s ease;
                }

                .dapfforwc-example-header.active .dapfforwc-example-arrow {
                    transform: rotate(180deg);
                }

                .dapfforwc-example-content {
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s ease;
                    background: #fff;
                }

                .dapfforwc-example-header.active + .dapfforwc-example-content {
                    max-height: 2000px;
                }

                .dapfforwc-example-content > div {
                    padding: 16px;
                    border-top: 1px solid #e2e8f0;
                }

                .dapfforwc-example-content > div:first-child {
                    border-top: none;
                }

                .dapfforwc-example-label {
                    display: block;
                    font-weight: 600;
                    color: #334155;
                    margin-bottom: 8px;
                    font-size: 13px;
                }

                .dapfforwc-example-url code {
                    display: block;
                    padding: 10px;
                    background: #f1f5f9;
                    border-radius: 6px;
                    color: #1e293b;
                    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                    font-size: 13px;
                    word-break: break-all;
                }

                .dapfforwc-example-headers pre,
                .dapfforwc-example-body pre,
                .dapfforwc-example-response pre {
                    margin: 0;
                    padding: 12px;
                    background: #1e293b;
                    border-radius: 6px;
                    overflow-x: auto;
                }

                .dapfforwc-example-headers code,
                .dapfforwc-example-body code,
                .dapfforwc-example-response code {
                    color: #e2e8f0;
                    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                    font-size: 12px;
                    line-height: 1.6;
                }

                .dapfforwc-example-steps {
                    padding: 12px;
                    background: #f8fafc;
                    border-radius: 6px;
                    border-left: 3px solid #3b82f6;
                }

                .dapfforwc-example-step-item {
                    padding: 8px 0;
                    border-bottom: 1px solid #e2e8f0;
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }

                .dapfforwc-example-step-item:last-child {
                    border-bottom: none;
                }

                .dapfforwc-example-step-item strong {
                    color: #1e293b;
                    font-size: 13px;
                }

                .dapfforwc-example-step-item span {
                    color: #64748b;
                    font-size: 13px;
                    padding-left: 12px;
                }
            </style>

            <script>
                function toggleExample(header) {
                    const item = header.closest('.dapfforwc-example-item');
                    const content = item.querySelector('.dapfforwc-example-content');
                    const isActive = header.classList.contains('active');
                    
                    // Close all other examples in the same section
                    const section = item.closest('.dapfforwc-tutorial-examples-section');
                    if (section) {
                        const allItems = section.querySelectorAll('.dapfforwc-example-item');
                        allItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.querySelector('.dapfforwc-example-header').classList.remove('active');
                            }
                        });
                    }
                    
                    // Toggle current example
                    if (isActive) {
                        header.classList.remove('active');
                    } else {
                        header.classList.add('active');
                    }
                }
            </script>
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
 * Handle cache clearing functionality
 */
function dapfforwc_handle_clear_cache()
{
    // Check if the action is correct and nonce is valid
    if (isset($_POST['action']) && $_POST['action'] === 'dapfforwc_clear_cache') {

        // Verify nonce for security
        if (
            !isset($_POST['dapfforwc_clear_cache_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dapfforwc_clear_cache_nonce'])), 'dapfforwc_clear_cache_nonce')
        ) {
            wp_die('Security check failed.');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to perform this action.');
        }

        // Call the cache clearing function
        dapfforwc_clear_woocommerce_caches();

        // Add success message
        add_settings_error(
            'dapfforwc_messages',
            'dapfforwc_cache_cleared',
            esc_html__('Cache cleared successfully! WooCommerce and object caches have been flushed.', 'dynamic-ajax-product-filters-for-woocommerce'),
            'updated'
        );

        // Redirect back to the advance settings tab
        $redirect_url = add_query_arg(array(
            'page' => 'dapfforwc-admin',
            'tab' => 'advance_settings',
            'cache-cleared' => 'true',
            '_wpnonce' => esc_js(wp_create_nonce('dapfforwc_tab_nonce'))
        ), admin_url('admin.php'));

        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_post_dapfforwc_clear_cache', 'dapfforwc_handle_clear_cache');


/**
 * Display admin notice for cache clearing
 */
function dapfforwc_cache_clear_notice()
{

    if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'dapfforwc_tab_nonce')) {

        if (isset($_GET['cache-cleared']) && $_GET['cache-cleared'] == 'true') {
    ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Cache cleared successfully! WooCommerce and object caches have been flushed.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
            </div>
    <?php
        }
    }
}
add_action('admin_notices', 'dapfforwc_cache_clear_notice');



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
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['reset_settings_nonce'])), 'reset_settings_nonce_action')
        ) {
            add_settings_error(
                'dapfforwc_messages',
                'dapfforwc_message',
                esc_html__('Security check failed. Settings were not reset.', 'dynamic-ajax-product-filters-for-woocommerce'),
                'error'
            );
            return;
        }

        // Delete all plugin settings
        delete_option('dapfforwc_filters');
        delete_option('dapfforwc_options');
        delete_option('dapfforwc_style_options');
        delete_option('dapfforwc_template_options');
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
            esc_html__('Dynamic AJAX Product Filters for WooCommerce settings have been reset to defaults.', 'dynamic-ajax-product-filters-for-woocommerce'),
            'updated'
        );

        // Redirect to prevent form resubmission
        $redirect_url = add_query_arg(array(
            'page' => isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'dapfforwc-settings',
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
        <h2><?php esc_html_e('Reset Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h2>
        <p class="description"><?php esc_html_e('Click the button below to reset all plugin settings to their default values. This action cannot be undone.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>

        <form method="post">
            <?php wp_nonce_field('reset_settings_nonce_action', 'reset_settings_nonce'); ?>
            <input type="hidden" name="reset_settings" value="1">
            <button type="submit" class="button button-secondary button-danger"
                onclick="return confirm('<?php esc_html_e('Are you sure you want to reset all settings? This action cannot be undone.', 'dynamic-ajax-product-filters-for-woocommerce'); ?>');">
                <?php esc_html_e('Reset All Settings', 'dynamic-ajax-product-filters-for-woocommerce'); ?>
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
    if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'dapfforwc_tab_nonce')) {
        if (isset($_GET['settings-reset']) && $_GET['settings-reset'] == 'true') {
    ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('All plugin settings have been reset to defaults.', 'dynamic-ajax-product-filters-for-woocommerce'); ?></p>
            </div>
    <?php
        }
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
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    </style>
<?php
}
add_action('admin_head', 'dapfforwc_admin_styles');


// Add this to admin-page.php before the closing PHP tag
function dapfforwc_activate_template()
{
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dapfforwc_template_nonce')) {
        wp_send_json_error('Security check failed.');
    }

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have sufficient permissions to perform this action.');
    }

    // Get the template ID
    $template_id = isset($_POST['template_id']) ? sanitize_text_field(wp_unslash($_POST['template_id'])) : '';

    if (empty($template_id)) {
        wp_send_json_error('No template ID provided.');
    }

    // Get existing template options
    global $template_options;

    // Update the active template
    $template_options['active_template'] = $template_id;

    // Update the option
    update_option('dapfforwc_template_options', $template_options);

    // Send success response
    wp_send_json_success(esc_html__('Template activated successfully.', 'dynamic-ajax-product-filters-for-woocommerce'));
}
add_action('wp_ajax_dapfforwc_activate_template', 'dapfforwc_activate_template');

// --- Admin FAQ renderer ---
function dapfforwc_render_admin_faq()
{
    // You can localize/edit these freely
    $faqs = [
        [
            'q' => __('Will this plugin work with my theme?', 'dynamic-ajax-product-filters-for-woocommerce'),
            'a' => __('Yes—most themes work out of the box. If products don’t update, adjust selectors in Advanced Settings → “Selectors”, then Clear Cache.', 'dynamic-ajax-product-filters-for-woocommerce'),
        ],
        [
            'q' => __('Do the filters support pretty/SEO URLs?', 'dynamic-ajax-product-filters-for-woocommerce'),
            'a' => __('Yes. See the “SEO & Permalinks Setup” tab to configure prefixes and enable attribute-based permalinks.', 'dynamic-ajax-product-filters-for-woocommerce'),
        ],
        [
            'q' => __('How to reorder filter widget?', 'dynamic-ajax-product-filters-for-woocommerce'),
            'a' => __('To reorder, use our built-in Dynamic Ajax Filter block or Elementor widget. You can see the “Form Manage” option there. Just drag & drop for re-order.', 'dynamic-ajax-product-filters-for-woocommerce'),
        ],
        [
            'q' => __('How can I speed up filtering?', 'dynamic-ajax-product-filters-for-woocommerce'),
            'a' => __('Enable caching at the server/plugin level, use smaller per-page sizes, and avoid overly broad queries. Our built-in AJAX cache also helps.', 'dynamic-ajax-product-filters-for-woocommerce'),
        ]
    ];
?>
    <div class="plugincy-dapfforwc-card" style="margin-top:16px;">
        <div class="plugincy-dapfforwc-card-header">
            <div class="plugincy-dapfforwc-card-header-icon" style="background:#6b46c1;">
                <span class="dashicons dashicons-editor-help" style="color:#fff;"></span>
            </div>
            <h3 style="margin:0;"><?php echo esc_html__('Frequently Asked Questions', 'dynamic-ajax-product-filters-for-woocommerce'); ?></h3>
        </div>

        <div class="plugincy-dapfforwc-card-body dapfforwc-faq">
            <?php foreach ($faqs as $i => $item):
                $qid = 'dapfforwc-faq-' . intval($i);
            ?>
                <div class="dapfforwc-faq-item">
                    <button class="dapfforwc-faq-q" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($qid); ?>">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                        <?php echo esc_html($item['q']); ?>
                    </button>
                    <div id="<?php echo esc_attr($qid); ?>" class="dapfforwc-faq-a" hidden>
                        <p><?php echo esc_html($item['a']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .dapfforwc-faq {
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .dapfforwc-faq-item {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff
        }

        .dapfforwc-faq-q {
            width: 100%;
            text-align: left;
            background: #f8fafc;
            border: 0;
            border-radius: 6px 6px 0 0;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #2d3748
        }

        .dapfforwc-faq-q[aria-expanded="true"] {
            background: #edf2f7
        }

        .dapfforwc-faq-q .dashicons {
            transition: transform .2s ease
        }

        .dapfforwc-faq-q[aria-expanded="true"] .dashicons {
            transform: rotate(90deg)
        }

        .dapfforwc-faq-a {
            padding: 10px 12px;
            color: #4a5568
        }

        .dapfforwc-faq-a p {
            margin: 0
        }
    </style>

    <script>
        (function() {
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.dapfforwc-faq-q');
                if (!btn) return;
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                const panelId = btn.getAttribute('aria-controls');
                const panel = document.getElementById(panelId);
                if (!panel) return;
                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                panel.hidden = expanded;
            });
        })();
    </script>
<?php
}

?>