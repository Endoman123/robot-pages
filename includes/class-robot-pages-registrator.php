<?php
/**
 * Registers robot custom post type
 *
 * @link       https://github.com/Endoman123
 * @since      2.0.0
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 */

/**
 * Registers robot custom post type
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 * @author     Jared Tulayan <2lyNJ.E@gmail.com>
 */
class Robot_Pages_Registrator {
    public function robot_pages_register_post_type() {
        $labels = array(
            'name'                  => _x( 'Robots', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Robot', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Robots', 'text_domain' ),
            'name_admin_bar'        => __( 'Robot', 'text_domain' ),
            'archives'              => __( 'Robot Archives', 'text_domain' ),
            'attributes'            => __( 'Robot Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Robot', 'text_domain' ),
            'all_items'             => __( 'All Robots', 'text_domain' ),
            'add_new_item'          => __( 'Add New Robot', 'text_domain' ),
            'add_new'               => __( 'Add Robot', 'text_domain' ),
            'new_item'              => __( 'New Robot', 'text_domain' ),
            'edit_item'             => __( 'Edit Robot', 'text_domain' ),
            'update_item'           => __( 'Update Robot', 'text_domain' ),
            'view_item'             => __( 'View Robot', 'text_domain' ),
            'view_items'            => __( 'View Robots', 'text_domain' ),
            'search_items'          => __( 'Search Robot', 'text_domain' ),
            'not_found'             => __( 'No Robot Found', 'text_domain' ),
            'not_found_in_trash'    => __( 'No Robot Found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into robot', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this robot', 'text_domain' ),
            'items_list'            => __( 'Robots list', 'text_domain' ),
            'items_list_navigation' => __( 'Robots list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter robots list', 'text_domain' ),
        );
        $rewrite = array(
            'slug'                  => 'robot',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __( 'Robot', 'text_domain' ),
            'description'           => __( 'One FRC robot and the season it competed in', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'thumbnail', 'revisions' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-admin-generic',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'robot',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'page',
        );
        register_post_type( 'robot', $args );
        flush_rewrite_rules(false);
    }    
}