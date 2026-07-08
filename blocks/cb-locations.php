<?php
/**
 * Block template for CB Locations.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id = $block['anchor'] ?? $block['id'];

?>
<section id="<?= esc_attr( $section_id ); ?>" class="cb-locations py-5 <?php echo esc_attr( $bg . ' ' . $fg ); ?>">
	<div class="id-container px-4 px-md-5">
		<?php
		while ( have_rows( 'locations' ) ) {
			the_row();
			?>
		<div class="row g-5 cb-locations__row mt-3 pb-5 mb-4">
			<div class="col-md-7 mt-2 cb-locations__name">
				<?= esc_html( get_sub_field( 'location' ) ); ?>
			</div>
			<div class="col-md-5 mt-2 cb-locations__address">
				<?= wp_kses_post( get_sub_field( 'address' ) ); ?>
			</div>
		</div>
			<?php
		}
		?>
	</div>
</section>
