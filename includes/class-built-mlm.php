<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://pressbuilt.com
 * @since      1.0.0
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Built_Mlm
 * @subpackage Built_Mlm/includes
 * @author     Pressbuilt <inquiries@pressbuilt.com>
 */
class Built_Mlm {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Built_Mlm_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Valid user roles for Vendors
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $vendor_roles     Valid user roles for Vendors
	 */
	private static $vendor_roles = array( 'vendor' );

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'built-mlm';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_shortcodes();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Built_Mlm_Loader. Orchestrates the hooks of the plugin.
	 * - Built_Mlm_i18n. Defines internationalization functionality.
	 * - Built_Mlm_Admin. Defines all hooks for the admin area.
	 * - Built_Mlm_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-built-mlm-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-built-mlm-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-built-mlm-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-built-mlm-public.php';

		$this->loader = new Built_Mlm_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Built_Mlm_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Built_Mlm_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Built_Mlm_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue scripts and styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Administration menus
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menus', 15 );

		// Plugin settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_plugin_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Built_Mlm_Public( $this->get_plugin_name(), $this->get_version() );

		// Vendor shop rewrite rules
		$this->loader->add_filter( 'init', $plugin_public, 'add_rewrite_rules', 0 );

		$this->loader->add_action( 'woocommerce_before_main_content', $plugin_public, 'shop_description', 30 );
		$this->loader->add_filter( 'wp_title', $plugin_public, 'shop_page_title' );

		$this->loader->add_action( 'woocommerce_product_query', $plugin_public, 'vendor_shop_query', 11, 2 );

