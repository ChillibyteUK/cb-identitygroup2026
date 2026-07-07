<?php
/**
 * Block template for CB Full Image.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

// Output.
?>
<div id="<?php echo esc_attr( $block_id ); ?>" class="cb-full-image">
	<div class="id-container overflow-hidden position-relative">
		<?php
		if ( get_field( 'top_border' ) ) {
			?>
			<div class="overlay"></div>
			<?php
		}
		?>
		<?=
		wp_get_attachment_image(
			get_field( 'image' ),
			'full',
			false,
			array(
				'class' => 'img-fluid',
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
