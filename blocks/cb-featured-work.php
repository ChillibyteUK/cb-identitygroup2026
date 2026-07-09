<?php
/**
 * Block template for CB Featured Work.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$hero_mode = get_field( 'hero_mode' );

if ( $hero_mode ) {
	$hero_case_study = get_field( 'hero_case_study' );
	if ( ! $hero_case_study ) {
		$latest_query = new WP_Query(
			array(
				'post_type'      => 'case_study',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		if ( $latest_query->have_posts() ) {
			$hero_case_study = $latest_query->posts[0]->ID;
			wp_reset_postdata();
		}
	}

	if ( $hero_case_study ) {
		?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-featured-work cb-featured-work--hero">
		<a href="<?= esc_url( get_the_permalink( $hero_case_study ) ); ?>" class="cb-featured-work__card cb-featured-work__card--hero">
			<?= get_work_image( $hero_case_study, 'cb-featured-work__image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="cb-featured-work__content px-4 px-md-5">
				<div class="cb-featured-work__title">
					<?php echo esc_html( get_the_title( $hero_case_study ) ); ?>
					<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-bk.svg' ); ?>" width=23 height=21 alt="" class="cb-featured-work__arrow" />
				</div>
				<div class="cb-featured-work__desc">
					<?php
					$post_blocks = parse_blocks( get_the_content( null, false, $hero_case_study ) );
					$subtitle    = cb_find_hero_subtitle( $post_blocks );
					if ( $subtitle ) {
						echo esc_html( $subtitle );
					} else {
						echo wp_kses_post( wp_trim_words( get_the_excerpt( $hero_case_study ), 18, '...' ) );
					}
					?>
				</div>
			</div>
		</a>
	</section>
		<?php
	}
	return;
}

$count             = get_field( 'count' ) ?? 4;
$selected_services = get_field( 'services' ) ?? array();
$taxonomy_filter   = get_field( 'taxonomy_filter' ) ?: 'none';

$query_args = array(
	'post_type'      => 'case_study',
	'posts_per_page' => $count,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( 'none' !== $taxonomy_filter && taxonomy_exists( $taxonomy_filter ) ) {
	// Auto-derive from the current post's terms in the chosen taxonomy —
	// generalises the fixed service+theme logic previously duplicated
	// across cb-related-work/-expo/-sports and the region logic in
	// cb-work-by-region.
	$current_terms = wp_get_post_terms( get_the_ID(), $taxonomy_filter, array( 'fields' => 'ids' ) );
	if ( ! empty( $current_terms ) && ! is_wp_error( $current_terms ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => $taxonomy_filter,
				'field'    => 'term_id',
				'terms'    => $current_terms,
			),
		);
		$query_args['post__not_in'] = array( get_the_ID() );
	}
} elseif ( ! empty( $selected_services ) && is_array( $selected_services ) ) {
	// If services are selected, filter by those term IDs.
	// Pull the full matching set so primary-service ranking is accurate before limiting.
	$query_args['posts_per_page'] = -1;
	$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'service',
			'field'    => 'term_id',
			'terms'    => $selected_services,
		),
	);
}

$q = new WP_Query( $query_args );

if ( ! function_exists( 'cb_get_primary_service_term_id' ) ) {
	/**
	 * Get the primary service term ID for a post.
	 *
	 * Uses Yoast's primary term API when available, with a post meta fallback.
	 *
	 * @param int $post_id The post ID.
	 * @return int The primary service term ID, or 0 if not set.
	 */
	function cb_get_primary_service_term_id( $post_id ) {
		$primary_term_id = 0;

		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$primary_term    = new WPSEO_Primary_Term( 'service', $post_id );
			$primary_term_id = (int) $primary_term->get_primary_term();
		} elseif ( metadata_exists( 'post', $post_id, '_yoast_wpseo_primary_service' ) ) {
			$primary_term_id = (int) get_post_meta( $post_id, '_yoast_wpseo_primary_service', true );
		}

		if ( $primary_term_id > 0 && term_exists( $primary_term_id, 'service' ) ) {
			return $primary_term_id;
		}

		// Fallback: first assigned service term when no Yoast primary is set.
		$assigned_terms = wp_get_post_terms(
			$post_id,
			'service',
			array(
				'fields' => 'ids',
			)
		);

		if ( is_wp_error( $assigned_terms ) || empty( $assigned_terms ) ) {
			return 0;
		}

		return (int) reset( $assigned_terms );
	}
}

$posts = $q->posts;

