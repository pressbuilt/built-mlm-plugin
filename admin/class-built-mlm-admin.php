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
	 * Valid user roles for Vendors
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $vendor_roles     Valid user roles for Vendors
	 */
	private $vendor_roles = array( 'vendor', 'pending_vendor' );

	/**
	 * Exlucde user roles for Vendors
	 * @since    1.0.1
	 * @access   private
	 * @var      array     $exclude_roles     Valid user roles for Vendors
	 */
	private $exclude_roles = array( 'administrator', 'shop_manager' );

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
		add_submenu_page( 'built-mlm', 'Commission Reports', 'Commission Reports', 'manage_options', 'built-mlm-commission-reports', array( $this, 'manage_commission_reports' ) );

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
		
		// Permalink settings field
		add_settings_field(
			'built_mlm_permalink_base',
			__( 'Shop Permalink Base', $this->plugin_name ),
			array( $this, 'settings_field_shop_permalink_base' ),
			'built-mlm',
			'built_mlm_settings_general',
			array( 'label_for' => 'built_mlm_permalink_base' )
		);
		
		// Vendors page settings field
/*		add_settings_field(
			'built_mlm_vendors_page',
			__( 'Vendors Page', $this->plugin_name ),
			array( $this, 'settings_field_vendors_page' ),
			'built-mlm',
			'built_mlm_settings_general',
			array( 'label_for' => 'built_mlm_vendors_page' )
		);
*/		
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
	 * Output Shop Permalink Base setting field
	 *
	 * @since    1.0.0
	 */
	function settings_field_shop_permalink_base( $args ) {

		$options = get_option( 'built_mlm_settings' );

		$permalink_text = '';
		if ( isset( $options['built_mlm_permalink_base'] ) ) {
			$permalink_text = $options['built_mlm_permalink_base'];
		}

 		?>
 		<input type="text" name="built_mlm_settings[built_mlm_permalink_base]" id="built_mlm_permalink_base" class="regular-text" value="<?php echo $permalink_text; ?>">
 		<p><small>e.g., <code>http://example.com/<strong>[your_permalink_base]</strong>/[vendor_name]/</code></small><br>
 			Note: you may need to re-save permalinks after setting the above.</p>
		<?php
	}

	/**
	 * Output Root Vendor Group setting field
	 *
	 * @since    1.0.0
	 */
	function settings_field_vendors_page( $args ) {

		$options = get_option( 'built_mlm_settings' );

		$selected = '';
		if ( isset( $options['built_mlm_vendors_page'] ) ) {
			$selected = $options['built_mlm_vendors_page'];
		}

		// Fetch all pages that have been published
		$pages = get_pages();

 		?>
 		<select name="built_mlm_settings[built_mlm_vendors_page]" id="built_mlm_vendors_page">
 			<option value="0">Select a page</option>
 			<?php foreach ( $pages as $page ) { ?>
 				<option value="<?php echo $page->ID; ?>" <?php echo ( $page->ID == $selected ? 'selected="selected"' : '' ); ?>><?php echo $page->post_title; ?></option>
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
				case 'built_mlm_permalink_base':
					if ( !ctype_alnum( $option_value ) ) {
						add_settings_error(
							$option_name,
							esc_attr( 'built_mlm_permalink_base' ),
							__( 'Invalid characters for Shop Permalink Base setting. Please remove special characters or spaces', $this->plugin_name ),
							'error'
						);
					}
					break;
				case 'built_mlm_vendors_page':
					if ( !ctype_digit( $option_value ) ) {
						$data[$option_name] = 0;
						add_settings_error(
							$option_name,
							esc_attr( 'built_mlm_vendors_page' ),
							__( 'Invalid option selected for Vendors Page setting', $this->plugin_name ),
							'error'
						);
					}

					// Fetch all pages that have been published
					$page_ids = get_all_page_ids();
					if ( !in_array( $option_value, $page_ids ) ) {
						$data[$option_name] = 0;
						add_settings_error(
							$option_name,
							esc_attr( 'built_mlm_vendors_page' ),
							__( 'Invalid option selected for Vendors Page setting', $this->plugin_name ),
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
	 * Update commission rate user meta values for all vendors
	 *
	 * @since    1.0.0
	 */
	public function updateVendorCommissionRates( $rates ) {
		foreach ( $rates as $user_id => $rate ) {
			update_user_meta( $user_id, 'built_mlm_commission_rate', $rate );
		}
	}

	/**
	 * Update commission rate user meta values for all vendors
	 *
	 * @since    1.0.0
	 */
	public function createNewVendor( $user_id, $parent_vendor_group_id = null ) {

		$user = get_userdata( $user_id );

		if ( empty( $user->ID ) ) {
			return false;
		}

		// Check to see if a group exists for this vendor already
		$group = Groups_Group::read_by_name( $user->user_login );

		if ( !empty( $group ) ) {
			$group_id = $group->group_id;

			// Update group with proper parent
			Groups_Group::update( array(
				'group_id' => $group->group_id,
				'parent_id' => $parent_vendor_group_id
			) );

		} else {

			// Create a new group for this vendor
			$group_id = Groups_Group::create( array(
				'name' => $user->user_login,
				'creator_id' => $user_id,
				'parent_id' => $parent_vendor_group_id
			) );

		}

		// Make sure user is assigned to this group
		Groups_User_Group::create( array(
			'group_id' => $group_id,
			'user_id' => $user_id
		) );

		$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => 'vendor' ) );
		if ( is_wp_error( $user_id ) ) {
			return false;
		}	

		return $group_id;
	}

	/**
	 * Manage commission rates for all vendors
	 *
	 * @since    1.0.0
	 */
	public function manage_commission_rates() {

		if ( !empty( $_POST['commission_rate'] ) ) {
			$this->updateVendorCommissionRates( $_POST['commission_rate'] );
		}

		if ( !empty( $_POST['vendor_user_id'] ) ) {
			$this->createNewVendor( $_POST['vendor_user_id'] , $_POST['parent_vendor_group_id'] );
		}

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
		$indent = 15;
		$this->build_commission_tree_output($group_tree, $output, $indent);

		?>

		<form id="commission-rates" method="post">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php echo __('Vendor (group name -- user name)', $this->plugin_name); ?></th>
						<th><?php echo __('Commission Rate', $this->plugin_name); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php echo $output; ?>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="save-commission-rates-button" id="save-commission-rates-button" class="button button-primary" value="Save Comission Rates">
			</p>
		</form>

		<h3>Add New Vendor</h3>
		<form id="add-vendor" method="post">
			<table class="form-table">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user">User</label>
						</th>
						<td>
							<select name="vendor_user_id" id="vendor-user-id">
							<?php
								$non_vendor_users = get_users( 'orderby=display_name' );
								foreach ( $non_vendor_users as $user ) {

									// We only want to list users who aren't already vendors
									$vendor_roles = array_intersect( $user->roles, $this->vendor_roles );
									if ( !empty( $vendor_roles ) ) continue;

									// Eclude users who are in the $exclude_roles list
									$exclude_roles = array_intersect( $user->roles, $this->exclude_roles );
									if ( !empty( $exclude_roles ) ) continue;

									echo '<option value="' . $user->ID . '">' . esc_html( $user->display_name ) . '</option>';
								}
							?>
							</select>
							<p class="description">Choose an existing user to add as a vendor</p>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user">Parent Vendor</label>
						</th>
						<td>
							<select name="parent_vendor_group_id" id="parent-vendor-group-id">
								<option value="<?php echo $options['built_mlm_root_group']; ?>"><?php echo Groups_Group::read($options['built_mlm_root_group'])->name; ?></option>
							<?php
								$group_select_output = '';
								$this->build_hierarchical_vendor_group_select($group_tree, $group_select_output, $indent = '—');
								echo $group_select_output;
							?>
							</select>
							<p class="description">Choose an parent vendor</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="add-vendor-button" id="add-vendor-button" class="button button-primary" value="Add New Vendor">
			</p>
		</form>
		<?php
	}

	/**
	 * View vendor commission reports
	 *
	 * @since    1.0.0
	 */
	public function manage_commission_reports() {

		if ( !class_exists( 'Groups_Group' ) ) {
			return;
		}

		$options = get_option( 'built_mlm_settings' );

		if ( empty( $options['built_mlm_root_group'] ) ) {
			?>
			<p><?php echo __( 'Please select a root vendor group in the main settings.', $this->plugin_name ); ?></p>
			<?php
		}

		// Apply any date-related filters
		$selected_date = 0;
		$start_date = null;
		$end_date = null;
		if ( !empty( $_POST['date'] ) && preg_match('/[^0-9-]/', $_POST['date'] ) !== false ) {
			$selected_date = $_POST['date'];

			$start_date = $_POST['date'].'-01';
			$end_date = date( 'Y-m-t', strtotime( $start_date ) );
		}

		$commissions = Built_Mlm::get_commissions(array(
		//	'vendor_id' => $vendor_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
		//	'include_sub_vendors' => $include_sub_vendors,
		//	'include_parent_vendors' => $include_parent_vendors
		));

		$dates = Built_Mlm::get_commission_dates();

		require( dirname( __FILE__) . '/partials/commission-reports.php' );
	}


	/**
	 * Recursively build a string that contains a hierarchical view
	 * of the entire commission tree and its users
	 *
	 * @since    1.0.0
	 */
	public function build_commission_tree_output( &$tree, &$output, $indent ) {
		foreach( $tree as $group_id => $nodes ) {
			$group = new Groups_Group( $group_id );
			if ( $group ) {

				$group_users = $group->users;

				if ( !empty( $group_users ) ) {

					foreach ( $group_users as $group_user ) {

						$valid_roles = array_intersect( $group_user->roles, $this->vendor_roles );
						
						if ( empty( $valid_roles ) ) continue;

						$commission_rate = get_user_meta( $group_user->ID, 'built_mlm_commission_rate', 1 );

						$commission_rate = !empty( $commission_rate ) ? $commission_rate : 0;

						$output .= '<tr>';
						$output .= '<td style="padding-left:' . $indent . 'px;">';
						$output .= $group->name . ' -- ' . $group_user->display_name . ' (' . $group_user->user_email . ')<br>';
						$output .= '</td>';
						$output .= '<td>';
						$output .= '<input id="commission_rate_' . $group_user->ID . '" name="commission_rate[' . $group_user->ID . ']" type="number" min="0" max="100" value="' . $commission_rate. '" />%';
						$output .= '</td>';
						$output .= '</tr>';
					}
				}

			}

			if ( !empty( $nodes ) ) {
				$this->build_commission_tree_output( $nodes, $output, $indent + 15 );
			}
		}
	}


	/**
	 * Recursively build a string that contains a hierarchical view
	 * of the entire commission tree and its users
	 *
	 * @since    1.0.0
	 */
	public function build_hierarchical_vendor_group_select( &$tree, &$output, $indent ) {
		foreach( $tree as $group_id => $nodes ) {
			$group = new Groups_Group( $group_id );
			if ( $group ) {
				$output .= '<option value="' . $group_id . '">' . $indent . ' ' . $group->name . '</option>';
			}

			if ( !empty( $nodes ) ) {
				$this->build_hierarchical_vendor_group_select( $nodes, $output, $indent . '—' );
			}
		}
	}

	/**
	 * Show the vendor shop fields
	 *
	 * @param unknown $user
	 */
	public function show_extra_profile_fields( $user ) {
		if ( !Built_Mlm::get_vendor_id( $user->ID ) ) return;
		?>
		<h3>Built MLM</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th><label for="built_mlm_shop_name">Shop name</label></th>
				<td><input type="text" name="built_mlm_shop_name" id="built_mlm_shop_name"
						   value="<?php echo get_user_meta( $user->ID, 'built_mlm_shop_name', true ); ?>" class="regular-text">
				</td>
			</tr>
			<tr>
				<th>Shop slug</label></th>
				<td><?php echo !empty( get_user_meta( $user->ID, 'built_mlm_shop_slug', true ) ) ? get_user_meta( $user->ID, 'built_mlm_shop_slug', true ) : '<em>Undefined. Created from shop name above.</em>' ; ?></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Update the vendor shop fields
	 *
	 * @param int $vendor_id
	 *
	 * @return bool
	 */
	public function save_extra_profile_fields( $vendor_id )
	{
		if ( !current_user_can( 'edit_user', $vendor_id ) ) return false;

		$users = get_users( array( 'meta_key' => 'built_mlm_shop_slug', 'meta_value' => sanitize_title( $_POST[ 'built_mlm_shop_name' ] ) ) );
		if ( empty( $users ) || $users[ 0 ]->ID == $vendor_id ) {
			update_user_meta( $vendor_id, 'built_mlm_shop_name', $_POST[ 'built_mlm_shop_name' ] );
			update_user_meta( $vendor_id, 'built_mlm_shop_slug', sanitize_title( $_POST[ 'built_mlm_shop_name' ] ) );
		}
	}

}
