<?php
/**
 * Holds all of the public side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/public
 */

namespace JM_Live_Blog;

/**
 * Runs the public side.
 *
 * This class defines all code necessary to run on the public side of the plugin.
 *
 * @since      2.0.0
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/public
 */
class JM_Live_Blog_Public {

	/**
	 * Version of the plugin.
	 *
	 * @since 2.0.0
	 * @var string $version Description.
	 */
	private $version;

	/**
	 * Builds the JM_Live_Blog_Public object.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueues the styles for the public side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'jm-live-blog-public', plugin_dir_url( __FILE__ ) . 'css/jm-live-blog.min.css', [], $this->version, 'all' );
	}

	/**
	 * Enqueues the scripts for the public side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jm-live-blog-public-script', plugin_dir_url( __FILE__ ) . 'js/jm-live-blog.min.js', [ 'jquery' ], $this->version, 'all' );
		wp_localize_script(
			'jm-live-blog-public-script',
			'jmliveblog',
			[
				'post_id' => get_the_ID(),
				'nonce'   => wp_create_nonce( 'jm-live-blog' ),
				'url'     => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	/**
	 * Registers the live blog shortcode.
	 *
	 * @since 2.0.0
	 */
	public function register_shortcode() {
		add_shortcode( 'jm-live-blog', [ $this, 'create_shortcode' ] );
	}

	/**
	 * Creates the shortcode for the live blog.
	 *
	 * @since 2.0.0
	 *
	 * @param array $atts      The attributes for the shortcode.
	 * @return string          The HTML for the live blog.
	 */
	public function create_shortcode( $atts ) {
		extract(
			shortcode_atts(
				[
					'title'       => '',
					'description' => '',
				],
				$atts
			)
		);

		$html = '';

		if ( 'dark' === get_post_meta( get_the_ID(), 'live_blog_color_scheme', true ) ) {
			$style = 'dark';
		} else {
			$style = '';
		}

		if ( get_post_meta( get_the_ID(), 'live_blog_alert_color', true ) ) {
			$color = 'style="background-color:' . get_post_meta( get_the_ID(), 'live_blog_alert_color', true ) . ';"';
		} else {
			$color = '';
		}

		$html .= '<div id="jm-live-blog" class="jm-live-blog-outer ' . $style . '">';
		$html .= '<div class="jm-live-blog-inner">';
		if ( '' !== $title ) {
			$html .= '<h3 class="jm-live-blog-title">' . $title . '</h3>';
		}
		if ( '' !== $description ) {
			$html .= '<p class="jm-live-blog-description">' . $description . '</p>';
		}
		$html      .= '<div class="jm-live-blog-section-outer">';
		$html      .= '<span id="jm-live-blog-new-updates"' . $color . '>' . __( 'New Updates', 'jm-live-blog' ) . '</span>';
		$html      .= '<section class="jm-live-blog-section">';
		$updates    = get_post_meta( get_the_ID(), 'live_blog_updates', true );
		$num_update = count( $updates );
		if ( $updates ) {
			foreach ( $updates as $update ) {
				$content = apply_filters( 'the_content', $update['live_blog_updates_content'] );
				$html   .= '<div id="' . esc_attr( $num_update ) . '" class="jm-live-blog-update clearfix">';
				$html   .= '<div class="live-blog-left">';
				$html   .= '<h5 class="live-blog-time">' . esc_html( $update['live_blog_updates_time'] ) . '</h5>';
				$html   .= '</div>';
				$html   .= '<div class="live-blog-right">';
				$html   .= '<h4 class="live-blog-title">' . esc_html( $update['live_blog_updates_title'] ) . '</h4>';
				$html   .= '<div class="live-blog-content">' . $content . '</div>';
				$html   .= '</div>';
				$html   .= '</div>';
				$num_update--;
			}
		}
		$html .= '</section>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Echos out the new updates for the live blog.
	 *
	 * @since 2.0.0
	 */
	public function jm_live_blog_ajax() {
		check_ajax_referer( 'jm-live-blog', 'nonce' );
		$post_id    = $_POST['post_id'];
		$update_ids = $_POST['update_ids'];
		$updates    = get_post_meta( $post_id, 'live_blog_updates', true );
		$num_update = count( $updates );

		ob_start();

		if ( $updates ) {
			foreach ( $updates as $update ) {
				if ( ! in_array( $num_update, $update_ids ) ) {
					$content = apply_filters( 'the_content', $update['live_blog_updates_content'] );
					echo '<div id="' . esc_attr( $num_update ) . '" class="jm-live-blog-update clearfix">';
					echo '<div class="live-blog-left">';
					echo '<h5 class="live-blog-time">' . esc_html( $update['live_blog_updates_time'] ) . '</h5>';
					echo '</div>';
					echo '<div class="live-blog-right">';
					echo '<h4 class="live-blog-title">' . esc_html( $update['live_blog_updates_title'] ) . '</h4>';
					echo '<div class="live-blog-content">' . $content . '</div>';
					echo '</div>';
					echo '</div>';
					$num_update --;
				}
			}
		}
		$data = ob_get_clean();
		wp_send_json_success( $data );
		wp_die();
	}

	/**
	 * Loads and registers the live blog widget.
	 *
	 * @since 2.0.0
	 */
	public function register_widget() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/jm-live-blog-widget.php';

		register_widget( 'JM_Live_Blog_Widget' );
	}

}
