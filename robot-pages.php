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
			'menu_icon' => plugins_url( '/mammoth-white.png', __FILE__ ),
			'capability_type' => 'page',
			'supports' => array(
				'title',
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
  $columns['status'] = __( 'Status' );

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

	// display robot status
	case 'status' :
		echo ucfirst( get_post_meta( $post_id, 'robot-status-meta', true ) );
		break;
  }
}

/**
 * Allow for sort by custom columns
 */
function set_custom_robot_sortable_columns( $columns ) {
	$columns['game'] = 'game';
	$columns['year'] = 'year';
	$columns['status'] = 'status';

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
    if ( $typenow == 'robot' ) {
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

// Add custom meta boxes
// hook: admin_menu
function robot_pages_create_custom_fields() {
	add_meta_box('robot_season_meta', 'FIRST Season', 'render_robot_season_meta', 'robot', 'side');
	add_meta_box('robot_media_meta', 'Robot Media', 'render_robot_media_meta', 'robot', 'normal');
	add_meta_box('robot_info_meta', 'Robot Info', 'render_robot_info_meta', 'robot', 'normal');
}

// Render the robot year custom box
function render_robot_season_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_season_nonce');
	
	$year = get_post_meta( $post->ID, 'robot-year-meta', true );
	$game = get_post_meta( $post->ID, 'robot-game-meta', true );
	$reveal = get_post_meta( $post->ID, 'robot-game-reveal-meta', true );
	$seasonDesc = wpautop( get_post_meta( $post->ID, 'robot-season-desc-meta', true ) );

	?>
		<div class="robotpage-meta__container">
			<section class="robotpage-meta__section">
				<p class="robotpage-meta-section__title">Season Year</p>
				<p class="robotpage-meta-section__tip">Year of competition season.</p>
				<input type="number" id="robot_pages_year_field" name="robot_pages_year_field" minlength="4" maxlength="4" size="4" min="2000" required value="<?php echo esc_attr( $year )?>"/>
			</section>
			<section class="robotpage-meta__section">
				<p class="robotpage-meta-section__title">Season Name</p>
				<p class="robotpage-meta-section__tip">Name of FRC game. Omit <i>FIRST</i>.</p>
				<input type="text" id="robot_pages_game_field" name="robot_pages_game_field" required value="<?php echo esc_attr( trim( $game ) )?>"/>
			</section>
			<section class="robotpage-meta__section">
				<p class="robotpage-meta-section__title">Game Reveal Video ID</p>
				<p class="robotpage-meta-section__tip">Copy from YouTube video URL, after ".../watch?v="</p>
				<input type="text" id="robot_pages_game_reveal_field" name="robot_pages_game_reveal_field" required value="<?php echo esc_attr( trim( $reveal ) ) ?>"/>
			</section>
		</div>
	<?php
		wp_editor($seasonDesc, 'robot_pages_season_desc_field', array(
			'media_buttons' => false,
			'teeny' => true,
			'textarea_rows' => 10,
			'textarea_name' => 'robot_pages_season_desc_field'
		));
}

// Render the robot media custom meta box
function render_robot_media_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_media_nonce');
	
	$reveal = get_post_meta( $post->ID, 'robot-robot-reveal-meta', true );
	$icon = get_post_meta( $post->ID, 'robot-icon-meta', true );

	?>
		<div class="robotpage-meta__container">
			<section class="robotpage-meta__section">
				<p class="robotpage-meta-section__title">Robot Reveal Video ID (Optional)</p>
				<p class="robotpage-meta-section__tip">Copy from YouTube video URL, after ".../watch?v="</p>
				<input type="text" id="robot_pages_robot_reveal_field" name="robot_pages_robot_reveal_field" value="<?php echo esc_attr( trim( $reveal ) ) ?>"/>
			</section>
	<?php if ( wp_script_is( 'meta-box-image', 'done' ) ) { ?>
			<section class="robotpage-meta__section">
				<p class="robotpage-meta-section__title">
					Robot Icon (Optional)
				</p>
				<p class="robotpage-meta-section__tip">
					Icon to display in robot archive. Optional, but recommended.<br>
					For best results, look for 512×512 images.
				</p>
				<input type="text" id="robot_pages_icon_field" name="robot_pages_icon_field" value="<?php echo esc_attr( trim( $icon ) ) ?>"/>
				<input type="button" id="robot_pages_icon_button" class="button" value="..."/>
			</section>
	<?php } ?>
		</div>
	<?php
}

