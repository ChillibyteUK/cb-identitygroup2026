<?php
/**
 * Block template for CB Vimeo Event.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$event_url = get_field( 'event_url' );

if ( ! $event_url ) {
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

$section_classes = array( 'vimeo-event' );
if ( $bg_class ) {
	$section_classes[] = $bg_class;
}
if ( $text_class ) {
	$section_classes[] = $text_class;
}

?>
<section class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container">
	<div class="ratio ratio-16x9 cb-video-hero__video-wrap">
		<iframe
			class="cb-video-hero__video"
			src="<?= esc_url( $event_url ); ?>"
				title="Vimeo Event Player"
				frameborder="0"
				allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
				referrerpolicy="strict-origin-when-cross-origin"
				allowfullscreen>
			</iframe>
		</div>
	</div>
</section>