<?php
/**
 * Block template for CB Service Page Header.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$width   = get_field( 'content_width' ) ? get_field( 'content_width' ) . 'ch' : '50ch';
$balance = get_field( 'balance' ) ? 'text-wrap: balance;' : '';

?>
<section class="service-page-header">
	<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
		<h1 class="id-container px-4 px-md-5 fs-850 fw-light has-lime-1100-color lh-tightest pt-2 pb-1"><?= esc_html( get_field( 'title' ) ); ?></h1>
	</div>
	<?php
	if ( get_field( 'subtitle' ) ) {
		?>
	<div class="has-lime-1000-border-bottom mb-4">
		<div class="id-container px-4 px-md-5">
			<h2 class="fw-light has-neutral-700-color fs-500 lh-tightest pt-2 pb-1"><?= esc_html( get_field( 'subtitle' ) ); ?></h2>
		</div>
	</div>
		<?php
	}
	?>
	<div class="id-container px-4 px-md-5 pt-5 pb-5">
		<div class="fs-700 fw-light has-lime-1000-color w-constrained pb-5" style="--width: <?= esc_attr( $width ); ?>; <?= esc_attr( $balance ); ?>">
			<?= wp_kses_post( get_field( 'content' ) ); ?>
		</div>
		<?php
		if ( get_field( 'link' ) ) {
			$l = get_field( 'link' );
			?>
		<a href="<?= esc_url( $l['url'] ); ?>" target="<?= esc_attr( $l['target'] ); ?>" class="id-button" tabindex="0"><?= esc_html( $l['title'] ); ?></a>
			<?php
		}
		?>
	</div>
</section>