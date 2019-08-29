<?php

/**
 * Check if WooCommerce is activated
 *
 * @return boolean
 */
function tapfiliate_is_woocommerce_activated() {
    return class_exists('WooCommerce');
}

/**
 * Check if WooCommerce v3
 *
 * @return     bool
 */
function tapfiliate_is_woo3() {
    if (tapfiliate_is_woocommerce_activated()) {
        global $woocommerce;
        return version_compare($woocommerce->version, "3.0", ">=");
    }

    return false;
}

/**
 * Rounding function adapted from woocommerce
 *
 * @param      number  $amount  The amount
 *
 * @return     number  Rounded amount
 */
function tapfiliate_woo_round($amount) {
    return number_format((float) $amount, wc_get_price_decimals(), '.', '');
}

/**
 * Check if has WooCommerce subscriptions
 *
 * @return     bool
 */
function tapfiliate_has_woo_subscriptions() {
    return function_exists('wcs_order_contains_subscription');
}

/**
 * Check if we have all the required webhooks
 *
 * @return string none|partial|full
 */
function tapfiliate_get_woocommerce_connection_status() {
    $data_store = WC_Data_Store::load('webhook');

    $webhooks = $data_store->search_webhooks();

    $required_webhooks = [
        "order.deleted",
        "order.updated",
        "order.created",
        "customer.deleted",
        "customer.updated",
        "customer.created"
    ];

    if (tapfiliate_has_woo_subscriptions()) {
        $required_webhooks = array_merge($required_webhooks, [
            "subscription.switched",
            "subscription.updated",
            "subscription.created",
            "subscription.deleted",
        ]);
    }

    $current_webhooks = array_reduce(
        $webhooks,
        function ($carry, $item) {
            $webhook = new WC_Webhook($item);
            $name = $webhook->get_name();
            if (strpos($name, 'Tapfiliate') !== false) {
                $carry[] = $webhook->get_topic();
            }

            return $carry;
        },
        []
    );

    // If there are no webhooks we're not connected
    if (!count($current_webhooks)) {
        return "none";
    }

    $missing_webhooks = array_diff($required_webhooks, array_unique($current_webhooks));

    if (count($missing_webhooks) > 0) {
        if (count($required_webhooks) !== count($missing_webhooks)) {
            echo '<div class="error"><p><strong>Tapfiliate is missing the following webhooks: ' . implode(", ", $missing_webhooks) . '. You can reconnect to Tapfiliate to fix this.</strong></p></div>';
        }

        return "partial";
    }

    return "full";
}