if ( ! empty( $posts ) && ! empty( $selected_services ) && is_array( $selected_services ) ) {
	$service_priority = array_values(
		array_filter(
			array_map(
			static function ( $service ) {
				if ( is_object( $service ) && isset( $service->term_id ) ) {
					return (int) $service->term_id;
				}

				if ( is_array( $service ) && isset( $service['term_id'] ) ) {
					return (int) $service['term_id'];
				}

				return (int) $service;
			},
			$selected_services
		),
		static function ( $id ) {
			return $id > 0;
		}
	)
	);

	$service_priority = array_values( array_unique( $service_priority ) );

	if ( ! empty( $service_priority ) ) {
		usort(
			$posts,
			static function ( $a, $b ) use ( $service_priority ) {
				$a_primary = cb_get_primary_service_term_id( $a->ID );
				$b_primary = cb_get_primary_service_term_id( $b->ID );

				$a_rank = array_search( $a_primary, $service_priority, true );
				$b_rank = array_search( $b_primary, $service_priority, true );

				$a_rank = false === $a_rank ? PHP_INT_MAX : $a_rank;
				$b_rank = false === $b_rank ? PHP_INT_MAX : $b_rank;

				if ( $a_rank !== $b_rank ) {
					return $a_rank <=> $b_rank;
				}

				return strcmp( $b->post_date_gmt, $a->post_date_gmt );
			}
		);

		$posts = array_slice( $posts, 0, (int) $count );
	}
}

if ( $q->have_posts() ) {
	// identity's real markup differs structurally (a dedicated
	// __pre-title element instead of a utility-class h2, no padding on
	// the cards' id-container, a white arrow icon) - not just a colour
	// variant, so branched here rather than adding another scoped CSS
	// override on top of two conflicting class lists.
	$is_identity        = 'identity' === cb_site_template_suffix();
	$cards_wrap_class   = $is_identity ? 'id-container' : 'id-container px-4 px-md-5 py-4';
	$arrow_src          = $is_identity ? 'arrow-wh.svg' : 'arrow-bk.svg';
	$arrow_class        = $is_identity ? 'cb-services-nav__item-icon' : 'cb-featured-work__arrow';
	?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-featured-work">
	<?php if ( $is_identity ) : ?>
	<h2 class="cb-featured-work__pre-title mb-0">
		<div class="id-container py-4 px-4 px-md-5">
			FEATURED WORK
		</div>
	</h2>
	<?php else : ?>
	<div class="has-lime-1000-border-bottom">
		<div class="id-container py-3 px-4 px-md-5">
			<h2 class="fs-300 fw-regular has-lime-900-color lh-tightest pt-1 pb-0 mb-0 text-uppercase" >Featured Work</h2>
		</div>
	</div>
	<?php endif; ?>
	<div class="<?= esc_attr( $cards_wrap_class ); ?>">
		<div class="row g-2">
		<?php
		global $post;
		foreach ( $posts as $featured_post ) {
			$post = $featured_post;
			setup_postdata( $post );
			$video     = get_field( 'vimeo_url', get_the_ID() );
			$has_video = $video ? 'has_video' : '';
			?>
			<div class="col-md-6">
				<a href="<?= esc_url( get_the_permalink() ); ?>" class="cb-featured-work__card <?= esc_attr( $has_video ); ?>">
				<?php
				$video = get_field( 'vimeo_url', get_the_ID() );
				if ( $video ) {
					?>
					<iframe class="work-video" src="<?= esc_url( cb_vimeo_url_with_dnt( $video ) ); ?>&background=1&autoplay=0" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
						<?php
				}
				?>
				<?= get_work_image( get_the_ID(), 'cb-featured-work__image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="cb-featured-work__content px-4 px-md-5">
						<div class="cb-featured-work__title">
						<?php the_title(); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/' . $arrow_src ); ?>" width=23 height=21 alt="" class="<?= esc_attr( $arrow_class ); ?>" />
						</div>
						<div class="cb-featured-work__desc">
						<?php
						// get the case_study_subtitle field from the cb-case-study-hero block if available.
						$post_blocks = parse_blocks( get_the_content( null, false, get_the_ID() ) );
						$subtitle    = cb_find_hero_subtitle( $post_blocks );
						if ( $subtitle ) {
							echo esc_html( $subtitle );
						} else {
							echo wp_kses_post( wp_trim_words( get_the_excerpt(), 18, '...' ) );
						}
						?>
						</div>
					</div>
				</a>
			</div>
					<?php
		}
		?>
		</div>
	</div>
</section>
	<?php

	wp_reset_postdata();
}


add_action(
	'wp_footer',
	function () {
		?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.cb-featured-work__card').forEach(function(card) {
		const iframe = card.querySelector('iframe.work-video');
		if (!iframe) return;

		card.addEventListener('mouseenter', function() {
			iframe.contentWindow?.postMessage({ method: 'play' }, '*');
		});
		card.addEventListener('mouseleave', function() {
			iframe.contentWindow?.postMessage({ method: 'pause' }, '*');
			iframe.contentWindow?.postMessage({ method: 'setCurrentTime', value: 0 }, '*');
		});
		card.addEventListener('focusin', function() {
			iframe.contentWindow?.postMessage({ method: 'play' }, '*');
		});
		card.addEventListener('focusout', function() {
			iframe.contentWindow?.postMessage({ method: 'pause' }, '*');
			iframe.contentWindow?.postMessage({ method: 'setCurrentTime', value: 0 }, '*');
		});
	});
});
</script>
		<?php
	}
);
