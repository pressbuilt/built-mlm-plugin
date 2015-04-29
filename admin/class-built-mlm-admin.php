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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/built-mlm-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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
	 * Create plugin settings sections, fields and callbacks
	 *
	 * @since    1.0.0
	 */
	function register_plugin_settings() {
		// General settings section
		add_settings_section(
			'built_mlm_settings_general',
			__( 'General', $this->plugin_name ),
			array( $this, 'settings_section_general' ),
			'built-mlm'
		);
		
		// General settings fields
		add_settings_field(
			'built_mlm_root_group',
			__( 'Root Vendor Group', $this->plugin_name ),
			array( $this, 'settings_field_root_vendor_group' ),
			'built-mlm',
			'built_mlm_settings_general',
			array( 'label_for' => 'built_mlm_root_group' )
		);
		
		// Register settings so that $_POST is handled
		register_setting( 'built-mlm', 'built_mlm_settings', array( $this, 'sanitize_setting_values' ) );
	}

	/**
	 * Output general section intro text
	 *
	 * @since    1.0.0
	 */
	function settings_section_general() {
 		echo '<p>' . __( 'Settings that control the behavior of the plugin.', $this->plugin_name ) . '</p>';
	}

	/**
	 * Output Root Vendor Group setting field
	 *
	 * @since    1.0.0
	 */
	function settings_field_root_vendor_group( $args ) {

		if ( !class_exists( 'Groups_Group' ) ) {
			return;
		}

		$options = get_option( 'built_mlm_settings' );

		$selected = '';
		if ( isset( $options['built_mlm_root_group'] ) ) {
			$selected = $options['built_mlm_root_group'];
		}

		// Fetch all groups that have been created in the groups plugin
		$groups = Groups_Group::get_groups();

 		?>
 		<select name="built_mlm_settings[built_mlm_root_group]" id="built_mlm_root_group">
 			<option value="0">Select a group</option>
 			<?php foreach ( $groups as $group ) { ?>
 				<option value="<?php echo $group->group_id; ?>" <?php echo ( $group->group_id == $selected ? 'selected="selected"' : '' ); ?>><?php echo $group->name; ?></option>
 			<?php } ?>
		</select>
		<?php
	}

	/**
	 * Sanitize settings values
	 *
	 * @since    1.0.0
	 */
	public function sanitize_setting_values( $data ) {

		foreach ( $data as $option_name => $option_value ) {
			switch ( $option_name ) {
				case 'built_mlm_root_group':
					if ( !ctype_digit( $option_value ) ) {
						$data[$option_name] = 0;
						add_settings_error(
							$option_name,
							esc_attr( 'built_mlm_root_group' ),
							__( 'Invalid option selected for Root Vendor Group setting', $this->plugin_name ),
							'error'
						);
					}

					// Fetch all groups that have been created in the groups plugin
					$group_ids = Groups_Group::get_group_ids();
					if ( !in_array( $option_value, $group_ids ) ) {
						$data[$option_name] = 0;
						add_settings_error(
							$option_name,
							esc_attr( 'built_mlm_root_group' ),
							__( 'Invalid option selected for Root Vendor Group setting', $this->plugin_name ),
							'error'
						);
					}
					break;
			}
		}

		return $data;

	}

	/**
	 * Main settings page
	 *
	 * @since    1.0.0
	 */
	public function main_settings() {
		?>
		<div class="wrap">
			<h2>
				<?php echo __( 'Settings', $this->plugin_name ); ?>
			</h2>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'built-mlm' ); ?>
				<?php do_settings_sections( 'built-mlm' ); ?>
				<?php submit_button(); ?>
			</form>

		</div>
		<?php
	}

	/**
	 * Manage commission ratese for all vendors
	 *
	 * @since    1.0.0
	 */
	public function manage_commission_rates() {

		if ( !class_exists( 'Groups_Group' ) ) {
			return;
		}

		$options = get_option( 'built_mlm_settings' );

		if ( empty( $options['built_mlm_root_group'] ) ) {
			?>
			<p><?php echo __( 'Please select a root vendor group in the main settings.', $this->plugin_name ); ?></p>
			<?php
		}


		?>
		<div class="wrap">
			<h2>
				<?php echo __( 'Manage Commission Rates', $this->plugin_name ); ?>
			</h2>
		</div>
		<?php


		$group_tree = Groups_Utility::get_group_tree();
		if ( !isset( $group_tree[$options['built_mlm_root_group']] ) ) {
			die('no group');
		}
		$group_tree = $group_tree[$options['built_mlm_root_group']];

		$output = '';
		$this->build_commission_tree_output($group_tree, $output);

		?>

		<form method="post" action="options.php">
			<?php echo $output; ?>
		</form>

		<?php
	}


	/**
	 * Recursively build a string that contains a hierarchical view
	 * of the entire commission tree and its users
	 *
	 * @since    1.0.0
	 */
	public function build_commission_tree_output( &$tree, &$output ) {
		$output .= '<ol>';
		foreach( $tree as $group_id => $nodes ) {
			$output .= '<li>';
			$group = new Groups_Group( $group_id );
			if ( $group ) {
				$group_users = $group->users;
				$output .= '<ul>';
				foreach ( $group_users as $group_user ) {
					$output .= '<li>';
					$output .= $group_user->display_name . ' (' . $group_user->user_email . ')<br>';
					$output .= '<label for="commission_rate_' . $group_user->ID . '">Commission Rate:</label> <input id="commission_rate_' . $group_user->ID . '" name="commission_rate_' . $group_user->ID . '" type="number" min="0" max="100" />%';
					$output .= '</li>';
				}
				$output .= '</ul>';

			}
			if ( !empty( $nodes ) ) {
				$this->build_commission_tree_output( $nodes, $output );
			}
			$output .= '</li>';
		}
		$output .= '</ol>';
	}

}