<?php
/**
 * Block template for CB Contact Form.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id    = $block['id'] ?? '';
$is_identity = 'identity' === cb_site_template_suffix();
$is_coda     = 'coda' === cb_site_template_suffix();

?>
<?php if ( $is_identity ) : ?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-contact-form">
	<div class="id-container px-4 px-md-5">
		<h2 class="cb-contact-form__title pt-4 pb-3">
			<?= esc_html( get_field( 'title' ) ); ?>
		</h2>
		<div class="row">
			<div class="col-md-6">
				<div class="cb-contact-form__intro mb-4">
					<?= wp_kses_post( get_field( 'intro' ) ); ?>
				</div>
			</div>
			<div class="col-md-6">
				<?= do_shortcode( get_field( 'contact_form_shortcode' ) ); ?>
			</div>
		</div>
	</div>
</section>
<?php elseif ( $is_coda ) : ?>
<section class="cb-contact-form">
	<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
		<h1 class="id-container px-4 px-md-5 fs-850 fw-light has-lime-1100-color lh-tightest pt-2 pb-1"><?= esc_html( get_field( 'title' ) ); ?></h1>
	</div>
	<div class="id-container px-4 px-md-5 pt-5 pb-5">
		<div class="row">
			<div class="col-md-6">
				<div class="fs-700 fw-light has-lime-1000-color w-constrained pb-5">
					<?= wp_kses_post( get_field( 'intro' ) ); ?>
				</div>
			</div>
			<div class="col-md-6">
				<?= do_shortcode( get_field( 'contact_form_shortcode' ) ); ?>
			</div>
		</div>
	</div>
</section>
<?php else : ?>
<section class="cb-contact-form">
	<div class="cb-contact-form__title">
		<h1 class="id-container px-4 px-md-5">
			<?= esc_html( get_field( 'title' ) ); ?>
		</h1>
	</div>
	<div class="id-container px-4 px-md-5 pt-5 pb-5">
		<div class="row">
			<div class="col-md-6">
				<div class="cb-contact-form__intro-text pb-5">
					<?= wp_kses_post( get_field( 'intro' ) ); ?>
				</div>
			</div>
			<div class="col-md-6">
				<?= do_shortcode( get_field( 'contact_form_shortcode' ) ); ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
<div class="cb-contact-form-spacer"></div>
<script>
document.addEventListener( 'DOMContentLoaded', function() {
	// Hide Gravity Forms required field asterisk
	document.querySelectorAll('.gform_wrapper.gravity-theme .gfield_required').forEach(function(el) {
		el.style.display = 'none';
	});
	// Add id-button class to Gravity Forms submit button
	document.querySelectorAll('.gform_button.button').forEach(function(btn) {
		btn.classList.add('id-button');
	});
} );
</script>