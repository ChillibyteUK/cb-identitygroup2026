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
$full_bleed = get_field( 'full_bleed_video' );

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
			'loop'     => '1',
			'controls' => '0',
		),
		$video_src
	);
}

$section_classes = array( 'cb-full-video' );
if ( $hero_mode ) {
	$section_classes[] = 'cb-full-video--hero';
}
if ( $full_bleed ) {
	$section_classes[] = 'cb-full-video--full-bleed';
}

$wrapper_classes = array( 'ratio', 'ratio-16x9' );
if ( ! $full_width ) {
	$wrapper_classes[] = 'id-container';
}

$iframe_id = $block_id ? $block_id . '-video' : wp_unique_id( 'cb-full-video-' );

if ( $hero_mode ) {
	wp_enqueue_script( 'vimeo-player', 'https://player.vimeo.com/api/player.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
}

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<?php if ( $full_bleed ) : ?>
    <div class="cb-full-video__bleed-wrapper">
        <iframe id="<?= esc_attr( $iframe_id ); ?>" class="full-video" src="<?= esc_url( $video_src ); ?>" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
		<?php if ( $hero_mode ) : ?>
        <button type="button" class="cb-full-video__unmute" data-video-id="<?= esc_attr( $iframe_id ); ?>" data-label-muted="Unmute" data-label-unmuted="Mute">Unmute</button>
		<?php endif; ?>
    </div>
	<?php else : ?>
    <div class="<?= esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
        <iframe id="<?= esc_attr( $iframe_id ); ?>" class="full-video" src="<?= esc_url( $video_src ); ?>" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
		<?php if ( $hero_mode ) : ?>
        <button type="button" class="cb-full-video__unmute" data-video-id="<?= esc_attr( $iframe_id ); ?>" data-label-muted="Unmute" data-label-unmuted="Mute">Unmute</button>
		<?php endif; ?>
    </div>
	<?php endif; ?>
</section>

<?php if ( $hero_mode ) : ?>
<script>
(function () {
	function initUnmute(attemptsLeft) {
		var iframe = document.getElementById(<?= wp_json_encode( $iframe_id ); ?>);
		var button = document.querySelector('.cb-full-video__unmute[data-video-id="' + <?= wp_json_encode( $iframe_id ); ?> + '"]');
		if (!iframe || !button) return;

		// player.js can itself be delayed by the same CDN/script-delay
		// layers - retry briefly instead of giving up on the first check.
		if (typeof Vimeo === 'undefined') {
			if (attemptsLeft > 0) {
				setTimeout(function () { initUnmute(attemptsLeft - 1); }, 200);
			}
			return;
		}

		var player = new Vimeo.Player(iframe);
		button.addEventListener('click', function () {
			player.getMuted().then(function (isMuted) {
				player.setMuted(!isMuted);
				button.textContent = isMuted ? button.getAttribute('data-label-unmuted') : button.getAttribute('data-label-muted');
				button.classList.toggle('cb-full-video__unmute--playing', isMuted);
				button.blur();
			});
		});
	}

	// don't rely solely on DOMContentLoaded - on some deployments (CDN/
	// script-delay layers) this inline script executes after that event
	// has already fired, silently leaving the button dead (found live on
	// identityhealth.com, 2026-09-10).
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { initUnmute(25); });
	} else {
		initUnmute(25);
	}
})();
</script>
<?php endif; ?>