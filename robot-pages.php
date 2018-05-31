<?php 
/**
 * Plugin Name: Robot pages
 * Description: This plugin adds a custom post type for robots and generates navigation.
 * Version: 2.0.0
 * Author: Tim Aveni, Jared Tulayan
 * License: X11
 */

function robot_pages_create_post_type() {
	register_post_type('robot',
		array(
			'labels' => array(
				'name' => __('Robots'),
				'singular_name' => __('Robot')),
			'description' => 'A robot, possibly with an associated reveal video.',
			'public' => true,
			'menu_position' => 20,	// below pages, above comments
			'menu_icon' => plugins_url('/mammoth-white.png', __FILE__),
			'capability_type' => 'page',
			'supports' => array(
				'title',
				'editor',
				'revisions',
                'thumbnail'
			),
			'rewrite' => array(
				'slug' => 'robot',
				'with_front' => false	// so that it shows as /robot/the-mammoth instead of /posts/robot/the-mammoth
			),
			'has_archive' => true
		)
	);
	flush_rewrite_rules(false);
}

/**
 * Add custom columns to robot pages admin display
 */
function set_custom_edit_robot_columns( $columns ) {
  $date = $columns['date'];
  unset( $columns['date'] );

  $columns['title'] = __( 'Robot Name' );
  $columns['game'] = __( 'Game Name' );
  $columns['year'] = __( 'Year' );

  $columns['date'] = $date;

  return $columns;
}

/**
 * Display values in custom columns
 */
function custom_robot_column( $column, $post_id ) {
  switch ( $column ) {
    // display season name
    case 'game' :
		echo get_post_meta( $post_id, 'robot-game-meta', true );
		break;

	// display season year
	case 'year' :
		echo get_post_meta( $post_id, 'robot-year-meta', true );
		break;
  }
}

/**
 * Allow for sort by custom columns
 */
function set_custom_robot_sortable_columns( $columns ) {
	$columns['game'] = 'game';
	$columns['year'] = 'year';

  return $columns;
}

/**
 * Set up sorting to use custom meta
 */
function robot_custom_orderby( $query ) {
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

/**
 * Loads the image management javascript
 */
function enqueue_image_mgmt() {
	global $typenow;
    if( $typenow == 'robot' ) {
		wp_enqueue_media();

		// Registers and enqueues the required javascript.
		wp_register_script( 'meta-box-image', plugins_url( 'meta-box-image.js' , __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'meta-box-image', 'meta_image',
			array(
				'title' => 'Choose or Upload an Image',
				'button' => 'Use this image',
			)
		);
		
		wp_enqueue_script( 'meta-box-image' );
	}
}

// Add the admin page stylesheet
// hook: admin_init
function robot_pages_admin_init() {
	wp_register_style('robot-pages', plugins_url('/robot-pages.css', __FILE__), array(), '1.0.1');
	wp_enqueue_style('robot-pages');
}

// Add custom fields to the robot editor page
// hook: admin_menu
function robot_pages_create_custom_fields() {
	add_meta_box('robot_season_meta', 'Season', 'render_robot_season_meta', 'robot', 'side');
	add_meta_box('robot_youtube_meta', 'YouTube Video ID', 'render_robot_youtube_meta', 'robot', 'side');
    add_meta_box('robot_icon_meta', 'Icon', 'render_robot_icon_meta', 'robot', 'side');
}

// Render the robot year custom box
function render_robot_season_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_season_nonce');
	
	$year = get_post_meta( $post->ID, 'robot-year-meta', true );
    $game = get_post_meta( $post->ID, 'robot-game-meta', true );
    

	?>
		<label for="robot_pages_year_field">Year of the competition season for this robot:</label>
		<br>
		<input type="text" id="robot_pages_year_field" name="robot_pages_year_field" value="<?php echo esc_attr( $year )?>" size="4" />
        <br>
        <br>
        <label for="robot_pages_game_field">Name of the competition season's game:</label>
		<br>
		<input type="text" id="robot_pages_game_field" name="robot_pages_game_field" value="<?php echo esc_attr( trim( $game ) )?>"/>
	<?php
}

// Render the robot YouTube URL custom box
function render_robot_youtube_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_youtube_nonce');
	
	$value = get_post_meta( $post->ID, 'robot-youtube-meta', true );

	?>
		<label for="robot_pages_youtube_field">
			YouTube video ID of this robot's reveal video (optional). <br>
			You can find this in the URL of the YouTube video, past "https://youtube.com/watch?v=".
		</label>
		<br>
		<input type="text" id="robot_pages_youtube_field" name="robot_pages_youtube_field" value="<?php echo esc_attr( trim( $value ) ) ?>"/>
	<?php
}

