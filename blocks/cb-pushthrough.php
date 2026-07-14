<?php
/**
 * Block template for CB Pushthrough.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-pushthrough-' );

$link            = get_field( 'link' );
$pretitle        = get_field( 'pretitle' );
$background      = get_field( 'background' );
$left_content    = get_field( 'left_content' );
$left_type       = get_field( 'left_content_type' ) ?: 'Text';
$title           = get_field( 'title' );
$description     = get_field( 'description' );
$background_url  = $background ? wp_get_attachment_image_url( $background, 'full' ) : '';
$is_identity     = 'identity' === cb_site_template_suffix();
$is_idtravel     = 'idtravel' === cb_site_template_suffix();
$section_classes = array( 'cb-pushthrough' );

if ( $background_url ) {
	// idtravel's own real SCSS targets the BEM modifier `--has-bg`; identity's
	// and coda's real sources both just add a bare `has-bg` — confirmed
	// neither ever emits the other site's class.
	$section_classes[] = $is_idtravel ? 'cb-pushthrough--has-bg' : 'has-bg';
}

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

// idtravel's real source is the only one with a light/dark-lines toggle on
// this block, driven by the Gutenberg backgroundColor picker; identity's
// and coda's real sources never emit either class (confirmed against both).
$line_class = '';
if ( $is_idtravel ) {
	$line_class = 'dark-lines';
	if ( ! empty( $block['backgroundColor'] ) ) {
		if ( preg_match( '/(\d+)(?!.*\d)/', $block['backgroundColor'], $matches ) ) {
			$line_class = (int) $matches[1] >= 600 ? 'light-lines' : 'dark-lines';
		} else {
			$line_class = 'light-lines';
		}
	}
}

$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg, $line_class ) ) );

$section_style = $background_url ? sprintf( '--_bg-url: url(%s);', esc_url_raw( $background_url ) ) : '';
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<?php
	// identity's and coda's real sources both render this overlay div
	// whenever there's a background image; idtravel's real source has it
	// commented out entirely (0 backgrounds ever used there in practice).
	if ( $background_url && ! $is_idtravel ) {
		$overlay_modifier = $left_content ? 'overlay--black' : '';
		?>
		<div class="overlay <?= esc_attr( $overlay_modifier ); ?>"></div>
		<?php
	}
	?>
	<?php if ( $pretitle ) : ?>
		<div class="cb-pushthrough__pretitle">
			<div class="id-container px-4 px-md-5">
				<?= esc_html( $pretitle ); ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="id-container px-4 px-md-5<?= $is_identity ? ' py-5' : ' py-4'; ?>">
		<div class="row g-5 py-4">
			<div class="col-md-6" data-aos="fade">
				<?php
				if ( 'Text' === $left_type ) {
					if ( $title ) {
						// identity's real h2 has no width constraint at all - both
						// coda's and idtravel's real sources do (25ch), so this
						// stays as-is for them.
						?>
						<h2 class="cb-pushthrough__title<?= $is_identity ? '' : ' w-constrained'; ?>"<?= $is_identity ? '' : ' style="--width:25ch"'; ?>><?= esc_html( $title ); ?></h2>
						<?php
					}
					if ( $left_content ) {
						?>
						<div class="cb-pushthrough__left-content mb-3 pt-3">
							<?= wp_kses_post( $left_content ); ?>
						</div>
						<?php
					}
				} else {
					// rm san if svg is used as left content type.
					if ( get_field( 'logo' ) ) {
						?>
						<div class="cb-pushthrough__logo mb-3 pt-3"><?= wp_get_attachment_image( get_field( 'logo' ), 'full' ); ?></div>
						<?php
					} else {
						$logo = get_attached_file( get_field( 'logo' ), 'full' ) ?: get_stylesheet_directory() . '/img/identity-logo.svg';
						?>
					<div class="cb-pushthrough__logo"><?= cb_sanitise_svg( $logo, '', '570px' ); ?></div>
						<?php
					}
				}
				?>
			</div>

			<div class="col-md-6" data-aos="fade" data-aos-delay="100">
				<?php
				if ( $description ) {
					?>
					<div class="cb-pushthrough__desc<?= $is_identity ? '' : ' fw-regular'; ?>">
						<?= wp_kses_post( $description ); ?>
					</div>
					<?php
				}
				if ( $link && isset( $link['url'], $link['title'] ) ) {
					?>
					<a href="<?= esc_url( $link['url'] ); ?>" class="cb-pushthrough__link" target="<?= esc_attr( $link['target'] ?: '_self' ); ?>">
						<?= esc_html( $link['title'] ); ?>
						<?php if ( $is_idtravel ) : ?>
						<span class="cb-pushthrough__link-arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'cb-pushthrough__link-arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php else : ?>
						<?php $arrow = ( $is_identity && ! $background ) ? 'arrow-wh.svg' : 'arrow-g400.svg'; ?>
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/' . $arrow ); ?>" width="33" height="26" alt="" />
						<?php endif; ?>
					</a>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
