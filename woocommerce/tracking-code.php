<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_render_woocommerce_code()
{
    $is_converting = false;
    $external_id_arg = null;
    $amount_arg = null;
    $options = [];
    $is_customer_only = false;
    $is_conversion_multi = false;
    $commission_type = null;
    $use_woo_customer_id_for_lifetime = get_option('tap_wc_use_woo_customer_id_for_lifetime');
    $customer_type = null;

    if (function_exists("is_order_received_page") && is_order_received_page() && isset($GLOBALS['order-received'])) {
        $is_converting = true;

        $isWoo3 = tapfiliate_is_woo3();

        $order_id  = apply_filters('woocommerce_thankyou_order_id', absint($GLOBALS['order-received']));
        $order_key = apply_filters('woocommerce_thankyou_order_key', empty($_GET['key']) ? '' : wc_clean($_GET['key']));

        if ($order_id <= 0) return;

        $order = new WC_Order($order_id);
        $order_key_check = $isWoo3 ? $order->get_order_key() : $order->order_key;

        if ($order_key_check !== $order_key) return;

        $containsSubscription = tapfiliate_has_woo_subscriptions() && wcs_order_contains_subscription($order_id);

        $options["meta_data"] = [];

        $commissions = [];
        $discount = $order->get_total_discount();

        $i = 1;
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

            $key = "product" . $i++;
            $line_item = "{$item['name']} - qty: {$item['qty']}";
            $options['meta_data'][$key] = $line_item;

            $commissions[] = [
                "sub_amount" => tapfiliate_woo_round($subtotal),
                "commission_type" => $tapfiliate_product_commission_type ?: $category_commission_type ?: "default",
            ];
        }

        // Check if we have multiple commission types
        $unique_commission_types = array_unique(array_column($commissions, 'commission_type'));
        $is_conversion_multi = count($unique_commission_types) > 1;

        // Get commission type if single commission type
        $commission_type = count($unique_commission_types) === 1 ? $unique_commission_types[0] : "default";

        // Get Customer Id
        $customerId = $use_woo_customer_id_for_lifetime ? $order->get_customer_id() : $order->get_billing_email();

        // Set options
        if ($coupons = $order->get_used_coupons()) {
            $options['coupons'] = array_values($coupons);
        }

        if ($customerId) {
            $options['customer_id'] = $customerId;
        }

        if ($currency = $order->get_currency()) {
            $options['currency'] = $currency;
        }

        $external_id_arg = $isWoo3 ? $order->get_id() : $order->id;
        $amount_arg = $order->get_subtotal() - $discount;

        $is_customer_only = $containsSubscription && $amount_arg === 0.00;
        $customer_type = $is_customer_only ? 'trial' : 'customer';
    }

    $script = tapfiliate_generate_inline_code($is_converting, $is_customer_only, $customer_type, $external_id_arg, $amount_arg, $options, $commission_type, $is_conversion_multi ? $commissions : [], "woocommerce", false);

    wp_add_inline_script("tapfiliate-js", $script);
}
