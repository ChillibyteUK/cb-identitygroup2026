<?php
/**
 * Blocks reference partial.
 *
 * Displays a preview and summary of all registered ACF blocks, including a cleaned-up
 * description extracted from each block template's file doc block.
 *
 * @package cb-identitygroup2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get all registered ACF block types, alphabetically by title rather than
// inc/cb-blocks.php's registration order.
$blocks = acf_get_block_types();
usort(
	$blocks,
	function ( $a, $b ) {
		return strcasecmp( $a['title'], $b['title'] );
	}
);

// Block icons here are Dashicon slugs (WP core's admin icon font) — every
// icon currently registered in inc/cb-blocks.php is a plain slug like
// "cover-image", not an SVG or {background,foreground,src} array. Dashicons'
// CSS is only enqueued in wp-admin by default, not the front end where this
// reference page lives, but the 'dashicons' style handle is always
// registered by WP core, so it can be enqueued/printed here without
// hardcoding a path. We're already past wp_head() by this point in the
// template, so print it directly instead of relying on the wp_head hook.
wp_enqueue_style( 'dashicons' );
wp_print_styles( 'dashicons' );

/**
 * Returns a real attachment ID for image/file/gallery field previews, whose
 * underlying file is the theme's own img/reference-placeholder.png — not a
 * copy sideloaded into wp-content/uploads. 30 of this theme's 39 image/
 * gallery fields use return_format "id" (confirmed via acf-json), and core
 * WP functions like wp_get_attachment_image() only resolve a real attachment
 * post — a plain URL can't satisfy that return format, so this can't be a
 * pure runtime filter the way link/text fields are.
 *
 * `_wp_attached_file` just needs to be non-empty for wp_attachment_is_image()
 * to pass (it short-circuits on post_mime_type before ever looking at the
 * file itself) — but wp_get_attachment_url() has no way to express "this
 * attachment's file lives outside wp-content/uploads entirely": it either
 * matches the uploads basedir or falls back to treating whatever's stored
 * there as uploads-relative, silently mangling an absolute URL if given one
 * (confirmed empirically — it does NOT special-case http(s) in this WP
 * version). The `wp_get_attachment_url` filter is the correct, WP-native way
 * to override the final resolved URL for just this one attachment ID instead.
 *
 * @return int Attachment ID, or 0 if creation failed.
 */
function cb_reference_get_placeholder_attachment_id() {
	static $attachment_id = null;
	if ( null !== $attachment_id ) {
		return $attachment_id;
	}

	$url = get_stylesheet_directory_uri() . '/img/reference-placeholder.png';

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_cb_reference_placeholder',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		$id = (int) $existing[0];
	} else {
		$id = wp_insert_attachment(
			array(
				'post_title'     => 'Theme Reference placeholder image',
				'post_mime_type' => 'image/png',
				'post_status'    => 'inherit',
				'guid'           => $url,
			)
		);

		if ( ! $id || is_wp_error( $id ) ) {
			$attachment_id = 0;
			return 0;
		}

		update_post_meta( $id, '_wp_attached_file', 'reference-placeholder.png' );
		update_post_meta(
			$id,
			'_wp_attachment_metadata',
			array(
				'width'  => 1200,
				'height' => 800,
				'file'   => 'reference-placeholder.png',
				'sizes'  => array(),
			)
		);
		update_post_meta( $id, '_cb_reference_placeholder', 1 );
	}

	add_filter(
		'wp_get_attachment_url',
		function ( $resolved_url, $resolved_id ) use ( $id, $url ) {
			return ( (int) $resolved_id === $id ) ? $url : $resolved_url;
		},
		10,
		2
	);

	$attachment_id = (int) $id;
	return $attachment_id;
}

