<?php
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

?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<div id="jm-live-blog" class="jm-live-blog-outer <?php echo esc_attr( $style ); ?>">
		<div class="jm-live-blog-inner">
			<?php
			if ( isset( $attributes['jm_live_blog_title'] ) && '' !== $attributes['jm_live_blog_title'] ) {
				?>
				<h3 class="jm-live-blog-title"><?php echo wp_kses_post( $attributes['jm_live_blog_title'] ); ?></h3>
				<?php
			}
			if ( isset( $attributes['jm_live_blog_description'] ) && '' !== $attributes['jm_live_blog_description'] ) {
				?>
				<p class="jm-live-blog-description"><?php echo wp_kses_post( $attributes['jm_live_blog_description'] ); ?></p>
				<?php
			}
			?>
			<div class="jm-live-blog-section-outer">
				<span id="jm-live-blog-new-updates" <?php echo wp_kses_post( $color ); ?>><?php esc_html_e( 'New Updates', 'jm-live-blog' ); ?></span>
				<section class="jm-live-blog-section">
					<?php
					$updates = get_post_meta( get_the_ID(), 'live_blog_updates', true );
					if ( $updates ) {
						$num_update = count( $updates );
						foreach ( $updates as $update ) {
							$content = apply_filters( 'the_content', $update['live_blog_updates_content'] );
							?>
							<div id="<?php echo esc_html( $num_update ); ?>" class="jm-live-blog-update clearfix">
								<div class="live-blog-left">
									<h5 class="live-blog-time"><?php echo wp_kses_post( $update['live_blog_updates_time'] ); ?></h5>
								</div>
								<div class="live-blog-right">
									<h4 class="live-blog-title"><?php echo wp_kses_post( $update['live_blog_updates_title'] ); ?></h4>
									<div class="live-blog-content"><?php echo wp_kses_post( $content ); ?></div>
								</div>
							</div>
							<?php
							$num_update--;
						}
					}
					?>
				</section>
			</div>
		</div>
	</div>
</div>
