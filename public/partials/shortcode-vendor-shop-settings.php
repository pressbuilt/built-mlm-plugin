<form method="post">
	<div>
		<label for="paypal_email"><?php _e( 'PayPal Email Address', 'built-mlm' ); ?></label>
		<input type="email" name="paypal_email" id="paypal_email" placeholder="paypal@example.com" value="<?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_paypal_email', true ) ); ?>">
	</div>

	<div>
		<label for="shop_name"><?php _e( 'Shop Name', 'built-mlm' ); ?></label>
		<input type="text" name="shop_name" id="shop_name" placeholder="My Shop Name" value="<?php echo esc_attr( get_user_meta( $user_id, 'built_mlm_shop_name', true ) ); ?>">
	</div>

	<div>
		<label>Shop Description</label>

		<?php wp_editor( get_user_meta( $user_id, 'built_mlm_shop_description', true ), 'shop_description' ); ?>
	</div>

	<div>
		<input type="submit" value="Save Settings">
	</div>
</form>