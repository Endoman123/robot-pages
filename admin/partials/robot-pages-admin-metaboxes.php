<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/Endoman123
 * @since      2.0.0
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/admin/partials
 */
// Render the robot year custom box
function render_robot_season_meta($post) {
    wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_season_nonce');
    
    $year = get_post_meta( $post->ID, 'robot-year-meta', true );
    $game = get_post_meta( $post->ID, 'robot-game-meta', true );
    $reveal = get_post_meta( $post->ID, 'robot-game-reveal-meta', true );
    $seasonDesc = wpautop( get_post_meta( $post->ID, 'robot-season-desc-meta', true ) );
?>
        <div class="robotpage-metabox">
            <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">Season Year</p>
                <p class="robotpage-metabox__tip">Year of competition season.</p>
                <input type="number" id="robot_pages_year_field" name="robot_pages_year_field" minlength="4" maxlength="4" size="4" min="2000" required value="<?php echo esc_attr( $year )?>"/>
            </section>
            <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">Season Name</p>
                <p class="robotpage-metabox__tip">Name of FRC game. Omit <i>FIRST</i>.</p>
                <input type="text" id="robot_pages_game_field" name="robot_pages_game_field" required value="<?php echo esc_attr( trim( $game ) )?>"/>
            </section>
            <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">Game Reveal Video ID</p>
                <p class="robotpage-metabox__tip">Copy from YouTube video URL, after ".../watch?v="</p>
                <input type="text" id="robot_pages_game_reveal_field" name="robot_pages_game_reveal_field" required value="<?php echo esc_attr( trim( $reveal ) ) ?>"/>
            </section>
            <section class="robotpage-metabox__section robotpage-metabox__section--width--full">
                <p class="robotpage-metabox__title">Season Description</p>
                <p class="robotpage-metabox__tip">One paragraph describing this season's game, along with possible challenges, metas, etc.</p>
                <?php
                    wp_editor($seasonDesc, 'robot_pages_season_desc_field', array(
                        'media_buttons' => false,
                        'teeny' => true,
                        'textarea_rows' => 10,
                        'textarea_name' => 'robot_pages_season_desc_field',
                        'tinymce' => array (
                            'toolbar1' => 'bold italic underline strikethrough | subscript superscript | undo redo | link',
                        ),
                        'quicktags' => false
                    ));
                ?>
            </section>
        </div>
    <?php	
}

