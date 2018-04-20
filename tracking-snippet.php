<script src="https://script.tapfiliate.com/tapfiliate.js" type="text/javascript" async></script>
<script type="text/javascript">
    (function(t,a,p){t.TapfiliateObject=a;t[a]=t[a]||function(){ (t[a].q=t[a].q||[]).push(arguments)}})(window,'tap');

    tap('create', '<?php echo $tap_account_id ?>');
    tap('detect');

    <?php
    if ($is_converting) {
        echo "tap('conversion', {$external_id_arg}, {$amount_arg}, {$options_arg});";
    }
    ?>
</script>
