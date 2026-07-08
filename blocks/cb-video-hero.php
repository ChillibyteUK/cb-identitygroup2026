<?php
/**
 * Block template for CB Video Hero.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

cb_deprecated_block_notice( 'CB Full Video', $is_preview );

$block_id  = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-video-hero-' );
$vimeo_url = get_field( 'vimeo_url' );

if ( ! $vimeo_url ) {
	return;
}

$vimeo_url = cb_vimeo_url_with_dnt( $vimeo_url );
$vimeo_url = add_query_arg(
	array(
		'autoplay' => '1',
		'muted'    => '1',
	),
	$vimeo_url
);
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-video-hero">
	<div class="id-container">
		<div class="ratio ratio-16x9 cb-video-hero__video-wrap">
			<iframe
				class="cb-video-hero__video"
				src="<?= esc_url( $vimeo_url ); ?>"
				title="Vimeo video player"
				frameborder="0"
				allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
				referrerpolicy="strict-origin-when-cross-origin"
				allowfullscreen>
			</iframe>
		</div>
	</div>
</section>
