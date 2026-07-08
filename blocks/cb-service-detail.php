<?php
/**
 * Block template for CB Service Detail.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-service-detail">
	<?php
	if ( get_field( 'pre_title' ) ) {
		?>
	<div class="cb-service-detail__pre-title">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			<?= esc_html( get_field( 'pre_title' ) ); ?>
		</div>
	</div>
		<?php
	}
	?>
	<div class="id-container px-4 px-md-5">
		<?php
		if ( have_rows( 'details' ) ) {
			$c = 0;
			while ( have_rows( 'details' ) ) {
				the_row();
				$item_title  = get_sub_field( 'title' );
				$description = get_sub_field( 'description' );
				?>
			<div class="row cb-service-detail__row" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="col-md-6">
					<h2 class="cb-service-detail__title"><?= esc_html( $item_title ); ?></h2>
				</div>
				<div class="col-md-1"></div>
				<div class="col-md-5">
					<div class="cb-service-detail__description">
						<?= wp_kses_post( $description ); ?>
					</div>
				</div>
			</div>
				<?php
				$c += 100;
			}
		}
		?>
	</div>
</section>
