<?php
/**
 * Block template for CB Stat Hero.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$block_title = get_field( 'title' ) ? get_field( 'title' ) : '';

$block_link = get_field( 'cta_link' );
$stats      = array();

for ( $index = 1; $index <= 4; $index++ ) {
	$stats[] = array(
		'intro'  => get_field( 'stat_intro_' . $index ),
		'prefix' => get_field( 'stat_' . $index . '_prefix' ),
		'value'  => get_field( 'stat_' . $index ),
		'suffix' => get_field( 'stat_' . $index . '_suffix' ),
	);
}

?>
<section class="stat-hero">
	<div class="id-container pt-2 pb-1 px-4 px-md-5">
		<?php
		if ( $block_title ) {
			?>
		<div class="h1-wrapper">
			<h1><?= esc_html( $block_title ); ?></h1>
		</div>
			<?php
		}
		?>
		<div class="row g-4">
			<div class="col-lg-4">
				<?php if ( ! empty( $block_link['url'] ) && ! empty( $block_link['title'] ) ) : ?>
					<a class="id-button mb-5" href="<?= esc_url( $block_link['url'] ); ?>" target="<?= esc_attr( $block_link['target'] ?: '_self' ); ?>"><?= esc_html( $block_link['title'] ); ?></a>
				<?php endif; ?>
				<div class="stat-hero__cta-title"><?= esc_html( get_field( 'cta_title' ) ); ?></div>
			</div>
			<?php foreach ( $stats as $stat ) : ?>
				<div class="col-md-6 col-lg-2">
					<div class="stat-hero__intro"><?= esc_html( $stat['intro'] ); ?></div>
					<div class="stat-hero__stat">
						<?php if ( '' !== (string) $stat['prefix'] ) : ?>
							<span class="stat-hero__stat-prefix"><?= esc_html( $stat['prefix'] ); ?></span>
						<?php endif; ?>
						<span class="stat-hero__stat-value" data-stat-target="<?= esc_attr( is_numeric( $stat['value'] ) ? $stat['value'] : 0 ); ?>">0</span>
						<?php if ( '' !== (string) $stat['suffix'] ) : ?>
							<span class="stat-hero__stat-suffix"><?= esc_html( $stat['suffix'] ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
