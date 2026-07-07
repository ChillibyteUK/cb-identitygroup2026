<?php
/**
 * Block template for CB Contact Form.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

?>
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