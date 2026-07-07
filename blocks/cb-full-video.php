<?php
/**
 * Block template for CB Full Video.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$vimeo_url  = get_field( 'vimeo_url' );
$full_width = get_field( 'full_width' );
$hero_mode  = get_field( 'hero_mode' );

if ( ! $vimeo_url ) {
    return;
}

if ( $block['anchor'] ) {
	?>
<a id="<?= esc_attr( $block['anchor'] ); ?>" class="anchor"></a>
	<?php
}

$video_src = cb_vimeo_url_with_dnt( $vimeo_url );

if ( $hero_mode ) {
	$video_src = add_query_arg(
		array(
			'autoplay' => '1',
			'muted'    => '1',
		),
		$video_src
	);
}

$section_classes = array( 'cb-full-video' );
if ( $hero_mode ) {
	$section_classes[] = 'cb-full-video--hero';
}

$wrapper_classes = array( 'ratio', 'ratio-16x9' );
if ( ! $full_width ) {
	$wrapper_classes[] = 'id-container';
}

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="<?= esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
        <iframe class="full-video" src="<?= esc_url( $video_src ); ?>" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
    </div>
</section>