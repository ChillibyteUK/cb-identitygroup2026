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
<?php
$render_style = get_field( 'render_style' ) ?: 'details';
$pre_title    = get_field( 'pre_title' );
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-details">
	<?php if ( $pre_title ) : ?>
	<div class="cb-details__pre-title">
		<div class="id-container pt-4 pb-3 px-4 px-md-5">
			<?= esc_html( $pre_title ); ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="id-container py-4 px-4 px-md-5">
		<?php
		if ( 'bullet_list' === $render_style ) {
			while ( have_rows( 'details' ) ) {
				the_row();
				?>
				<div class="cb-details__item cb-details__item--bullet">
					<?= esc_html( get_sub_field( 'title' ) ); ?>
				</div>
				<?php
			}
		} else {
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
		}
		?>
	</div>
</section>