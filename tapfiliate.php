<?php
/**
 * Plugin Name: Tapfiliate
 * Plugin URI: https://wordpress.org/plugins/tapfiliate/
 * Description: Easily integrate the Tapfiliate tracking code.
 * Author: Tapfiliate
 * Author URI: https://tapfiliate.com/
 * Version: 3.0.0
 * Requires at least: 4.4
 * Tested up to: 5.2.2
 * WC requires at least: 2.6
 * WC tested up to: 3.7.0
 * Text Domain: tapfiliate
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
if (!defined('TAPFILIATE_PLUGIN_VERSION'))
      define('TAPFILIATE_PLUGIN_VERSION', '3.0.0');

define('TAPFILIATE_PLUGIN_PATH', plugin_dir_path(__FILE__));
include(TAPFILIATE_PLUGIN_PATH . 'helpers.php');
include(TAPFILIATE_PLUGIN_PATH . 'snippet/generate-inline-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/admin.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/tracking-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'wordpress/admin.php');
include(TAPFILIATE_PLUGIN_PATH . 'wordpress/tracking-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'wp-easy-cart/tracking-code.php');

function activate_tapfiliate()
{
    if (empty(get_option('tap_account_id'))) {
        add_option('tap_account_id', '1-123abc');
    }

    do_action('tapfiliate_plugin_activated');
}

function deactive_tapfiliate()
{
    do_action('tapfiliate_plugin_deactivated');
}

function admin_init_tapfiliate()
{
    register_setting('tapfiliate', 'tap_account_id');
    register_setting('tapfiliate', 'tap_wc_enabled');
    register_setting('tapfiliate', 'tap_wc_connected');
    register_setting('tapfiliate', 'tap_wc_use_woo_customer_id_for_lifetime');
    register_setting('tapfiliate', 'tap_ec_enabled');
}

function admin_menu_tapfiliate()
{
    add_options_page('Tapfiliate', 'Tapfiliate', 'manage_options', 'tapfiliate', 'options_page_tapfiliate');
}

function options_page_tapfiliate()
{
    include(TAPFILIATE_PLUGIN_PATH . 'options.php');
}

function tapfiliate()
{
    wp_enqueue_script("tapfiliate-js", "https://script.tapfiliate.com/tapfiliate.js");

    $woo_enabled = get_option('tap_wc_enabled');
    $woo_active = tapfiliate_is_woocommerce_activated();
    $wp_easycart_active = in_array('wp-easycart/wpeasycart.php', apply_filters('active_plugins', get_option('active_plugins')));

    $is_woocommerce_page = $woo_active && (is_woocommerce() || is_cart() || is_checkout());

    if ((!$woo_active || !$woo_enabled) && !$wp_easycart_active) {
        tapfiliate_render_wordpress_code();
    }

    if ($woo_enabled) {
        tapfiliate_render_woocommerce_code();
    }

    if ($wp_easycart_active) {
        tapfiliate_output_inline_code(false, null, null, [], [], "wp-easy-cart");
    }
}

function tapfiliate_migrate_2_x_to_3_0()
{
    if ($page_title = get_option('thank_you_page')) {
        $page = get_page_by_title($page_title);

        $optionQueryParamExternalId = get_option('query_parameter_external_id') ? " external_id_query_param=${get_option('query_parameter_external_id')}" : "";
        $optionQueryParamConversionAmount = get_option('query_parameter_conversion_amount') ? " external_id_query_param=${get_option('query_parameter_conversion_amount')}" : "";

        $shortcode = trim(
            '<!-- wp:shortcode -->
            [tapfiliate' . $optionQueryParamExternalId . $optionQueryParamConversionAmount . ']
            <!-- /wp:shortcode -->'
        );

        $updatedPage = [
            'ID' => $page->ID,
            'post_content' => $shortcode . $page->post_content,
            'post_title' => $page->post_title,
        ];

        wp_update_post($updatedPage);
        delete_option('thank_you_page');
        delete_option('query_parameter_external_id');
        delete_option('query_parameter_conversion_amount');
    }
}

function tapfiliate_version_check()
{
    $persistedVersion = get_option('tap_plugin_version');
    if (version_compare($persistedVersion, "3.0.0", "<")) {
        tapfiliate_migrate_2_x_to_3_0();
    }

    if ($persistedVersion !== TAPFILIATE_PLUGIN_VERSION) {
        update_option('tap_plugin_version', TAPFILIATE_PLUGIN_VERSION);
    }
}

add_action('plugins_loaded', 'tapfiliate_version_check');

register_activation_hook(__FILE__, 'activate_tapfiliate');
register_deactivation_hook(__FILE__, 'deactive_tapfiliate');

if (is_admin()) {
    add_action('admin_init', 'admin_init_tapfiliate');
    add_action('admin_menu', 'admin_menu_tapfiliate');
}

if (!is_admin()) {
    add_action('wpeasycart_success_page_content_top', 'tapfiliate_render_wpeasycart_conversion_code', 10, 2);
    add_action('wp_enqueue_scripts', 'tapfiliate');
}
