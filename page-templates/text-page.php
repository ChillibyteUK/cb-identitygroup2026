<?php
/**
 * Template Name: Text Page
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;
get_header();

?>
<main id="main" class="text-page">
	<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
		<h1 class="id-container px-4 px-md-5 fs-800 fw-light has-lime-1100-color lh-tightest pt-2 pb-1"><?= esc_html( get_the_title() ); ?></h1>
	</div>
	<div class="id-container">
		<div class="row post-content-row mb-5">
			<div class="col-md-3"></div>
			<div class="col-md-9 post-content px-4 px-md-5 ps-md-0 pe-md-5">
				<?php
				echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
?>