// Render the robot icon custom box
function render_robot_icon_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_icon_nonce');
	
	$value = get_post_meta( $post->ID, 'robot-icon-meta', true );
	
	if ( wp_script_is( 'meta-box-image', 'done' ) ) {
		?>
			<label for="robot_pages_icon_field" class="prfx-row-title">
                URL for icon to display in the robot list (512x512).<br>
                You can copy this URL from an uploaded media file</label>
			<input type="text" id="robot_pages_icon_field" name="robot_pages_icon_field" value="<?php echo esc_attr( trim( $value ) ) ?>"/>
			<input type="button" id="robot_pages_icon_button" class="button" value="Choose or Upload an Image"/>
		<?php
	}
}

// Save the robot's custom fields when the post is saved.
// hook: save_post
function robot_pages_save_custom_fields($postID, $post, $update) {
    // Nonce helps to verify if the edit has not been made yet
    // This exists purely for security reasons
	if (!isset($_POST['robot_pages_robot_season_nonce']) || !wp_verify_nonce($_POST['robot_pages_robot_season_nonce'], 'robot_pages_save_meta_boxes'))
		return;
    
	if (!isset($_POST['robot_pages_robot_youtube_nonce']) || !wp_verify_nonce($_POST['robot_pages_robot_youtube_nonce'], 'robot_pages_save_meta_boxes'))
		return;
    
    if (!isset($_POST['robot_pages_robot_icon_nonce']) || !wp_verify_nonce($_POST['robot_pages_robot_icon_nonce'], 'robot_pages_save_meta_boxes'))
		return;
    
	if (!current_user_can('edit_page', $postID))
		return;
    
	if ($post->post_type !== 'robot')
		return;

    // Update post meta
	if (isset($_POST['robot_pages_year_field']) && trim($_POST['robot_pages_year_field'])) {
		$year = trim($_POST['robot_pages_year_field']);
		update_post_meta($postID, 'robot-year-meta', intval($year));
	} else {
		delete_post_meta($postID, 'robot-year-meta');
	}
    
    
    if (isset($_POST['robot_pages_game_field']) && trim($_POST['robot_pages_game_field'])) {
		$game = trim($_POST['robot_pages_game_field']);
		update_post_meta( $postID, 'robot-game-meta',  $game );
	} else {
		delete_post_meta($postID, 'robot-game-meta');
	}

	if (isset($_POST['robot_pages_youtube_field']) && trim($_POST['robot_pages_youtube_field'])) {
		$youtube = trim($_POST['robot_pages_youtube_field']);
		update_post_meta($postID, 'robot-youtube-meta', $youtube);
	} else {
		update_post_meta($postID, 'robot-youtube-meta', '');
	}
    
    if (isset($_POST['robot_pages_icon_field']) && trim($_POST['robot_pages_icon_field'])) {
		$icon = trim($_POST['robot_pages_icon_field']);
		update_post_meta($postID, 'robot-icon-meta', $icon);
	} else {
		delete_post_meta($postID, 'robot-icon-meta');
	}
}

// Add actions and filters to hook functions into WordPress process
add_action('init', 'robot_pages_create_post_type' );
add_action('admin_init', 'robot_pages_admin_init' );
add_action('admin_menu', 'robot_pages_create_custom_fields');
add_action('admin_enqueue_scripts', 'enqueue_image_mgmt');
add_action('save_post', 'robot_pages_save_custom_fields', 1, 3);
add_action('manage_robot_posts_custom_column' , 'custom_robot_column', 10, 2 );
add_action( 'pre_get_posts', 'robot_custom_orderby' );

add_filter( 'manage_robot_posts_columns', 'set_custom_edit_robot_columns' );
add_filter( 'manage_edit-robot_sortable_columns', 'set_custom_robot_sortable_columns' );
?>