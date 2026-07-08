<?php
/**
 * Block template for CB Lined Title.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

cb_deprecated_block_notice( 'CB Signpost Header', $is_preview );

$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id = $block['anchor'] ?? $block['id'];

?>
<a id="<?= esc_attr( $section_id ); ?>" class="anchor"></a>
<section class="cb-lined-title <?php echo esc_attr( $bg . ' ' . $fg ); ?>">
	<div class="id-container px-4 px-md-5">
		<h2><?= wp_kses_post( get_field( 'title' ) ); ?></h2>
	</div>
</section>
