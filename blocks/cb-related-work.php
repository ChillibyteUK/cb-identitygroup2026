<?php
/**
 * Block template for CB Related Work.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

// Get service and theme terms from current post.
$services = wp_get_post_terms( get_the_ID(), 'service' );
$themes   = wp_get_post_terms( get_the_ID(), 'theme' );

$pretitle         = 'Related';
$pretitle_padding = 'pt-4 pb-3';

// If no service terms, try to derive from page slug (for service description pages).
if ( empty( $services ) && is_page() ) {
	$page_slug          = get_post_field( 'post_name', get_the_ID() );
	$maybe_service_term = get_term_by( 'slug', $page_slug, 'service' );
	$pretitle           = get_the_title( get_the_ID() );
	$pretitle_padding   = 'pt-2 pb-1';
	if ( ! $maybe_service_term || is_wp_error( $maybe_service_term ) ) {
		// Try to find the term by slug manually if get_term_by fails.
		$all_terms = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => false ) );
		foreach ( $all_terms as $term ) {
			if ( $term->slug === $page_slug ) {
				$maybe_service_term = $term;
				break;
			}
		}
	}
}

// Dynamically determine the service term for this page.
$service_id = null;
if ( ! empty( $services ) ) {
	$yoast_primary_id = get_post_meta( get_the_ID(), '_yoast_wpseo_primary_service', true );
	if ( $yoast_primary_id ) {
		foreach ( $services as $service ) {
			if ( intval( $service->term_id ) === intval( $yoast_primary_id ) ) {
				$service_id = intval( $yoast_primary_id );
				break;
			}
		}
	}
	if ( ! $service_id ) {
		$service_id = intval( $services[0]->term_id );
	}
} elseif ( is_page() ) {
	$page_slug          = get_post_field( 'post_name', get_the_ID() );
	$maybe_service_term = get_term_by( 'slug', $page_slug, 'service' );
	if ( ! $maybe_service_term || is_wp_error( $maybe_service_term ) ) {
		$all_terms = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => false ) );
		foreach ( $all_terms as $term ) {
			if ( $term->slug === $page_slug ) {
				$maybe_service_term = $term;
				break;
			}
		}
	}
	if ( $maybe_service_term && ! is_wp_error( $maybe_service_term ) ) {
		$service_id = intval( $maybe_service_term->term_id );
	}
}

// Only run query if $service_id was resolved.
if ( $service_id ) {
	$posts = array();

	// 1. Get up to 4 posts where Yoast primary service matches.
	$yoast_query = new WP_Query(
		array(
			'post_type'      => 'case_study',
			'posts_per_page' => 4,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yoast_wpseo_primary_service',
					'value'   => $service_id,
					'compare' => '=',
				),
			),
			'post__not_in'   => array( get_the_ID() ),
		)
	);
	if ( $yoast_query->have_posts() ) {
		while ( $yoast_query->have_posts() ) {
			$yoast_query->the_post();
			$posts[] = get_the_ID();
		}
		wp_reset_postdata();
	}

	// 2. If less than 4, fill with posts assigned to the service via taxonomy.
	if ( count( $posts ) < 4 ) {
		$remaining  = 4 - count( $posts );
		$fill_query = new WP_Query(
			array(
				'post_type'      => 'case_study',
				'posts_per_page' => $remaining,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'service',
						'field'    => 'term_id',
						'terms'    => $service_id,
					),
				),
				'post__not_in'   => array_merge( array( get_the_ID() ), $posts ),
			)
		);
		if ( $fill_query->have_posts() ) {
			while ( $fill_query->have_posts() ) {
				$fill_query->the_post();
				$posts[] = get_the_ID();
			}
			wp_reset_postdata();
		}
	}

	if ( ! empty( $posts ) ) {
		?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-related-work">
		<div class="cb-related-work__pre-title">
			<div class="id-container <?= esc_attr( $pretitle_padding ); ?> px-4 px-md-5">
				<?= esc_html( $pretitle ); ?> Work
			</div>
		</div>
		<div class="id-container">
			<div class="row g-2">
		<?php
		foreach ( $posts as $post_id ) {
			setup_postdata( get_post( $post_id ) );
			?>
				<div class="col-md-6">
					<a href="<?= esc_url( get_the_permalink( $post_id ) ); ?>" class="cb-related-work__card">
						<?= get_work_image( $post_id, 'cb-related-work__image' ); ?>
						<div class="cb-related-work__content px-4 px-md-5">
							<div class="cb-related-work__title">
								<?php echo esc_html( get_the_title( $post_id ) ); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width="23" height="21" alt="" class="cb-services-nav__item-icon" />
							</div>
							<div class="cb-related-work__desc">
								<?php
								$post_blocks = parse_blocks( get_post_field( 'post_content', $post_id ) );
								$subtitle    = cb_find_hero_subtitle( $post_blocks );
								if ( $subtitle ) {
									echo esc_html( $subtitle );
								} else {
									echo wp_kses_post( wp_trim_words( get_the_excerpt( $post_id ), 18, '...' ) );
								}
								?>
							</div>
						</div>
					</a>
				</div>
			<?php
		}
		wp_reset_postdata();
		?>
			</div>
		</div>
	</section>
		<?php
	}
}
