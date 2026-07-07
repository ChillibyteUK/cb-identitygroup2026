<?php
/**
 * CB File Block Block Template
 *
 * @package  cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

$section_title = get_field( 'section_title' );

// convert title to slug
$section_id = str_replace( ' ', '-', strtolower( $section_title ) );

?>
<a id="<?= esc_attr( $section_id ); ?>" class="anchor"></a>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-file-block">
	<div class="cb-file-block__pretitle">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			<?= esc_html( strtoupper( $section_title ) ); ?>
		</div>
	</div>

	<div class="id-container cb-file-block__list px-4 px-md-5">
		<?php
		$c = 0;
		while ( have_rows( 'files' ) ) {
			the_row();

			if ( get_sub_field( 'page_link' ) ) {
				$href = get_sub_field( 'page_link' );
				$href = $href['url'];
			} elseif ( get_sub_field( 'file' ) ) {
				$href = get_sub_field( 'file' );
			} else {
				$href = '#';
			}

			$target = get_sub_field( 'page_link' ) ? '_self' : '_blank';
			?>
		<a href="<?= esc_url( $href ); ?>" class="cb-file-block__item" tabindex="0" target="<?= esc_attr( $target ); ?>">
			<div class="id-container px-4 px-md-5 d-flex justify-content-between" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="cb-file-block__item-title"><?= esc_html( get_sub_field( 'title' ) ); ?></div>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=65 height=60 alt="" class="cb-file-block__item-icon" />
			</div>
		</a>
			<?php
			$c += 100;
		}
		?>
	</div>
</section>
