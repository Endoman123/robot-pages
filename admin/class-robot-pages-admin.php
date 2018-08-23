<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Endoman123
 * @since      1.0.0
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/admin
 * @author     Jared Tulayan <2lyNJ.E@gmail.com>
 */
class Robot_Pages_Admin {

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

		$this->load_partials();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_partials() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/robot-pages-admin-metaboxes.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {
		global $typenow;
		
		if  ( $typenow === 'robot' ) {
			wp_enqueue_style( $this->plugin_name . '-admin-css' , plugin_dir_url( __FILE__ ) . 'css/robot-pages-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {
		global $typenow;

		$handle = $this->plugin_name . '-admin-js';

		if  ( $typenow === 'robot' ) {
			wp_enqueue_media();
			// Registers and enqueues the required javascript.
			wp_register_script( $handle, plugin_dir_url( __FILE__ ) . 'js/robot-pages-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( $handle, 'meta_image',
				array(
					'title' => 'Choose or Upload an Image',
					'button' => 'Use this image',
				)
			);
			wp_enqueue_script( $handle );
		}
	}

	/**
	 * Render the robot page meta boxes
	 * 
	 * This method hooks into the admin_menu action
	 *
	 * @return void
	 */
	public function robot_pages_admin_menu() {
		add_meta_box(
			'robot_season_meta', 
			'FIRST Season', 
			'render_robot_season_meta', 
			'robot', 
			'side',
			'high',
			array(
				'plugin_name' => $this->plugin_name,
				'version' => $this->version
			)
		);
		
		add_meta_box(
			'robot_media_meta', 
			'Robot Media', 
			'render_robot_media_meta', 
			'robot', 
			'normal',
			'high',
			array(
				'plugin_name' => $this->plugin_name,
				'version' => $this->version
			)
		);
		
		add_meta_box(
			'robot_info_meta', 
			'Robot Info', 
			'render_robot_info_meta', 
			'robot', 
			'normal',
			'high',
			array(
				'plugin_name' => $this->plugin_name,
				'version' => $this->version
			)
		);
	}

	/**
	 * Verifies nonce based on the assumption that the action name was "robot_pages_save_meta_boxes"
	 *
	 * @param string $nonce
	 * @return bool
	 */
	private function robot_pages_verify_meta_nonce($nonce) {
		return isset($_POST[$nonce]) && wp_verify_nonce($_POST[$nonce], 'robot_pages_save_meta_boxes');
	}

	/**
	 * Write new value to meta key, or default if value is invalid
	 *
	 * @param string $id
	 * @param string $key
	 * @param string $value
	 * @param string|null $default
	 * @return void
	 */
	private function robot_pages_write_meta(string $id, string $key, string $value, ?string $default) {
		if (isset($value) && trim($value)) {
			$meta = trim($value);
			update_post_meta($id, $key, $meta);
		} else {
			if ( !isset($default) )
				delete_post_meta($id, $key);
			else
				update_post_meta($id, $key, $default);
		}
	}

	/**
	 * Save the robot's custom fields when the post is saved.
	 * Hooks into save_post function
	 *
	 * @param int $postID
	 * @param WP_Post $post
	 * @param bool $update
	 * @return void
	 */
	public function robot_pages_save_custom_fields($postID, $post, $update) {
		// Nonce verification to make sure that the edit request came from
		// a site editor and not some outside source.
		// This exists purely for security reasons, and should not be removed
		if (!$this->robot_pages_verify_meta_nonce('robot_pages_robot_season_nonce'))
			return;
		
		if (!$this->robot_pages_verify_meta_nonce('robot_pages_robot_media_nonce'))
			return;
		
		if (!$this->robot_pages_verify_meta_nonce('robot_pages_robot_info_nonce'))
			return;
			
		// Make sure the current user can even edit the page
		if (!current_user_can('edit_page', $postID))
			return;
		
		// Make sure the edit is being done for robot pages only
		if ($post->post_type !== 'robot')
			return;
			
		// Update post meta
		
		// Season meta
		$this->robot_pages_write_meta($postID, 'robot-year-meta', $_POST['robot_pages_year_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-game-meta', $_POST['robot_pages_game_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-season-desc-meta', $_POST['robot_pages_season_desc_field'], null);   
		
		// Robot media meta
		$this->robot_pages_write_meta($postID, 'robot-icon-meta', $_POST['robot_pages_icon_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-img-meta', $_POST['robot_pages_img_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-robot-reveal-meta', $_POST['robot_pages_robot_reveal_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-game-reveal-meta', $_POST['robot_pages_game_reveal_field'], null);
		
		// Robot info meta
		$this->robot_pages_write_meta($postID, 'robot-length-meta', $_POST['robot_pages_length_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-width-meta', $_POST['robot_pages_width_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-height-meta', $_POST['robot_pages_height_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-weight-meta', $_POST['robot_pages_weight_field'], null);
		$this->robot_pages_write_meta($postID, 'robot-status-meta', $_POST['robot_pages_status_field'], 'Active');
		$this->robot_pages_write_meta($postID, 'robot-features-meta', $_POST['robot_pages_features_field'], '<ul></ul>');
	}

	/**
	 * Add custom columns to robot pages admin display
	 *
	 * @param array $columns
	 * @return void
	 */
	public function set_custom_edit_robot_columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );
		
		$columns['title'] = __( 'Robot Name' );
		$columns['game'] = __( 'Game Name' );
		$columns['year'] = __( 'Year' );
		$columns['status'] = __( 'Status' );
		$columns['date'] = $date;
		
		return $columns;
	}

	/**
	 * Display values in custom columns
	 */
	public function custom_robot_column( $column, $post_id ) {
		switch ( $column ) {
			// display season name
			case 'game' :
				echo get_post_meta( $post_id, 'robot-game-meta', true );
				break;
			// display season year
			case 'year' :
				echo get_post_meta( $post_id, 'robot-year-meta', true );
				break;
			// display robot status
			case 'status' :
				echo ucfirst( get_post_meta( $post_id, 'robot-status-meta', true ) );
				break;
		}
	}

	/**
	 * Allow for sort by custom columns
	 */
	public function set_custom_robot_sortable_columns( $columns ) {
		$columns['game'] = 'game';
		$columns['year'] = 'year';
		$columns['status'] = 'status';
		return $columns;
	}

	/**
	 * Set up sorting to use custom meta
	 */
	public function robot_custom_orderby( $query ) {
		if ( ! is_admin() )
		return;

		$orderby = $query->get('orderby');

		switch ($orderby) {
			case 'game':
				$query->set( 'meta_key', 'robot-game-meta' );
				$query->set( 'orderby', 'meta_value' );
				break;
			case 'year':
				$query->set( 'meta_key', 'robot-year-meta' );
				$query->set( 'orderby', 'meta_value_num' );
				break;
		}
	}
}
