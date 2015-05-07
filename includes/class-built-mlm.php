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

		add_shortcode( 'built_join',  array( __CLASS__, 'built_join' ) );
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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Administration menus
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menus' );

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

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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

	/**
	 * Renders a form that lets a user join a group.
	 * * Attributes:
	 * - "group" : (required) group name or id
	 * 
	 * @param array $atts attributes
	 * @param string $content not used
	 */
	public static function built_join( $atts, $content = null ) {
		$nonce_action = 'groups_action';
		$nonce        = 'nonce_join';
		$output       = "";

		$options = shortcode_atts(
			array(
				'vendor_id'         => '',
				'display_message'   => true,
				'display_is_member' => false,
				'submit_text'       => __( 'Join the %s vendor', GROUPS_PLUGIN_DOMAIN )
			),
			$atts
		);
		extract( $options );

		if ( $display_message === 'false' ) {
			$display_message = false;
		}
		if ( $display_is_member === 'true' ) {
			$display_is_member = true;
		}

		$mlm_options = get_option( 'built_mlm_settings' );
		$group_tree = Groups_Utility::get_group_tree();
		if ( !isset( $group_tree[$mlm_options['built_mlm_root_group']] ) ) {
			die('no group');
		}
		$group_tree = $group_tree[$mlm_options['built_mlm_root_group']];

		$vendor_id = trim( $options['vendor_id'] );
		$vendor = new Groups_User($vendor_id);
		foreach ($vendor->groups as $vendor_group) {
			if ($vendor_group->group->group_id == 1) continue;
			$group = $vendor_group->group->group_id;
		}

		$current_group = Groups_Group::read( $group );
		if ( !$current_group ) {
			$current_group = Groups_Group::read_by_name( $group );
		}
		if ( $current_group ) {
			if ( $user_id = get_current_user_id() ) {
				$submitted     = false;
				$invalid_nonce = false;
				if ( !empty( $_POST['groups_action'] ) && $_POST['groups_action'] == 'join' ) {
					$submitted = true;
					if ( !wp_verify_nonce( $_POST[$nonce], $nonce_action ) ) {
						$invalid_nonce = true;
					}
				}
				if ( $submitted && !$invalid_nonce ) {
					// remove user from current group
					$user = new Groups_User($user_id);
					$leave_group = '';
					foreach ($user->groups as $user_group) {
						if ($user_group->group->group_id == 1) continue;
						$leave_group = $user_group->group;
					}
					if ($leave_group) {
						Groups_User_Group::delete( $user_id, $leave_group->group_id );
					}

					// add user to group
					if ( isset( $_POST['group_id'] ) ) {
						$join_group = Groups_Group::read( $_POST['group_id'] );
						Groups_User_Group::create(
							array(
								'group_id' => $join_group->group_id,
								'user_id' => $user_id
							)
						);
					}
				}
				if ( !Groups_User_Group::read( $user_id, $current_group->group_id ) ) {
					$submit_text = sprintf( $options['submit_text'], wp_filter_nohtml_kses( $current_group->name ) );
					$output .= '<div class="groups-join">';
					$output .= '<form action="#" method="post">';
					$output .= '<input type="hidden" name="groups_action" value="join" />';
					$output .= '<input type="hidden" name="group_id" value="' . esc_attr( $current_group->group_id ) . '" />';
					$output .= '<input type="submit" value="' . $submit_text . '" />';
					$output .=  wp_nonce_field( $nonce_action, $nonce, true, false );
					$output .= '</form>';
					$output .= '</div>';
				} else if ( $display_message ) {
					if ( $submitted && !$invalid_nonce && isset( $join_group ) && $join_group->group_id === $current_group->group_id ) {
						$output .= '<div class="groups-join joined">';
						$output .= sprintf( __( 'You have joined the %s vendor.', GROUPS_PLUGIN_DOMAIN ), wp_filter_nohtml_kses( $join_group->name ) );
						$output .= '</div>';
					}
					else if ( $display_is_member && isset( $current_group ) && $current_group !== false ) {
						$output .= '<div class="groups-join member">';
						$output .= sprintf( __( 'You are with the %s vendor.', GROUPS_PLUGIN_DOMAIN ), wp_filter_nohtml_kses( $current_group->name ) );
						$output .= '</div>';
					}
				}
			}
		}
		return $output;
	}

}
