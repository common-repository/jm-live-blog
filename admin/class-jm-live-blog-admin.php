<?php
/**
 * Holds all of the admin side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/admin
 */

namespace JM_Live_Blog;

/**
 * Runs the admin side.
 *
 * This class defines all code necessary to run on the admin side of the plugin.
 *
 * @since      2.0.0
 * @package    JM_Live_Blog
 * @subpackage JM_Live_Blog/admin
 */
class JM_Live_Blog_Admin {

	/**
	 * Version of the plugin.
	 *
	 * @since 2.0.0
	 * @var string $version Description.
	 */
	private $version;


	/**
	 * Builds the JM_Live_Blog_Admin object.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueues the styles for the admin side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {
		global $pagenow;
		global $post;
		if ( ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && ( 'post' === $post->post_type || 'page' === $post->post_type ) ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jm-live-blog-admin', plugin_dir_url( __FILE__ ) . 'css/admin-styles.min.css', [], $this->version, 'all' );
		}
	}

	/**
	 * Enqueues the scripts for the admin side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow;
		global $post;
		if ( ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && ( 'post' === $post->post_type || 'page' === $post->post_type ) ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jm-live-blog-admin-script', plugin_dir_url( __FILE__ ) . 'js/jm-live-blog-admin.min.js', [ 'jquery' ], $this->version, 'all' );
		}
	}

	/**
	 * Adds the meta box for the live blog.
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {
		add_meta_box( 'live-blog-updates-meta', __( 'Live Blog Updates', 'jm-live-blog' ), [ $this, 'create_meta_box' ], array( 'post', 'page' ), 'normal', 'default' );
	}

	/**
	 * Creates the meta box for the live blog.
	 *
	 * @since 2.0.0
	 */
	public function create_meta_box() {
		global $post;
		global $current_screen;

		$color_array['light'] = 'Light';
		$color_array['dark']  = 'Dark';
		$current_screen       = get_current_screen();

		$updates            = get_post_meta( $post->ID, 'live_blog_updates', true );
		$color_scheme       = get_post_meta( $post->ID, 'live_blog_color_scheme', true );
		$alert_color        = get_post_meta( $post->ID, 'live_blog_alert_color', true );
		$show_widget        = get_post_meta( $post->ID, 'live_blog_show_widget', true );
		$widget_title       = get_post_meta( $post->ID, 'live_blog_widget_title', true );
		$widget_description = get_post_meta( $post->ID, 'live_blog_widget_description', true );
		wp_nonce_field( 'live_blog_updates_meta_box_nonce', 'live_blog_updates_meta_box_nonce' );
		if ( '' === $show_widget || null === $show_widget ) {
			$show_widget = 0;
		}

		echo '<div id="jm-live-blog-repeatable-fieldset-one" width="100%">';

		wp_editor( '', 'jm-test-editor', array( 'textarea_name' => 'jm_test_area', 'default_editor' => 'quicktags', 'tinymce' => false ) );

		if ( ( ! method_exists( $current_screen, 'is_block_editor' ) && ! $current_screen->is_block_editor() ) || ( function_exists( 'is_gutenberg_page' )) && ! is_gutenberg_page() ) {
			echo '<table class="jm-live-blog-field">';
			echo '<tr>';
			echo '<td><label for="live_blog_color_scheme">' . __( 'Color Scheme', 'jm-live-blog' ) . '</label></td>';
			echo '<td colspan="2"><select class="live_blog_color_scheme" name="live_blog_color_scheme">';
			foreach ( $color_array as $key => $name ) {
				if ( $key === $color_scheme ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				echo '<option value="' . esc_attr( $key ) . '" ' . esc_html( $selected ) . '>' . esc_html( $name ) . '</option>';
			}
			echo '</select></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><label for="live_blog_alert_color">' . esc_html__( 'New Update Alert Color', 'jm-live-blog' ) . '</label></td>';
			echo '<td colspan="2"><input type="text" name="live_blog_alert_color" id="live_blog_alert_color" value="' . esc_attr( $alert_color ) . '" class="cpa-color-picker" ></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><label for="live_blog_show_widget">' . esc_html__( 'Use Live Blog Widget', 'jm-live-blog' ) . '</label></td>';
			echo '<td><input type="radio" name="live_blog_show_widget" id="live_blog_hide_widget" value="no" ' . checked( $show_widget, 0, false ) . ' /> ' . __( 'No', 'jm-live-blog' ) . '</td>';
			echo '<td><input type="radio" name="live_blog_show_widget" id="live_blog_show_widget" value="yes" ' . checked( $show_widget, 1, false ) . ' /> ' . __( 'Yes', 'jm-live-blog' ) . '</td>';
			echo '</tr>';
			echo '<tr id="jm-live-blog-widget-title-row">';
			echo '<td><label for="live_blog_widget_title">' . esc_html__( 'Live Blog Widget Title', 'jm-live-blog' ) . '</label></td>';
			echo '<td colspan="2"><input type="text" name="live_blog_widget_title" id="live_blog_widget_title" value="' . esc_attr( $widget_title ) . '" /></td>';
			echo '</tr>';
			echo '<tr id="jm-live-blog-widget-description-row">';
			echo '<td><label for="live_blog_widget_description">' . esc_html__( 'Live Blog Widget Description', 'jm-live-blog' ) . '</label></td>';
			echo '<td colspan="2"><input type="text" name="live_blog_widget_description" id="live_blog_widget_description" value="' . esc_attr( $widget_description ) . '" /></td>';
			echo '</tr>';
			echo '</table>';

		}

		echo '<p><a id="live-blog-add-row" class="button" href="#">' . esc_html__( 'Add Update', 'jm-live-blog' ) . '</a></p>';

		echo '<table class="live-blog-empty-row screen-reader-text">';
		echo '<tr>';
		echo '<td><label for="live_blog_updates_title">' . esc_html__( 'Update Title', 'jm-live-blog' ) . '</label></td>';
		echo '<td><input class="new-field jm_live_blog_input" disabled="disabled" type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" value="" /></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label for="live_blog_updates_time">' . esc_html__( 'Update Time', 'jm-live-blog' ) . '</label></td>';
		echo '<td><input class="new-field jm_live_blog_input" disabled="disabled" type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" value="" /></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label for="live_blog_updates_content">' . esc_html__( 'Update Content', 'jm-live-blog' ) . '</label></td>';
		echo '<td>';
		wp_editor( '', 'live_blog_updates_content_hidden', $settings = array( 'textarea_name' => 'live_blog_updates_content[]', 'default_editor' => 'quicktags', 'tinymce' => false ) );
		echo '</td>';
		echo '</tr>';

		echo '<tr><td><a class="button live-blog-remove-row" href="#">' . esc_html__( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
		echo '</table>';

		if ( $updates ) {

			$i = 1;

			foreach ( $updates as $update ) {
				$num = $this->convert_number_to_words( $i );
				$num = preg_replace( "/[\s_]/", "_", $num );
				$i++;
				echo '<table class="jm-live-blog-fields">';
				echo '<tr>';
				echo '<td><label for="live_blog_updates_title">' . esc_html__( 'Update Title', 'jm-live-blog' ) . '</label></td>';
				echo '<td><input type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" class="jm_live_blog_input" value="' . esc_attr( htmlentities( $update['live_blog_updates_title'] ) ) . '" /></td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td><label for="live_blog_updates_time">' . esc_html__( 'Update Time', 'jm-live-blog' ) . '</label></td>';
				echo '<td><input type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" class="jm_live_blog_input" value="' . esc_attr( $update['live_blog_updates_time'] ) . '" /></td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td><label for="live_blog_updates_content">' . esc_html__( 'Update Content', 'jm-live-blog' ) . '</label></td>';
				$update_content = $update['live_blog_updates_content'];
				echo '<td>';
				wp_editor( htmlspecialchars_decode( $update_content ), 'live_blog_updates_content_' . $num, $settings = array( 'textarea_name'=>'live_blog_updates_content[]', 'default_editor' => 'quicktags', 'tinymce' => false ) );
				echo '</td>';
				echo '</p>';

				echo '<tr><td><a class="button live-blog-remove-row" href="#">' . esc_html__( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
				echo '</table>';
			}

		} else {
			echo '<table class="jm-live-blog-fields">';
			echo '<tr>';
			echo '<td><label for="live_blog_updates_title">' . esc_html__( 'Update Title', 'jm-live-blog' ) . '</label></td>';
			echo '<td><input type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" class="jm_live_blog_input" value="" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td><label for="live_blog_updates_time">' . esc_html__( 'Update Time', 'jm-live-blog' ) . '</label></td>';
			echo '<td><input type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" class="jm_live_blog_input" value="" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td><label for="live_blog_updates_content">' . esc_html__( 'Update Content', 'jm-live-blog' ) . '</label></td>';
			echo '<td>';
			wp_editor( '', 'live_blog_updates_content', $settings = array( 'textarea_name'=>'live_blog_updates_content[]', 'default_editor' => 'quicktags', 'tinymce' => false ) );
			echo '</td>';
			echo '</tr>';

			echo '<tr><td><a class="button live-blog-remove-row" href="#">' . esc_html__( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
			echo '</table>';
		}

		echo '</div>';
	}

	/**
	 * Saves the meta box data for the live blog.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id      The id of the post.
	 */
	public function save_meta_box( $post_id ) {
		$color_array['light'] = 'Light';
		$color_array['dark']  = 'Dark';
		if ( ! isset( $_POST['live_blog_updates_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['live_blog_updates_meta_box_nonce'], 'live_blog_updates_meta_box_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$old = get_post_meta( $post_id, 'live_blog_updates', true );
		$new = array();

		if ( isset( $_POST['live_blog_updates_title'] ) ) {
			$title = $_POST['live_blog_updates_title'];
		} else {
			$title = [];
		}
		if ( isset( $_POST['live_blog_updates_time'] ) ) {
			$time = $_POST['live_blog_updates_time'];
		} else {
			$time = [];
		}
		$content            = $_POST['live_blog_updates_content'];
		$color              = $_POST['live_blog_color_scheme'];
		$alert_color        = $_POST['live_blog_alert_color'];
		$widget_title       = $_POST['live_blog_widget_title'];
		$widget_description = $_POST['live_blog_widget_description'];

		$num = count( $title );

		if ( $color && array_key_exists( $color, $color_array ) ) {
			update_post_meta( $post_id, 'live_blog_color_scheme', wp_filter_nohtml_kses( $_POST['live_blog_color_scheme'] ) );
		}

		if ( 'yes' === $_POST['live_blog_show_widget'] ) {
			$widget = 1;
		} else {
			$widget = 0;
		}

		update_post_meta( $post_id, 'live_blog_show_widget', intval( $widget ) );
		update_post_meta( $post_id, 'live_blog_widget_title', wp_filter_nohtml_kses( $widget_title ) );
		update_post_meta( $post_id, 'live_blog_widget_description', wp_filter_nohtml_kses( $widget_description ) );

		$alert_color = trim( $alert_color );
		$alert_color = wp_strip_all_tags( stripslashes( $alert_color ) );

		if ( true === $this->check_color( $alert_color ) ) {
			update_post_meta( $post_id, 'live_blog_alert_color', $alert_color );
		}

		for ( $i = 0; $i < $num; $i++ ) {
			if ( '' === $content[ $i ] || null === $content[ $i ] ) {
				unset( $content[ $i ] );
				$content = array_values( $content );
			}
		}

		for ( $i = 0; $i < $num; $i++ ) {

			if ( ( isset( $title[ $i ] ) && '' !== $title[ $i ] ) ) {

				if ( isset( $title[ $i ] ) ) {
					$new[ $i ]['live_blog_updates_title'] = wp_filter_nohtml_kses( $title[ $i ] );
				}

				if ( isset( $time[ $i ] ) ) {
					$new[ $i ]['live_blog_updates_time'] = wp_filter_nohtml_kses( $time[ $i ] );
				}

				$new[ $i ]['live_blog_updates_content'] = $content[ $i ];

			}
		}
		if ( ! empty( $new ) && $old !== $new ) {
			update_post_meta( $post_id, 'live_blog_updates', $new );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $post_id, 'live_blog_updates', $old );
		}
	}

	/**
	 * Adds in the TinyMCE Editor buttons.
	 *
	 * @since 2.0.0
	 */
	public function jm_Live_blog_buttons() {
		add_filter( 'mce_external_plugins', [ $this, 'add_buttons' ] );
		add_filter( 'mce_buttons', [ $this, 'register_buttons' ] );
	}

	/**
	 * Adds in the JavaScript for the button.
	 *
	 * @since 2.0.0
	 *
	 * @param array $plugin_array      The incoming array of plugins.
	 */
	public function add_buttons( $plugin_array ) {
		$plugin_array['jm_live_blog'] = plugin_dir_url( __FILE__ ) . 'js/jm-live-blog-button.min.js';
		return $plugin_array;
	}

	/**
	 * Adds in the button for the TinyMCY editor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $buttons      The incoming array of buttons.
	 */
	public function register_buttons( $buttons ) {
		array_push( $buttons, 'jm_live_blog' );
		return $buttons;
	}

	/**
	 * Checks to make sure the value is a color in hexidecimal format.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value      The color value given.
	 * @return bool              Whether or not the value is a color code in hexidecimal format.
	 */
	public function check_color( $value ) {
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Loads the block editor scripts and styles.
	 *
	 * @since 2.0.0
	 */
	public function blocks_editor_scripts() {
		$block_path = '../public/js/editor.blocks.js';

		wp_enqueue_style(
			'jm-live-blog-blocks-editor-css',
			plugin_dir_url( __FILE__ ) . 'css/blocks.editor.css'
		);

		wp_enqueue_script(
			'jm-live-blog-blocks-js',
			plugins_url( $block_path, __FILE__ ),
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api', 'wp-editor' ],
			filemtime( plugin_dir_path(__FILE__) . $block_path )
		);

		wp_localize_script(
			'jm-live-blog-js',
			'jm_live_blog_globals',
			[
				'rest_url'  => esc_url( rest_url() ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
			]
		);
	}

	/**
	 * Checks to make sure Gutenberg is active or the WP version is greater than 5.0.
	 *
	 * @since 2.0.0
	 */
	public function check_gutenberg() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Block editor is not available.
			return;
		}

		add_action( 'enqueue_block_editor_assets', [ $this, 'blocks_editor_scripts' ] );
		register_block_type(
			'jm-live-blog/jm-live-blog-block',
			[
				'render_callback' => [ $this, 'rendered_jm_live_blog' ],
			]
		);
	}

	/**
	 * Renders the JM Live Blog block.
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes      The attributes of the block.
	 * @return string                The HTML of the block.
	 */
	public function rendered_jm_live_blog( $attributes ) {
		$html = '';

		if ( isset( $attributes['jm_live_blog_color_scheme'] ) && 'dark' === $attributes[ 'jm_live_blog_color_scheme' ] ) {
			$style = 'dark';
		} else {
			$style = '';
		}

		if ( isset( $attributes['jm_live_blog_update_color'] ) && '' !== $attributes['jm_live_blog_update_color'] ) {
			$color = 'style="background-color:' . $attributes['jm_live_blog_update_color'] . ';"';
		} else {
			$color = '';
		}

		$html .= '<div id="jm-live-blog" class="jm-live-blog-outer ' . $style . '">';
		$html .= '<div class="jm-live-blog-inner">';
		if ( isset( $attributes['jm_live_blog_title'] ) && '' !== $attributes['jm_live_blog_title'] ) {
			$html .= '<h3 class="jm-live-blog-title">' . $attributes['jm_live_blog_title'] . '</h3>';
		}
		if ( isset( $attributes['jm_live_blog_description'] ) && '' !== $attributes['jm_live_blog_description'] ) {
			$html .= '<p class="jm-live-blog-description">' . $attributes['jm_live_blog_description'] . '</p>';
		}
		$html   .= '<div class="jm-live-blog-section-outer">';
		$html   .= '<span id="jm-live-blog-new-updates"' . $color . '>' . esc_html__( 'New Updates', 'jm-live-blog' ) . '</span>';
		$html   .= '<section class="jm-live-blog-section">';
		$updates = get_post_meta( get_the_ID(), 'live_blog_updates', true );
		if ( $updates ) {
			$num_update = count( $updates );
			foreach ( $updates as $update ) {
				$content = apply_filters( 'the_content', $update['live_blog_updates_content'] );
				$html   .= '<div id="' . $num_update . '" class="jm-live-blog-update clearfix">';
				$html   .= '<div class="live-blog-left">';
				$html   .= '<h5 class="live-blog-time">' . $update['live_blog_updates_time'] . '</h5>';
				$html   .= '</div>';
				$html   .= '<div class="live-blog-right">';
				$html   .= '<h4 class="live-blog-title">' . $update['live_blog_updates_title'] . '</h4>';
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
	 * Converts a number into what it is in words.
	 *
	 * @since 2.0
	 *
	 * @param int $number      The number in integer form.
	 * @return string          The number written out in words.
	 */
	public function convert_number_to_words( $number ) {
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion',
		);

		if ( ! is_numeric( $number ) ) {
			return false;
		}

		if ( ( $number >= 0 && (int) $number < 0 ) || (int) $number < 0 - PHP_INT_MAX ) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ( $number < 0 ) {
			return $negative . convert_number_to_words( abs( $number ) );
		}

		$string = $fraction = null;

		if ( strpos( $number, '.' ) !== false ) {
			list( $number, $fraction ) = explode( '.', $number );
		}

		switch ( true ) {
			case $number < 21:
				$string = $dictionary[ $number ];
				break;
			case $number < 100:
				$tens   = ( (int) ( $number / 10 ) ) * 10;
				$units  = $number % 10;
				$string = $dictionary[ $tens ];
				if ( $units ) {
					$string .= $hyphen . $dictionary[ $units ];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[ $hundreds ] . ' ' . $dictionary[ 100 ];
				if ( $remainder ) {
					$string .= $conjunction . convert_number_to_words( $remainder );
				}
				break;
			default:
				$baseUnit = pow( 1000, floor( log( $number, 1000 ) ) );
				$numBaseUnits = (int) ( $number / $baseUnit );
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words( $numBaseUnits ) . ' ' . $dictionary[ $baseUnit ];
				if ( $remainder ) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words( $remainder);
				}
				break;
		}

		if ( null !== $fraction && is_numeric( $fraction ) ) {
			$string .= $decimal;
			$words   = array();
			foreach ( str_split( (string) $fraction ) as $number ) {
				$words[] = $dictionary[ $number ];
			}
			$string .= implode( ' ', $words );
		}

		return $string;
	}

}
