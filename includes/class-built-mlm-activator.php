<?php

/**
 * Fired during plugin activation
 *
 * @link       https://pressbuilt.com
 * @since      1.0.0
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Built_Mlm
 * @subpackage Built_Mlm/includes
 * @author     Pressbuilt <inquiries@pressbuilt.com>
 */
class Built_Mlm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		remove_role( 'vendor' );

		add_role( 'vendor', 'Vendor', array(
			'view_woocommerce_reports' => true,
		) );
		
	}

}
