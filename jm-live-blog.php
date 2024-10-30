<?php
/**
 * Plugin Name:       JM Live Blog
 * Plugin URI:        http://www.jacobmartella.com/wordpress/wordpress-plugins/jm-live-blog
 * Description:       Live blogs are the essential tool for keeping readers up to date in any breaking news situation or sporting event. Using the power of AJAX, JM Live Blog allows you to add a live blog to any post with a simple shortcode to keep your readers in the know.
 * Version:           2.1.0
 * Author:            Jacob Martella Web Development
 * Author URI:        https://jacobmartella.com
 * Text Domain:       jm-live-blog
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 *
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/includes
 */

namespace JM_Live_Blog;

// If this file is called directly, then about execution.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jm-live-blog-activator.php
 *
 * @since 1.0.0
 */
function activate_jm_live_blog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jm-live-blog-activator.php';
	JM_Live_Blog_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jm-live-blog-deactivator.php
 *
 * @since 1.0.0
 */
function deactivate_jm_live_blog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jm-live-blog-deactivator.php';
	JM_Live_Blog_Deactivator::deactivate();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_jm_live_blog' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_jm_live_blog' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jm-live-blog.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jm_live_blog() {

	$spmm = new JM_Live_Blog();
	$spmm->run();

}

// Call the above function to begin execution of the plugin.
run_jm_live_blog();
