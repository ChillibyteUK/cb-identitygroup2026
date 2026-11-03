<?php
/**
 * Template for displaying single posts.
 *
 * @package cb-identity2025
 */

defined( 'ABSPATH' ) || exit;
get_header( cb_site_template_suffix() );

$subtitle = get_field( 'case_study_subtitle' ) ? get_field( 'case_study_subtitle' ) : 'Top 5 Pharma Company';

?>
<main id="main" class="case-study">
	<?php
	// Travel: guarantee exactly one <h1>. Other sites rely on the CB Case
	// Study Hero block (or, for coda, the branch below); a Travel case study
	// saved without that block would otherwise render no heading at all.
	// Subtitle comes from the post-level Case Study Subtitle meta box.
	if ( 'idtravel' === cb_site_template_suffix() && ! has_block( 'acf/cb-case-study-hero' ) ) :
		?>
	<section class="case-study-hero has-primary-black-background-color pt-5">
		<h1>
			<div class="id-container px-4 px-md-5 pt-1"><?= esc_html( get_the_title() ); ?></div>
		</h1>
		<?php $travel_subtitle = get_post_meta( get_the_ID(), 'case_study_subtitle', true ); ?>
		<?php if ( $travel_subtitle ) : ?>
		<h2>
			<div class="id-container px-4 px-md-5 pt-2"><?= esc_html( $travel_subtitle ); ?></div>
		</h2>
		<?php endif; ?>
	</section>
	<?php endif; ?>
	<?php if ( 'coda' === cb_site_template_suffix() ) : ?>
	<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
		<h1 class="id-container px-4 px-md-5 fs-800 fw-light has-lime-1100-color lh-tightest pt-2 pb-1"><?= get_the_title(); ?></h1>
	</div>
	<div class="has-lime-1000-border-bottom mb-4">
		<div class="id-container px-4 px-md-5">
			<div class="fw-light has-neutral-700-color fs-500 lh-tightest pt-2 pb-1"><?= esc_html( $subtitle ); ?></div>
		</div>
	</div>
	<?php endif; ?>
    <?php
    the_content();
    ?>
</main>
<?php
get_footer( cb_site_template_suffix() );
?>