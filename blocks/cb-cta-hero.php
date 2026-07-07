<?php
/**
 * Block template for cb-cta-hero.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$block_title = get_field( 'title' ) ? get_field( 'title' ) : get_the_title();

$block_link = get_field( 'cta_link' );
$has_block_link = ! empty( $block_link['url'] ) && ! empty( $block_link['title'] );

?>
<section class="cta-hero">
	<div class="h1-wrapper">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			<h1 class="hero-animate"><?= esc_html( $block_title ); ?></h1>
		</div>
	</div>
	<div class="id-container pt-2 pb-1 px-4 px-md-5">
		<div class="row g-5">
			<?php if ( $has_block_link ) : ?>
				<div class="col-lg-8 cta-hero__content hero-animate hero-animate--delay-1">
					<?= wp_kses_post( get_field( 'content' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="col-lg-4 hero-animate hero-animate--delay-2">
				<?php if ( $has_block_link ) : ?>
					<a class="id-button mb-5" href="<?= esc_url( $block_link['url'] ); ?>" target="<?= esc_attr( $block_link['target'] ?: '_self' ); ?>"><?= esc_html( $block_link['title'] ); ?></a>
				<?php endif; ?>
				<div class="cta-hero__cta-title"><?= esc_html( get_field( 'cta_title' ) ); ?></div>
			</div>
			<?php if ( ! $has_block_link ) : ?>
				<div class="col-lg-8 cta-hero__content hero-animate hero-animate--delay-1">
					<?= wp_kses_post( get_field( 'content' ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
