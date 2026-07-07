<?php
/**
 * File responsible for registering custom ACF blocks and modifying core block arguments.
 *
 * @package cb-identitygroup2026
 */

/**
 * Registers custom ACF blocks.
 *
 * This function checks if the ACF plugin is active and registers custom blocks
 * for use in the WordPress block editor. Each block has its own name, title,
 * category, icon, render template, and supports various features.
 */
function acf_blocks() {
	if ( function_exists( 'acf_register_block_type' ) ) {

		// INSERT NEW BLOCKS HERE.

		// Copied from cb-coda2026 — did not exist in idtravel base.
		acf_register_block_type(
			array(
				'name'            => 'cb_details',
				'title'           => __( 'CB Details' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-details.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_about_page_header',
				'title'           => __( 'CB About Page Header' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-about-page-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_service_page_header',
				'title'           => __( 'CB Service Page Header' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-service-page-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_work_index',
				'title'           => __( 'CB Work Index' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-work-index.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_full_image',
				'title'           => __( 'CB Full Image' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-full-image.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_full_video',
				'title'           => __( 'CB Full Video' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-full-video.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_latest_insights',
				'title'           => __( 'CB Latest Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-latest-insights.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_featured_work',
				'title'           => __( 'CB Featured Work' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-featured-work.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_services_nav',
				'title'           => __( 'CB Services Nav' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-services-nav.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		// Converted from identity's block.json/register_block_type — identity-only, gated by cb_site (see Phase D).
		acf_register_block_type(
			array(
				'name'            => 'cb_case_study_hero',
				'title'           => __( 'CB Case Study Hero' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-case-study-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_brand_title_text',
				'title'           => __( 'CB Brand Title Text' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-brand-title-text.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_culture_page_header',
				'title'           => __( 'CB Culture Page Header' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-culture-page-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_innovation_header',
				'title'           => __( 'CB Innovation Header' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-innovation-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_policies_page',
				'title'           => __( 'CB Policies Page' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-policies-page.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_region_page_header',
				'title'           => __( 'CB Region Page Header' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-region-page-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_file_block',
				'title'           => __( 'CB File Block' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-file-block.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_work_carousel',
				'title'           => __( 'CB Work Carousel' ),
				'category'        => 'layout',
				'icon'            => 'admin-generic',
				'render_template' => 'blocks/cb-work-carousel.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_faq',
				'title'           => __( 'CB FAQ' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-faq.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_vimeo_event',
				'title'           => __( 'CB Vimeo Event' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-vimeo-event.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_slido_embed',
				'title'           => __( 'CB Slido Embed' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-slido-embed.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_leadership',
				'title'           => __( 'CB Leadership' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-leadership.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_decision_gate',
				'title'           => __( 'CB Decision Gate' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-decision-gate.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_hero_prop_cta',
				'title'           => __( 'CB Hero Prop CTA' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-hero-prop-cta.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_service_grid',
				'title'           => __( 'CB Service Grid' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-service-grid.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'content_grid_links',
				'title'           => __( 'content grid links' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/content-grid-links.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_plain_hero',
				'title'           => __( 'CB Plain Hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-plain-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_video_hero',
				'title'           => __( 'CB Video Hero' ),
				'description'     => 'A simple Vimeo hero section with a large embedded video.',
				'category'        => 'layout',
				'icon'            => 'video-alt3',
				'render_template' => 'blocks/cb-video-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_contact_form',
				'title'           => __( 'CB Contact Form' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-contact-form.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_contact_page',
				'title'           => __( 'CB Contact Page' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-contact-page.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_careers_page',
				'title'           => __( 'CB Careers Page' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-careers-page.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-our-brands',
				'title'           => __( 'CB Our Brands' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-our-brands.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-pushthrough',
				'title'           => __( 'CB Pushthrough' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-pushthrough.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_about_hero',
				'title'           => __( 'CB About Hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-about-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-specialist-travel-nav',
				'title'           => __( 'CB Specialist Travel Nav' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-specialist-travel-nav.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-logo-slider',
				'title'           => __( 'CB Logo Slider' ),
				'category'        => 'layout',
				'icon'            => 'images-alt2',
				'render_template' => 'blocks/cb-logo-slider.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_tmc_post_index',
				'title'           => __( 'CB TMC Post Index' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-tmc-post-index.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-cta-hero',
				'title'           => __( 'cb-cta-hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-cta-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-business-travel-nav',
				'title'           => __( 'CB Business Travel Nav' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-business-travel-nav.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb-testimonial',
				'title'           => 'CB Testimonial',
				'description'     => 'Testimonial quote block.',
				'render_template' => 'blocks/cb-testimonial.php',
				'category'        => 'layout',
				'icon'            => 'format-quote',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_solutions_nav',
				'title'           => __( 'CB Solutions Nav' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-solutions-nav.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_stat_hero',
				'title'           => __( 'CB Stat Hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-stat-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_stats',
				'title'           => __( 'CB Stats' ),
				'description'     => 'Stat counters with an optional background image.',
				'category'        => 'layout',
				'icon'            => 'chart-bar',
				'render_template' => 'blocks/cb-stats.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_image_feature_overlay',
				'title'           => __( 'CB Image Feature Overlay' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-image-feature-overlay.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_signpost_header',
				'title'           => __( 'CB Signpost Header' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-signpost-header.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
                    'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_cta',
				'title'           => 'CB CTA',
				'description'     => 'Reusable CTA block powered by site-wide CTA options.',
				'render_template' => 'blocks/cb-cta.php',
				'category'        => 'layout',
				'icon'            => 'megaphone',
				'keywords'        => array( 'cta', 'call to action' ),
				'mode'            => 'edit',
				'supports'        => array(
					'align' => false,
					'mode'  => true,
				),
			)
		);

        acf_register_block_type(
            array(
                'name'            => 'cb_recent_news',
                'title'           => __( 'CB Recent News' ),
                'category'        => 'layout',
                'icon'            => 'cover-image',
                'render_template' => 'blocks/cb-recent-news.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'      => false,
                    'anchor'    => true,
                    'className' => true,
                    'align'     => true,
                ),
            )
        );

		acf_register_block_type(
			array(
				'name'            => 'cb-content-grid',
				'title'           => 'CB Content Grid',
				'description'     => 'Modular content grid with rows and columns.',
				'render_template' => 'blocks/cb-content-grid.php',
				'category'        => 'layout',
				'icon'            => 'layout',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'  => true,
					'align' => true,
					'color' => array(
						'text'       => true,
						'background' => true,
					),
				),
			)
		);

    }
}
add_action( 'acf/init', 'acf_blocks' );

// Auto-sync ACF field groups from acf-json folder.
add_filter(
	'acf/settings/save_json',
	function ( $path ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		return get_stylesheet_directory() . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		unset( $paths[0] );
		$paths[] = get_stylesheet_directory() . '/acf-json';
		return $paths;
	}
);

/**
 * Modifies the arguments for specific core block types.
 *
 * @param array  $args The block type arguments.
 * @param string $name The block type name.
 * @return array Modified block type arguments.
 */
function core_block_type_args( $args, $name ) {

	if ( 'core/paragraph' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/heading' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/list' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/separator' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/spacer' === $name ) {
		$args['supports']['color'] = array(
			'background' => true,
			'text'       => false,
			'gradients'  => true,
		);
	}

    return $args;
}
add_filter( 'register_block_type_args', 'core_block_type_args', 10, 3 );

/**
 * Helper function to detect if footer.php is being rendered.
 *
 * @return bool True if footer.php is being rendered, false otherwise.
 */
function is_footer_rendering() {
    $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
    foreach ( $backtrace as $trace ) {
        if ( isset( $trace['file'] ) && basename( $trace['file'] ) === 'footer.php' ) {
            return true;
        }
    }
    return false;
}

/**
 * Adds a container div around the block content unless footer.php is being rendered.
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 * @return string The modified block content wrapped in a container div.
 */
function modify_core_add_container( $attributes, $content ) {
    if ( is_footer_rendering() ) {
        return $content;
    }

    ob_start();
    ?>
    <div class="container">
        <?= wp_kses_post( $content ); ?>
    </div>
	<?php
	$content = ob_get_clean();
    return $content;
}
