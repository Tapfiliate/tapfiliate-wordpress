<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add a custom field per product to set commission type
 */
function tapfiliate_product_create_comission_type()
{
    $args = array(
        'id' => 'tapfiliate_product_commission_type',
        'label' => __('Tapfiliate commission type identifier', 'tapfiliate'),
        'class' => 'tapfiliate-commission-type',
        'desc_tip' => true,
        'description' => __('Enter your Tapfiliate commission type identifier. Product commission types will override category commission types', 'tapfiliate'),
    );
    woocommerce_wp_text_input($args);
}

add_action('woocommerce_product_options_general_product_data', 'tapfiliate_product_create_comission_type');

/**
 * Saves WooCommerce product custom fields.
 *
 * @param string $post_id The post identifier
 */
function tapfiliate_product_save_commission_type($post_id)
{
    $product = wc_get_product($post_id);
    $custom_fields_woocommerce_title = isset($_POST['tapfiliate_product_commission_type']) ? $_POST['tapfiliate_product_commission_type'] : '';
    $product->update_meta_data('tapfiliate_product_commission_type', sanitize_text_field($custom_fields_woocommerce_title));
    $product->save();
}

add_action('woocommerce_process_product_meta', 'tapfiliate_product_save_commission_type');

/**
 * Add custom field per category to set commission type
 */
function tapfiliate_category_add_commission_type() {
    ?>
    <div class="form-field">
        <label for="tapfiliate_category_commission_type"><?php _e('Tapfiliate Commission Type', 'wh'); ?></label>
        <input type="text" name="tapfiliate_category_commission_type" id="tapfiliate_category_commission_type" />
        <p class="description"><?php _e('Enter your Tapfiliate commission type identifier, <= 160 character', 'wh'); ?></p>
    </div>
    <?php
}

/**
 * Add custom field per category to set commission type when editing
 *
 * @param string $term The term
 */
function tapfiliate_category_edit_commission_type($term)
{
    // Get term ID
    $term_id = $term->term_id;

    // Retrieve the existing value(s) for this meta field.
    $tapfiliate_category_commission_type = get_term_meta($term_id, 'tapfiliate_category_commission_type', true);

    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="tapfiliate_category_commission_type"><?php _e('Tapfiliate Commission Type', 'wh'); ?></label></th>
        <td>
            <input type="text" name="tapfiliate_category_commission_type" id="tapfiliate_category_commission_type" value="<?php echo esc_attr($tapfiliate_category_commission_type) ? esc_attr($tapfiliate_category_commission_type) : '' ?>"/>
            <p class="description"><?php _e('Enter your Tapfiliate commission type identifier', 'wh'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('product_cat_add_form_fields', 'tapfiliate_category_add_commission_type', 10, 1);
add_action('product_cat_edit_form_fields', 'tapfiliate_category_edit_commission_type', 10, 1);

/**
 * Store commission type
 *
 * @param mixed $term_id The term identifier
 */
function tapfiliate_save_category_commission_type($term_id)
{
    $tapfiliate_category_commission_type = filter_input(INPUT_POST, 'tapfiliate_category_commission_type');
    update_term_meta($term_id, 'tapfiliate_category_commission_type', $tapfiliate_category_commission_type);
}

add_action('edited_product_cat', 'tapfiliate_save_category_commission_type', 10, 1);
add_action('create_product_cat', 'tapfiliate_save_category_commission_type', 10, 1);

function tapfiliate_admin_notice_woo_disabled()
{
    echo '<div class="error"><p><strong>' . sprintf(esc_html__('Tapfiliate requires WooCommerce to be installed and active if you are using Tapfiliate with WooCommerce. You can download %s here.', 'tapfiliate'), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>') . '</strong></p></div>';
}

function tapfiliate_woocommerce_check() {
    if (!tapfiliate_is_woocommerce_activated() && get_option('tap_wc_enabled')) {
        add_action('admin_notices', 'tapfiliate_admin_notice_woo_disabled');
    }
}

add_action('plugins_loaded', 'tapfiliate_woocommerce_check');
