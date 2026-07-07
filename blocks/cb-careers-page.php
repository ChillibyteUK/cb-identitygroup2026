<?php
/**
 * Block template for CB Careers Page.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

?>
<style>
.cb-careers-page {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( get_field( 'background' ), 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-careers-page">
	<div class="overlay"></div>
	<h1 class="cb-careers-page__title">
		<div class="id-container px-4 px-md-5">
			<?= esc_html( get_field( 'title' ) ); ?>
		</div>
	</h1>
	<div class="id-container">
		<div class="cb-careers-page__inner px-4 px-md-5">
			<div class="row">
				<div class="col-md-9 offset-md-3 cb-careers-page__content">
					<?= wp_kses_post( get_field( 'content' ) ); ?>
					<?php
					if ( get_field( 'link' ) ) {
						$l = get_field( 'link' );
						?>
					<div class="mt-5">
						<a href="<?= esc_url( $l['url'] ); ?>" target="<?= esc_attr( $l['target'] ); ?>" class="id-button"><?= esc_html( $l['title'] ); ?></a>
					</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
