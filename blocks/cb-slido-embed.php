<?php
/**
 * Block template for CB Slido Embed.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$event_hash = get_field( 'event_hash' );
if ( empty( $event_hash ) ) {
	return;
}

$bg_class = '';
if ( isset( $block['supports']['color']['background'] ) && $block['supports']['color']['background'] ) {
	$bg_color = $block['backgroundColor'] ?? '';
	if ( $bg_color ) {
		$bg_class = 'has-' . esc_attr( $bg_color ) . '-background-color';
	}
}

$text_class = '';
if ( isset( $block['supports']['color']['text'] ) && $block['supports']['color']['text'] ) {
	$text_color = $block['textColor'] ?? '';
	if ( $text_color ) {
		$text_class = 'has-' . esc_attr( $text_color ) . '-color';
	}
}

$section_classes = array( 'cb-slido-embed' );
if ( $bg_class ) {
	$section_classes[] = $bg_class;
}
if ( $text_class ) {
	$section_classes[] = $text_class;
}

?>
<section class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container">
		<iframe	src="https://app.sli.do/event/<?= esc_attr( $event_hash ); ?>" frameborder="0" allow="clipboard-write" class="cb-slido-embed__iframe"></iframe>
	</div>
</section>
