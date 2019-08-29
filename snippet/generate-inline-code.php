<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_generate_inline_code($is_converting, $customer_only, $customer_type = null, $external_id_arg = null, $amount_arg = null, $options = [], $commission_type = null, $commissions = [], $integration = "wordpress", $shortcode = false)
{
    if ($customer_only) {
        $customer_type = $customer_type ?: 'customer';
    }

    $tap_account_id = get_option('tap_account_id');
    $external_id_arg = apply_filters('tapfiliate_snippet_external_id', $external_id_arg);
    $amount_arg = apply_filters('tapfiliate_snippet_amount', $amount_arg);
    $is_converting = apply_filters('tapfiliate_snippet_is_converting', $is_converting);
    $customer_only = apply_filters('tapfiliate_snippet_customer_only', $customer_only);
    $customer_type = apply_filters('tapfiliate_snippet_customer_type', $customer_type);
    $customer_id_arg = apply_filters('tapfiliate_snippet_customer_id', isset($options['customer_id']) ? $options['customer_id'] : null);
    $commission_type = apply_filters('tapfiliate_snippet_commission_type', $commission_type);
    $commissions = apply_filters('tapfiliate_snippet_commissions', $commissions);
    $shortcode = apply_filters('tapfiliate_snippet_shortcode', $shortcode);
    $options = apply_filters('tapfiliate_snippet_options', $options);

    if ($customer_only) {
      unset($options["customer_id"]);
      unset($options["currency"]);
    }

    $options_arg = count($options) ? json_encode($options) : json_encode($options, JSON_FORCE_OBJECT);
    $commissions_arg = count($commissions) ? json_encode($commissions) : json_encode($commissions, JSON_FORCE_OBJECT);

    ob_start();
    include(dirname(__FILE__) . '/tracking-snippet.php');
    $script = ob_get_contents();
    ob_end_clean();

    $script = apply_filters('tapfiliate_snippet', $script);

    return $script;
}
