<?php
/**
 * Block template for CB Content Grid.
 *
 * Ported 2026-07-10 from identity's/coda's real templates (near-identical,
 * same field keys - coda's own theme copied identity's schema wholesale,
 * adding one extra `title` field per multi-module_row module). The shared
 * theme's previous version invented an entirely different `rows`/`modules`
 * repeater schema that matched neither site's real saved content at all -
 * silently rendering nothing, since `$block['data']['grid_rows']` (what
 * every real content-grid instance on every site actually has) was never
 * read. See SESSION-HANDOFF.md 2026-07-10 for the full story.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cb_normalize_excel_paste' ) ) {
	/**
	 * Normalize HTML content pasted from Excel.
	 *
	 * @param string $html The HTML content to normalize.
	 * @return string The normalized HTML content.
	 */
	function cb_normalize_excel_paste( $html ) {
		$html = (string) $html;
		if ( '' === trim( $html ) ) {
			return $html;
		}
		$prev = libxml_use_internal_errors( true );
		$doc  = new DOMDocument();
		$doc->loadHTML( '<?xml encoding="utf-8" ?><div id="cb-normalize-wrapper">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		$xpath = new DOMXPath( $doc );

		// Deepest-first processing of ewa-rteLine wrappers.
		$nodes = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " ewa-rteLine ")]' );
		$arr   = array();
		foreach ( $nodes as $n ) {
			$arr[] = $n;
		}
		$arr = array_reverse( $arr );

		foreach ( $arr as $div ) {
			$has_child_ewa = false;
			foreach ( $div->childNodes as $child ) {
				if ( $child instanceof DOMElement ) {
					$cls = $child->getAttribute( 'class' );
					if ( $cls && preg_match( '/(^|\s)ewa-?rteLine(\s|$)/i', $cls ) ) {
						$has_child_ewa = true;
						break;
					}
				}
			}

			// Is this wrapper effectively empty?
			$has_non_empty = false;
			foreach ( $div->childNodes as $child ) {
				if ( $child instanceof DOMText ) {
					if ( trim( $child->wholeText ) !== '' ) {
						$has_non_empty = true;
						break;
					}
				} elseif ( $child instanceof DOMElement ) {
					if ( strtolower( $child->tagName ) === 'br' || trim( $child->textContent ) !== '' ) {
						$has_non_empty = true;
						break;
					}
				}
			}

			if ( ! $has_non_empty && ! $has_child_ewa ) {
				if ( $div->parentNode ) {
					$div->parentNode->removeChild( $div );
				}
				continue;
			}

			if ( $has_child_ewa ) {
				// Unwrap: move children out.
				while ( $div->firstChild ) {
					$div->parentNode->insertBefore( $div->firstChild, $div );
				}
				$div->parentNode->removeChild( $div );
			} else {
				// Convert to paragraph.
				$p = $doc->createElement( 'p' );
				while ( $div->firstChild ) {
					$p->appendChild( $div->firstChild );
				}
				$div->parentNode->replaceChild( $p, $div );
			}
		}

		// Remove empty generic divs left over.
		$empties = $xpath->query( '//div[not(normalize-space()) and not(*)]' );
		foreach ( $empties as $e ) {
			if ( $e->parentNode ) {
				$e->parentNode->removeChild( $e );
			}
		}

		$out     = '';
		$wrapper = $doc->getElementById( 'cb-normalize-wrapper' );
		if ( $wrapper ) {
			foreach ( $wrapper->childNodes as $child ) {
				$out .= $doc->saveHTML( $child );
			}
		} else {
			$out = $html;
		}
		libxml_clear_errors();
		libxml_use_internal_errors( $prev );
		return $out;
	}
}

// idtravel's own real content-grid schema (a `rows` repeater, completely
// different from identity's/coda's `grid_rows` flexible_content below) —
// route by which field actually has data, not by cb_site, since that's
// what determines which real template the saved content needs.
if ( ! empty( get_field( 'rows' ) ) ) {
	require get_theme_file_path( 'blocks/cb-content-grid-idtravel.php' );
	return;
}

