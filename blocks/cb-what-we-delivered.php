<?php
/**
 * Block template for CB What We Delivered.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Travel only: optional editor-set background image (see cb-site-tokens.php).
$section_classes = 'cb-what-we-delivered';
$section_style   = '';
if ( 'idtravel' === cb_site_template_suffix() ) {
	$bg_url = get_field( 'background_image' ) ? wp_get_attachment_image_url( get_field( 'background_image' ), 'full' ) : '';
	if ( $bg_url ) {
		$section_classes .= ' cb-what-we-delivered--has-background-image';
		$section_style    = sprintf( '--cb-what-we-delivered-bg: url(%s);', esc_url_raw( $bg_url ) );
	}
}

?>
<section class="<?= esc_attr( $section_classes ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="cb-what-we-delivered__container pb-5">
		<div class="cb-what-we-delivered__header">
			<div class="id-container pt-1 px-4 px-md-5">
				<h2>What We Delivered</h2>
			</div>
		</div>
        <div class="cb-what-we-delivered__grid-wrapper">
			<?php
			$delay = 0;
			while ( have_rows( 'deliverables' ) ) {
				the_row();
				$item = get_sub_field( 'item' );
				?>
				<div class="cb-what-we-delivered__item">
					<div class="id-container px-4 px-md-5"  data-aos="fade-up" data-aos-delay="<?= esc_attr( $delay ); ?>">
						<?= esc_html( $item ); ?>
					</div>
				</div>
				<?php
				$delay += 100;
			}
			?>
		</div>
	</div>
</section>
