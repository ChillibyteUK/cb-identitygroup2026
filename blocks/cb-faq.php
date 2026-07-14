<?php
/**
 * Block template for CB FAQ.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cb_faq_add_schema_items' ) ) {
	/**
	 * Collect FAQ items and output a single FAQPage schema in wp_footer.
	 *
	 * @param array $items Array of items with 'question' and 'answer' keys.
	 * @return void
	 */
	function cb_faq_add_schema_items( array $items ) {
		static $all_items = array();
		static $hooked    = false;

		foreach ( $items as $item ) {
			$all_items[] = $item;
		}

		if ( ! $hooked ) {
			$hooked = true;
			add_action(
				'wp_footer',
				function () use ( &$all_items ) {
					if ( empty( $all_items ) ) {
						return;
					}

					$entities = array_map(
						function ( $item ) {
							return array(
								'@type'          => 'Question',
								'name'           => $item['question'],
								'acceptedAnswer' => array(
									'@type' => 'Answer',
									'text'  => $item['answer'],
								),
							);
						},
						$all_items
					);

					$schema = array(
						'@context'   => 'https://schema.org',
						'@type'      => 'FAQPage',
						'mainEntity' => $entities,
					);

					echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
				}
			);
		}
	}
}

$faqs = get_field( 'faqs' );

if ( empty( $faqs ) || ! is_array( $faqs ) ) {
	return;
}

$block_faq_items = array();

foreach ( $faqs as $faq ) {
	$question = isset( $faq['question'] ) ? wp_strip_all_tags( $faq['question'] ) : '';
	$answer   = isset( $faq['answer'] ) ? wp_strip_all_tags( $faq['answer'] ) : '';

	if ( '' === $question || '' === $answer ) {
		continue;
	}

	$block_faq_items[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}

cb_faq_add_schema_items( $block_faq_items );

$block_id   = $block['anchor'] ?? ( $block['id'] ?? wp_unique_id( 'cb-faq-' ) );
$extra      = $block['className'] ?? '';
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$is_post_context = is_singular( 'post' );
$is_identity      = 'identity' === cb_site_template_suffix();

// identity's own real cb-faq is a fixed dark theme with no Gutenberg
// backgroundColor-driven light/dark-lines toggle at all (see the
// .cb-site-identity .cb-faq override in _header-site-overrides.scss) -
// coda's and idtravel's real sources both have the toggle.
$line_class = '';
if ( ! $is_identity ) {
	$line_class = 'dark-lines';

	if ( ! empty( $block['backgroundColor'] ) ) {
		if ( preg_match( '/(\d+)(?!.*\d)/', $block['backgroundColor'], $matches ) ) {
			$line_class = (int) $matches[1] >= 600 ? 'light-lines' : 'dark-lines';
		} else {
			$line_class = 'light-lines';
		}
	}
}

?>
<?php if ( $is_post_context ) : ?>
	<?php if ( $is_identity ) : ?><div class="container"><?php endif; ?>
	<?php foreach ( $faqs as $faq ) : ?>
		<?php
		$question = $faq['question'] ?? '';
		$answer   = $faq['answer'] ?? '';

		if ( '' === trim( wp_strip_all_tags( $question ) ) && '' === trim( wp_strip_all_tags( $answer ) ) ) {
			continue;
		}
		?>
		<?php if ( '' !== trim( $question ) ) : ?>
	<h2><?= esc_html( $question ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== trim( wp_strip_all_tags( $answer ) ) ) : ?>
	<?= wpautop( wp_kses_post( $answer ) ); ?>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if ( $is_identity ) : ?></div><?php endif; ?>
<?php else : ?>
<section class="cb-faq <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $line_class . ' ' . $extra ) ); ?>" id="<?= esc_attr( $block_id ); ?>">
	<div class="id-container px-4 px-md-5">
		<?php foreach ( $faqs as $index => $faq ) : ?>
			<?php
			$question = $faq['question'] ?? '';
			$answer   = $faq['answer'] ?? '';

			if ( '' === trim( wp_strip_all_tags( $question ) ) && '' === trim( wp_strip_all_tags( $answer ) ) ) {
				continue;
			}
			?>
			<div class="cb-faq__item row" data-aos="fade-up" data-aos-delay="<?= esc_attr( $index * 100 ); ?>">
				<div class="<?= $is_identity ? 'col-md-6' : 'col-lg-6'; ?>">
					<?php if ( '' !== trim( $question ) ) : ?>
						<p class="cb-faq__question"><?= esc_html( $question ); ?></p>
					<?php endif; ?>
				</div>
				<div class="<?= $is_identity ? 'col-md-1' : 'col-lg-1'; ?>"></div>
				<div class="<?= $is_identity ? 'col-md-5' : 'col-lg-5'; ?>">
					<?php if ( '' !== trim( wp_strip_all_tags( $answer ) ) ) : ?>
						<div class="cb-faq__answer">
							<?= wp_kses_post( $answer ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>
