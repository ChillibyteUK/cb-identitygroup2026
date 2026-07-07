<?php
/**
 * Block template for CB About Page Header.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$bg = get_query_var( 'background', get_field( 'background' ) );

?>
<style>
.cb-about-page-header {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( $bg, 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-about-page-header">
    <div class="cb-about-page-header__top">
        <div class="intro-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <h1><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
            <div class="row">
                <div class="col-md-8">
                    <div class="cb-about-page-header__intro-text fs-500 fw-light"><?= wp_kses_post( get_field( 'intro_text' ) ); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