/**
 * Generates a dummy raw value for a single (non-repeating) ACF field, in the
 * same shape ACF itself would store in postmeta/block data — e.g. an
 * attachment ID for image fields, a {title,url,target} array for link
 * fields — so get_field() formats it exactly as it would real content.
 *
 * @param array $field ACF field array.
 * @return mixed|null Null means "no sensible dummy value", caller should skip it.
 */
function cb_reference_dummy_scalar_value( $field ) {
	switch ( $field['type'] ) {
		case 'text':
			return 'Sample ' . ( $field['label'] ? $field['label'] : 'text' );
		case 'email':
			return 'sample@example.com';
		case 'url':
			return '#';
		case 'textarea':
			return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
		case 'wysiwyg':
			return '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam, quis nostrud exercitation.</p>';
		case 'number':
		case 'range':
			return ( isset( $field['default_value'] ) && '' !== $field['default_value'] ) ? $field['default_value'] : 42;
		case 'true_false':
			return 1;
		case 'select':
		case 'radio':
		case 'button_group':
			$choices = ! empty( $field['choices'] ) ? array_keys( $field['choices'] ) : array();
			if ( empty( $choices ) ) {
				return null;
			}
			return ! empty( $field['multiple'] ) ? array( $choices[0] ) : $choices[0];
		case 'checkbox':
			$choices = ! empty( $field['choices'] ) ? array_keys( $field['choices'] ) : array();
			return empty( $choices ) ? null : array( $choices[0] );
		case 'link':
			return array(
				'title'  => 'Sample link',
				'url'    => '#',
				'target' => '',
			);
		case 'image':
		case 'file':
			$id = cb_reference_get_placeholder_attachment_id();
			return $id ? $id : null;
		case 'gallery':
			$id = cb_reference_get_placeholder_attachment_id();
			return $id ? array( $id, $id ) : null;
		default:
			// Relational fields (taxonomy/relationship/post_object) need real
			// DB records to point at — leave unset so the block's own "no
			// items" guard applies rather than faking IDs that don't exist.
			return null;
	}
}

/**
 * Populates $data with a dummy raw value for $field under $name, recursing
 * into repeater/flexible_content/group sub-fields using the same flattened
 * key convention ACF's own field-type classes read from postmeta (see
 * class-acf-field-repeater.php / class-acf-field-flexible-content.php /
 * class-acf-field-group.php load_value() methods) — e.g. a repeater named
 * "items" with 2 rows needs `items` => 2 (a row count) plus
 * `items_0_title`/`items_1_title` for a "title" sub-field, not a nested
 * array. Top-level fields also need a `_{name}` => field key entry so
 * get_field()/have_rows() can resolve the field's type at all.
 *
 * @param array  $data  Reference to the flat data array being built.
 * @param array  $field ACF field array.
 * @param string $name  Storage key for this field (differs from $field['name'] once nested).
 */
function cb_reference_add_dummy_field( &$data, $field, $name ) {
	switch ( $field['type'] ) {
		case 'tab':
		case 'message':
			return;

		case 'repeater':
			if ( empty( $field['sub_fields'] ) ) {
				return;
			}
			$rows           = 2; // A couple of rows so lists/sliders don't look like a single-item edge case.
			$data[ $name ]  = $rows;
			$data[ '_' . $name ] = $field['key'];
			for ( $i = 0; $i < $rows; $i++ ) {
				foreach ( $field['sub_fields'] as $sub_field ) {
					cb_reference_add_dummy_field( $data, $sub_field, "{$name}_{$i}_{$sub_field['name']}" );
				}
			}
			return;

		case 'flexible_content':
			if ( empty( $field['layouts'] ) ) {
				return;
			}
			$layout        = reset( $field['layouts'] );
			$data[ $name ] = array( $layout['name'] );
			$data[ '_' . $name ] = $field['key'];
			if ( ! empty( $layout['sub_fields'] ) ) {
				foreach ( $layout['sub_fields'] as $sub_field ) {
					cb_reference_add_dummy_field( $data, $sub_field, "{$name}_0_{$sub_field['name']}" );
				}
			}
			return;

		case 'group':
			if ( empty( $field['sub_fields'] ) ) {
				return;
			}
			$data[ $name ] = 1;
			$data[ '_' . $name ] = $field['key'];
			foreach ( $field['sub_fields'] as $sub_field ) {
				cb_reference_add_dummy_field( $data, $sub_field, "{$name}_{$sub_field['name']}" );
			}
			return;

		default:
			$value = cb_reference_dummy_scalar_value( $field );
			if ( null === $value ) {
				return;
			}
			$data[ $name ]       = $value;
			$data[ '_' . $name ] = $field['key'];
	}
}

