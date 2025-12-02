<?php

if (!defined('ABSPATH')) {
    exit;
}

function dapfforwc_get_min_max_price($products, $products_id = []) {
    $loop = $products_id;
    $min_price = null;
    $max_price = null;

    foreach ($loop as $product_id) {
        foreach ($products as $product) {
            if ($product['ID'] == $product_id) {
                $price = (float) $product['price'];

                if (is_null($min_price) || $price < $min_price) {
                    $min_price = $price;
                }

                if (is_null($max_price) || $price > $max_price) {
                    $max_price = $price;
                }
                break;
            }
        }
    }

    if (!is_null($min_price)) {
        $min_price = floor($min_price); // always take lower integer
    }

    if (!is_null($max_price)) {
        $max_price = ceil($max_price); // always take upper integer
    }

    return array('min' => $min_price, 'max' => $max_price);
}


function dapfforwc_getFilteredProductIds($array) {
    // Collect all non-empty filter arrays (indexed numerically)
    $filters = array_filter($array, function($filter) {
        return !empty($filter);
    });

    // Return empty array if no filters are applied
    if (empty($filters)) {
        return [];
    }

    // If only one filter is applied, return its values
    if (count($filters) === 1) {
        return array_values(reset($filters));
    }

    // Intersect all non-empty filters to get common product IDs
    return array_values(call_user_func_array('array_intersect', $filters));
}

function dapfforwc_get_wc_brand_image_by_slug($brand_slug) {
    // Get the term object for the brand slug
    $term = get_term_by('slug', $brand_slug, 'product_brand');

    // Check if the term exists
    if ($term) {
        // Get the thumbnail ID
        $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

        // Get the image URL
        $image_url = wp_get_attachment_url($thumbnail_id);

        // Return the image URL
        return $image_url;
    }
    return false;
}