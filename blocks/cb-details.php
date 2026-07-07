<?php
/**
 * Block template for CB Details.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['anchor'] ?? $block['id'];

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-details">
	<div class="id-container py-4 px-4 px-md-5">
		<?php
		while ( have_rows( 'details' ) ) {
			the_row();
			?>
			<div class="cb-details__item">
				<div class="row g-5">
					<div class="col-md-6 cb-details__title">
						<?= esc_html( get_sub_field( 'title' ) ); ?>
					</div>
					<div class="col-md-6 cb-details__content fw-regular">
						<?= wp_kses_post( get_sub_field( 'content' ) ); ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</section>