// Render the robot media custom meta box
function render_robot_media_meta($post) {
    wp_nonce_field('robot_pages_save_meta_boxes', 'robot_pages_robot_media_nonce');
    
    $reveal = get_post_meta( $post->ID, 'robot-robot-reveal-meta', true );
    $icon = get_post_meta( $post->ID, 'robot-icon-meta', true );
    $img = get_post_meta( $post->ID, 'robot-img-meta', true );
?>
        <div class="robotpage-metabox">
            <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">Robot Reveal Video ID (Optional)</p>
                <p class="robotpage-metabox__tip">
                    Robot reveal YouTube video to showcase robot. Optional, but recommended.<br>
                    Copy from YouTube video URL, after ".../watch?v=."
                </p>
                <input type="text" id="robot_pages_robot_reveal_field" name="robot_pages_robot_reveal_field" value="<?php echo esc_attr( trim( $reveal ) ) ?>"/>
            </section>
    <?php if ( wp_script_is( 'meta-box-image', 'done' ) ) { ?>
        <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">
                    Robot Icon (Optional)
                </p>
                <p class="robotpage-metabox-section__tip">
                    Icon to display in robot archive. Optional, but recommended.<br>
                    For best results, look for 512×512 images.
                </p>
                <input type="text" id="robot_pages_icon_field" name="robot_pages_icon_field" value="<?php echo esc_attr( trim( $icon ) ) ?>"/>
                <input type="button" id="robot_pages_icon_field" class="button button--meta-image" value="..." />
            </section>
            <section class="robotpage-metabox__section">
                <p class="robotpage-metabox__title">
                    Full Robot Image (Optional)
                </p>
                <p class="robotpage-metabox-section__tip">
                    Full image of robot to display on robot page. Optional, but recommended.<br>
                    For best results, find high-definition images (720p and up).
                </p>
                <input type="text" id="robot_pages_img_field" name="robot_pages_img_field" value="<?php echo esc_attr( trim( $icon ) ) ?>"/>
                <input type="button" id="robot_pages_img_field" class="button button--meta-image" value="..." />
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
    $features = wpautop( get_post_meta( $post->ID, 'robot-features-meta', true ) );
?>
    <div class="robotpage-metabox">
        <section class="robotpage-metabox__section">
            <p class="robotpage-metabox__title">Current Robot Status</p>
            <p class="robotpage-metabox__tip">
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
        <section class="robotpage-metaboxbox__section">
            <p class="robotpage-metabox__title">Robot Dimensions</p>
            <p class="robotpage-metabox__tip">
                Transport dimensions of robot, in inches, from bumper to bumper, and from the bottom of the drivetrain to the top of the robot.<br>
                Max dimensions: 50" × 50" × 100"
            </p>
            <input type="number" id="robot_pages_length_field" name="robot_pages_length_field" maxlength="3" size="3" min="1" max="50" placeholder="L" required value="<?php echo esc_attr( trim( $length ) ) ?>"/>
            ×
            <input type="number" id="robot_pages_width_field" name="robot_pages_width_field" maxlength="3" size="3"  min="1" max="50" placeholder="W" required value="<?php echo esc_attr( trim( $width ) ) ?>"/>
            ×
            <input type="number" id="robot_pages_height_field" name="robot_pages_height_field" maxlength="3" size="3" min="1" max="100" placeholder="H" required value="<?php echo esc_attr( trim( $height ) ) ?>"/>
        </section>
        <section class="robotpage-metabox__section">
            <p class="robotpage-metabox__title">Robot Weight</p>
            <p class="robotpage-metabox__tip">Weight of robot, in pounds, including bumpers.</p>
            <input type="number" id="robot_pages_weight_field" name="robot_pages_weight_field" maxlength="3" size="3" min="1" max="200" required value="<?php echo esc_attr( trim( $weight ) ) ?>"/> lbs.
        </section>
        <section class="robotpage-metabox__section robotpage-metabox__section--width--full">
                <p class="robotpage-metabox__title">Robot Features</p>
                <p class="robotpage-metabox__tip">List of features and skills that the robot is capable of.</p>
                <?php
                    wp_editor($features, 'robot_pages_features_field', array(
                        'media_buttons' => false,
                        'teeny' => true,
                        'textarea_rows' => 10,
                        'textarea_name' => 'robot_pages_features_field',
                        'tinymce' => array (
                            'toolbar1' => 'bold italic underline strikethrough | subscript superscript | bullist | undo redo | link',
                            "plugins" => "link, paste",
                            "paste_as_text" => true,
                            "setup" => "function (editor) {
                                function isValid() {
                                    return tinymce.activeEditor.dom.getParent(tinyMCE.activeEditor.selection.getNode(), 'li') != null ||
                                    tinyMCE.activeEditor.selection.getNode().nodeName.toLowerCase() == 'li';
                                }
                                editor.on(\"ready\", function() {
                                    if (!isValid()) {
                                        editor.execCommand('InsertUnorderedList');
                                    }
                                });
                                // This forces all 'enter' and 'backspace' keys to create an 'ol li' element
                                editor.on('keyup', function(e) {
                                    if (e.keyCode == 13 || e.keyCode == 8){
                                        if (!isValid()) {
                                            editor.execCommand('InsertUnorderedList');
                                        }
                                    }
                                });
                            }"
                        ),
                        'quicktags' => false
                    ));
                ?>
            </section>
    </div>
<?php
}


