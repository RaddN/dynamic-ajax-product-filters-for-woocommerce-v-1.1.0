/**
 * Product Filter - Frontend JavaScript
 * Save this as js/product-filter.js in your theme
 */
(function ($) {
    'use strict';

    // Configuration options
    const WPC_FILTER = {
        formSelector: '#product-filter',
        productSelector: '.products',
        paginationSelector: 'nav.woocommerce-pagination',
        filterTriggers: 'input[type="checkbox"], input[type="radio"], select, input[type="range"]',
        searchField: '#search-field',
        searchButton: '.plugincy-search-submit',
        spinnerClass: 'wpc-spinner',
        loadingClass: 'wpc-loading',
        debounceTime: 500, // ms to wait before processing input changes
        autoScrollOffset: 100
    };

    let advancesettings, dapfforwc_options, dapfforwc_seo_permalinks_options;
    let front_page_slug;
    if (typeof dapfforwc_data !== 'undefined' && dapfforwc_data.dapfforwc_advance_settings) {
        advancesettings = dapfforwc_data.dapfforwc_advance_settings;
    }
    if (typeof dapfforwc_data !== 'undefined' && dapfforwc_data.dapfforwc_front_page_slug) {
        front_page_slug = dapfforwc_data.dapfforwc_front_page_slug;
    }
    if (typeof dapfforwc_data !== 'undefined' && dapfforwc_data.dapfforwc_options) {
        dapfforwc_options = dapfforwc_data.dapfforwc_options;
    }
    if (typeof dapfforwc_data !== 'undefined' && dapfforwc_data.dapfforwc_seo_permalinks_options) {
        dapfforwc_seo_permalinks_options = dapfforwc_data.dapfforwc_seo_permalinks_options;
    }

    console.log(dapfforwc_seo_permalinks_options);

    // Store the current filter state
    let filterState = {
        currentRequest: null,
        isLoading: false,
        pendingChanges: false
    };

    /**
     * Initialize the filter functionality
     */
    function initProductFilter() {
        const $form = $(WPC_FILTER.formSelector);

        if ($form.length === 0) {
            console.warn('Filter form not found');
            return;
        }

        // Bind events to filter elements
        bindFilterEvents($form);

        // Handle browser back/forward navigation
        window.addEventListener('popstate', function (event) {
            // Only handle our own state changes
            if (event.state && event.state.wpcFilter) {
                window.location.reload();
            }
        });
    }

    /**
     * Bind events to filter form elements
     */
    function bindFilterEvents($form) {
        // Handle filter changes with debounce
        let debounceTimer;

        // Track checkbox and radio changes
        $form.on('change', WPC_FILTER.filterTriggers, function () {
            clearTimeout(debounceTimer);
            filterState.pendingChanges = true;

            debounceTimer = setTimeout(function () {
                if (filterState.pendingChanges) {
                    handleFilterChange($form);
                }
            }, WPC_FILTER.debounceTime);
        });

        // Handle price range inputs
        $form.on('input', 'input[type="range"]', function () {
            updatePriceDisplay($(this));
        });

        // Handle search button click
        $form.on('click', WPC_FILTER.searchButton, function (e) {
            e.preventDefault();
            handleFilterChange($form);
        });

        // Handle search on enter key
        $(WPC_FILTER.searchField).on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                handleFilterChange($form);
            }
        });

        // Reset rating filter
        $form.on('click', '#reset-rating', function () {
            const $ratingInputs = $form.find('input[name="rating[]"]');
            $ratingInputs.prop('checked', false);
            handleFilterChange($form);
        });
    }

    /**
     * Handle changes to filter form elements
     */
    function handleFilterChange($form) {
        filterState.pendingChanges = false;

        // Check if we're already processing a request
        if (filterState.isLoading) {
            return;
        }

        // Get the serialized form data
        const formData = $form.serialize();

        // Build the query URL
        const currentUrl = window.location.href.split('?')[0];
        const queryString = formData.replace(/\+/g, '%20');
        const fullUrl = currentUrl + (queryString ? '?' + queryString : '');

        // Detect if we're using AJAX or regular page load
        const useAjax = true; // Set to false to disable AJAX and use regular page loads

        if (useAjax) {
            // Use AJAX to update content
            loadFilteredContentAjax(fullUrl, formData);
        } else {
            // Use regular page navigation
            window.history.pushState({ wpcFilter: true }, '', fullUrl);
            window.location.href = fullUrl;
        }
    }

    /**
     * Load filtered content via AJAX
     */
    function loadFilteredContentAjax(url, formData) {
        // Abort any pending request
        if (filterState.currentRequest) {
            filterState.currentRequest.abort();
        }

        // Set loading state
        setLoadingState(true);

        if (dapfforwc_seo_permalinks_options &&
            dapfforwc_seo_permalinks_options.use_attribute_type_in_permalinks === "on") {

                

            // Transform the URL to SEO format
            const seoUrl = transformToSeoUrl(url, dapfforwc_seo_permalinks_options);

            // Use the SEO URL for browser history if not using AJAX mode
            if (dapfforwc_options.use_url_filter !== "ajax") {
                console.log(dapfforwc_seo_permalinks_options.use_attribute_type_in_permalinks);
                url = seoUrl;
            }
        }

        // Add X-Requested-With header for WordPress to detect AJAX
        filterState.currentRequest = $.ajax({
            url: url,
            data: formData,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.success) {
                    if (dapfforwc_options.use_url_filter !== "ajax") {
                        // Update browser history / url with transformed URL if necessary
                        window.history.pushState({ wpcFilter: true }, '', url);
                    }

                    // Update the products container
                    $(WPC_FILTER.productSelector).html(response.data.html);
                    $(WPC_FILTER.paginationSelector).html(response.data.pagination);


                    // Update product count if element exists
                    if ($('.woocommerce-result-count').length) {
                        $('.woocommerce-result-count').text('Showing 1-' +
                            Math.min(response.data.found, 12) + ' of ' + response.data.found + ' results');
                    }

                    // Auto-scroll to products container
                    scrollToProducts();

                    // Trigger events for other scripts
                    $(document).trigger('wpc_filters_updated');
                    $(window).trigger('scroll');
                    $(window).trigger('resize');
                } else {
                    console.error('Filter request failed');
                }
            },
            error: function (xhr, status, error) {
                if (status !== 'abort') {
                    console.error('Filter request failed:', error);
                }
            },
            complete: function () {
                setLoadingState(false);
                filterState.currentRequest = null;
            }
        });
    }

    /**
     * Set loading state
     */
    function setLoadingState(isLoading) {
        filterState.isLoading = isLoading;
        let isloadingenablebyadmin = dapfforwc_options.show_loader === "on";

        if (isLoading && isloadingenablebyadmin) {
            $('html, body').css('cursor', 'wait');
            showSpinner();
            $(WPC_FILTER.formSelector).addClass(WPC_FILTER.loadingClass);
        } else {
            $('html, body').css('cursor', 'auto');
            hideSpinner();
            $(WPC_FILTER.formSelector).removeClass(WPC_FILTER.loadingClass);
        }
    }

    /**
     * Show loading spinner
     */
    function showSpinner() {
        $('#roverlay').show();
        $('#loader').show();
    }

    /**
     * Hide loading spinner
     */
    function hideSpinner() {
        $('#roverlay').hide();
        $('#loader').hide();
    }

    /**
     * Update price range display
     */
    function updatePriceDisplay($rangeInput) {
        const isMin = $rangeInput.hasClass('range-min');
        const value = $rangeInput.val();

        if (isMin) {
            $('#min-price').val(value);
            $('.progress').css('left', (value / $rangeInput.attr('max') * 100) + '%');
        } else {
            $('#max-price').val(value);
            $('.progress').css('right', (100 - (value / $rangeInput.attr('max') * 100)) + '%');
        }
    }

    /**
     * Scroll to products container
     */
    function scrollToProducts() {
        const $target = $(WPC_FILTER.productSelector);

        if ($target.length > 0) {
            $('html, body').animate({
                scrollTop: $target.offset().top - WPC_FILTER.autoScrollOffset
            }, 500);
        }
    }


    /**
     * Transform standard filter URL to SEO-friendly URL format
     * @param {string} url - The original URL with standard filter parameters
     * @param {object} seoOptions - The SEO permalink configuration options
     * @returns {string} - The transformed SEO-friendly URL
     */
    function transformToSeoUrl(url, seoOptions) {
        // Parse the current URL
        const urlObj = new URL(url);
        const searchParams = urlObj.searchParams;

        // Create new URLSearchParams for our SEO-friendly URL
        const seoParams = new URLSearchParams();
        seoParams.append('filters', '1'); // Add the filters=1 parameter

        // Prefixes config from SEO options
        const prefixes = seoOptions.dapfforwc_permalinks_prefix_options;

        // Process category parameters
        if (searchParams.has('category[]')) {
            const categories = searchParams.getAll('category[]');
            if (categories.length > 0) {
                seoParams.append(prefixes.category, categories.join(','));
            }
        }

        // Process tag parameters
        if (searchParams.has('tags[]')) {
            const tags = searchParams.getAll('tags[]');
            if (tags.length > 0) {
                seoParams.append(prefixes.tag, tags.join(','));
            }
        }

        // Process rating parameters
        if (searchParams.has('rating[]')) {
            const ratings = searchParams.getAll('rating[]');
            if (ratings.length > 0) {
                seoParams.append(prefixes.rating, ratings.join(','));
            }
        }

        // Process price parameters
        if (searchParams.has('min_price') || searchParams.has('max_price')) {
            const minPrice = searchParams.get('min_price') || '';
            const maxPrice = searchParams.get('max_price') || '';
            if (minPrice || maxPrice) {
                seoParams.append(prefixes.price, `${minPrice}-${maxPrice}`);
            }
        }

        // Process search parameter
        if (searchParams.has('s')) {
            seoParams.append('s', searchParams.get('s'));
        }

        // Process attribute parameters
        const attributeParams = {};
        for (const [key, value] of searchParams.entries()) {
            // Check if this is an attribute parameter
            if (key.startsWith('attribute[') && key.endsWith('][]')) {
                // Extract attribute name, e.g., 'attribute[brand][]' -> 'brand'
                const attributeName = key.replace('attribute[', '').replace('][]', '');

                if (!attributeParams[attributeName]) {
                    attributeParams[attributeName] = [];
                }
                attributeParams[attributeName].push(value);
            }
        }

        // Add attribute parameters with their SEO prefix
        for (const [attributeName, values] of Object.entries(attributeParams)) {
            if (prefixes.attribute && prefixes.attribute[attributeName]) {
                seoParams.append(prefixes.attribute[attributeName], values.join(','));
            }
        }

        // Build the new URL
        const baseUrl = urlObj.origin + urlObj.pathname;
        const seoQueryString = seoParams.toString();

        return baseUrl + (seoQueryString ? '?' + seoQueryString : '');
    }

    // Initialize on document ready
    $(document).ready(function () {
        initProductFilter();
    });

    // Expose public API
    window.WPC_FILTER_API = {
        refresh: function () {
            handleFilterChange($(WPC_FILTER.formSelector));
        },
        reset: function () {
            const $form = $(WPC_FILTER.formSelector);
            $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
            $form.find('select').prop('selectedIndex', 0);
            $form.find('input[type="text"], input[type="search"]').val('');

            // Reset price range sliders
            const $minRange = $form.find('.range-min');
            const $maxRange = $form.find('.range-max');

            if ($minRange.length && $maxRange.length) {
                $minRange.val($minRange.attr('min'));
                $maxRange.val($maxRange.attr('max'));
                updatePriceDisplay($minRange);
                updatePriceDisplay($maxRange);
            }

            handleFilterChange($form);
        }
    };

})(jQuery);