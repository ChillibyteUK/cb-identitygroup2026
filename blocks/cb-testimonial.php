<?php
/**
 * CB Testimonial Block Template.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-testimonial-' );

$quote   = get_field( 'quote' );
$author  = get_field( 'author' );
$company = get_field( 'company' );
$style   = get_field( 'style' );

if ( ! $quote ) {
	return;
}

$classes = array( 'cb-testimonial' );

if ( $style ) {
	$classes[] = $style;
}

if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="id-container px-4 px-md-5 py-5" data-aos="fade-up">
		<div class="cb-testimonial__quote">&ldquo;<?= wp_kses_post( $quote ); ?>&rdquo;</div>

		<?php if ( $author ) : ?>
			<div class="cb-testimonial__author"><?= esc_html( $author ); ?></div>
		<?php endif; ?>

		<?php if ( $company ) : ?>
			<div class="cb-testimonial__company"><?= esc_html( $company ); ?></div>
		<?php endif; ?>
	</div>
</section>
