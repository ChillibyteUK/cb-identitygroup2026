<?php
/**
 * Block template for CB Related Work.
 *
 * Filters case studies by any of the three taxonomies - service, theme and
 * region - each with its own optional filter field. A filter left empty falls
 * back to that taxonomy's default: service derives from the current page
 * (its service terms, its Yoast primary service, or its slug), while theme and
 * region simply go unfiltered. Set one, two or all three; they combine with AND.
 *
 * This replaces what used to be separate blocks per fixed term:
 * cb-related-work-expo/cb-related-work-sports each hardcoded a theme term
 * (retired via migrations 003/004), and cb-work-by-region did the same for a
 * region term (retired 2026-08-17). That last one needed no migration script -
 * it was identity-only with just two instances, both re-pointed by hand.
 *
 * Service is the one filter that does more than narrow the result set: it also
 * ranks, running a first pass on Yoast primary service so those case studies
 * lead. Theme and region are pure tax_query clauses, which is why they share a
 * clause builder and service does not.
 *
 * The region merge deliberately did NOT bring cb-work-by-region's one visual
 * difference with it - that block rendered its first card full width
 * (col-md-12) and the rest col-md-6. Every card here is col-md-6, so there is
 * one card layout instead of two. Its arrow also used
 * .cb-services-nav__item-icon, which is styled (65x60 on identity at >=768px)
 * where this block's .cb-featured-work__arrow is not (31x23); the migrated
 * region pages therefore get the smaller arrow, on purpose.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$service_filter = get_field( 'service_filter' );
$theme_filter   = get_field( 'theme_filter' );
$region_filter  = get_field( 'region_filter' );

// Blank/absent means the historical default of 4 - the 48 instances that
// predate this field have no value saved, so they must keep behaving as
// before. -1 means unbounded, matching WP_Query's own posts_per_page idiom,
// which is what cb-work-by-region always passed. Anything else below 1 is
// normalised to -1 rather than being allowed to reach WP_Query as a 0
// (posts_per_page => 0 returns nothing, which would look like a broken block).
$count = get_field( 'count' );
$limit = ( '' === $count || null === $count ) ? 4 : intval( $count );
if ( $limit < 1 ) {
	$limit = -1;
}

// Get service terms from current post. The "service" taxonomy is
// identity-specific and may not be registered on every site this block is
// now available on, so treat a WP_Error (invalid taxonomy) the same as "none".
$services = wp_get_post_terms( get_the_ID(), 'service' );
if ( is_wp_error( $services ) ) {
	$services = array();
}

// If no service terms, try to derive from page slug (for service description pages).
if ( empty( $services ) && is_page() ) {
	$page_slug          = get_post_field( 'post_name', get_the_ID() );
	$maybe_service_term = get_term_by( 'slug', $page_slug, 'service' );
	if ( ! $maybe_service_term || is_wp_error( $maybe_service_term ) ) {
		$all_terms = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => false ) );
		if ( ! is_wp_error( $all_terms ) ) {
			foreach ( $all_terms as $term ) {
				if ( $term->slug === $page_slug ) {
					$maybe_service_term = $term;
					break;
				}
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
		if ( ! is_wp_error( $all_terms ) ) {
			foreach ( $all_terms as $term ) {
				if ( $term->slug === $page_slug ) {
					$maybe_service_term = $term;
					break;
				}
			}
		}
	}
	if ( $maybe_service_term && ! is_wp_error( $maybe_service_term ) ) {
		$service_id = intval( $maybe_service_term->term_id );
	}
}

// An explicit Service Filter overrides whatever the derivation above found,
// which is what makes the three filters behave consistently: each one, when
// set, pins its taxonomy regardless of the current page. Overriding the
// derived $service_id rather than adding a separate clause deliberately keeps
// service on ONE code path, so an explicitly-filtered service still gets the
// Yoast primary-service ranking pass below - a pinned service page and a real
// service page order their results identically.
//
// Reduced to a single int because $service_id feeds a meta_query 'value' with
// compare '=' further down; ACF hands taxonomy fields back as an array even
// when field_type is a single select, and an array there would break the
// comparison rather than error.
if ( $service_filter ) {
	$service_id = intval( is_array( $service_filter ) ? reset( $service_filter ) : $service_filter );
}

// With none of the three filters set and no service derivable from the page,
// there is nothing to relate to - this block's original behaviour.
if ( ! $theme_filter && ! $region_filter && ! $service_id ) {
	return;
}

// One clause list shared by both queries below, so a filter can never apply
// to the Yoast-primary pass but not the fill pass (or vice versa) - that is
// what keeps the three filters interchangeable instead of each growing its own
// path. Helper lives in inc/cb-utility.php because block templates render once
// per instance, so declaring it here would fatal on any page holding two of
// these blocks; it also drops any taxonomy not registered on this site, so an
// absent one means "not filtered" rather than "no results".
//
// Service is absent here on purpose: it narrows only in the fill pass below,
// because the Yoast pass already restricts to the service via its meta_query
// and adding the taxonomy clause too would exclude case studies that have the
// service as their Yoast primary but no service term assigned.
$term_clauses = cb_related_work_term_clauses(
	array(
		'theme'  => $theme_filter,
		'region' => $region_filter,
	)
);

$posts = array();

// 1. Get up to $limit posts where Yoast primary service matches (only if
// service exists), additionally filtered to the theme/region if one is set.
if ( $service_id ) {
	$yoast_query_args = array(
		'post_type'      => 'case_study',
		'posts_per_page' => $limit,
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
	);
	if ( $term_clauses ) {
		$yoast_query_args['tax_query'] = $term_clauses; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	$yoast_query = new WP_Query( $yoast_query_args );
	if ( $yoast_query->have_posts() ) {
		while ( $yoast_query->have_posts() ) {
			$yoast_query->the_post();
			$posts[] = get_the_ID();
		}
		wp_reset_postdata();
	}
}

// 2. If short of $limit, fill with posts assigned to the service and/or the
// theme/region via taxonomy. An unbounded block ($limit === -1) always runs
// this pass: there is no count left to satisfy, so "short" means "always", and
// with no service it is the only pass that runs at all - which is the path the
// migrated region pages take.
if ( -1 === $limit || count( $posts ) < $limit ) {
	$remaining = ( -1 === $limit ) ? -1 : $limit - count( $posts );

	// Built through the same helper as $term_clauses rather than appended by
	// hand, so service gets the same unregistered-taxonomy guard the other two
	// do - otherwise a site without the service taxonomy would return an empty
	// block instead of falling back to theme/region.
	$tax_query = cb_related_work_term_clauses(
		array(
			'theme'   => $theme_filter,
			'region'  => $region_filter,
			'service' => $service_id,
		)
	);
	$fill_query = new WP_Query(
		array(
			'post_type'      => 'case_study',
			'posts_per_page' => $remaining,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
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
							<?php echo esc_html( get_the_title( $post_id ) ); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width="23" height="21" alt="" class="cb-featured-work__arrow" />
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
