<?php
/**
 * Block template for CB Work Index.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$bg_case_study = get_field( 'hero_case_study' )[0] ?? null;

if ( ! $bg_case_study ) {
	$latest_query = new WP_Query(
		array(
			'post_type'      => 'case_study',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	if ( $latest_query->have_posts() ) {
		$latest_query->the_post();
		$bg_case_study = get_the_ID();
		wp_reset_postdata();
	}
}
?>
<section class="work-index-hero">
	<div class="work-index-hero__title my-4">
		<h1 class="has-850-font-size fw-light">
			<div class="id-container px-4 px-md-5 pt-2">
				Work
			</div>
		</h1>
		<h2 class="has-500-font-size fw-light">
			<div class="id-container px-4 px-md-5 pt-2 pb-1">
				Where experience changes everything
			</div>
		</h2>
	</div>
	<div class="work-index-hero__intro grad-main pt-1 pb-5">
		<div class="id-container px-4 px-md-5">
			<p class="has-700-font-size fw-light">
				From global congresses to investigator meetings, we deliver healthcare experiences with technical precision and creative clarity. Every programme is designed to bring complex science to life – whether live, hybrid, or digital. 
			</p>
		</div>
	</div>
	<div class="id-container">
		<?php
			// get title and thumbnail of first sticky or latest case study for background image.

		if ( $bg_case_study ) {
			?>
		<a href="<?= esc_url( get_the_permalink( $bg_case_study ) ); ?>" class="work-index-hero__background">
			<?php
			$bg_image_id = get_post_thumbnail_id( $bg_case_study );
			if ( $bg_image_id ) {
				echo wp_get_attachment_image( $bg_image_id, 'full', false, array( 'class' => 'work-index-hero__image' ) );
			}
		}
		?>
			<div class="overlay"></div>
			<div class="bottom-overlay"></div>
			<div class="work-index-hero__content px-4 px-md-5">
				<div class="work-index-hero__card-title has-700-font-size fw-semi">
					<?php echo esc_html( get_the_title( $bg_case_study ) ); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=23 height=21 alt="" class="cb-services-nav__item-icon" />
				</div>
				<div class="work-index-hero__card-desc">
					<?php
					// get the case_study_subtitle field from the cb-case-study-hero block if available.
					$post_blocks = parse_blocks( get_post_field( 'post_content', $bg_case_study ) );
					$subtitle    = cb_find_hero_subtitle( $post_blocks );
					if ( $subtitle ) {
						echo esc_html( $subtitle );
					} else {
						$excerpt = get_the_excerpt( $bg_case_study );
						echo wp_kses_post( wp_trim_words( $excerpt, 18, '...' ) );
					}
					?>
				</div>
			</div>
		<?php
		if ( $bg_case_study ) {
			?>
		</a>
			<?php
		}
		?>
	</div>
</section>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-work-index">
	<?php
	/* // phpcs:disable
	<div class="cb-work-index__filter-bar py-4 px-4 px-md-5">
		<div class="id-container py-4 cb-work-index__filters" data-service-map='<?php echo esc_attr( $service_map_json ); ?>' data-theme-map='<?php echo esc_attr( $theme_map_json ); ?>'>
			<div class="row g-4 align-items-center">
				<div class="col-12 col-md-2 col-lg-2 col-x1-1">
					FILTER BY:
				</div>
				<div class="col-md-6">
			<?php
			$service_terms = get_terms(
				array(
					'taxonomy'   => 'service',
					'hide_empty' => true,
					'parent'     => 0, // only top-level parent terms.
				)
			);
			if ( ! is_wp_error( $service_terms ) && ! empty( $service_terms ) ) {
				?>
			<select id="cb-work-index-service-filter" class="cb-work-index__filter-select">
				<option value="all">All Services</option>
				<?php
				foreach ( $service_terms as $service_term ) {
					?>
					<option value="<?php echo esc_attr( $service_term->slug ); ?>"><?php echo esc_html( $service_term->name ); ?></option>
					<?php
				}
				// Add sports from theme taxonomy.
				$sports_term = get_term_by( 'slug', 'sports', 'theme' );
				if ( $sports_term && ! is_wp_error( $sports_term ) ) {
					?>
					<option value="sports">Identity Sport</option>
					<?php
				}
				?>
			</select>
				<?php
			}
			?>
				</div>
				<div class="col-md-2">
					<button id="cb-work-index-filter-reset" class="btn btn-id-outline-lime">Reset</button>
				</div>
			</div>
		</div>
	</div>
	// phpcs:enable 
	*/
	?>
	<div class="has-lime-1000-border-bottom">
		<div class="id-container py-3 px-4 px-md-5">
			<h2 class="fs-300 fw-regular lh-tightest pt-1 pb-0 mb-0 text-uppercase" >Featured Work</h2>
		</div>
	</div>
	<div class="id-container px-4 px-md-5 py-4">
		<div class="cb-work-index__cards row g-2">
			<?php

			$q = new WP_Query(
				array(
					'post_type'      => 'case_study',
					'posts_per_page' => -1,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'post__not_in'   => array( $bg_case_study ),
				)
			);


			if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					// get service terms for filtering and include ancestors so parent filters match.
					$service_terms   = get_the_terms( get_the_ID(), 'service' );
					$service_classes = '';
					if ( ! is_wp_error( $service_terms ) && ! empty( $service_terms ) ) {
						$s_slugs = array();
						foreach ( $service_terms as $service_term ) {
							$s_slugs[] = $service_term->slug;
							$ancestors = get_ancestors( $service_term->term_id, 'service' );
							if ( ! empty( $ancestors ) ) {
								foreach ( $ancestors as $anc_id ) {
									$anc_term = get_term( $anc_id, 'service' );
									if ( $anc_term && ! is_wp_error( $anc_term ) ) {
										$s_slugs[] = $anc_term->slug;
									}
								}
							}
						}
						$s_slugs = array_values( array_unique( $s_slugs ) );
						foreach ( $s_slugs as $slug ) {
							$service_classes .= ' service-' . $slug;
						}
					}
					// Add sports theme term to service classes.
					$theme_terms = get_the_terms( get_the_ID(), 'theme' );
					if ( ! is_wp_error( $theme_terms ) && ! empty( $theme_terms ) ) {
						foreach ( $theme_terms as $theme_term ) {
							if ( 'sports' === $theme_term->slug ) {
								$service_classes .= ' service-sports';
								break;
							}
						}
					}
					?>
			<div class="col-md-6" data-service-terms="<?php echo esc_attr( trim( $service_classes ) ); ?>">
					<?php
					$video     = get_field( 'vimeo_url', get_the_ID() );
					$has_video = $video ? 'has_video' : '';
					?>
				<a href="<?= esc_url( get_the_permalink() ); ?>" class="cb-work-index__card <?= esc_attr( $has_video ); ?>">
					<?php
					if ( $video ) {
						?>
					<iframe class="work-video" src="<?= esc_url( cb_vimeo_url_with_dnt( $video ) ); ?>&background=1&autoplay=0" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
						<?php
					}
					?>
					<?php echo get_work_image( get_the_ID(), 'cb-work-index__image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="cb-work-index__content px-4 px-md-5">
						<div class="cb-work-index__title">
							<?php the_title(); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=23 height=21 alt="" class="cb-services-nav__item-icon" />
						</div>
						<div class="cb-work-index__desc">
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
				wp_reset_postdata();
			}
			?>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Video hover play/pause binding.
	document.querySelectorAll('.cb-work-index__card').forEach(function(card) {
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

	// Cross-filtering selects and maps.
	const filterContainer = document.querySelector('.cb-work-index__filters');
	if (!filterContainer) return;

	const serviceSelect = document.getElementById('cb-work-index-service-filter');
	const cardsContainer = document.querySelector('.cb-work-index__cards');
	const resetButton = document.getElementById('cb-work-index-filter-reset');

	let serviceTom = serviceSelect ? new TomSelect(serviceSelect, {
		allowEmptyOption: true,
		closeAfterSelect: true
	}) : null;

	// Check for URL parameters and apply filtering
	function getUrlParameter(name) {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get(name);
	}

	// Apply URL parameters on page load
	const serviceParam = getUrlParameter('service');
	if (serviceParam && serviceTom) {
		// Check if the parameter value exists in the select options
		const options = Array.from(serviceSelect.options).map(opt => opt.value);
		if (options.includes(serviceParam)) {
			serviceTom.setValue(serviceParam);
			// Trigger filtering after setting the value
			filterCards();
		}
	}

	function filterCards() {
		const selectedService = serviceTom ? serviceTom.getValue() : (serviceSelect ? serviceSelect.value : 'all');

		document.querySelectorAll('.cb-work-index__card').forEach(function(card) {
			const cardWrapper = card.parentElement;
			const cardServiceTerms = cardWrapper.getAttribute('data-service-terms') || '';
			const serviceMatch = (selectedService === 'all') || (cardServiceTerms && cardServiceTerms.includes('service-' + selectedService));
			if (serviceMatch) {
				cardWrapper.style.display = '';
			} else {
				cardWrapper.style.display = 'none';
			}
		});

		if (cardsContainer) {
			cardsContainer.scrollTop = 0;
		}
	}

	if (serviceTom) {
		serviceTom.on('change', function() {
			filterCards();
		});
	}

	function resetFilters() {
		if (serviceTom) {
			serviceTom.setValue('all');
		}
		filterCards();
	}

	if (resetButton) {
		resetButton.addEventListener('click', function(e) {
			e.preventDefault();
			resetFilters();
		});
	}
});
</script>
		<?php
	}
);
