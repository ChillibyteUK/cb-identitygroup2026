<?php
/**
 * Template for displaying single posts.
 *
 * @package cb-identity2025
 */

defined( 'ABSPATH' ) || exit;
get_header();

$subtitle = get_field( 'case_study_subtitle' ) ? get_field( 'case_study_subtitle' ) : 'Top 5 Pharma Company';

?>
<main id="main" class="case-study">
	<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
		<h1 class="id-container px-4 px-md-5 fs-800 fw-light has-lime-1100-color lh-tightest pt-2 pb-1"><?= get_the_title(); ?></h1>
	</div>
	<div class="has-lime-1000-border-bottom mb-4">
		<div class="id-container px-4 px-md-5">
			<div class="fw-light has-neutral-700-color fs-500 lh-tightest pt-2 pb-1"><?= esc_html( $subtitle ); ?></div>
		</div>
	</div>
    <?php
    the_content();
    ?>
</main>
<?php
get_footer();
?>