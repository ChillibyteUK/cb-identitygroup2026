<?php
/**
 * CB Our Brands Block Template.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id   = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-our-brands-' );
$intro_text = get_field( 'intro_text' );
$brands     = get_field( 'brands' ) ?: array();

$section_style = '';

$section_classes = array( 'cb-our-brands' );

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg ) ) );

if ( ! empty( $block['backgroundColor'] ) ) {
	$section_style = '--cb-our-brands-card-bg: var(--col-' . sanitize_html_class( $block['backgroundColor'] ) . ');';
}

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

$display_count = 0;
foreach ( $brands as $brand ) {
	if ( ! empty( $brand['brand_logo'] ) && ! empty( $brand['link'] ) ) {
		++$display_count;
	}
}

$xxl_rem      = $display_count % 4;
$xl_rem       = $display_count % 3;
$md_rem       = $display_count % 2;
$last_col_xxl = 0 === $xxl_rem ? 12 : ( 4 - $xxl_rem ) * 3;
$last_col_xl  = 0 === $xl_rem ? 12 : ( 3 - $xl_rem ) * 4;
$last_col_md  = 0 === $md_rem ? 12 : 6;
?>
<a id="brands" class="anchor"></a>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<?php if ( $intro_text ) : ?>
		<div class="id-container px-4 px-md-5">
			<div class="cb-our-brands__intro mb-5">
				<div class="cb-our-brands__title"><?= wp_kses_post( $intro_text ); ?></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="cb-our-brands__brands id-container px-4 px-md-5 pb-5">
		<div class="row g-5" data-aos-stagger-group>
			<?php if ( have_rows( 'brands' ) ) : ?>
				<?php while ( have_rows( 'brands' ) ) : the_row(); ?>
					<?php
					$brand_logo = get_sub_field( 'brand_logo' ) ?? '';
					$brand_name = get_sub_field( 'brand_name' );
					$brand_link = get_sub_field( 'link' );

					if ( ! $brand_logo || ! $brand_link ) {
						continue;
					}
					?>
					<div class="col-md-6 col-xl-4 col-xxl-3" data-aos="fade">
						<?php if ( '#' !== $brand_link['url'] && '' !== $brand_link['url'] ) : ?>
							<a href="<?= esc_url( $brand_link['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="brand-card">
								<div class="brand-card__front">
									<?= wp_get_attachment_image( $brand_logo, 'full', false, array( 'class' => 'brand-card__logo', 'alt' => esc_attr( $brand_name ) ) ); ?>
								</div>
								<div class="brand-card__back">
									<div class="brand-card__name"><?= esc_html( $brand_name ); ?></div>
									<div class="brand-card__strap">
										<?= esc_html( $brand_link['title'] ); ?>
										<span class="brand-card__arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'brand-card__arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									</div>
								</div>
							</a>
						<?php else : ?>
							<div class="brand-card">
								<div class="brand-card__front">
									<?= wp_get_attachment_image( $brand_logo, 'full', false, array( 'class' => 'brand-card__logo', 'alt' => esc_attr( $brand_name ) ) ); ?>
								</div>
								<div class="brand-card__back">
									<div class="brand-card__name"><?= esc_html( $brand_name ); ?></div>
									<div class="brand-card__strap"><?= esc_html( $brand_link['title'] ); ?></div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>

			<div class="col-md-<?= esc_attr( $last_col_md ); ?> col-xl-<?= esc_attr( $last_col_xl ); ?> col-xxl-<?= esc_attr( $last_col_xxl ); ?>">
				<div class="brand-card__last">
					Together, we deliver a breadth of expertise with the simplicity of one team.
				</div>
			</div>
		</div>
	</div>
</section>
