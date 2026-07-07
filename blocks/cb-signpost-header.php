<?php
/**
 * Block template for CB Signpost Header.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id = $block['anchor'] ?? null;
$extra      = $block['className'] ?? '';
$line_class = 'dark-lines';

if ( ! empty( $block['backgroundColor'] ) ) {
	if ( preg_match( '/(\d+)(?!.*\d)/', $block['backgroundColor'], $matches ) ) {
		$line_class = (int) $matches[1] >= 600 ? 'light-lines' : 'dark-lines';
	} else {
		$line_class = 'light-lines';
	}
}

$block_title = get_field( 'title' ) ?: 'Signpost Subheader';

?>
<section class="cb-signpost-header <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $line_class . ' ' . $extra ) ); ?>" id="<?= esc_attr( $section_id ); ?>">
	<div class="id-container px-4 px-md-5">
		<?= esc_html( $block_title ); ?>
	</div>
</section>
