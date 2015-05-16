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
	private $vendor_roles = array( 'vendor' );

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

		//add_action( 'woocommerce_product_query', array( $this, 'vendor_shop_query' ), 11, 2 );
	}

	/**
	 * Store the rates and commission earned for each vendor in an array and
	 * attach array to order_itemmeta. This creates a snapshot in time of what
	 * happened so that rates/commissions stay the same.
	 */
	public static function add_vendor_to_order_item_meta( $item_id, $cart_item ) {

		$group_id = Built_Mlm::get_user_group_id( get_current_user_id() );

		if ( !empty( $group_id ) ) {
			$vendor_id = Built_Mlm::get_group_vendor_id( $group_id );

			$options = get_option( 'built_mlm_settings' );

			Built_Mlm::calculate_vendor_commissions( $vendor_id, null, 0, $cart_item['line_total'], $rates, $options['built_mlm_root_group'] );
			
			if ( !empty( $vendor_id ) ) {
				wc_update_order_item_meta( $item_id, 'built_mlm_vendor_user_id', $vendor_id);
				wc_update_order_item_meta( $item_id, 'built_mlm_commissions', $rates);
			}
		}
	}

	public static function vendor_shop_query( $q, $this ) {


		$options = get_option( 'built_mlm_settings' );
		if ( empty( $options['built_mlm_root_group'] ) ) return;

		$root_group = new Groups_Group( $options['built_mlm_root_group'] );

		$vendor_id = Built_Mlm::get_group_vendor_id( $root_group->group_id );

		//$vendor_id   = WCV_Vendors::get_vendor_id( 'root_vendor' );

		if ( !$vendor_id ) return;

		$q->set( 'author', $vendor_id );
	}

	/*
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
	*/

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

	/**
	  * 	list of vendors 
	  * 
	  * 	@param $atts shortcode attributs 
	*/
	public function shortcode_vendors_list( $atts ) {

		$html = ''; 
		
	  	if ( !get_current_user_id() ) {
	  		$html = '<p>Please <a href="'.site_url().'/wp-login.php">log in</a> first.</p>';
	  		return $html;
	  	}

	  	extract( shortcode_atts( array(
	  			'orderby' 		=> 'registered',
	  			'order'			=> 'ASC',
				'per_page'      => '8',
				'columns'       => '4', 
				'show_products'	=> 'yes' 
			), $atts ) );

	  	$paged      = (get_query_var('paged')) ? get_query_var('paged') : 1;   
	  	$offset     = ($paged - 1) * $per_page;

	  	// Hook into the user query to modify the query to return users that have at least one product 
	  	//if ($show_products == 'yes') add_action( 'pre_user_query', array( $this, 'vendors_with_products') );

	  	// Get all vendors 
	  	$vendor_total_args = array ( 
	  		'role' 				=> 'vendor', 
	  		'meta_key' 			=> 'pv_shop_slug', 
  			'meta_value'   		=> '',
			'meta_compare' 		=> '>',
			'orderby' 			=> $orderby,
  			'order'				=> $order,
	  	);

	  	//if ($show_products == 'yes') $vendor_total_args['query_id'] = 'vendors_with_products'; 

	  	$vendor_query = New WP_User_Query( $vendor_total_args ); 
	  	$all_vendors =$vendor_query->get_results(); 

	  	// Get the paged vendors 
	  	$vendor_paged_args = array ( 
	  		'role' 				=> 'vendor', 
	  		'meta_key' 			=> 'pv_shop_slug', 
  			'meta_value'   		=> '',
			'meta_compare' 		=> '>',
			'orderby' 			=> $orderby,
  			'order'				=> $order,
	  		'offset' 			=> $offset, 
	  		'number' 			=> $per_page, 
	  	);

	  	//if ($show_products == 'yes' ) $vendor_total_args['query_id'] = 'vendors_with_products'; 

	  	$vendor_paged_query = New WP_User_Query( $vendor_paged_args ); 
	  	$paged_vendors = $vendor_query->get_results(); 

	  	// Pagination calcs 
		$total_vendors = count($all_vendors);  
		$total_vendors_paged = count($paged_vendors);  
		$total_pages = intval($total_vendors / $per_page) + 1;

	   	ob_start();

	    // Loop through all vendors and output a simple link to their vendor pages
	    foreach ($paged_vendors as $vendor) {
	       wc_get_template( 'shortcode-vendors-list.php', array(
	      												'shop_link'			=> Built_Mlm::get_vendor_shop_page($vendor->ID), 
														'shop_name'			=> $vendor->built_mlm_shop_name, 
														'vendor_id' 		=> $vendor->ID
												), '', dirname( __FILE__ ) . '/partials/' );
	    } // End foreach 
	   	
	   	$html .= '<ul class="wcv_vendorslist">' . ob_get_clean() . '</ul>';

	    if ($total_vendors > $total_vendors_paged) {  
			$html .= '<div class="wcv_pagination">';  
			  $current_page = max( 1, get_query_var('paged') );  
			  $html .= paginate_links( 	array(  
			        'base' => get_pagenum_link(1) . '%_%',  
			        'format' => 'page/%#%/',  
			        'current' => $current_page,  
			        'total' => $total_pages,  
			        'prev_next'    => false,  
			        'type'         => 'list',  
			    ));  
			$html .= '</div>'; 
		}

	    return $html; 
	}

	/**
	 * Renders a form that lets a user join a group.
	 * * Attributes:
	 * - "group" : (required) group name or id
	 * 
	 * @param array $atts attributes
	 * @param string $content not used
	 */
	public static function shortcode_join_vendor_group( $atts, $content = null ) {
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

		$vendor_id = trim( $options['vendor_id'] );
		$group = Built_Mlm::get_user_group_id( $vendor_id );

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
					$leave_group_id = Built_Mlm::get_user_group_id( $user_id );
					if ($leave_group_id) {
						Groups_User_Group::delete( $user_id, $leave_group_id );
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
					$submit_text = sprintf( $options['submit_text'], wp_filter_nohtml_kses( Built_Mlm::get_vendor_shop_name( $vendor_id ) ) );
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
						$output .= sprintf( __( 'You have joined the %s vendor.', GROUPS_PLUGIN_DOMAIN ), wp_filter_nohtml_kses( Built_Mlm::get_vendor_shop_name( $vendor_id ) ) );
						$output .= '</div>';
					}
					else if ( $display_is_member && isset( $current_group ) && $current_group !== false ) {
						$output .= '<div class="groups-join member">';
						$output .= sprintf( __( 'You are with the %s vendor.', GROUPS_PLUGIN_DOMAIN ), wp_filter_nohtml_kses( Built_Mlm::get_vendor_shop_name( $vendor_id ) ) );
						$output .= '</div>';
					}
				}
			}
		}
		return $output;
	}

	/**
	 * Shortcode to display the vendor dashboard
	 *
	 * @since 1.0.0
	 */
	public function shortcode_vendor_dashboard( $atts ) {
		global $wpdb;

		$user_id = get_current_user_id();

		$sub_vendor_users = Built_Mlm::get_sub_vendors( $user_id );

		$vendor_user_ids = array( $user_id );
		foreach ( $sub_vendor_users as $sub_vendor_user ) {
			$vendor_user_ids[] = $sub_vendor_user->ID;
		}

		$sql = "
			SELECT item.order_id, item.order_item_id, item.order_item_name, vendor.meta_value as 'vendor_user_id', commissions.meta_value as 'commissions'
			FROM {$wpdb->prefix}woocommerce_order_items item
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta vendor ON
					item.order_item_id = vendor.order_item_id
					AND vendor.meta_key = 'built_mlm_vendor_user_id'
					AND vendor.meta_value IN (" . implode( ', ', array_fill( 0, count( $vendor_user_ids ), '%d' ) ) . ")
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta commissions ON
					item.order_item_id = commissions.order_item_id
					AND commissions.meta_key = 'built_mlm_commissions'
		";

		$query = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $vendor_user_ids ) );
		$order_items = $wpdb->get_results( $query, ARRAY_A  );

		$line_items = array();
		foreach ( $order_items as $key => $order_item ) {

			$line_item_data = array(
				'order_id' => $order_item['order_id'],
				'order_item_id' => $order_item['order_item_id'],
				'order_item_name' => $order_item['order_item_name'],
				'origin_vendor' => get_userdata( $order_item['vendor_user_id'] ),
				'commission' => 0,
				'sub_vendor_commission' => 0,
				'order' => new WC_Order( $order_item['order_id'] )
			);

			$commissions = unserialize( $order_item['commissions'] );
			foreach ( $commissions as $commission ) {
				if ( $commission['vendor_id'] == $user_id ) {
					$line_item_data['rate'] = $commission['net_rate'];
					$line_item_data['commission'] += $commission['commission_earned'];
				} else {
					$line_item_data['sub_vendor_commission'] += $commission['commission_earned'];
				}

			}
				$line_items[] = $line_item_data;
		}

		ob_start();

		require( dirname( __FILE__) . '/partials/shortcode-vendor-dashboard.php' );

		return ob_get_clean();

	}

	/**
	 * Shortcode to display the vendor shop settings
	 *
	 * @since 1.0.0
	 */
	public function shortcode_vendor_shop_settings( $atts ) {

		$user_id = get_current_user_id();

		if ( !empty( $_POST ) ) {
			update_user_meta( $user_id, 'built_mlm_paypal_email', $_POST['paypal_email'] );
			update_user_meta( $user_id, 'built_mlm_shop_name', $_POST['shop_name'] );
			update_user_meta( $user_id, 'built_mlm_shop_slug', sanitize_title( $_POST['shop_name'] ) );
			update_user_meta( $user_id, 'built_mlm_shop_description', $_POST['shop_description'] );
		}


		//wc_update_order_item_meta( $item_id, apply_filters('wcvendors_sold_by_in_email', __('Sold by', 'wcvendors')), $sold_by);
		wc_update_order_item_meta( 81, 'built_mlm_vendor_user_id', 12);

		ob_start();
		
		require( dirname( __FILE__) . '/partials/shortcode-vendor-shop-settings.php' );

		return ob_get_clean();

	}

	/**
	 *
	 */
	public static function add_rewrite_rules() {
		$options = get_option( 'built_mlm_settings' );
		if ( empty( $options['built_mlm_permalink_base'] ) ) return;
		$permalink = untrailingslashit( $options['built_mlm_permalink_base'] );

		// Remove beginning slash
		if ( substr( $permalink, 0, 1 ) == '/' ) {
			$permalink = substr( $permalink, 1, strlen( $permalink ) );
		}

		add_rewrite_tag( '%vendor_shop%', '([^&]+)' );

		add_rewrite_rule( $permalink . '/([^/]*)/page/([0-9]+)', 'index.php?post_type=product&vendor_shop=$matches[1]&paged=$matches[2]', 'top' );
		add_rewrite_rule( $permalink . '/([^/]*)', 'index.php?post_type=product&vendor_shop=$matches[1]', 'top' );
	}

	public static function shop_page_title( $title ) {
    	if ( is_shop() ) {
			$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
			$vendor_id = Built_Mlm::get_vendor_id( $vendor_shop );
			$shop_name = Built_Mlm::get_vendor_shop_name( $vendor_id );
			if ( !empty( $shop_name) ) 
				return str_replace( __( 'Products', 'woocommerce' ), $shop_name, $title );
		}

		return $title;
	}

	/**
	 * Show the description a vendor sets when viewing products by that vendor
	 */
	public static function shop_description() {
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = Built_Mlm::get_vendor_id( $vendor_shop );

		if ( $vendor_id ) {
			$description = do_shortcode( get_user_meta( $vendor_id, 'built_mlm_shop_description', true ) );

			echo '<div class="pv_shop_description">';
			echo wpautop( wptexturize( wp_kses_post( $description ) ) );
			echo '</div>';
		}
	}

}