		//overwrite the order item meta from wcvendors
		$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_public, 'add_vendor_to_order_item_meta', 15, 2 );

		// redirect visitors to a shop page not in a vendor group to the login page, otherwise redirect to the vendor shop page
		//$this->loader->add_action( 'template_redirect', $plugin_public, 'shop_redirect' );

		$this->loader->add_filter( 'woocommerce_checkout_fields' , $plugin_public, 'vendor_checkout_field' );

		// Enqueue scripts and styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register plugin shortcodes
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_shortcodes() {

		$plugin_public = new Built_Mlm_Public( $this->get_plugin_name(), $this->get_version() );

		add_shortcode( 'built_vendors_list',  array( $plugin_public, 'shortcode_vendors_list' ) );
		add_shortcode( 'built_join_vendor_group',  array( $plugin_public, 'shortcode_join_vendor_group' ) );
		add_shortcode( 'built_vendor_dashboard', array( $plugin_public, 'shortcode_vendor_dashboard' ) );
		add_shortcode( 'built_vendor_shop_settings', array( $plugin_public, 'shortcode_vendor_shop_settings' ) );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Built_Mlm_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public static function get_vendor_roles() {
		return self::$vendor_roles;
	}

	// get the group id of a user
	public static function get_user_group_id ( $user_id ) {
		// Try to fail as early as possible for performance
		if ( !class_exists( 'Groups_User' ) ) return;
		if ( !class_exists( 'Groups_Group' ) ) return;

		if ( empty( $user_id ) ) return;

		$options = get_option( 'built_mlm_settings' );
		if ( empty( $options['built_mlm_root_group'] ) ) return;

		$user_group = new Groups_User( $user_id );
		if ( !in_array( $options['built_mlm_root_group'], $user_group->group_ids_deep ) ) return;

		foreach ( $user_group->group_ids as $group_id ) {
			$ancestors = array();
			self::get_group_ancestors( $group_id, $ancestors );
			if ( in_array( $options['built_mlm_root_group'], $ancestors ) ) {
				$vendor_group_id = $group_id;
				break;
			}
		}

		if ( !empty( $vendor_group_id ) ) {
			return $vendor_group_id;
		}
	}

	// get the user id of the vendor role of the vendor group the user belongs to
	public static function get_group_vendor_id( $vendor_group_id ) {

		$vendor_group = new Groups_Group( $vendor_group_id );
		foreach ( $vendor_group->users as $vendor_group_user ) {
			$vendor_roles = array_intersect( $vendor_group_user->roles, self::get_vendor_roles() );
			if ( !empty( $vendor_roles ) ) {
				$vendor_id = $vendor_group_user->user->ID;
				break;
			}
		}

		return $vendor_id;
	}

	public static function get_group_ancestors( $group_id, &$ancestors ) {
		$group = new Groups_Group( $group_id );
		if ( !empty( $group->group->parent_id ) ) {
			$ancestors[] = $group->group->parent_id;
			self::get_group_ancestors( $group->group->parent_id, $ancestors );
		}
	}
	
	public static function custom_woocommerce_auto_complete_order( $order_id ) {
		global $woocommerce;

		if ( !$order_id )
			return;
		$order = new WC_Order( $order_id );
		$order->update_status( 'completed' );
	}

	/**
	 * Fetch users that are sub-vendors of the provided user
	 *
	 */
	public static function get_sub_vendors( $parent_vendor_user_id ) {

		$options = get_option( 'built_mlm_settings' );
		if ( empty( $options['built_mlm_root_group'] ) ) return;

		$parent_vendor_group_id = self::get_user_group_id( $parent_vendor_user_id );

		$vendor_users = get_users( array(
			'role' => 'vendor'
		) );

		$sub_vendors = array();
		foreach ( $vendor_users as $user ) {
			$user_group = new Groups_User( $user->ID );
			if (
				$user->ID != $parent_vendor_user_id &&
				in_array( $parent_vendor_group_id, $user_group->group_ids_deep )
			) {
				$sub_vendors[] = $user;
			}
		}

		return $sub_vendors;
	}

	/**
	 * Given a price and vendor, calculate commissions for that vendor and its ancestors
	 *
	 */
	public static function calculate_vendor_commissions( $vendor_id, $child_vendor_id, $child_commission_rate, $price, &$rates = array(), $root_group_id ) {

		$price = (float) $price;
		$commission_rate = (float) get_user_meta( $vendor_id, 'built_mlm_commission_rate', true );
		$child_commission_rate = (float) $child_commission_rate;

		$rates[$vendor_id] = array(
			'vendor_id' => $vendor_id,
			'vendor_commission_rate' => $commission_rate,
			'child_vendor_id' => $child_vendor_id,
			'child_commission_rate' => $child_commission_rate,
			'net_rate' => $commission_rate - $child_commission_rate,
			'commission_earned' => round( $price * ( ( $commission_rate - $child_commission_rate ) / 100 ), 2 )
		);

		$group_id = self::get_user_group_id( $vendor_id );
		$group = new Groups_Group( $group_id );

		if ( !empty( $group->group->parent_id ) && $group->group->parent_id != $root_group_id ) {
			$parent_vendor_user_id = self::get_group_vendor_id( $group->group->parent_id );
			self::calculate_vendor_commissions( $parent_vendor_user_id, $vendor_id, $commission_rate, $price, $rates, $root_group_id );
		}
	}

	/**
	 * Grabs the vendor ID whether a username or an int is provided
	 * and returns the vendor_id if it's actually a vendor
	 *
	 * @param unknown $input
	 *
	 * @return unknown
	 */
	public static function get_vendor_id( $input )
	{
		if ( empty( $input ) ) {
			return false;
		}

		$users = get_users( array( 'meta_key' => 'built_mlm_shop_slug', 'meta_value' => sanitize_title( $input ) ) );

		if ( !empty( $users ) && count( $users ) == 1 ) {
			$vendor = $users[ 0 ];
		} else {
			$int_vendor = is_numeric( $input );
			$vendor     = !empty( $int_vendor ) ? get_userdata( $input ) : get_user_by( 'login', $input );
		}

		if ( $vendor ) {
			$vendor_id = $vendor->ID;
			// check to make sure the provided $vendor_id is in a vendor group
			if ( self::get_user_group_id( $vendor_id ) ) {
				return $vendor_id;
			}
		}

		return false;
	}

	/**
	 * Retrieve the shop page for a specific vendor
	 *
	 * @param unknown $vendor_id
	 *
	 * @return string
	 */
	public static function get_vendor_shop_page( $vendor_id )
	{
		// check to make sure the provided $vendor_id is in a vendor group
		if ( !self::get_user_group_id( $vendor_id ) ) return;

		$slug   = get_user_meta( $vendor_id, 'built_mlm_shop_slug', true );
		$vendor = !$slug ? get_userdata( $vendor_id )->user_login : $slug;

		$options = get_option( 'built_mlm_settings' );
		if ( $options['built_mlm_permalink_base'] ) {
			$permalink = trailingslashit( $options['built_mlm_permalink_base'] );

			return trailingslashit( home_url( sprintf( '/%s%s', $permalink, $vendor ) ) );
		} else {
			return esc_url( add_query_arg( array( 'vendor_shop' => $vendor ), get_post_type_archive_link( 'product' ) ) );
		}
	}

	/**
	 * Retrieve the shop name for a specific vendor
	 *
	 * @param unknown $vendor_id
	 *
	 * @return string
	 */
	public static function get_vendor_shop_name( $vendor_id )
	{
		$vendor_id = self::get_vendor_id( $vendor_id );
		$name      = $vendor_id ? get_user_meta( $vendor_id, 'built_mlm_shop_name', true ) : false;
		$shop_name = !$name ? get_userdata( $vendor_id )->user_login : $name;

		return $shop_name;
	}

	/**
	 * Generic function for generating commission reports
	 *
	 * @return array
	 */
	public static function get_commissions( $parameters )
	{
		global $wpdb;

		/*
		array(
			'vendor_id' => $vendor_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'include_sub_vendors' => $include_sub_vendors,
			'include_parent_vendors' => $include_parent_vendors
		)
		*/


		$conditions = array();
		if ( !empty( $parameters['start_date'] ) ) {
			$conditions[] = "order.post_date >= '" . $parameters['start_date'] . "'";
		}

		if ( !empty( $parameters['end_date'] ) ) {
			$conditions[] = "order.post_date <= '" . $parameters['end_date'] . "'";
		}

		$vendor_user_ids = array(1);

		$sql = "
			SELECT
				order.post_date as 'order_date',
				item.order_id,
				item.order_item_id,
				item.order_item_name,
				vendor.meta_value as 'vendor_user_id',
				commissions.meta_value as 'commissions'
			FROM {$wpdb->prefix}posts `order`
				INNER JOIN {$wpdb->prefix}woocommerce_order_items item ON
					order.ID = item.order_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta vendor ON
					item.order_item_id = vendor.order_item_id
					AND vendor.meta_key = 'built_mlm_vendor_user_id'
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta commissions ON
					item.order_item_id = commissions.order_item_id
					AND commissions.meta_key = 'built_mlm_commissions'
			WHERE
				order.post_type = 'shop_order'
		";

		if ( !empty( $conditions ) ) {
			$sql .= ' AND ' . implode( ' AND ', $conditions );
		}

		$query = $sql;
		$order_items = $wpdb->get_results( $query, ARRAY_A  );

		$commissions = array();
		foreach ( $order_items as $order_item ) {

			$commission_payouts = maybe_unserialize( $order_item['commissions'] );
			foreach ( $commission_payouts as $payout ) {

				if ( !isset( $commissions[ $payout['vendor_id'] ]['user'] ) ) {
					$commissions[ $payout['vendor_id'] ]['user'] = get_userdata( $payout['vendor_id'] );
				}

				if ( !isset( $commissions[ $payout['vendor_id'] ]['earnings'] ) ) {
					$commissions[ $payout['vendor_id'] ]['earnings'] = 0;
				}

				$commissions[ $payout['vendor_id'] ]['earnings'] += $payout['commission_earned'];
			}

		}
		
		return $commissions;
	}

	/**
	 * Get all dates for orders that contain commission information
	 *
	 * @return array
	 */
	public static function get_commission_dates( $key_format = 'Y-m', $date_format = 'F, Y' )
	{
		global $wpdb;

		$sql = "
			SELECT
				order.post_date as 'order_date'
			FROM {$wpdb->prefix}posts `order`
				INNER JOIN {$wpdb->prefix}woocommerce_order_items item ON
					order.ID = item.order_id
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta commissions ON
					item.order_item_id = commissions.order_item_id
					AND commissions.meta_key = 'built_mlm_commissions'
			WHERE
				order.post_type = 'shop_order'
		";

		$query = $sql;
		$order_items = $wpdb->get_results( $query, ARRAY_A  );

		$dates = array();
		foreach ( $order_items as $order_item ) {
			$key = date( $key_format, strtotime( $order_item['order_date'] ) );
			$date = date( $date_format, strtotime( $order_item['order_date'] ) );
			$dates[$key] = $date;
		}

		return $dates;
	}
}
