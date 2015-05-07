<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://pressbuilt.com
 * @since      1.0.0
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/public
 * @author     Pressbuilt <inquiries@pressbuilt.com>
 */
class Built_Mlm_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Valid user roles for Vendors
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $vendor_roles     Valid user roles for Vendors
	 */
	private $vendor_roles = array( 'vendor', 'pending_vendor' );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'woocommerce_product_query', array( $this, 'vendor_shop_query' ), 11, 2 );

		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 10, 2);
	}

	public static function vendor_shop_query( $q, $this ) {
		$vendor_id   = WCV_Vendors::get_vendor_id( 'root_vendor' );

		if ( !$vendor_id ) return;

		$q->set( 'author', $vendor_id );
	}

	public function update_order_meta( $order_id, $posted ) {

		// Try to fail as early as possible for performance
		if ( !class_exists( 'Groups_User' ) ) return;
		if ( !class_exists( 'Groups_Group' ) ) return;

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) return;

		$options = get_option( 'built_mlm_settings' );
		if ( empty( $options['built_mlm_root_group'] ) ) return;

		$user_group = new Groups_User( $user_id );
		if ( !in_array( $options['built_mlm_root_group'], $user_group->group_ids_deep ) ) return;

		foreach ( $user_group->group_ids as $group_id ) {
			$ancestors = array();
			$this->get_group_ancestors( $group_id, $ancestors );
			if ( in_array( $options['built_mlm_root_group'], $ancestors ) ) {
				$vendor_group_id = $group_id;
				break;
			}
		}

		if ( empty( $vendor_group_id ) ) return;

		$vendor_group = new Groups_Group( $vendor_group_id );
		foreach ( $vendor_group->users as $vendor_group_user ) {
			$vendor_roles = array_intersect( $vendor_group_user->roles, $this->vendor_roles );
			if ( !empty( $vendor_roles ) ) {
				$vendor_user = $vendor_group_user;
				break;
			}
		}

		//echo '<pre>' . print_r($vendor_group_user, 1) . '</pre>';
		//echo '<pre>' . print_r($order_id, 1) . '</pre>';
		//echo '<pre>' . print_r($posted, 1) . '</pre>';

		// Set order post meta with vendor_group_user->ID
	}

	private function get_group_ancestors( $group_id, &$ancestors ) {
		$group = new Groups_Group( $group_id );
		if ( !empty( $group->group->parent_id ) ) {
			$ancestors[] = $group->group->parent_id;
			$this->get_group_ancestors( $group->group->parent_id, $ancestors );
		}
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Built_Mlm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Built_Mlm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/built-mlm-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Built_Mlm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Built_Mlm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/built-mlm-public.js', array( 'jquery' ), $this->version, false );

	}

}
