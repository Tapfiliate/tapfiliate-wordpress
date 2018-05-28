<div class="wrap">
<h2>Tapfiliate</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('tapfiliate'); ?>

<table class="form-table">

<tr valign="top">
<th style="width: auto;" scope="row">Tapfiliate account id:<br>
	<span style="font-size:0.8em; color: #aaa;">Can be found on the <a href="https://tapfiliate.com/a/integration/" target="_blank">integrations page</a></span>
</th>
<td><input type="text" name="tap_account_id" value="<?php echo get_option('tap_account_id'); ?>" /></td>
</tr>

<tr valign="top">
<th style="width: auto;" scope="row">Integrate for:</th>
<td>
	<div style="float: left; margin-right:20px">
		<input type="radio" id="integrate_for_wp" value="wp"  name="integrate_for" <?php echo (get_option('integrate_for') == 'wp') ? 'checked' : null; ?>/>
		<label for="integrate_for_wp">Wordpress</label>
	</div>
	<div style="float: left; margin-right:20px">
		<input type="radio" id="integrate_for_wc" value="wc" name="integrate_for"  <?php echo (get_option('integrate_for') == 'wc') ? 'checked' : null; ?>/>
		<label for="integrate_for_wc">WooCommerce</label>
	</div>
	<div style="float: left; margin-right:20px">
		<input type="radio" id="integrate_for_ec" value="ec" name="integrate_for"  <?php echo (get_option('integrate_for') == 'ec') ? 'checked' : null; ?>/>
		<label for="integrate_for_wc">WP Easy Cart</label>
	</div>
</td>
</tr>

<tbody id="integrate_for_wordpress_settings" style="display: none">
	<tr valign="top">
	<th style="width: auto;" scope="row">Conversion/Thank you page:</th>
	<td>
		<select name="thank_you_page">
			<?php
				foreach (get_pages() as $page) {
					$field = "<option value='{$page->post_name}'";
					$field .= (get_option('thank_you_page') === $page->post_name) ? " selected" : null;
					$field .= ">{$page->post_title}</option>";
					echo $field;
				}
			?>
		</select>
	</td>
	</tr>
	<tr valign="top">
	<th style="width: auto;" scope="row">Url parameter: External id <span style="font-size:0.8em; color: #aaa">(optional)</span></th>
	<td>
		<input type="text" name="query_parameter_external_id" value="<?php echo get_option('query_parameter_external_id'); ?>" />
	</td>
	</tr>

	<tr valign="top">
	<th style="width: auto;" scope="row">Url parameter: Conversion amount<span style="font-size:0.8em; color: #aaa">(optional)</span></th>
	<td>
		<input type="text" name="query_parameter_conversion_amount" value="<?php echo get_option('query_parameter_conversion_amount'); ?>" />
	</td>
	</tr>
</tbody>
<tbody id="integrate_for_wordpress_or_woocommerce_settings">
	<tr valign="top">
		<th style="width: auto;" scope="row">Program group id <span style="font-size:0.8em; color: #aaa">(optional)</span></th>
		<td>
			<input type="text" name="program_group" value="<?php echo get_option('program_group'); ?>" />
		</td>
	</tr>

	</table>
</tbody>

<input type="hidden" name="action" value="update" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<script>
jQuery(function() {
	jQuery('[name=integrate_for]').on('change', function(){
		var ifor = jQuery('[name=integrate_for]:checked').val();
		if (ifor == 'wp') {
			jQuery('#integrate_for_wordpress_settings').show();
		} else {
			jQuery('#integrate_for_wordpress_settings').hide();
		}
	});

	jQuery('[name=integrate_for]').change();
});
</script>
