(function(t,a,p){t.TapfiliateObject=a;t[a]=t[a]||function(){ (t[a].q=t[a].q||[]).push(arguments)}})(window,'tap');

tap('create', '<?php echo $tap_account_id ?>', {integration: '<?php echo $integration ?>'});
tap('detect');

<?php
if ($is_converting) {
    echo "tap('conversion', {$external_id_arg}, {$amount_arg}, {$options_arg});";
}