?>
<style>
	.blocks-grid {
		display: grid;
		/* minmax(0, 1fr), not bare 1fr — a grid track's min width defaults to
		   the content's intrinsic min-content size, so a block with wide,
		   non-wrapping content (fixed-width images, tables, full-bleed
		   `100vw` sections) would otherwise stretch the column past the
		   viewport instead of being contained/scaled inside it. */
		grid-template-columns: minmax(0, 1fr);
		gap: 1.5rem;
		margin-top: 2rem;
	}
	.block-card {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
		font-family: monospace;
		min-width: 0; /* same reason as .blocks-grid above */
		/* Ensure cards stretch to equal height */
		height: 100%;
	}
	.block-card-header {
		margin-bottom: 0.5rem;
	}
	.block-card-header h3 {
		margin: 0;
	}
	.block-icon {
		font-size: 20px;
		width: 20px;
		height: 20px;
		vertical-align: middle;
		margin-right: 0.35rem;
	}
	.block-name {
		display: block;
		font-size: 0.75rem;
		font-weight: normal;
		color: #666;
		margin-top: 0.15rem;
	}
	.block-preview {
		flex: 1;
		border: 1px dashed #ddd;
		padding: 1rem;
		margin-top: 1rem;
		max-height: 300px;
		overflow: hidden;
		min-width: 0;
		/* Real theme font, not the card's own monospace label styling —
		   this area shows the block as it actually renders on the site. */
		font-family: var(--font-family);
	}
	.block-preview-inner {
		/* Blocks are designed for a full desktop-width page, not a card —
		   zoom shrinks the whole rendered subtree (including any fixed-px
		   widths or 100vw full-bleed sections) so it actually fits, rather
		   than just clipping the right-hand side off. Unlike transform:
		   scale(), zoom also shrinks the layout box itself, so there's no
		   left-over blank space below to compensate for. */
		zoom: 0.6;
	}
	.block-meta {
		margin-top: 1rem;
		font-size: 0.875rem;
	}
	.block-meta code {
		display: block;
	}
</style>

