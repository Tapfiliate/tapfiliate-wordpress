<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add a link to the settings page on the plugins.php page.
 *
 * @param  array  $links List of existing plugin action links.
 * @return array         List of modified plugin action links.
 */
function tapfiliate_settings_link( $links )
{
    $links = array_merge(['<a href="' . esc_url(admin_url('/options-general.php?page=tapfiliate')) . '">' . __('Settings', 'textdomain') . '</a>'], $links);
    return $links;
}

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'tapfiliate_settings_link');
