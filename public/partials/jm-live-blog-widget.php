<?php
/**
 * File that creates the widget to display read more about in the sidebar.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/public/partials
 */

/**
 * Jm-live-blog-widget.php
 *
 * Creates the widget to show the live blog in the sidebar of a single post
 *
 * @author Jacob Martella
 * @package JM Live Blog
 * @version 2.0.0
 */
class JM_Live_Blog_Widget extends WP_Widget {

	/**
	 * JM_Live_Blog_Widget constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		parent::__construct(
			'jm_live_blog_widget',
			__( 'JM Live Blog Widget', 'jm-live-blog' ),
			[
				'classname'     => 'jm_live_blog_widget',
				'description'   => __( 'Displays the live blog for a post or page in a widget for the sidebar.', 'jm-live-blog' ),
			]
		);
	}

	/**
	 * Outputs the HTML of the widget
	 *
	 * @since 2.0.0
	 *
	 * @param array $args          The arguments for the widget.
	 * @param array $instance      The instance for the widget.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		if ( is_singular() ) {

			if ( 1 === get_post_meta( get_the_ID(), 'live_blog_show_widget', true ) ) {

				$title       = get_post_meta( get_the_ID(), 'live_blog_widget_title', true );
				$description = get_post_meta( get_the_ID(), 'live_blog_widget_description', true );
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

				echo $args['before_widget'];

				echo '<div id="jm-live-blog" class="jm-live-blog-outer ' . $style . ' jm-live-blog-widget-outer">';
				echo '<div class="jm-live-blog-inner">';
				if ( '' !== $title ) {
					echo '<h3 class="jm-live-blog-title">' . $title . '</h3>';
				}
				if ( '' !== $description ) {
					echo '<p class="jm-live-blog-description">' . $description . '</p>';
				}
				echo '<div class="jm-live-blog-section-outer">';
				echo '<span id="jm-live-blog-new-updates"' . $color . '>' . __( 'New Updates', 'jm-live-blog' ) . '</span>';
				echo '<section class="jm-live-blog-section">';
				$updates    = get_post_meta( get_the_ID(), 'live_blog_updates', true );
				$num_update = count( $updates );
				if ( $updates ) {
					foreach ( $updates as $update ) {
						$content = apply_filters( 'the_content', $update[ 'live_blog_updates_content' ] );
						echo '<div id="' . esc_attr( $num_update ) . '" class="jm-live-blog-update clearfix">';
						echo '<div class="live-blog-left">';
						echo '<h5 class="live-blog-time">' . esc_html( $update[ 'live_blog_updates_time' ] ) . '</h5>';
						echo '</div>';
						echo '<div class="live-blog-right">';
						echo '<h4 class="live-blog-title">' . esc_esc_htmlattr( $update[ 'live_blog_updates_title' ] ) . '</h4>';
						echo '<div class="live-blog-content">' . $content . '</div>';
						echo '</div>';
						echo '</div>';
						$num_update--;
					}
				}
				echo '</section>';
				echo '</div>';
				echo '</div>';
				echo '</div>';

				echo $args['after_widget'];

			}
		}

	}

}
