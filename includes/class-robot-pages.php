<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/Endoman123
 * @since      2.0.0
 *
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Robot_Pages
 * @subpackage Robot_Pages/includes
 * @author     Jared Tulayan <2lyNJ.E@gmail.com>
 */
class Robot_Pages {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Robot_Pages_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'ROBOT_PAGES_VERSION' ) ) {
			$this->version = ROBOT_PAGES_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'robot-pages';

		$this->load_dependencies();
		$this->register_robot_post_type();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Robot_Pages_Loader. Orchestrates the hooks of the plugin.
	 * - Robot_Pages_i18n. Defines internationalization functionality.
	 * - Robot_Pages_Admin. Defines all hooks for the admin area.
	 * - Robot_Pages_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-robot-pages-loader.php';

		/**
		 * The class responsible for registering the custom post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-robot-pages-registrator.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-robot-pages-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-robot-pages-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-robot-pages-public.php';

		$this->loader = new Robot_Pages_Loader();

	}

	/**
	 * Register the robot post type
	 *
	 * Uses the Robot_Pages_Registrator class in order to define the Robot post type
	 * and its parameters.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function register_robot_post_type() {

		$plugin_page = new Robot_Pages_Registrator();

		$this->loader->add_action( 'init', $plugin_page, 'robot_pages_register_post_type' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Robot_Pages_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Robot_Pages_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Robot_Pages_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'robot_pages_admin_menu');
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'robot_custom_orderby' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'robot_pages_save_custom_fields', 1, 3 );
		$this->loader->add_action( 'manage_robot_posts_custom_column', $plugin_admin, 'custom_robot_column', 10, 2 );
		
		$this->loader->add_filter( 'manage_robot_posts_columns', $plugin_admin, 'set_custom_edit_robot_columns' );
		$this->loader->add_filter( 'manage_edit-robot_sortable_columns', $plugin_admin, 'set_custom_robot_sortable_columns' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Robot_Pages_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Robot_Pages_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
