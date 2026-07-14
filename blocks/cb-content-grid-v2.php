<?php
/**
 * CB Content Grid V2 Block Template
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id           = $block['anchor'] ?? ( $block['id'] ?? wp_unique_id( 'cb-content-grid-v2-' ) );
$rows               = get_field( 'rows' );
$background_image   = get_field( 'background_image' );
$section_style_attr = '';

if ( empty( $rows ) ) {
	return;
}

if ( $background_image ) {
	$background_image_url = wp_get_attachment_image_url( $background_image, 'full' );

	if ( $background_image_url ) {
		$section_style_attr = sprintf( '--cb-content-grid-v2-bg: url(%s);', esc_url_raw( $background_image_url ) );
	}
}

$bg_class   = '';
$line_class = 'dark-lines';
if ( isset( $block['backgroundColor'] ) && $block['backgroundColor'] ) {
	$bg_color = $block['backgroundColor'];
	$bg_class = 'has-' . esc_attr( $bg_color ) . '-background-color';

	if ( preg_match( '/(\d+)(?!.*\d)/', $bg_color, $matches ) ) {
		$line_class = (int) $matches[1] >= 600 ? 'light-lines' : 'dark-lines';
	} else {
		$line_class = 'light-lines';
	}
}

$text_class = '';
if ( isset( $block['textColor'] ) && $block['textColor'] ) {
	$text_class = 'has-' . esc_attr( $block['textColor'] ) . '-color';
}

$extra_class = trim( (string) ( $block['className'] ?? '' ) );

$section_classes = array( 'cb-content-grid-v2', $line_class );
if ( $bg_class ) {
	$section_classes[] = $bg_class;
}
if ( $text_class ) {
	$section_classes[] = $text_class;
}
if ( $extra_class ) {
	$section_classes[] = $extra_class;
}
if ( $section_style_attr ) {
	$section_classes[] = 'cb-content-grid-v2--has-background-image';
}

$arrow_icon = sprintf(
	'<img src="%1$s" width="38" height="29" alt="" loading="lazy">',
	esc_url( get_stylesheet_directory_uri() . '/img/arrow-n600-solid.svg' )
);
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style_attr ? ' style="' . esc_attr( $section_style_attr ) . '"' : ''; ?>>
	<div class="id-container py-5 px-4 px-md-5">
		<?php foreach ( $rows as $row_index => $row ) : ?>
			<?php
			$column_layout = $row['column_layout'] ?? '12';
			$modules       = $row['modules'] ?? array();
			$has_h2_module = false;

			foreach ( $modules as $module ) {
				if ( 'h2' === ( $module['module_type'] ?? '' ) && ! empty( $module['h2_text'] ) ) {
					$has_h2_module = true;
					break;
				}
			}

			$row_classes = array( 'cb-content-grid-v2__row', 'pt-4', 'pb-5' );
			if ( $has_h2_module ) {
				$row_classes[] = 'cb-content-grid-v2__row--has-h2';
			}
			if ( ! empty( $row['has_line'] ) && is_array( $row['has_line'] ) && in_array( 'Yes', $row['has_line'], true ) ) {
				$row_classes[] = 'cb-content-grid-v2__row--has-line';
			}

			$grid_classes = array( 'row', 'g-5' );
			switch ( $column_layout ) {
				case '12':
					$grid_classes[] = 'cb-content-grid-v2__row--full';
					break;
				case '6-6':
					$grid_classes[] = 'cb-content-grid-v2__row--two-col';
					break;
				case '4-8':
					$grid_classes[] = 'cb-content-grid-v2__row--third-twothirds';
					break;
				case '8-4':
					$grid_classes[] = 'cb-content-grid-v2__row--twothirds-third';
					break;
				case '4-4-4':
					$grid_classes[] = 'cb-content-grid-v2__row--three-col';
					break;
				case '3-3-3-3':
					$grid_classes[] = 'cb-content-grid-v2__row--four-col';
					break;
			}
			?>
			<div class="<?= esc_attr( implode( ' ', $row_classes ) ); ?>" data-row-index="<?= esc_attr( $row_index ); ?>">
				<div class="<?= esc_attr( implode( ' ', $grid_classes ) ); ?>" data-aos-stagger-group>
					<?php foreach ( $modules as $module_index => $module ) : ?>
						<?php
						$module_type = $module['module_type'] ?? 'empty';
						$col_classes = array( 'cb-content-grid-v2__module', 'cb-content-grid-v2__module--' . esc_attr( $module_type ) );

						switch ( $column_layout ) {
							case '12':
								$col_classes[] = 'col-md-12';
								break;
							case '6-6':
								$col_classes[] = 'col-md-6';
								break;
							case '4-8':
								$col_classes[] = 0 === $module_index ? 'col-md-4' : 'col-md-8';
								break;
							case '8-4':
								$col_classes[] = 0 === $module_index ? 'col-md-8' : 'col-md-4';
								break;
							case '4-4-4':
								$col_classes[] = 'col-md-4';
								break;
							case '3-3-3-3':
								$col_classes[] = 'col-md-3';
								break;
						}
						?>
						<div class="<?= esc_attr( implode( ' ', $col_classes ) ); ?>" data-module-index="<?= esc_attr( $module_index ); ?>" data-aos="fade">
							<?php switch ( $module_type ) {
								case 'h2':
									$h2_text = $module['h2_text'] ?? '';
									if ( $h2_text ) {
										?>
										<h2 class="cb-content-grid-v2__h2"><?= wp_kses_post( $h2_text ); ?></h2>
										<?php
									}
									break;

								case 'h3':
									$h3_text = $module['h3_text'] ?? '';
									if ( $h3_text ) {
										?>
										<h3 class="cb-content-grid-v2__h3"><?= wp_kses_post( $h3_text ); ?></h3>
										<?php
									}
									break;

								case 'text':
									$text_content = $module['text_content'] ?? '';
									$text_fs      = $module['text_font_size'] ?? 'fs-100';
									$text_fw      = $module['text_font_weight'] ?? 'fw-regular';
									?>
									<div class="cb-content-grid-v2__text <?= esc_attr( $text_fs ); ?> <?= esc_attr( $text_fw ); ?>">
										<?= wp_kses_post( $text_content ); ?>
									</div>
									<?php
									break;

								case 'list':
									$list_content = $module['list_content'] ?? '';
									$list_fs      = $module['list_font_size'] ?? 'fs-100';
									$list_fw      = $module['list_font_weight'] ?? 'fw-regular';
									if ( $list_content ) {
										?>
										<ul class="cb-content-grid-v2__list <?= esc_attr( $list_fs ); ?> <?= esc_attr( $list_fw ); ?>">
											<?= cb_list( $list_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</ul>
										<?php
									}
									break;

								case 'quote':
									$quote_text = $module['quote_text'] ?? '';
									$quote_link = is_array( $module['quote_link'] ?? null ) ? $module['quote_link'] : array();

									if ( $quote_text ) {
										?>
										<div class="cb-content-grid-v2__quote-wrap">
											<blockquote class="cb-content-grid-v2__quote mb-4"><?= wp_kses_post( $quote_text ); ?></blockquote>
											<?php if ( ! empty( $quote_link['url'] ) && ! empty( $quote_link['title'] ) ) : ?>
												<a class="id-button id-button--sm mt-4" href="<?= esc_url( $quote_link['url'] ); ?>" target="<?= esc_attr( $quote_link['target'] ?: '_self' ); ?>">
													<?= esc_html( $quote_link['title'] ); ?>
												</a>
											<?php endif; ?>
										</div>
										<?php
									}
									break;

								case 'links':
									$links_rows = $module['links_rows'] ?? array();
									if ( ! empty( $links_rows ) && is_array( $links_rows ) ) {
										?>
										<div class="cb-content-grid-v2__links">
											<?php foreach ( $links_rows as $links_row ) : ?>
												<?php
												$link_title = $links_row['title'] ?? '';
												$file_url   = $links_row['file'] ?? '';
												if ( ! $file_url ) {
													continue;
												}
												?>
												<a class="cb-content-grid-v2__links-link" href="<?= esc_url( $file_url ); ?>" target="_blank" rel="noopener noreferrer">
													<span><?= esc_html( $link_title ? $link_title : wp_basename( $file_url ) ); ?></span>
													<span class="cb-content-grid-v2__links-arrow"><?= wp_kses_post( $arrow_icon ); ?></span>
												</a>
											<?php endforeach; ?>
										</div>
										<?php
									}
									break;

								case 'logo_grid':
									$logo_grid_rows = $module['logo_grid_rows'] ?? array();
									if ( ! empty( $logo_grid_rows ) && is_array( $logo_grid_rows ) ) {
										?>
										<div class="cb-content-grid-v2__logo-grid">
											<?php foreach ( $logo_grid_rows as $logo_grid_row ) : ?>
												<?php
												$row_title = $logo_grid_row['title'] ?? '';
												$row_logos = $logo_grid_row['logo'] ?? array();
												if ( empty( $row_logos ) || ! is_array( $row_logos ) ) {
													continue;
												}
												?>
												<div class="cb-content-grid-v2__logo-grid-row row g-4 align-items-start">
													<div class="col-lg-3">
														<?php if ( $row_title ) : ?>
															<h3 class="cb-content-grid-v2__logo-grid-title"><?= esc_html( $row_title ); ?></h3>
														<?php endif; ?>
													</div>
													<div class="col-lg-9">
														<div class="cb-content-grid-v2__logo-grid-gallery">
															<?php foreach ( $row_logos as $logo_id ) : ?>
																<div class="cb-content-grid-v2__logo-grid-item">
																	<?= wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'cb-content-grid-v2__logo-grid-image', 'alt' => get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ) ); ?>
																</div>
															<?php endforeach; ?>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
										<?php
									}
									break;

								case 'image':
									$image_id     = $module['image'] ?? null;
									$aspect       = $module['image_aspect_ratio'] ?? '16x9';
									$image_mode   = $module['image_size'] ?? '';
									$image_size   = 'contain' === $image_mode ? 'cb-content-grid-v2__image-contain' : '';
									$native_class = ( 'native' === $aspect && 'contain' === $image_mode ) ? 'cb-content-grid-v2__image-native' : '';
									$aspect_class = '';
									$wrap_style   = '';

									switch ( $aspect ) {
										case 'native':
											break;
										case '21x9':
											$aspect_class = 'ratio ratio-21x9';
											break;
										case '16x9':
											$aspect_class = 'ratio ratio-16x9';
											break;
										case '4x3':
											$aspect_class = 'ratio ratio-4x3';
											break;
										case '1x1':
											$aspect_class = 'ratio ratio-1x1';
											break;
									}

									if ( $image_id ) {
										if ( $native_class ) {
											$image_src = wp_get_attachment_image_src( $image_id, 'full' );
											if ( ! empty( $image_src[1] ) ) {
												$wrap_style = sprintf( 'style="width:min(100%%, %dpx);"', (int) $image_src[1] );
											}
										}
										?>
										<div class="cb-content-grid-v2__image-wrap <?= esc_attr( trim( $aspect_class . ' ' . $image_size . ' ' . $native_class ) ); ?>" <?= $wrap_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
											<?= wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'img-fluid cb-content-grid-v2__image' ) ); ?>
										</div>
										<?php
									}
									break;

								case 'video':
									$video_url = $module['video_url'] ?? '';
									if ( $video_url ) {
										$video_url = cb_vimeo_url_with_dnt( $video_url );
										?>
										<div class="cb-content-grid-v2__video-wrap ratio ratio-16x9">
											<iframe
												class="cb-content-grid-v2__video"
												src="<?= esc_url( $video_url ); ?>"
												title="Vimeo video player"
												frameborder="0"
												allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
												referrerpolicy="strict-origin-when-cross-origin"
												allowfullscreen>
											</iframe>
										</div>
										<?php
									}
									break;

								case 'qa':
									$qa_rows = $module['qa_rows'] ?? array();
									if ( ! empty( $qa_rows ) && is_array( $qa_rows ) ) {
										$first_q = ! empty( $module['lead_first'] ) ? 'cb-content-grid-v2__qa-question--first' : '';
										$first_a = ! empty( $module['lead_first'] ) ? 'cb-content-grid-v2__qa-answer--first' : '';
										$large_q = ! empty( $module['large_left'] ) ? 'cb-content-grid-v2__qa-question--large' : '';
										?>
										<div class="cb-content-grid-v2__qa">
											<?php foreach ( $qa_rows as $qa_row ) : ?>
												<?php
												$qa_question = $qa_row['qa_question'] ?? '';
												$qa_answer   = $qa_row['qa_answer'] ?? '';
												if ( ! $qa_question && ! $qa_answer ) {
													continue;
												}
												?>
												<div class="cb-content-grid-v2__qa-row row g-4 pb-5 align-items-start">
													<div class="col-lg-6">
														<?php if ( $qa_question ) : ?>
															<h3 class="cb-content-grid-v2__qa-question <?= esc_attr( trim( $first_q . ' ' . $large_q ) ); ?>"><?= wp_kses_post( $qa_question ); ?></h3>
														<?php endif; ?>
													</div>
													<div class="col-lg-6">
														<?php if ( $qa_answer ) : ?>
															<div class="cb-content-grid-v2__qa-answer <?= esc_attr( $first_a ); ?>"><?= wp_kses_post( $qa_answer ); ?></div>
														<?php endif; ?>
													</div>
												</div>
												<?php
												$first_q = '';
												$first_a = '';
												?>
											<?php endforeach; ?>
										</div>
										<?php
									}
									break;

								case 'button':
									$button_link = is_array( $module['button_link'] ?? null ) ? $module['button_link'] : array();
									$cta_text    = $module['cta_text'] ?? '';
									if ( ! empty( $button_link['url'] ) && ! empty( $button_link['title'] ) ) {
										?>
										<a class="id-button mb-5 mt-3" href="<?= esc_url( $button_link['url'] ); ?>" target="<?= esc_attr( $button_link['target'] ?: '_self' ); ?>"><?= esc_html( $button_link['title'] ); ?></a>
										<?php
									}
									if ( $cta_text ) {
										?>
										<div class="cb-content-grid-v2__cta-title"><?= esc_html( $cta_text ); ?></div>
										<?php
									}
									break;

								case 'empty':
								default:
									break;
							} ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php if ( $section_style_attr ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--cb-content-grid-v2-parallax-y', translateY.toFixed(1) + 'px');
		}

		ticking = false;
	}

	function onScroll() {
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	onScroll();
});
</script>
<?php endif; ?>

<script>
(function() {
	function matchImageHeights() {
		document.querySelectorAll('.cb-content-grid-v2__row').forEach(function(row) {
			var modules = row.querySelectorAll('.cb-content-grid-v2__module--image');
			if (modules.length < 2) return;

			var maxHeight = 0;
			var images = [];

			modules.forEach(function(mod) {
				var img = mod.querySelector('.cb-content-grid-v2__image-wrap');
				if (img) {
					img.style.height = '';
					var rect = img.getBoundingClientRect();
					if (rect.height > maxHeight) {
						maxHeight = rect.height;
					}
					images.push(img);
				}
			});

			if (maxHeight > 0) {
				images.forEach(function(img) {
					img.style.height = maxHeight + 'px';
				});
			}
		});
	}

	var resizeTimer;
	window.addEventListener('resize', function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(matchImageHeights, 100);
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', matchImageHeights);
	} else {
		matchImageHeights();
	}
})();
</script>