<div class="container-xl">
	<h2>Blocks</h2>
	<p>This section shows all registered ACF blocks with a preview of their default state and a summary of their expected fields and description.</p>

	<div class="blocks-grid">
		<?php
		if ( ! empty( $blocks ) ) {
			foreach ( $blocks as $block_type ) {
				// Fetch this block's fields first (rather than after
				// rendering, as before) so they can be used to build dummy
				// field data ahead of the include.
				$field_groups = acf_get_field_groups( array( 'block' => $block_type['name'] ) );
				$fields       = array();
				if ( ! empty( $field_groups ) ) {
					foreach ( $field_groups as $group ) {
						$group_fields = acf_get_fields( $group['key'] );
						if ( $group_fields ) {
							$fields = array_merge( $fields, $group_fields );
						}
					}
				}

				$dummy_data = array();
				foreach ( $fields as $field ) {
					cb_reference_add_dummy_field( $dummy_data, $field, $field['name'] );
				}

				// Build the same render context acf_register_block_type()'s render
				// callback normally provides — $block/$content/$is_preview/$post_id —
				// since most block templates reference these directly and a raw
				// include() otherwise leaves them undefined. acf_setup_meta() with
				// the dummy data above mirrors ACF's own local-meta context (see
				// acf_setup_meta() in local-meta.php) so get_field()/have_rows()
				// calls inside the template resolve to realistic sample content
				// instead of erroring or rendering an empty state.
				$is_preview = true;
				$content    = '';
				$post_id    = get_the_ID();
				$block      = array(
					'id'              => 'block_reference_preview_' . $block_type['name'],
					'name'            => 'acf/' . $block_type['name'],
					'data'            => $dummy_data,
					'align'           => '',
					'anchor'          => '',
					'className'       => '',
					'backgroundColor' => '',
					'textColor'       => '',
					'supports'        => $block_type['supports'] ?? array(),
				);

				if ( function_exists( 'acf_setup_meta' ) ) {
					acf_setup_meta( $block['data'], $block['id'], true );
				}

				// Start output buffering to capture the rendered preview.
				ob_start();
				$template = ! empty( $block_type['render_template'] ) ? locate_template( $block_type['render_template'] ) : '';
				if ( ! empty( $template ) && file_exists( $template ) ) {
					include $template;
				} else {
					echo '<p>No template found.</p>';
				}
				$block_preview = ob_get_clean();

				if ( function_exists( 'acf_reset_meta' ) ) {
					acf_reset_meta( $block['id'] );
				}

				// Retrieve the block description from the file doc block.
				$description = '';
				if ( ! empty( $template ) && file_exists( $template ) ) {
					$description = get_template_description( $template );
				}

				// Build the "expected fields" badges from the fields already fetched above.
				$fields_output = '';
				foreach ( $fields as $field ) {
					$fields_output .= '<code>' . esc_html( $field['name'] ) . '</code> ';
				}
				?>
				<?php
				// Build the icon markup (Dashicon slug string, or ACF's
				// {background, foreground, src} array form) to show inline
				// before the block title, rather than as a separate line.
				$icon_html = '';
				if ( ! empty( $block_type['icon'] ) ) {
					$icon = $block_type['icon'];
					if ( is_array( $icon ) && ! empty( $icon['src'] ) ) {
						$icon_style = '';
						if ( ! empty( $icon['foreground'] ) ) {
							$icon_style .= 'color:' . esc_attr( $icon['foreground'] ) . ';';
						}
						if ( ! empty( $icon['background'] ) ) {
							$icon_style .= 'background:' . esc_attr( $icon['background'] ) . ';';
						}
						$icon_html = '<span class="dashicons dashicons-' . esc_attr( $icon['src'] ) . ' block-icon" style="' . esc_attr( $icon_style ) . '"></span>';
					} else {
						$icon_html = '<span class="dashicons dashicons-' . esc_attr( $icon ) . ' block-icon"></span>';
					}
				}
				?>
				<div class="block-card">
					<div class="block-card-header">
						<h3><?php echo wp_kses_post( $icon_html ) . esc_html( $block_type['title'] ); ?></h3>
						<small class="block-name"><?php echo esc_html( $block_type['name'] ); ?></small>
					</div>
					<?php
					if ( $description ) {
						echo '<p><strong>Description:</strong> ' . esc_html( $description ) . '</p>';
					} else {
						echo '<p>No description available.</p>';
					}
					?>
					<div class="block-preview">
						<div class="block-preview-inner">
							<?php echo wp_kses_post( $block_preview ); ?>
						</div>
					</div>
					<div class="block-meta">
						<?php
						$clean_fields_output = trim( wp_strip_all_tags( $fields_output, false ) );
						if ( ! empty( $clean_fields_output ) ) {
							echo '<strong>Expected fields:</strong>';
							echo wp_kses_post( $fields_output );
						} else {
							echo '<p>No fields defined.</p>';
						}
						?>
					</div>
				</div>
				<?php
			}
		} else {
			echo '<p>No ACF blocks registered.</p>';
		}
		?>
	</div>
</div>
<?php
// End of blocks reference.
?>
