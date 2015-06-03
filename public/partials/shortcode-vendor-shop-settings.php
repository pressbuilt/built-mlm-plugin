<?php 
	$options = get_option( 'built_mlm_settings' );
	$permalink_base = $options['built_mlm_permalink_base'];
?>
<form method="post">
	<div>
		<label for="paypal_email"><?php _e( 'PayPal Email Address', 'built-mlm' ); ?></label>
		<input type="email" name="paypal_email" id="paypal_email" placeholder="paypal@example.com" value="<?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_paypal_email', true ) ); ?>">
	</div>

	<div>
		<label for="shop_name"><?php _e( 'Shop Name', 'built-mlm' ); ?></label>
		<input type="text" name="shop_name" id="shop_name" placeholder="My Shop Name" value="<?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_shop_name', true ) ); ?>">
	</div>

<?php if (get_user_meta( $user_id, 'built_mlm_shop_name', true )) { ?>
	<div>
		<?php _e( 'Shop URL', 'built-mlm' ); ?> 
		<a href="<?php echo site_url().'/'.$permalink_base.'/';?><?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_shop_slug', true ) ).'/'; ?>"><?php echo '/'.$permalink_base.'/';?><?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_shop_slug', true ) ).'/'; ?></a>
	</div>
<?php } ?>

	<div>
		<label>Shop Description</label>

		<?php wp_editor( get_user_meta( $user_id, 'built_mlm_shop_description', true ), 'shop_description' ); ?>
	</div>

	<div>
		<input type="submit" value="Save Settings">
	</div>
</form>