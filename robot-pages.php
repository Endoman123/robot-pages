<?php 
/**
 * Plugin Name: Robot pages
 * Description: This plugin adds a custom post type for robots and generates navigation.
 * Version: 1.0.1
 * Author: Timothy J. Aveni
 * Author URI: http://timothyaveni.com/
 * License: X11
 */

add_action('init', 'robot_pages_create_post_type');

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
				'revisions'
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

add_action('admin_init', 'robot_pages_admin_init');
add_action('admin_menu', 'robot_pages_create_custom_fields');
add_action('admin_enqueue_scripts', 'enqueue_image_mgmt');
add_action('save_post', 'robot_pages_save_custom_fields', 1, 3);

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
			You can find this in the URL of the TouTube video, past "https://youtube.com/watch?v=".
		</label>
		<br>
		<input type="text" id="robot_pages_youtube_field" name="robot_pages_youtube_field" value="<?php echo esc_attr( trim( $value ) ) ?>"/>
	<?php
}

// Save the robot's custom fields when the post is saved.
// hook: save_post
function robot_pages_save_custom_fields($postID, $post, $update) {
    // Nonce helps to verify if the edit can still be made
    // This exists purely for security reasons
	if (!isset($_POST['robot_pages_robot_season_nonce']) || !wp_verify_nonce($_POST['robot_pages_robot_season_nonce'], 'robot_pages_save_meta_boxes'))
		return;
    
	if (!isset($_POST['robot_pages_robot_youtube_nonce']) || !wp_verify_nonce($_POST['robot_pages_robot_youtube_nonce'], 'robot_pages_save_meta_boxes'))
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
		update_post_meta($postID, 'robot-youtube-meta', 'Failed!');
	}
}
?>