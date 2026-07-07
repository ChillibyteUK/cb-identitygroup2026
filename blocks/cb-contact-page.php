<?php
/**
 * Block template for CB Contact Page.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$l = get_field( 'contact_link' );

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-contact-page">
	<div class="cb-contact-page__title">
		<h1 class="id-container px-4 px-md-5">
			Contact
		</h1>
	</div>
    <div class="id-container px-4 px-md-5">
        <div class="row cb-contact-page__intro-content">
            <div class="col-md-6">
                <div class="pt-5 pb-4 cb-contact-page__intro-text">
                    <?= wp_kses_post( get_field( 'contact_intro' ) ); ?>
                </div>
                <div class="cb-cta__button">
                    <a href="<?= esc_url( $l['url'] ); ?>" class="id-button">
                        <?= esc_html( $l['title'] ); ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="cb-contact-page__emails">
            <div class="row align-items-start">
                <div class="col-md-6 mb-3">
                    <h2>New business</h2>
                </div>
                <div class="col-md-6 mb-5 mb-md-3">
                    <a href="mailto:<?= esc_attr( antispambot( get_field( 'new_business' ) ) ); ?>">
                        <?= esc_html( antispambot( get_field( 'new_business' ) ) ); ?>
                    </a>
                    <?php
                    if ( get_field( 'new_business_phone' ) ) {
                        ?>
                        <div class="mt-2">
                            <a href="tel:<?= esc_attr( parse_phone( get_field( 'new_business_phone' ) ) ); ?>">
                                <?= esc_html( get_field( 'new_business_phone' ) ); ?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="cb-contact-page__emails">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <h2>PR & Media</h2>
                </div>
                <div class="col-md-6 mb-5 mb-md-3">
                    <a href="mailto:<?= esc_attr( antispambot( get_field( 'pr_media' ) ) ); ?>">
                        <?= esc_html( antispambot( get_field( 'pr_media' ) ) ); ?>
                    </a>
                    <?php
                    if ( get_field( 'pr_media_phone' ) ) {
                        ?>
                        <div class="mt-2">
                            <a href="tel:<?= esc_attr( parse_phone( get_field( 'pr_media_phone' ) ) ); ?>">
                                <?= esc_html( get_field( 'pr_media_phone' ) ); ?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="cb-contact-page__emails">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <h2>Talent</h2>
                </div>
                <div class="col-md-6 mb-5 mb-md-3">
                    <a href="mailto:<?= esc_attr( antispambot( get_field( 'talent' ) ) ); ?>">
                        <?= esc_html( antispambot( get_field( 'talent' ) ) ); ?>
                    </a>
                    <?php
                    if ( get_field( 'talent_phone' ) ) {
                        ?>
                        <div class="mt-2">
                            <a href="tel:<?= esc_attr( parse_phone( get_field( 'talent_phone' ) ) ); ?>">
                                <?= esc_html( get_field( 'talent_phone' ) ); ?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<a id="locations" class="anchor"></a>
<section class="cb-contact-addresses__title">
    <div class="id-container px-4 px-md-5 py-4">
        LOCATIONS
    </div>
</section>
<section class="cb-contact-addresses">
    <div class="id-container px-4 px-md-5">
        <?php
        while ( have_rows( 'addresses' ) ) {
            the_row();
            ?>
        <div class="row mx-0 g-2 cb-contact-addresses__office mb-5">
            <div class="col-lg-6 px-0">
                <h2><?= esc_html( get_sub_field( 'office' ) ); ?></h2>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <?php
                    if ( have_rows( 'sub_addresses' ) ) {
                        while ( have_rows( 'sub_addresses' ) ) {
                            the_row();
							$group = get_sub_field( 'is_group' ) ? 'is-group' : '';
                            ?>
                    <div class="col-md-6 mb-4 <?= esc_attr( $group ); ?>">
							<?php
							if ( get_sub_field( 'office_title' ) ) {
								?>
                        <div class="mb-2"><strong><?= esc_html( get_sub_field( 'office_title' ) ); ?></strong></div>
								<?php
							}
							?>
                        <div class="mb-2"><?= wp_kses_post( get_sub_field( 'address' ) ); ?></div>
							<?php
							if ( get_sub_field( 'phone' ) ) {
								?>
                        <div class="mb-2"><a href="tel:<?= esc_attr( parse_phone( get_sub_field( 'phone' ) ) ); ?>"><?= esc_html( get_sub_field( 'phone' ) ); ?></a></div>
								<?php
							}
							?>
                    </div>
                        	<?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
            <?php
        }
        ?>
		<div class="row mx-0">
			<div class="col-lg-6 offset-lg-6 px-0">
				<div class="cb-contact-addresses__caveat">Group locations</div>
			</div>
		</div>
    </div>
</section>
