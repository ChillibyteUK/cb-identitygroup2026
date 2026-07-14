<?php
/**
 * Block template for CB Services Nav.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$is_identity = 'identity' === cb_site_template_suffix();

$c = ( ! $is_identity && is_front_page() ) ? 'cb-services-nav--front' : '';

$services = get_page_by_path( 'services' );

if ( ! $is_identity ) {
	$current_page_parent = wp_get_post_parent_id( get_the_ID() );
	$t                    = ( $services && $current_page_parent === $services->ID ) ? 'OTHER SERVICES' : 'SERVICES';
}

?>
<div id="<?php echo esc_attr( $block_id ); ?>" class="cb-services-nav <?= esc_attr( $c ); ?>">
	<?php if ( $is_identity ) : ?>
	<div class="cb-services-nav__container pb-5">
		<h2 class="cb-services-nav__header mb-0">
			<div class="id-container px-4 px-md-5">
				SERVICES
			</div>
		</h2>
	<?php else : ?>
    <div class="cb-services-nav__container">
		<div class="cb-services-nav__header">
			<div class="id-container px-4 px-md-5 pt-1 pb-0">
				<?= esc_html( $t ); ?>
			</div>
		</div>
	<?php endif; ?>
        <?php
		if ( $services ) {
			// get child pages.
			$child_pages = get_pages(
				array(
					'child_of'    => $services->ID,
					'sort_column' => 'menu_order',
					'sort_order'  => 'ASC',
				)
			);
			if ( $child_pages ) {
				foreach ( $child_pages as $service_page ) {
					if ( get_the_ID() === $service_page->ID ) {
						continue;
					}
					$service_title = get_the_title( $service_page->ID );
					if ( ! $is_identity ) {
						$service_title = ucfirst( strtolower( $service_title ) );
					}
					?>
		<a href="<?php echo esc_url( get_permalink( $service_page->ID ) ); ?>" class="cb-services-nav__item" tabindex="0">
			<div class="id-container px-4 px-md-5 d-flex justify-content-between<?= $is_identity ? '' : ' align-items-center'; ?>" data-aos="fade-up" data-aos-delay="<?= esc_attr( 50 * ( array_search( $service_page, $child_pages, true ) + 1 ) ); ?>">
				<?php if ( $is_identity ) : ?>
				<h3 class="cb-services-nav__item-title"><?php echo esc_html( $service_title ); ?></h3>
				<?php else : ?>
				<div class="cb-services-nav__item-title"><?php echo esc_html( $service_title ); ?></div>
				<?php endif; ?>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" alt="" class="cb-services-nav__item-icon" />
			</div>
		</a>
					<?php
				}
			}
		}
		?>
		<?php if ( ! $is_identity ) : ?>
		<div class="cb-services-nav__header pb-4">&nbsp;</div>
		<?php endif; ?>
    </div>
</div>
