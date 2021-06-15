<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$woo_active = tapfiliate_is_woocommerce_activated();
$woo_connection_status = $woo_active ? tapfiliate_get_woocommerce_connection_status() : "none";
$woo_connected = $woo_connection_status === "full";
$woo_should_reconnect = $woo_connection_status === "partial";

if (isset($_GET['woo_connected']) && $_GET['woo_connected'] && $woo_connected) {
    echo '<div class="notice notice-success is-dismissible"><p><strong>WooCommerce was successfully connected with Tapfiliate</strong></p></div>';
}

if (isset($_GET['woo_connected']) && !$_GET['woo_connected'] && !$woo_connected) {
    echo '<div class="notice notice-success is-dismissible"><p><strong>WooCommerce was successfully disconnected from Tapfiliate</strong></p></div>';
}
?>

<div class="wrap">
<h2>Tapfiliate</h2>

<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields('tapfiliate'); ?>

    <table class="form-table">

        <tr valign="top">
        <th class="titledesc" scope="row"><label for="tap_account_id">Tapfiliate account id</label></th>
        <td><input type="text" name="tap_account_id" id="tap_account_id" value="<?php echo get_option('tap_account_id'); ?>" />
            <p class="description">Your Tapfiliate account id can be found on your <a href="https://app.tapfiliate.com/user/edit/" target="_blank">profile page</a></p>
        </td>
        </tr>

        <tr valign="top">
        <th class="titledesc" scope="row">Enable Woocommerce</th>
            <td>
                <input type="checkbox" id="tap_wc_enabled"
                <?php echo $woo_active ? '' : 'disabled="disabled" title="WooCommerce is not active or installed"' ?>
                name="tap_wc_enabled" <?php echo get_option('tap_wc_enabled') ? 'checked' : null; ?>
                />
                <label for="tap_wc_enabled">WooCommerce</label>
                <p class="description">Enable tracking of WooCommerce orders. This will also enable other WooCommerce features for Tapfiliate, like per product / category commission rates and coupon code tracking.</p>
            </td>
        </tr>

        <tr valign="top">
        <th class="titledesc" scope="row">Enable WP Easy Cart</th>
            <td>
                <input type="checkbox" id="tap_ec_enabled" name="tap_ec_enabled" <?php echo get_option('tap_ec_enabled') ? 'checked' : null; ?>/>
                <label for="tap_ec_enabled">WP Easy Cart</label>
            </td>
        </tr>

        <tbody id="integrate_for_woocommerce_settings" <?php echo get_option('tap_wc_enabled') ? '' : 'style="display: none"'?>>
            <tr valign="top">
            <th class="titledesc" scope="row">Connect with Tapfiliate</th>
            <td>
            <?php
                if (tapfiliate_is_woo3()) {
                    if (!is_ssl()) {
                        echo "<b>Your wordpress site must use SSL to connect to Tapfiliate.</b> <br> <a href='https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/'>Find out more on how to set up SSL</a>.";
                    } else {
                         if ($woo_connected) {
                        ?>
                            <a class="button-secondary button-link-delete" href="https://app.tapfiliate.com/a/integrations/woocommerce/disconnect/?site=<?php echo get_site_url() ?>">Disconnect</a>
                        <?php } else { ?>
                            <a class="button-primary" href="https://app.tapfiliate.com/a/integrations/woocommerce/connect/?site=<?php echo get_site_url() ?>&r=<?php echo $woo_should_reconnect ?>"><?php echo $woo_should_reconnect ? "Reconnect" : "Connect" ?></a>
                        <?php
                        }
                        ?>
                        <p class="description">By connecting WooCommerce to Tapfiliate, you can access advanced features such as auto-handling of subscriptions status changes and automatic refund handling.</p>
                        <?php
                    }
                } else {
                    echo "<b>Only WooCommerce 3.0.0 stores and higher can directly connect to Tapfiliate for automated refund handling and recurring commissions.</b>";
                }
                ?>
            </td>
            </tr>
            <?php
                if (tapfiliate_is_woo3()) {
            ?>
                <tr valign="top">
                    <th class="titledesc" scope="row">Lifetime / recurring commissions handling</th>
                    <td>
                        <input type="checkbox" id="tap_wc_use_woo_customer_id_for_lifetime" name="tap_wc_use_woo_customer_id_for_lifetime" <?php echo get_option('tap_wc_use_woo_customer_id_for_lifetime') ? 'checked' : null; ?>/>
                        <label for="tap_wc_use_woo_customer_id_for_lifetime">Enable Tapfiliate Lifetime and Recurring commissions for registered users <b>only</b>.</label>
                        <p class="description">Recommended when only using WooCommerce Subscriptions. With this option enabled, we will use the WooCommerce customer id instead of the customer email for awarding lifetime / recurring commissions.</p>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>

    </table>

    <input type="hidden" name="action" value="update" />
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<script>
jQuery(function() {
    jQuery('#tap_wc_enabled').on('change', function(){
        var wooCommerceActive = jQuery('#tap_wc_enabled').is(':checked');

        jQuery('#integrate_for_woocommerce_settings').hide();
        if (wooCommerceActive) {
            jQuery('#integrate_for_woocommerce_settings').show();
        }
    });
});
</script>
