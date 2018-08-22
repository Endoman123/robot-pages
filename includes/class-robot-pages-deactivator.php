<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/Endoman123
 * @since      2.0.0
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 * @author     Jared Tulayan <2lyNJ.E@gmail.com>
 */
class Robot_Pages_Deactivator {

	/**
	 * Unregisters the robot post type.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
		global $wp_post_types;

		if ( isset( $wp_post_types[ $post_type ] ) ) {
			unset( $wp_post_types[ $post_type ] );
			return true;
		}
		
		return false;
	}

}
