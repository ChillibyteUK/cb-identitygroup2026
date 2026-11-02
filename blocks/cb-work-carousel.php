<?php
/**
 * CB Work Carousel Block Template
 *
 * @package cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

// Block classes.
$block_classes = array( 'block', 'cb-work-carousel' );
if ( ! empty( $block['className'] ) ) {
    $block_classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $block_classes[] = 'align' . $block['align'];
}

// Get fields.
$block_title   = get_field( 'title' );
$block_content = get_field( 'content' );

// Output.
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $block_classes ) ); ?>">
	<div class="cb-work-carousel__container">
		<?php
		   	$work_ids = get_field( 'work' ); // ACF relationship field, returns array of post IDs.
		if ( $work_ids && is_array( $work_ids ) && count( $work_ids ) ) {
			?>
		<div class="swiper work-swiper">
			<div class="swiper-wrapper">
			<?php
			foreach ( $work_ids as $work_id ) {
				setup_postdata( $GLOBALS['post'] =& get_post( $work_id ) );
				$video     = get_field( 'vimeo_url', $work_id );
				$has_video = $video ? 'has_video' : '';
				?>
				<div class="swiper-slide">
					<a href="<?php echo esc_url( get_permalink( $work_id ) ); ?>" class="work-carousel-link <?= esc_attr( $has_video ); ?>" tabindex="0">
					<?php
					if ( $video ) {
						?>
					<div class="iframe-cover swiper-video" data-vimeo-src="<?= esc_url( cb_vimeo_url_with_dnt( $video ) ); ?>&background=1&autoplay=1">
					</div>
						<?php
					}
					?>
					<?= get_work_image( $work_id, 'swiper-poster' ); ?>
					<div class="work-carousel-text">
						<div class="id-container pb-2">
							<div class="work-carousel-title"><?= esc_html( get_the_title( $work_id ) ); ?></div>
							<div class="work-carousel-excerpt">
						<?php
						// get the case_study_subtitle field from the cb-case-study-hero block if available.
						$subtitle = cb_get_case_study_subtitle( $work_id );
						if ( $subtitle ) {
							echo esc_html( $subtitle );
						} else {
							echo wp_kses_post( wp_trim_words( get_the_excerpt( $work_id ), 18, '...' ) );
						}
						?>
							</div>
						</div>
					</div>
				</a>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
				<div class="id-container px-4 px-md-5">
					<div class="swiper-navigation">
						<div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"></div>
						<div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"></div>
					</div>
				</div>
			</div>
		</div>
			<?php
		}
		?>
	</div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const swiper = new Swiper('.work-swiper', {
		loop: true,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		effect: 'fade',
		fadeEffect: {
			crossFade: true
		},
		autoplay: {
			delay: 4000,
			disableOnInteraction: true,
			pauseOnMouseEnter: true,
		},
		slidesPerView: 1,
		spaceBetween: 0,
		pagination: false,
	});
	document.querySelectorAll('.swiper-video, .work-carousel-link').forEach(function(card) {
		card.addEventListener('mouseenter', function() {
			if (swiper.autoplay) swiper.autoplay.stop();
		});
		card.addEventListener('mouseleave', function() {
			if (swiper.autoplay) swiper.autoplay.start();
		});
		card.addEventListener('focusin', function() {
			if (swiper.autoplay) swiper.autoplay.stop();
		});
		card.addEventListener('focusout', function() {
			if (swiper.autoplay) swiper.autoplay.start();
		});
	});
	// Video hover preview: only mount the Vimeo iframe while the slide is
	// hovered, so idle slides never hold a live player in memory.
	document.querySelectorAll('.swiper-video').forEach(function(container) {
		const src = container.getAttribute('data-vimeo-src');
		if (!src) return;
		const card = container.closest('.work-carousel-link') || container;

		function mountVideo() {
			if (container.querySelector('iframe')) return;
			const iframe = document.createElement('iframe');
			iframe.src = src;
			iframe.frameBorder = '0';
			iframe.allow = 'autoplay; fullscreen';
			iframe.allowFullscreen = true;
			container.appendChild(iframe);
		}
		function unmountVideo() {
			const iframe = container.querySelector('iframe');
			if (iframe) iframe.remove();
		}

		card.addEventListener('mouseenter', mountVideo);
		card.addEventListener('mouseleave', unmountVideo);
	});
});
</script>
		<?php
	}
);