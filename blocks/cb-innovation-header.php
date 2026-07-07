<?php
/**
 * CB Innovation Header Block Template
 *
 * @package  cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

$bg = get_query_var( 'background', get_field( 'background' ) );

$quote   = get_field( 'quote' );
$author  = get_field( 'author' );
$company = get_field( 'company' );
?>
<style>
.cb-innovation-header {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( $bg, 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-innovation-header">
    <div class="cb-innovation-header__top">
        <div class="intro-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <h1><?= esc_html( get_field( 'title' ) ); ?></h1>
            <div class="row">
                <div class="col-md-9">
                    <p class="cb-innovation-header__intro-text"><?= wp_kses_post( get_field( 'intro_text' ) ); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="cb-innovation-header__quote-wrapper">
        <div class="quote-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <div class="cb-innovation-header__quote">
                “<?= wp_kses_post( $quote ); ?>”
            </div>
            <div class="cb-innovation-header__author">
                <?= esc_html( $author ); ?>
            </div>
			<?php
			if ( $company ) {
				?>
			<br>
            <div class="cb-innovation-header__company">
                <?= esc_html( $company ); ?>
            </div>
				<?php
			}
			?>
        </div>
    </div>
</section>
