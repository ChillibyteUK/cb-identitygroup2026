<?php
/**
 * Block template for CB Contact Page.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$l           = get_field( 'contact_link' );
$is_identity = 'identity' === cb_site_template_suffix();

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-contact-page">
	<?php if ( ! $is_identity ) : ?>
	<div class="cb-contact-page__title">
		<h1>
			<div class="id-container px-4 px-md-5">
				Contact
			</div>
		</h1>
	</div>
	<?php endif; ?>
	<div class="id-container px-4 px-md-5">
		<?php if ( $is_identity ) : ?>
		<h1>Contact Us</h1>
		<?php endif; ?>
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
		<?php
		// Consecutive rows are grouped under one shared wrapper/divider
		// unless a row has new_section checked, which starts a fresh one -
		// e.g. New Business/US/ME (identity's real content) render as one
		// section, PR & Media and Talent each start their own (2026-08-06,
		// replacing 10 fixed fields - see SESSION-HANDOFF.md).
		$in_section = false;
		while ( have_rows( 'email_departments' ) ) :
			the_row();
			$department  = get_sub_field( 'department' );
			$email       = get_sub_field( 'email' );
			$phone       = get_sub_field( 'phone' );
			$new_section = get_sub_field( 'new_section' );

			if ( ! $in_section || $new_section ) {
				if ( $in_section ) {
					?>
			</div>
		</div>
					<?php
				}
				?>
		<div class="cb-contact-page__emails">
			<div class="row align-items-start">
				<?php
				$in_section = true;
			}
			?>
				<div class="col-md-6 mb-3">
					<h2><?= esc_html( $department ); ?></h2>
				</div>
				<div class="col-md-6 mb-5 mb-md-3">
					<?php if ( $email ) : ?>
					<a href="mailto:<?= esc_attr( antispambot( $email ) ); ?>">
						<?= esc_html( antispambot( $email ) ); ?>
					</a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
					<div class="mt-2">
						<a href="tel:<?= esc_attr( parse_phone( $phone ) ); ?>">
							<?= esc_html( $phone ); ?>
						</a>
					</div>
					<?php endif; ?>
				</div>
			<?php
		endwhile;
		if ( $in_section ) {
			?>
			</div>
		</div>
			<?php
		}
		?>
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
		$has_group_location = false;
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
							if ( $group ) {
								$has_group_location = true;
							}
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
		<?php if ( $has_group_location ) : ?>
		<div class="row mx-0">
			<div class="col-lg-6 offset-lg-6 px-0">
				<div class="cb-contact-addresses__caveat">Group locations</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>
