<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ($shortcode) {
    echo "<script>";
}
?>

(function(t,a,p){t.TapfiliateObject=a;t[a]=t[a]||function(){ (t[a].q=t[a].q||[]).push(arguments)}})(window,'tap');

tap('create', '<?php echo $tap_account_id ?>', {integration: '<?php echo $integration ?>'});
<?php
if (!$shortcode) {
    echo "tap('detect');";
}

if ($is_converting) {
    if ($customer_only) {
        echo "\ntap('{$customer_type}', '{$customer_id_arg}', {$options_arg});";
    } else {
        if ($commissions) {
            echo "\ntap('conversionMulti', {$external_id_arg}, {$options_arg}, {$commissions_arg});";
        } else {
            echo "\ntap('conversion', {$external_id_arg}, {$amount_arg}, {$options_arg}, '{$commission_type}');";
        }
    }
}
if ($shortcode) {
    echo "</script>";
}
