<?php
function tapfiliate_render_wpeasycart_conversion_code($ec_order_id, $ec_order)
{
    $is_converting = true;

    $external_id_arg = $ec_order_id;
    $amount_arg = $ec_order->sub_total;

    $script = tapfiliate_generate_inline_code($is_converting, null, null, $external_id_arg, $amount_arg, [], null, null, "wp-easy-cart", false);

    wp_add_inline_script("tapfiliate-js", $script);
}
