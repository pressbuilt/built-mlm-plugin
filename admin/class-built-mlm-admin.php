<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pressbuilt.com
 * @since      1.0.0
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Built_Mlm
 * @subpackage Built_Mlm/admin
 * @author     Pressbuilt <inquiries@pressbuilt.com>
 */
class Built_Mlm_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/built-mlm-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/built-mlm-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Create admin area menus and sub-menus
	 *
	 * @since    1.0.0
	 */
	public function admin_menus() {

		// Top level menu
		add_menu_page( 'Built MLM Settings', 'Built MLM', 'manage_options', 'built-mlm' );

		// Sub-menus
		add_submenu_page( 'built-mlm', 'Settings', 'Settings', 'manage_options', 'built-mlm', array( $this, 'main_settings' ) );
		add_submenu_page( 'built-mlm', 'Manage Commission Rates', 'Commission Rates', 'manage_options', 'built-mlm-commission-rates', array( $this, 'manage_commission_rates' ) );

	}

	/**
	 * Main settings page
	 *
	 * @since    1.0.0
	 */
	public function main_settings() {

		$output = '';

		$output .=
			'<div class="wrap">' .
				'<h2>' .
					__( 'Settings', $this->plugin_name ) .
				'</h2>' .
			'</div>';

		echo $output;

	}

	/**
	 * Manage commission ratese for all vendors
	 *
	 * @since    1.0.0
	 */
	public function manage_commission_rates() {

		$output = '';

		$output .=
			'<div class="wrap">' .
				'<h2>' .
					__( 'Manage Commission Rates', $this->plugin_name ) .
				'</h2>' .
			'</div>';

		echo $output;

	}

}