// Get grid rows from block data.
$grid_rows = $block['data']['grid_rows'] ?? array();
if ( empty( $grid_rows ) ) {
	return;
}

// Two background mechanisms exist across real content: identity's own
// dedicated "background" radio field, and coda's use of Gutenberg's native
// colour picker (block backgroundColor/textColor attributes). Support both.
$background_field = get_field( 'background' );
$bg                = $background_field ? $background_field : ( ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '' );
$fg                = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id        = $block['anchor'] ?? null;

?>
<section id="<?= esc_attr( $section_id ); ?>" class="content-grid <?= esc_attr( $bg ); ?> <?= esc_attr( $fg ); ?>">
	<div class="id-container pt-5 px-4 px-md-5">
	<?php
	foreach ( $grid_rows as $row_index => $layout ) {
		// Support both 'multi-module_row' and 'multi_module_row' for compatibility.
		if ( 'single_module_row' === $layout ) { // --- Single module row ---
			$mtype      = $block['data'][ 'grid_rows_' . $row_index . '_module_type' ] ?? '';
			$width_raw  = $block['data'][ 'grid_rows_' . $row_index . '_column_width' ] ?? 12;
			$offset_raw = $block['data'][ 'grid_rows_' . $row_index . '_column_offset' ] ?? 0;
			$width      = intval( $width_raw );
			$offset     = intval( $offset_raw );

			if ( $width < 1 || $width > 12 ) {
				$width = 12;
			}
			if ( $offset < 0 ) {
				$offset = 0;
			}
			if ( $offset > 11 ) {
				$offset = 11;
			}
			if ( $width + $offset > 12 ) {
				$offset = max( 0, 12 - $width );
			}
			$class = trim( $block['data'][ 'grid_rows_' . $row_index . '_custom_class' ] ?? '' );

			$col_classes = array(
				'col-md-' . esc_attr( $width ),
			);
			if ( $offset && '0' !== $offset ) {
				$col_classes[] = 'offset-md-' . esc_attr( $offset );
			}
			if ( $class ) {
				$col_classes[] = sanitize_html_class( $class );
			}
			?>
			<div class="row g-5 pb-5">
				<div class="<?= esc_attr( implode( ' ', $col_classes ) ); ?>" data-aos="fade-up">
					<?php
					if ( 'image' === $mtype || 'Image' === $mtype ) {
						$image_id = $block['data'][ 'grid_rows_' . $row_index . '_image' ] ?? null;
						$caption  = $block['data'][ 'grid_rows_' . $row_index . '_caption' ] ?? '';
						$aspect   = $block['data'][ 'grid_rows_' . $row_index . '_aspect_ratio' ] ?? '';

						switch ( $aspect ) {
							case 'Native':
								$aspect_class = '';
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
							default:
								$aspect_class = '';
						}
						if ( $image_id ) {
							echo '<div class="img-cover ' . esc_attr( $aspect_class ) . '">';
							echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'img-fluid' ) );
							echo '</div>';
						}

						if ( $caption ) {
							echo '<p class="image-caption small text-muted mt-2">' . esc_html( $caption ) . '</p>';
						}
					} elseif ( 'text' === $mtype || 'Text' === $mtype ) {
						$block_title = $block['data'][ 'grid_rows_' . $row_index . '_title' ] ?? '';
						if ( $block_title ) {
							echo '<h2 class="content-grid-title mb-4">' . esc_html( $block_title ) . '</h2>';
						}
						$text = $block['data'][ 'grid_rows_' . $row_index . '_text' ] ?? '';
						$text = cb_normalize_excel_paste( $text );
						echo apply_filters( 'the_content', $text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} elseif ( 'video' === $mtype || 'Video' === $mtype ) {
						$vimeo_url = $block['data'][ 'grid_rows_' . $row_index . '_vimeo_url' ] ?? '';
						if ( $vimeo_url ) {
							echo '<div class="ratio ratio-16x9">';
							?>
							<iframe src="<?= esc_url( cb_vimeo_url_with_dnt( $vimeo_url ) ); ?>" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
							<?php
							echo '</div>';
						}
					}
					?>
				</div>
			</div>
			<?php
		} elseif ( 'multi_module_row' === $layout || 'multi-module_row' === $layout ) { // --- Multi-module row ---
			// Try to determine module count by looking for the highest module index.
			$modules_count = 0;
			// Prefer 'grid_rows_X_module' (as in real saved data), fallback to 'grid_rows_X_modules'.
			if ( isset( $block['data'][ 'grid_rows_' . $row_index . '_module' ] ) ) {
				$modules_count = intval( $block['data'][ 'grid_rows_' . $row_index . '_module' ] );
			} elseif ( isset( $block['data'][ 'grid_rows_' . $row_index . '_modules' ] ) ) {
				$modules_count = intval( $block['data'][ 'grid_rows_' . $row_index . '_modules' ] );
			} else {
				// Try to count by scanning keys.
				foreach ( $block['data'] as $k => $v ) {
					if ( strpos( $k, 'grid_rows_' . $row_index . '_module_' ) === 0 && strpos( $k, '_module_type' ) !== false ) {
						++$modules_count;
					}
				}
			}
			if ( $modules_count > 0 ) {
				?>
				<div class="row g-5 pb-5">
					<?php
					for ( $module_index = 0; $module_index < $modules_count; $module_index++ ) {
						$mtype      = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_module_type' ] ?? '';
						$width_raw  = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_column_width' ] ?? 12;
						$offset_raw = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_column_offset' ] ?? 0;
						$width      = intval( $width_raw );
						$offset     = intval( $offset_raw );

						if ( $width < 1 || $width > 12 ) {
							$width = 12;
						}
						if ( $offset < 0 ) {
							$offset = 0;
						}
						if ( $offset > 11 ) {
							$offset = 11;
						}
						if ( $width + $offset > 12 ) {
							$offset = max( 0, 12 - $width );
						}
						$class = trim( $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_custom_class' ] ?? '' );

						$col_classes = array(
							'col-md-' . esc_attr( $width ),
						);
						if ( $offset && '0' !== $offset ) {
							$col_classes[] = 'offset-md-' . esc_attr( $offset );
						}
						if ( $class ) {
							$col_classes[] = sanitize_html_class( $class );
						}
						?>
						<div class="<?= esc_attr( implode( ' ', $col_classes ) ); ?>" data-aos="fade-up">
							<?php
							if ( 'image' === $mtype || 'Image' === $mtype ) {
								$image_id = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_image' ] ?? null;
								$caption  = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_caption' ] ?? '';
								$aspect   = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_aspect_ratio' ] ?? '';

								switch ( $aspect ) {
									case 'Native':
										$aspect_class = '';
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
									default:
										$aspect_class = '';
								}
								if ( $image_id ) {
									echo '<div class="img-cover ' . esc_attr( $aspect_class ) . '">';
									echo wp_get_attachment_image(
										$image_id,
										'full',
										false,
										array(
											'class' => 'img-fluid',
											'alt'   => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
										)
									);
									echo '</div>';
								}
								if ( $caption ) {
									echo '<p class="image-caption small text-muted mt-2">' . esc_html( $caption ) . '</p>';
								}
							} elseif ( 'text' === $mtype || 'Text' === $mtype ) {
								$block_title = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_title' ] ?? '';
								if ( $block_title ) {
									echo '<h2 class="content-grid-title mb-4">' . esc_html( $block_title ) . '</h2>';
								}
								$text = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_text' ] ?? '';
								$text = cb_normalize_excel_paste( $text );
								echo apply_filters( 'the_content', $text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} elseif ( 'video' === $mtype || 'Video' === $mtype ) {
								$vimeo_url = $block['data'][ 'grid_rows_' . $row_index . '_module_' . $module_index . '_vimeo_url' ] ?? '';
								if ( $vimeo_url ) {
									echo '<div class="ratio ratio-16x9">';
									echo wp_kses_post( wp_oembed_get( esc_url( cb_vimeo_url_with_dnt( $vimeo_url ) ) ) );
									echo '</div>';
								}
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			} else {
				echo '<!-- No modules found for row ' . esc_html( $row_index ) . ' -->';
			}
		}
	}
	?>
	</div>
</section>
