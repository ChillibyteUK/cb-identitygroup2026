<?php
/**
 * CB Policies Page Block Template
 *
 * @package  cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

?>
<style>
.cb-policies-page {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( get_field( 'secondary_background' ), 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-policies-page">
	<div class="id-container px-4 px-md-5">
		<div class="cb-policies-page__inner">
			<h1 class="cb-policies-page__title pt-1">
				<?= esc_html( get_field( 'title' ) ); ?>
			</h1>
			<div class="row">
				<div class="col-md-9 cb-policies-page__content">
					<?= wp_kses_post( get_field( 'intro_content' ) ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="cb-policies-page__secondary">
		<div class="overlay"></div>
		<div class="id-container">
			<div class="row py-5 px-4 px-md-5">
				<div class="col-md-9 cb-policies-page__secondary-content">
					<?= wp_kses_post( get_field( 'secondary_content' ) ); ?>
				</div>
			</div>
		</div>
	</div>
	<a id="policies" class="anchor"></a>
	<div class="cb-policies-page__pretitle">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			POLICIES
		</div>
	</div>
	<div class="id-container cb-policies-page__list px-4 px-md-5">
		<?php
		$c = 0;
		while ( have_rows( 'policies' ) ) {
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
		<a href="<?= esc_url( $href ); ?>" class="cb-policies-page__item" tabindex="0" target="<?= esc_attr( $target ); ?>">
			<div class="id-container px-4 px-md-5 d-flex justify-content-between" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="cb-policies-page__item-title"><?= esc_html( get_sub_field( 'title' ) ); ?></div>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=65 height=60 alt="" class="cb-policies-page__item-icon" />
			</div>
		</a>
			<?php
			$c += 100;
		}
		?>
	</div>
	<a id="accreditations" class="anchor"></a>
	<div class="cb-policies-page__pretitle">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			ACCREDITATIONS
		</div>
	</div>

	<div class="id-container cb-policies-page__list px-4 px-md-5">
		<?php
		$c = 0;
		while ( have_rows( 'accreditations' ) ) {
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
		<a href="<?= esc_url( $href ); ?>" class="cb-policies-page__item" tabindex="0" target="<?= esc_attr( $target ); ?>">
			<div class="id-container px-4 px-md-5 d-flex justify-content-between" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="cb-policies-page__item-title"><?= esc_html( get_sub_field( 'title' ) ); ?></div>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width=65 height=60 alt="" class="cb-policies-page__item-icon" />
			</div>
		</a>
			<?php
			$c += 100;
		}
		?>
	</div>
</section>
