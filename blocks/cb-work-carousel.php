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
					<div class="iframe-cover swiper-video">
						<iframe src="<?= esc_url( cb_vimeo_url_with_dnt( $video ) ); ?>&background=1&autoplay=0" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
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
						$post_blocks = parse_blocks( get_the_content( null, false, $work_id ) );
						$subtitle    = cb_find_hero_subtitle( $post_blocks );
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
	document.querySelectorAll('.swiper-video').forEach(function(card) {
		const iframe = card.querySelector('iframe');
		if (!iframe) return;
		card.addEventListener('mouseenter', function() {
			if (iframe.contentWindow) {
				iframe.contentWindow.postMessage({ method: 'play' }, '*');
			}
		});
		card.addEventListener('mouseleave', function() {
			if (iframe.contentWindow) {
				iframe.contentWindow.postMessage({ method: 'pause' }, '*');
				iframe.contentWindow.postMessage({ method: 'setCurrentTime', value: 0 }, '*');
			}
		});
	});
});
</script>
		<?php
	}
);