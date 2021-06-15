<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_woocommerce_get_commissions_for_order($order, $discount)
{
    $commissions = [];
    foreach ($order->get_items() as $item) {
        // If total is zero the item has a full discount applied
        $item_subtotal = floatval(tapfiliate_woo_round($item->get_subtotal()));
        if ($item_subtotal === 0.00) {
            $subtotal = 0;
        } else {
            $proportional_discount = ($item_subtotal / tapfiliate_woo_round($order->get_subtotal())) * $discount;
            $subtotal = $item_subtotal - $proportional_discount;
        }

        $product_id = $item->get_product_id();
        $tapfiliate_product_commission_type = get_post_meta($product_id, "tapfiliate_product_commission_type", true);

        $category_commission_type = null;
        if (!$tapfiliate_product_commission_type && $categories = wp_get_post_terms($product_id, 'product_cat')) {
            // We always use the "latest" category as the category commision type
            foreach ( $categories as $category ) {
                $category_commission_type = get_term_meta($category->term_id, 'tapfiliate_category_commission_type', true);
            }
        }

        $commissions[] = [
            "sub_amount" => tapfiliate_woo_round($subtotal),
            "commission_type" => $tapfiliate_product_commission_type ?: $category_commission_type ?: "default",
        ];
    }

    return $commissions;
}

function tapfiliate_woocommerce_get_metadata_for_order($order)
{
    $i = 1;
    $meta_data = [];
    foreach ($order->get_items() as $item) {
        $key = "product" . $i++;
        $line_item = "{$item['name']} - qty: {$item['qty']}";
        $meta_data[$key] = $line_item;
    }

    return $meta_data;
}
