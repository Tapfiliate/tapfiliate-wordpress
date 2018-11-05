(function(t,a,p){t.TapfiliateObject=a;t[a]=t[a]||function(){ (t[a].q=t[a].q||[]).push(arguments)}})(window,'tap');

tap('create', '<?php echo $tap_account_id ?>');
tap('detect');

<?php
$external_id_arg = apply_filters('tapfiliate_snippet_external_id_arg', $external_id_arg);
$amount_arg = apply_filters('tapfiliate_snippet_amount_arg', $amount_arg);
$options_arg = apply_filters('tapfiliate_snippet_options_arg', $options_arg);
$is_converting = apply_filters('tapfiliate_snippet_is_converting', $is_converting);
if ($is_converting) {
    echo "tap('conversion', {$external_id_arg}, {$amount_arg}, {$options_arg});";
}