// Render the robot info custom box
function render_robot_info_meta($post) {
	wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_info_nonce');

	$status = get_post_meta($post->ID, 'robot-status-meta', true);
	$length = get_post_meta($post->ID, 'robot-length-meta', true);
	$width = get_post_meta($post->ID, 'robot-width-meta', true);
	$height = get_post_meta($post->ID, 'robot-height-meta', true);
	$weight = get_post_meta($post->ID, 'robot-weight-meta', true);
?>
	<div class="robotpage-meta__container">
		<section class="robotpage-meta__section">
			<p class="robotpage-meta-section__title">Current Robot Status</p>
			<p class="robotpage-meta-section__tip">
				Active: Currently being used in competition, either in-season or off-season.<br>
				Showbot: Only used in parades, team demos, etc.<br>
				Inactive: In storage, but not disassembled.<br>
				Disassembled: Taken apart, never to see the light of day ever again.
			</p>
			<select name="robot_pages_status_field" id="robot_pages_status_field">
				<option value="active" <?php selected( $status, 'active' ); ?>>Active</option>
				<option value="showbot" <?php selected( $status, 'showbot' ); ?>>Showbot</option>
				<option value="inactive" <?php selected( $status, 'inactive' ); ?>>Inactive</option>
				<option value="disassembled" <?php selected( $status, 'disassembled' ); ?>>Disassembled</option>
			</select>
		</section>
		<section class="robotpage-meta__section">
			<p class="robotpage-meta-section__title">Robot Dimensions</p>
			<p class="robotpage-meta-section__tip">
				Transport dimensions of robot, in inches, from bumper to bumper, and from the bottom of the drivetrain to the top of the robot.<br>
				Max dimensions: 50" × 50" × 100"
			</p>
			<input type="number" id="robot_pages_length_field" name="robot_pages_length_field" maxlength="3" size="3" min="1" max="50" placeholder="L" required value="<?php echo esc_attr( trim( $length ) ) ?>"/>
			×
			<input type="number" id="robot_pages_width_field" name="robot_pages_width_field" maxlength="3" size="3"  min="1" max="50" placeholder="W" required value="<?php echo esc_attr( trim( $width ) ) ?>"/>
			×
			<input type="number" id="robot_pages_height_field" name="robot_pages_height_field" maxlength="3" size="3" min="1" max="100" placeholder="H" required value="<?php echo esc_attr( trim( $height ) ) ?>"/>
		</section>
		<section class="robotpage-meta__section">
			<p class="robotpage-meta-section__title">Robot Weight</p>
			<p class="robotpage-meta-section__tip">Weight of robot, in pounds, including bumpers.</p>
			<input type="number" id="robot_pages_weight_field" name="robot_pages_weight_field" maxlength="3" size="3" min="1" max="200" required value="<?php echo esc_attr( trim( $weight ) ) ?>"> lbs.</input>
		</section>
	</div>
<?php
}

// Verifies nonce based on the assumption that the
// action name was "robot_pages_save_meta_boxes"
function robot_pages_verify_meta_nonce($nonce) {
	return isset($_POST[$nonce]) && wp_verify_nonce($_POST[$nonce], 'robot_pages_save_meta_boxes');
}

// Write new value to meta key, or default if value is invalid
function robot_pages_write_meta(string $id, string $key, string $value, ?string $default) {
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

// Save the robot's custom fields when the post is saved.
// hook: save_post
function robot_pages_save_custom_fields($postID, $post, $update) {
	// Nonce verification to make sure that the edit request came from
	// a site editor and not some outside source.
    // This exists purely for security reasons
	if (!robot_pages_verify_meta_nonce('robot_pages_robot_season_nonce'))
		return;
    
	if (!robot_pages_verify_meta_nonce('robot_pages_robot_media_nonce'))
		return;
	
	if (!robot_pages_verify_meta_nonce('robot_pages_robot_info_nonce'))
		return;
    	
	// Make sure the current user can even edit the page
	if (!current_user_can('edit_page', $postID))
		return;
	
	// Make sure the edit is being done for robot pages only
	if ($post->post_type !== 'robot')
		return;

	// Update post meta
	// Season meta
	robot_pages_write_meta($postID, 'robot-year-meta', $_POST['robot_pages_year_field'], null);
	robot_pages_write_meta($postID, 'robot-game-meta', $_POST['robot_pages_game_field'], null);
	robot_pages_write_meta($postID, 'robot-game-reveal-meta', $_POST['robot_pages_game_reveal_field'], null);
	robot_pages_write_meta($postID, 'robot-season-desc-meta', $_POST['robot_pages_season_desc_field'], null);   

	// Robot media meta
	robot_pages_write_meta($postID, 'robot-icon-meta', $_POST['robot_pages_icon_field'], null);
	robot_pages_write_meta($postID, 'robot-robot-reveal-meta', $_POST['robot_pages_robot_reveal_field'], null);

	// Robot info meta
	robot_pages_write_meta($postID, 'robot-length-meta', $_POST['robot_pages_length_field'], null);
	robot_pages_write_meta($postID, 'robot-width-meta', $_POST['robot_pages_width_field'], null);
	robot_pages_write_meta($postID, 'robot-height-meta', $_POST['robot_pages_height_field'], null);
	robot_pages_write_meta($postID, 'robot-weight-meta', $_POST['robot_pages_weight_field'], null);
	robot_pages_write_meta($postID, 'robot-status-meta', $_POST['robot_pages_status_field'], null);
}

// Add actions and filters to hook functions into WordPress process
add_action('init', 'robot_pages_create_post_type' );
add_action('admin_init', 'robot_pages_admin_init' );
add_action('admin_menu', 'robot_pages_create_custom_fields');
add_action('admin_enqueue_scripts', 'enqueue_image_mgmt');
add_action('save_post', 'robot_pages_save_custom_fields', 1, 3);
add_action('manage_robot_posts_custom_column' , 'custom_robot_column', 10, 2 );
add_action('pre_get_posts', 'robot_custom_orderby');

add_filter('manage_robot_posts_columns', 'set_custom_edit_robot_columns');
add_filter('manage_edit-robot_sortable_columns', 'set_custom_robot_sortable_columns');
?>