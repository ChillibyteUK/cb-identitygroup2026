<?php
/**
 * Block template for CB Case Study Key Stats.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$pre_title = get_field( 'pre_title' ) ? get_field( 'pre_title' ) : 'Key Stats';

$bg = get_field( 'background' ) ? wp_get_attachment_image_url( get_field( 'background' ), 'full' ) : get_stylesheet_directory_uri() . '/img/contact-addresses-bg.jpg';
?>
<style>
#<?= esc_attr( $block_id ); ?> {
    --_bg-url: url('<?= esc_url( $bg ); ?>');
}
</style>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-case-study-key-stats">
    <div class="cb-case-study-key-stats__container pb-5">
		<div class="cb-case-study-key-stats__header">
			<div class="id-container pt-1 px-4 px-md-5">
				<?= esc_html( $pre_title ); ?>
			</div>
		</div>
        <div class="cb-case-study-key-stats__grid-wrapper">
        <?php
        if ( have_rows( 'stats' ) ) {
            $delay = 0;
            while ( have_rows( 'stats' ) ) {
                the_row();
                ?>
        <div class="cb-case-study-key-stats__item" data-aos="fade-up" data-aos-delay="<?= esc_attr( $delay ); ?>">
            <div class="id-container px-4 px-md-5" style="display: contents;">
                <div class="cb-case-study-key-stats__stat"><?= esc_html( get_sub_field( 'stat' ) ); ?></div>
                <div class="cb-case-study-key-stats__descriptor"><?= esc_html( get_sub_field( 'descriptor' ) ); ?></div>
            </div>
        </div>
                <?php
                $delay += 50;
            }
        }
        ?>
        </div>
    </div>
</section>
