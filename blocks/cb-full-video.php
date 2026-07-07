<?php
/**
 * Block template for CB Full Video.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$vimeo_url = get_field( 'vimeo_url' );

if ( ! $vimeo_url ) {
    return;
}

if ( $block['anchor'] ) {
	?>
<a id="<?= esc_attr( $block['anchor'] ); ?>" class="anchor"></a>
	<?php
}

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-full-video">
    <div class="id-container ratio ratio-16x9">
        <iframe class="full-video" src="<?= esc_url( cb_vimeo_url_with_dnt( $vimeo_url ) ); ?>" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
    </div>
</section>