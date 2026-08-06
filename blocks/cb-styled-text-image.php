<?php
/**
 * CB Styled Text Image Block Template
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? ( $block['id'] ?? wp_unique_id( 'cb-styled-text-image-' ) );

?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-styled-text-image">
	<div class="id-container ps-5">
		<div class="row">
			<div class="col-md-6 py-5 cb-styled-text-image__text-content">
				<?= wp_kses_post( get_field( 'text_content' ) ); ?>
			</div>
			<div class="col-md-6 d-none d-md-block">
				<div class="cb-styled-text-image__image-wrapper">
					<?=
					wp_get_attachment_image(
						get_field( 'image' ),
						'full',
						false,
						array(
							'class' => 'cb-styled-text-image__image',
							'alt'   => get_post_meta(
								get_field( 'image' ),
								'_wp_attachment_image_alt',
								true
							),
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>
</section>
