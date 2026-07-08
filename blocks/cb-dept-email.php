<?php
/**
 * Block template for CB Dept Email.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id = $block['anchor'] ?? $block['id'];

?>
<section id="<?= esc_attr( $section_id ); ?>" class="cb-dept-email py-5 <?php echo esc_attr( $bg . ' ' . $fg ); ?>">
	<div class="id-container px-4 px-md-5">
		<?php
		while ( have_rows( 'departments' ) ) {
			the_row();
			?>
		<div class="row g-5 cb-dept-email__row mt-3 pb-5 mb-4">
			<div class="col-md-7 mt-2 cb-dept-email__department"><?= esc_html( get_sub_field( 'department' ) ); ?></div>
			<div class="col-md-5 mt-2 cb-dept-email__emails">
				<?php
				$emails = get_sub_field( 'email' );
				echo cb_list_to_email( $emails ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
			<?php
		}
		?>
	</div>
</section>
