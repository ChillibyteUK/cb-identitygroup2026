<?php
/**
 * CB Culture Page Header Block Template
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

?>
<style>
.cb-culture-page-header {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( $bg, 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-culture-page-header">
    <div class="cb-culture-page-header__top">
        <div class="intro-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <h1><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
            <div class="row">
                <div class="col-md-9">
                    <div class="cb-culture-page-header__intro-text"><?= wp_kses_post( get_field( 'intro_text' ) ); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="cb-culture-page-header__content-wrapper">
        <div class="culture-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <div class="row">
                <div class="col-md-9">
                    <div class="cb-culture-page-header__intro">
                        <?= wp_kses_post( get_field( 'secondary_text' ) ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="cb-culture-page-header__careers">
		<div class="careers-overlay"></div>
		<div class="cb-culture-page-header__careers-pretitle">
			<div class="id-container px-4 px-md-5 pt-2 pb-1">
				JOIN US
			</div>
		</div>
		<div class="id-container px-4 px-md-5 py-5">
			<div class="row g-5">
				<div class="col-md-6">
					<h2>Careers</h2>
				</div>
				<div class="col-md-6">
					<div class="careers-content">
						<?= wp_kses_post( get_field( 'careers_content' ) ); ?>
					</div>
					<?php
					$careers_link = get_field( 'careers_link' );
					if ( $careers_link ) {
						?>
						<a href="<?= esc_url( $careers_link['url'] ); ?>" target="<?= esc_attr( $careers_link['target'] ); ?>" class="careers-link">
							<?= esc_html( $careers_link['title'] ); ?>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=33 height=26 alt="" />
						</a>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="culture-life">
	<div class="culture-life__pretitle">
		<div class="id-container px-4 px-md-5 pt-2 pb-1">
			LIFE AT IDENTITY
		</div>
	</div>
	<div class="id-container px-4 px-md-5">
		<?php
		if ( have_rows( 'life' ) ) {
			$c = 0;
			while ( have_rows( 'life' ) ) {
				the_row();
				$item_title  = get_sub_field( 'title' );
				$description = get_sub_field( 'content' );
				?>
			<div class="row life-detail-row pb-5" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="col-md-6">
					<h2 class="life-detail-title"><?= wp_kses_post( $item_title ); ?></h2>
				</div>
				<div class="col-md-6">
					<div class="life-detail-description">
						<?= wp_kses_post( $description ); ?>
					</div>
				</div>
			</div>
				<?php
				$c += 100;
			}
		}
		?>
	</div>
</section>