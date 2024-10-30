<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/includes
 */

namespace JM_Live_Blog;

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
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/includes
 */
class JM_Live_Blog {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    JM_Live_Blog_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Builds the main object for the plugin.
	 *
	 * @since  2.0.0
	 */
	public function __construct() {

		$this->plugin_slug = 'jm-live-blog';
		$this->version     = '2.1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_setup_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_block_hooks();

	}

	/**
	 * Loads all of the files we're depending on to run the plugin.
	 *
	 * @since  2.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jm-live-blog-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jm-live-blog-setup.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jm-live-blog-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jm-live-blog-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'blocks/class-jm-live-blog-blocks.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jm-live-blog-database-updates.php';

		require_once plugin_dir_path( __FILE__ ) . 'class-jm-live-blog-loader.php';
		$this->loader = new JM_Live_Blog_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the JM_Client_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new JM_Live_Blog_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Runs all of the setup functions for the plugin.
	 *
	 * @since 2.0.0
	 */
	private function define_setup_hooks() {
		$plugin_setup = new JM_Live_Blog_Setup( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Runs all of the admin hooks for the plugin.
	 *
	 * @since 2.0.0
	 */
	private function define_admin_hooks() {
		$admin = new JM_Live_Blog_Admin( $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $admin, 'add_meta_box' );
		$this->loader->add_action( 'save_post', $admin, 'save_meta_box' );
		$this->loader->add_action( 'init', $admin, 'jm_Live_blog_buttons' );
		//$this->loader->add_action( 'init', $admin, 'check_gutenberg' );
	}

	/**
	 * Runs all of the public hooks for the plugin.
	 *
	 * @since 2.0.0
	 */
	private function define_public_hooks() {
		$public = new JM_Live_Blog_Public( $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $public, 'register_shortcode' );
		$this->loader->add_action( 'widgets_init', $public, 'register_widget' );
		$this->loader->add_action( 'wp_ajax_nopriv_jm_live_blog_ajax', $public, 'jm_live_blog_ajax' );
		$this->loader->add_action( 'wp_ajax_jm_live_blog_ajax', $public, 'jm_live_blog_ajax' );
	}

	/**
	 * Runs all of the block hooks for the plugin.
	 *
	 * @since 2.0.0
	 */
	private function define_block_hooks() {
		$blocks = new JM_Live_Blog_Blocks( $this->get_version() );
		$this->loader->add_action( 'init', $blocks, 'create_blocks' );
	}

	/**
	 * Runs any updates needed to the database
	 *
	 * @since 2.0.0
	 */
	private function update_database() {
		$database = new JM_Live_Blog_Database_Updates();
		update_option( 'jm_live_blog_version', $this->version );
	}

	/**
	 * Runs the plugin set up.
	 *
	 * @since 2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Gets the current version of the plugin.
	 *
	 * @since  2.0.0
	 * @return string    The version of the plugin.
	 */
	public function get_version() {
		return $this->version;
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
	 * @return    JM_Live_Blog_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
