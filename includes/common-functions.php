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

function dapfforwc_format_dimension_bound_value($value)
{
    if ($value === null || !is_numeric($value)) {
        return '';
    }

    $normalized = number_format((float) $value, 6, '.', '');
    $normalized = rtrim(rtrim($normalized, '0'), '.');

    if ($normalized === '' || $normalized === '-0') {
        return '0';
    }

    return $normalized;
}

function dapfforwc_dimension_row_matches_bound_filters(array $dimension_row, array $dimension_filters)
{
    foreach ($dimension_filters as $dimension => $range) {
        $value = $dimension_row[$dimension] ?? '';

        if ($value === '' || !is_numeric($value)) {
            return false;
        }

        $value = (float) $value;

        if (isset($range['min']) && $range['min'] !== null && $value < (float) $range['min']) {
            return false;
        }

        if (isset($range['max']) && $range['max'] !== null && $value > (float) $range['max']) {
            return false;
        }
    }

    return true;
}

function dapfforwc_get_dimension_filter_bounds($products, $products_id = [], $active_dimension_filters = [])
{
    $dimensions = array('length', 'width', 'height', 'weight');
    $bounds = array();

    foreach ($dimensions as $dimension) {
        $bounds[$dimension] = array('min' => '', 'max' => '');
    }

    if (empty($products) || !is_array($products)) {
        return $bounds;
    }

    $product_lookup = array();
    foreach ($products as $product) {
        if (!is_array($product) || empty($product['ID'])) {
            continue;
        }

        $product_lookup[(int) $product['ID']] = $product;
    }

    if (empty($product_lookup)) {
        return $bounds;
    }

    $target_product_ids = !empty($products_id)
        ? array_values(array_unique(array_filter(array_map('intval', (array) $products_id))))
        : array_keys($product_lookup);

    foreach ($dimensions as $target_dimension) {
        $other_dimension_filters = $active_dimension_filters;
        unset($other_dimension_filters[$target_dimension]);

        $min_value = null;
        $max_value = null;

        foreach ($target_product_ids as $product_id) {
            if (!isset($product_lookup[$product_id])) {
                continue;
            }

            $product = $product_lookup[$product_id];
            $dimension_rows = array();

            if (!empty($product['dimension_rows']) && is_array($product['dimension_rows'])) {
                foreach ($product['dimension_rows'] as $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $dimension_rows[] = array(
                        'length' => dapfforwc_format_dimension_bound_value($row['length'] ?? ''),
                        'width' => dapfforwc_format_dimension_bound_value($row['width'] ?? ''),
                        'height' => dapfforwc_format_dimension_bound_value($row['height'] ?? ''),
                        'weight' => dapfforwc_format_dimension_bound_value($row['weight'] ?? ''),
                    );
                }
            }

            if (empty($dimension_rows)) {
                $dimension_rows[] = array(
                    'length' => dapfforwc_format_dimension_bound_value($product['length'] ?? ''),
                    'width' => dapfforwc_format_dimension_bound_value($product['width'] ?? ''),
                    'height' => dapfforwc_format_dimension_bound_value($product['height'] ?? ''),
                    'weight' => dapfforwc_format_dimension_bound_value($product['weight'] ?? ''),
                );
            }

            $seen_rows = array();
            foreach ($dimension_rows as $dimension_row) {
                $row_key = md5(wp_json_encode($dimension_row));
                if (isset($seen_rows[$row_key])) {
                    continue;
                }

                $seen_rows[$row_key] = true;

                if (!dapfforwc_dimension_row_matches_bound_filters($dimension_row, $other_dimension_filters)) {
                    continue;
                }

                $value = $dimension_row[$target_dimension] ?? '';
                if ($value === '' || !is_numeric($value)) {
                    continue;
                }

                $value = (float) $value;

                if ($min_value === null || $value < $min_value) {
                    $min_value = $value;
                }

                if ($max_value === null || $value > $max_value) {
                    $max_value = $value;
                }
            }
        }

        $bounds[$target_dimension] = array(
            'min' => dapfforwc_format_dimension_bound_value($min_value),
            'max' => dapfforwc_format_dimension_bound_value($max_value),
        );
    }

    return $bounds;
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
