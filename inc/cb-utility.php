<?php
/**
 * Utility functions and shortcodes.
 *
 * @package cb-identitygroup2026
 */

/**
 * Recursively searches parsed blocks for a case study hero's subtitle field.
 *
 * Was duplicated inline (with function_exists guards) across cb-work-carousel,
 * cb-work-index, cb-related-work, cb-work-by-region, and cb-featured-work.
 * Extracted here per the consolidation plan's Decisions section.
 *
 * Checks both the new block name (acf/cb_case_study_hero, after converting
 * cb-case-study-hero from block.json to classic ACF registration) and the
 * legacy block.json name (cb/cb-case-study-hero), since existing published
 * content still has the legacy name serialized in post_content until it is
 * migrated — see the render_block_data rename shim below for the render-time
 * equivalent of this fallback.
 *
 * @param array $blocks The array of parsed blocks to search through.
 * @return string The case study subtitle if found, empty string otherwise.
 */
function cb_find_hero_subtitle( $blocks ) {
	foreach ( $blocks as $block ) {
		if (
			isset( $block['blockName'] ) &&
			in_array( $block['blockName'], array( 'acf/cb_case_study_hero', 'cb/cb-case-study-hero' ), true ) &&
			! empty( $block['attrs']['data']['case_study_subtitle'] )
		) {
			return $block['attrs']['data']['case_study_subtitle'];
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = cb_find_hero_subtitle( $block['innerBlocks'] );
			if ( $found ) {
				return $found;
			}
		}
	}
	return '';
}

/**
 * Rewrites legacy block.json block names to their converted classic-ACF
 * equivalents at render time, so existing published content (which still
 * has the old names serialized in post_content) keeps rendering correctly
 * on the frontend without a database migration. Editing these blocks in
 * wp-admin still requires the content to be migrated to the new block name,
 * since this filter only runs at render time, not in the block editor.
 *
 * @param array $block A single parsed block.
 * @return array The block, with blockName rewritten if it matches a legacy name.
 */
function cb_rename_legacy_block_names( $block ) {
	static $renames = array(
		'cb/cb-case-study-hero'     => 'acf/cb_case_study_hero',
		'cb/cb-brand-title-text'    => 'acf/cb_brand_title_text',
		'cb/cb-culture-page-header' => 'acf/cb_culture_page_header',
		'cb/cb-innovation-header'   => 'acf/cb_innovation_header',
		'cb/cb-policies-page'       => 'acf/cb_policies_page',
		'cb/cb-region-page-header'  => 'acf/cb_region_page_header',
		'cb/cb-file-block'          => 'acf/cb_file_block',
		'cb/cb-work-carousel'       => 'acf/cb_work_carousel',
	);
	if ( isset( $block['blockName'] ) && isset( $renames[ $block['blockName'] ] ) ) {
		$block['blockName'] = $renames[ $block['blockName'] ];
	}
	return $block;
}
add_filter( 'render_block_data', 'cb_rename_legacy_block_names' );

/**
 * Outputs an admin-only notice inside a deprecated block's editor preview,
 * pointing editors at its replacement. Renders nothing on the front end.
 *
 * Per the consolidation plan, deprecated blocks stay fully registered and
 * functional for one release cycle before removal, so existing content
 * keeps working while editors are steered away from adding new instances.
 *
 * @param string $replacement Human-readable name of the block to use instead.
 * @param bool   $is_preview  The block template's $is_preview variable.
 */
function cb_deprecated_block_notice( $replacement, $is_preview ) {
	if ( ! $is_preview ) {
		return;
	}
	?>
	<div class="cb-deprecated-block-notice" style="background:#fcf0f1;border-left:4px solid #d63638;padding:10px 14px;margin-bottom:1rem;font-size:13px;line-height:1.4;">
		<strong>Deprecated block.</strong> Use <strong><?php echo esc_html( $replacement ); ?></strong> instead — this block is scheduled for removal.
	</div>
	<?php
}

/**
 * Parse and format phone number.
 *
 * @param string $phone The phone number to parse.
 * @return string The formatted phone number.
 */
function parse_phone( $phone ) {
    $phone = preg_replace( '/\s+/', '', $phone );
    $phone = preg_replace( '/\(0\)/', '', $phone );
    $phone = preg_replace( '/[\(\)\.]/', '', $phone );
    $phone = preg_replace( '/-/', '', $phone );
    // $phone = preg_replace( '/^0/', '+44', $phone );
    return $phone;
}

/**
 * Split lines by adding extra line breaks.
 *
 * @param string $content The content to process.
 * @return string The processed content.
 */
function split_lines( $content ) {
    $content = preg_replace( '/<br \/>/', '<br>&nbsp;<br>', $content );
    return $content;
}

add_shortcode(
    'contact_address',
    function () {
		$output = get_field( 'contact_address', 'option' );
		return $output;
	}
);

add_shortcode(
    'contact_phone',
    function ( $atts ) {
        $atts = shortcode_atts(
            array(
                'class' => '',
                'text'  => '',
                'icon'  => false,
            ),
            $atts,
            'contact_phone'
        );

        if ( get_field( 'contact_phone', 'option' ) ) {
            $phone       = get_field( 'contact_phone', 'option' );
            $icon_html   = $atts['icon'] ? '<i class="fa-solid fa-phone"></i> ' : '';
            $anchor_text = $icon_html . ( ! empty( $atts['text'] ) ? wp_kses_post( $atts['text'] ) : esc_html( $phone ) );
            $class       = esc_attr( $atts['class'] );

            return '<a href="tel:' . parse_phone( $phone ) . '" class="' . $class . '">' . $anchor_text . '</a>';
        }
    }
);

add_shortcode(
    'contact_email',
    function ( $atts ) {
        $atts = shortcode_atts(
            array(
                'class' => '',
                'text'  => '',
                'icon'  => false,
            ),
            $atts,
            'contact_email'
        );

        if ( get_field( 'contact_email', 'option' ) ) {
            $email       = get_field( 'contact_email', 'option' );
            $icon_html   = $atts['icon'] ? '<i class="fa-solid fa-envelope"></i> ' : '';
            $anchor_text = $icon_html . ( ! empty( $atts['text'] ) ? wp_kses_post( $atts['text'] ) : esc_html( $email ) );
            $class       = esc_attr( $atts['class'] );

            $obfuscated_email = antispambot( $email );
            return '<a href="mailto:' . esc_attr( $obfuscated_email ) . '" class="' . $class . '">' . $anchor_text . '</a>';
        }
    }
);

add_shortcode(
	'contact_email_icon',
	function () {
        $email            = get_field( 'contact_email', 'option' );
        $obfuscated_email = antispambot( $email );
		if ( $obfuscated_email ) {
			return '<a href="mailto:' . esc_attr( $obfuscated_email ) . '" aria-label="Email us"><i class="fas fa-envelope" aria-hidden="true"></i></a>';
		}
	}
);

/**
 * Generates a social icon shortcode based on the provided type.
 *
 * @param array $atts Attributes passed to the shortcode.
 * @return string The HTML for the social icon or an empty string if the type is invalid.
 */
function social_icon_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'type' => '' ), $atts );
    if ( ! $atts['type'] ) {
        return '';
    }

    $social = get_field( 'socials', 'option' );
	$urls   = array(
        'facebook'    => $social['facebook_url'] ?? '',
        'instagram'   => $social['instagram_url'] ?? '',
        'x-twitter'   => $social['twitter_url'] ?? '',
        'pinterest'   => $social['pinterest_url'] ?? '',
        'youtube'     => $social['youtube_url'] ?? '',
        'linkedin-in' => $social['linkedin_url'] ?? '',
	);

    if ( ! isset( $urls[ $atts['type'] ] ) || empty( $urls[ $atts['type'] ] ) ) {
        return '';
    }

    $url  = esc_url( $urls[ $atts['type'] ] );
    $icon = esc_attr( $atts['type'] );

    return '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
}

// Register individual social icon shortcodes.
$social_types = array( 'facebook', 'instagram', 'twitter', 'pinterest', 'youtube', 'linkedin' );
foreach ( $social_types as $social_type ) {
    add_shortcode(
		'social_' . $social_type . '_icon',
		function () use ( $social_type ) {
            return social_icon_shortcode( array( 'type' => $social_type ) );
    	}
	);
}

// Generate a single shortcode to output all social icons.
add_shortcode(
	'social_icons',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts,
			'social_icons'
		);

		$social = get_field( 'social', 'option' );
		if ( ! $social ) {
			return '';
		}

		$icons      = array();
		$social_map = array(
			'twitter'   => 'x-twitter',
			'facebook'  => 'facebook-f',
			'instagram' => 'instagram',
			'pinterest' => 'pinterest',
			'youtube'   => 'youtube',
			'linkedin'  => 'linkedin-in',
		);

		foreach ( $social_map as $key => $icon ) {
			if ( ! empty( $social[ $key . '_url' ] ) ) {
				$url     = esc_url( $social[ $key . '_url' ] );
				$icons[] = '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
			}
		}

    	$class = esc_attr( trim( $atts['class'] ) );

	    return ! empty( $icons ) ? '<div class="social-icons ' . $class . '">' . implode( ' ', $icons ) . '</div>' : '';
	}
);


/**
 * Grab the specified data like Thumbnail URL of a publicly embeddable video hosted on Vimeo.
 *
 * @param  str $video_id The ID of a Vimeo video.
 * @param  str $data      Video data to be fetched.
 * @return str            The specified data
 */
function get_vimeo_data_from_id( $video_id, $data ) {
    // Width can be 100, 200, 295, 640, 960 or 1280.
    $request = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/' . $video_id . '&width=960' );

    $response = wp_remote_retrieve_body( $request );

    $video_array = json_decode( $response, true );

    return $video_array[ $data ];
}

/**
 * Add custom Gutenberg admin styles.
 */
function cb_gutenberg_admin_styles() {
    echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1040px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }
            .block-editor-page #wpwrap {
                overflow-y: auto !important;
            }

            @media (min-width:992px) {
                .acf-block-component .acf-checkbox-list {
                    columns: 1;
                }
            }
            @media (min-width:1200px) {
                .acf-block-component .acf-checkbox-list {
                    columns: 1;
                }
            }
        </style>
    ';
}
add_action( 'admin_head', 'cb_gutenberg_admin_styles' );


// Disable full-screen editor view by default.
if ( is_admin() ) {
	/**
	 * Disable editor fullscreen by default.
	 */
	function cb_disable_editor_fullscreen_by_default() {
		$script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
		wp_add_inline_script( 'wp-blocks', $script );
	}
	add_action( 'enqueue_block_editor_assets', 'cb_disable_editor_fullscreen_by_default' );
}

// Force all ACF blocks to always display in edit mode in the block editor.
// The 'mode' registration key only sets the default for new blocks; existing blocks
// have their mode persisted in the serialised HTML comment. This JS subscriber
// watches the block store and resets any ACF block that drifts to preview/auto.
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_add_inline_script(
			'wp-blocks',
			"( function () {
	var processed = {};
	wp.data.subscribe( function () {
		var select   = wp.data.select( 'core/block-editor' );
		var dispatch = wp.data.dispatch( 'core/block-editor' );
		if ( ! select || ! dispatch ) return;
		var blocks = select.getBlocks();
		( function walk( list ) {
			list.forEach( function ( block ) {
				if (
					block.name &&
					block.name.indexOf( 'acf/' ) === 0 &&
					block.attributes &&
					block.attributes.mode !== 'edit' &&
					! processed[ block.clientId ]
				) {
					processed[ block.clientId ] = true;
					dispatch.updateBlockAttributes( block.clientId, { mode: 'edit' } );
				}
				if ( block.innerBlocks && block.innerBlocks.length ) {
					walk( block.innerBlocks );
				}
			} );
		}( blocks ) );
	} );
}() );"
		);
	}
);



/**
 * Retrieves the ID of the top-level ancestor of the current post.
 *
 * This function checks if the current post has a parent. If it does, it retrieves
 * the top-level ancestor by traversing the post's ancestry. If the post has no parent,
 * it returns the current post's ID.
 *
 * @return int The ID of the top-level ancestor or the current post's ID if no parent exists.
 */
function get_the_top_ancestor_id() {
    global $post;
    if ( $post->post_parent ) {
        $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
        return $ancestors[0];
    } else {
        return $post->ID;
    }
}

/**
 * Encodes a string into JSON format with additional escaping for special characters.
 *
 * This function replaces specific characters in the input string with their escaped equivalents
 * before encoding it into JSON format.
 *
 * @param string $content The input string to encode.
 * @return string The JSON-encoded string with additional escaping.
 */
function cb_json_encode( $content ) {
    $escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
    $replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
    $result       = str_replace( $escapers, $replacements, $content );
    $result       = wp_json_encode( $result );
    return $result;
}

/**
 * Convert time string to ISO 8601 duration format.
 *
 * @param string $time_string The time string to convert.
 * @return string The ISO 8601 duration format.
 */
function cb_time_to_8601( $time_string ) {
    $time   = explode( ':', $time_string );
    $output = 'PT' . $time[0] . 'H' . $time[1] . 'M' . $time[2] . 'S';
    return $output;
}

/**
 * Slugify text for URL-safe strings.
 *
 * @param string $text The text to slugify.
 * @param string $divider The divider character to use.
 * @return string The slugified text.
 */
function cbslugify( $text, string $divider = '-' ) {
    // Replace non letter or digits by divider.
    $text = preg_replace( '~[^\pL\d]+~u', $divider, $text );

    // Transliterate.
    $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

    // Remove unwanted characters.
    $text = preg_replace( '~[^-\w]+~', '', $text );

    // Trim.
    $text = trim( $text, $divider );

    // Remove duplicate divider.
    $text = preg_replace( '~-+~', $divider, $text );

    // Lowercase.
    $text = strtolower( $text );

    if ( empty( $text ) ) {
        return 'n-a';
    }

    return $text;
}

/**
 * Generate a random string of specified length.
 *
 * @param int    $length The length of the random string.
 * @param string $keyspace The characters to use for the random string.
 * @return string The random string.
 * @throws \RangeException If length is less than 1.
 */
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ( $length < 1 ) {
        throw new \RangeException( 'Length must be a positive integer' );
    }
    $pieces = array();
    $max    = mb_strlen( $keyspace, '8bit' ) - 1;
    for ( $i = 0; $i < $length; ++$i ) {
        $pieces[] = $keyspace[ random_int( 0, $max ) ];
    }
    return implode( '', $pieces );
}

/**
 * Generate social share buttons for a post.
 *
 * @param int $id The post ID.
 * @return string The HTML for social share buttons.
 */
function cb_social_share( $id ) {
    ob_start();
    $url = get_the_permalink( $id );

	?>
    <div class="text-larger text--yellow mb-5">
        <div class="h4 text-dark">Share</div>
        <a target='_blank' href='https://twitter.com/share?url=<?php echo esc_url( $url ); ?>' class="mr-2" aria-label="Share on Twitter"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
        <a target='_blank' href='http://www.linkedin.com/shareArticle?url=<?php echo esc_url( $url ); ?>' class="mr-2" aria-label="Share on LinkedIn"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
        <a target='_blank' href='http://www.facebook.com/sharer.php?u=<?php echo esc_url( $url ); ?>' aria-label="Share on Facebook"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 3.667h-3.533v7.98H9.101z"/></svg></a>
    </div>
    <?php

    $out = ob_get_clean();
    return $out;
}

/**
 * Enable HSTS header for security.
 */
function enable_strict_transport_security_hsts_header() {
    header( 'Strict-Transport-Security: max-age=31536000' );
}
add_action( 'send_headers', 'enable_strict_transport_security_hsts_header' );

/**
 * Convert field content to an HTML list.
 *
 * @param string $field The field content to convert.
 * @return string The HTML list.
 */
function cb_list( $field ) {
    ob_start();
    $field   = strip_tags( $field, '<br />' );
    $bullets = preg_split( "/\r\n|\n|\r/", $field );
    foreach ( $bullets as $b ) {
        if ( '' === $b ) {
            continue;
        }
		?>
        <li><?php echo esc_html( $b ); ?></li>
		<?php
    }
    return ob_get_clean();
}

/**
 * Convert field content to a list of mailto links.
 *
 * @param string $field The field content containing email addresses.
 * @return string The HTML output of mailto links.
 */
function cb_list_to_email( $field ) {
    ob_start();
    $field     = strip_tags( $field, '<br />' );
    $addresses = preg_split( "/\r\n|\n|\r/", $field );
    foreach ( $addresses as $address ) {
        if ( '' === $address ) {
            continue;
        }
		?>
	<a href="mailto:<?php echo esc_html( antispambot( $address ) ); ?>"><?php echo esc_html( antispambot( $address ) ); ?></a><br>
		<?php
	}
	return ob_get_clean();
}


/**
 * Converts a file size in bytes to a human-readable format.
 *
 * @param int $size      The size in bytes.
 * @param int $precision The number of decimal places to round to. Default is 2.
 * @return string        The formatted size with appropriate unit (e.g., KB, MB).
 */
function format_bytes( $size, $precision = 2 ) {
    if ( $size <= 0 ) {
        return '0 B'; // Return 0 bytes as a default value.
    }

    $base     = log( $size, 1024 );
    $suffixes = array( '', 'K', 'M', 'G', 'T' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) . ' ' . $suffixes[ floor( $base ) ];
}


/**
 * Estimate reading time in minutes.
 *
 * @param string $content The content to analyze.
 * @param int    $words_per_minute Words per minute reading speed.
 * @param bool   $with_gutenberg Whether to parse Gutenberg blocks.
 * @param bool   $formatted Whether to return formatted output.
 * @return int|string The estimated reading time.
 */
function estimate_reading_time_in_minutes( $content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false ) {
    // In case if content is build with Gutenberg parse blocks.
    if ( $with_gutenberg ) {
        $blocks       = parse_blocks( $content );
        $content_html = '';

        foreach ( $blocks as $block ) {
            $content_html .= render_block( $block );
        }

        $content = $content_html;
    }

    // Remove HTML tags from string.
    $content = wp_strip_all_tags( $content );

    // When content is empty return 0.
    if ( ! $content ) {
        return 0;
    }

    // Count words containing string.
    $words_count = str_word_count( $content );

    // Calculate time for read all words and round.
    $minutes = ceil( $words_count / $words_per_minute );

    if ( $formatted ) {
        $minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise( $minutes, 'minute' ) . '</p>';
    }

    return $minutes;
}

/**
 * Pluralise a word based on quantity.
 *
 * @param int         $quantity The quantity to check.
 * @param string      $singular The singular form.
 * @param string|null $plural The plural form (optional).
 * @return string The pluralized word.
 */
function pluralise( $quantity, $singular, $plural = null ) {
    if ( 1 === $quantity || ! strlen( $singular ) ) {
		return $singular;
    }
    if ( null !== $plural ) {
		return $plural;
    }

    $last_letter = strtolower( $singular[ strlen( $singular ) - 1 ] );
    switch ( $last_letter ) {
        case 'y':
            return substr( $singular, 0, -1 ) . 'ies';
        case 's':
            return $singular . 'es';
        default:
            return $singular . 's';
    }
}

/**
 * Get all block names from post content.
 *
 * @param int $id The post ID.
 * @return array Array of block names.
 */
function get_all_block_names_from_content( $id ) {
    // Parse blocks from the content.
    $content     = get_post_field( 'post_content', $id );
    $blocks      = parse_blocks( $content );
    $block_names = array();

    // Recursively find all block names.
    foreach ( $blocks as $block ) {
        if ( isset( $block['blockName'] ) && ! empty( $block['blockName'] ) ) {
            $block_names[] = $block['blockName'];
        }
        if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {
            $inner_block_names = get_all_block_names_from_content( serialize_blocks( $block['innerBlocks'] ) );
            $block_names       = array_merge( $block_names, $inner_block_names );
        }
    }

    // Remove duplicates and reindex.
    return array_values( array_unique( $block_names ) );
}

/**
 * Display pages in a hierarchical list.
 *
 * @param int $parent_id The parent page ID.
 * @return string The HTML for the hierarchical page list.
 */
function display_page_hierarchy( $parent_id = 0 ) {

    $posts_page_id = get_option( 'page_for_posts' );

    // Get the pages with the specified parent, sorted by title.
    $args = array(
        'post_type'   => 'page',
        'post_status' => 'publish',
        'parent'      => $parent_id,
        'sort_column' => 'post_title',  // Sort by post title for alphabetical order.
        'sort_order'  => 'ASC',          // Sort ascending (A-Z).
        'exclude'     => $posts_page_id,     // Exclude the posts page (blog index).
    );

    $pages = get_pages( $args );

    $output = '';

    if ( ! empty( $pages ) ) {
        $output .= '<ul>';
        foreach ( $pages as $page ) {
            // Check index status.
            $noindex = get_post_meta( $page->ID, '_yoast_wpseo_meta-robots-noindex', true );

            if ( '1' !== $noindex ) {
                $output .= '<li><a href="' . get_permalink( $page->ID ) . '">' . $page->post_title . '</a>';

                // Recursively display child pages, also sorted by title.
                $output .= display_page_hierarchy( $page->ID ); // Get nested child pages.

                $output .= '</li>';
            }
        }
        $output .= '</ul>';
    }

    return $output;
}

/**
 * Register the shortcode to display the hierarchical page list.
 *
 * @return string The buffered content.
 */
function register_page_list_shortcode() {
    // Start output buffering.
    ob_start();

    // Display the hierarchical list.
    echo wp_kses_post( display_page_hierarchy() );

    // Return the buffered content.
    return ob_get_clean();
}
add_shortcode( 'page_list', 'register_page_list_shortcode' );


/**
 * Filters the excerpt length.
 *
 * @param int $length The current excerpt length.
 * @return int The modified excerpt length.
 */
function custom_excerpt_length( $length ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    return 25; // Set to your desired number of words.
}
add_filter( 'excerpt_length', 'custom_excerpt_length' );

/**
 * Filters the excerpt content to modify or return it as is.
 *
 * @param string $post_excerpt The current post excerpt.
 * @return string The filtered or unmodified post excerpt.
 */
function understrap_all_excerpts_get_more_link( $post_excerpt ) {
    if ( is_admin() || ! get_the_ID() ) {
        return $post_excerpt;
    }
    return $post_excerpt;
}

/**
 * Retrieves all H2 headings from the Gutenberg content of the current page.
 *
 * This function parses the Gutenberg blocks of the current post's content,
 * extracts H2 headings, and returns them along with their IDs if available.
 *
 * @return array An array of H2 headings, each containing 'content' and 'id'.
 */
function get_gutenberg_h2_headings_from_page() {
    global $post;

    // Get the raw post content.
    $content = $post->post_content;

    // Parse the blocks in the content.
    $blocks = parse_blocks( $content );

    $h2_headings = array();
    $stack       = $blocks; // Use a stack to process blocks iteratively.

    while ( ! empty( $stack ) ) {
        $block = array_shift( $stack ); // Get the blocks in order.

        // Check if it's a heading block.
        if ( 'core/heading' === $block['blockName'] ) {
            // Check the level attribute (assume default is 2 if not set).
            $level = $block['attrs']['level'] ?? 2;

            if ( 2 === $level ) {
                // Extract the rendered HTML.
                $html = $block['innerHTML'] ?? '';

                // Extract the ID using regex.
                if ( preg_match( '/<h2[^>]*id=["\']([^"\']+)["\']/', $html, $matches ) ) {
                    $id = $matches[1];
                } else {
                    $id = null;
                }

                // Extract the content.
                $content = wp_strip_all_tags( $html );

                // Add the H2 heading to the array.
                $h2_headings[] = array(
                    'content' => $content,
                    'id'      => $id,
				);
            }
        }

        // Add inner blocks to the stack.
        if ( ! empty( $block['innerBlocks'] ) ) {
            $stack = array_merge( $stack, $block['innerBlocks'] );
        }
    }

    return $h2_headings;
}

/**
 * Disable Contact Form 7 autop (no <p>/<br> wrapping).
 */
add_filter(
	'wpcf7_autop_or_not',
	function () {
		return false;
	}
);

/**
 * Disables TinyMCE cleanup to prevent stripping of JavaScript.
 *
 * @param array $options The TinyMCE options array.
 * @return array The modified TinyMCE options array.
 */
function disable_tinymce_cleanup( $options ) {
    $options['verify_html'] = false;
    return $options;
}
add_filter( 'tiny_mce_before_init', 'disable_tinymce_cleanup' );


add_action(
	'wpcf7_init',
	function () {
		wpcf7_add_form_tag(
			array( 'honeypot', 'honeypot*' ),
			'lc_cf7_honeypot_form_tag_handler',
			array(
				'name-attr'    => true,
				'do-not-store' => true,
			)
		);
	}
);

/**
 * Handles the honeypot form tag in Contact Form 7.
 *
 * Creates a hidden honeypot field that should remain empty. This helps prevent spam
 * by catching automated form submissions.
 *
 * @param WPCF7_FormTag $tag The form tag object.
 * @return string HTML for the honeypot field.
 */
function lc_cf7_honeypot_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class            = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts                 = array();
	$atts['class']        = $tag->get_class_option( $class );
	$atts['id']           = $tag->get_id_option();
	$atts['message']      = $tag->get_option( 'message', '', true );
	$atts['name']         = $tag->name;
	$atts['type']         = 'text';
	$atts['autocomplete'] = 'off';

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="contact-field" style="position: fixed; top: 0; left: 0; margin: -1px; padding: 0; height: 1px; width: 1px; clip: rect(0 0 0 0); clip-path: inset(50%%); overflow: hidden; white-space: nowrap; border: 0;">
			<label>
				<span>%1$s</span>
				<input %2$s value="" tabindex="-1">
			</label>
			%3$s
		</span>',
		esc_html( ! empty( $atts['message'] ) ? $atts['message'] : __( 'If you are human, leave this field blank.', 'lc-silverline2025' ) ),
		$atts,
		$validation_error
	);

	return $html;
}

add_filter( 'wpcf7_validate_honeypot', 'lc_cf7_honeypot_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_honeypot*', 'lc_cf7_honeypot_validation_filter', 10, 2 );

/**
 * Validates the honeypot field in Contact Form 7.
 *
 * Checks if the honeypot field has been filled out. If it has, the submission
 * is marked as spam.
 *
 * @param WPCF7_Validation $result The validation result object.
 * @param WPCF7_FormTag    $tag    The form tag object.
 * @return WPCF7_Validation The modified validation result.
 */
function lc_cf7_honeypot_validation_filter( $result, $tag ) {
	$name = $tag->name;

	// We don't need nonce verification here as this is handled by CF7.
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';

	if ( ! empty( $value ) ) {
		$result->invalidate( $tag, __( 'Spam detected.', 'lc-silverline2025' ) );
	}

	return $result;
}

/**
 * Ensure Vimeo URL has dnt=1 parameter.
 *
 * @param string $url Vimeo video URL.
 * @return string Vimeo URL with dnt=1 parameter.
 */
function cb_vimeo_url_with_dnt( $url ) {
    if ( strpos( $url, 'vimeo.com' ) === false ) {
        return $url;
    }
    // Parse URL.
    $parts = wp_parse_url( $url );
    if ( ! isset( $parts['query'] ) ) {
        // No query string, just add dnt=1.
        return $url . ( strpos( $url, '?' ) === false ? '?' : '' ) . 'dnt=1';
    }
    // Parse query string.
    parse_str( $parts['query'], $query );
    if ( isset( $query['dnt'] ) ) {
        return $url;
    }
    // Add dnt=1.
    $query['dnt'] = '1';
    // Build new query string.
    $parts['query'] = http_build_query( $query );
    // Rebuild URL.
    $new_url = $parts['scheme'] . '://' . $parts['host'];
    if ( isset( $parts['path'] ) ) {
        $new_url .= $parts['path'];
    }
    $new_url .= '?' . $parts['query'];
    if ( isset( $parts['fragment'] ) ) {
        $new_url .= '#' . $parts['fragment'];
    }
    return $new_url;
}

/**
 * REST endpoint: sideload an external image into the local media library.
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'cb-utility/v1',
			'/sideload-image',
			array(
				'methods'             => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'upload_files' );
				},
				'callback'            => function ( $request ) {
					$url     = $request->get_param( 'url' );
					$post_id = $request->get_param( 'post_id' );

					if ( empty( $url ) ) {
						return new WP_Error( 'missing_url', 'Image URL is required.', array( 'status' => 400 ) );
					}

					if ( ! current_user_can( 'upload_files' ) ) {
						return new WP_Error( 'forbidden', 'You do not have permission to upload files.', array( 'status' => 403 ) );
					}

					// Strip size suffix from IDENTITY URL for the original.
					$url_original = preg_replace( '/-\d{2,4}x\d{2,4}(\.\w+)$/', '$1', $url );

					// Sideload the image.
					$attachment_id = media_sideload_image( $url_original, $post_id, null, 'id' );

					if ( is_wp_error( $attachment_id ) ) {
						return $attachment_id;
					}

					$attachment_url = wp_get_attachment_url( $attachment_id );
					$alt           = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

					return array(
						'id'       => (int) $attachment_id,
						'url'      => $attachment_url,
						'filename' => get_post_field( 'post_name', $attachment_id ),
						'alt'      => $alt,
					);
				},
			)
		);
	}
);

/**
 * Enqueue admin-only block editor JS for the external image download feature.
 */
add_action(
	'enqueue_block_editor_assets',
	function () {
		$file = get_stylesheet_directory() . '/js/cb-sideload-image.js';
		if ( ! file_exists( $file ) ) {
			return;
		}
		wp_enqueue_script(
			'cb-sideload-image',
			get_stylesheet_directory_uri() . '/js/cb-sideload-image.js',
			array( 'wp-data', 'wp-dom-ready', 'wp-api-fetch', 'wp-element', 'wp-edit-post' ),
			filemtime( $file ),
			true
		);
	}
);

/**
 * Enqueue admin-only ACF editor fix for repeater WYSIWYG fields.
 */
add_action(
	'acf/input/admin_enqueue_scripts',
	function () {
		$file = get_stylesheet_directory() . '/js/cb-editor-fix.js';

		if ( ! file_exists( $file ) ) {
			return;
		}

		wp_enqueue_script(
			'cb-editor-fix',
			get_stylesheet_directory_uri() . '/js/cb-editor-fix.js',
			array( 'acf-input' ),
			filemtime( $file ),
			true
		);
	}
);
add_action( 'admin_footer', function() {
	echo '<div id="cb-sideload-debug" style="position:fixed;bottom:5px;right:5px;background:#000;color:#0f0;padding:4px 8px;font-size:12px;z-index:999999;display:none;">CB SIDELOAD: ' . ( wp_script_is( 'cb-sideload-image', 'enqueued' ) ? 'ENQUEUED' : 'NOT ENQUEUED' ) . '</div>';
}, 999 );
add_action( 'admin_head', function() {
	echo '<style>#cb-sideload-debug{display:' . ( isset( $_GET['cb_debug'] ) ? 'block' : 'none' ) . '}</style>';
}, 999 );

/**
 * Load, augment, and sanitize an SVG from attachment ID or local file path.
 *
 * @param int|string   $svg_source Attachment ID or local SVG file path/URL.
 * @param string|array $classes    Optional CSS class or classes to add to the root SVG element.
 * @param string|int   $width      Optional width attribute for the root SVG element.
 * @param string|int   $height     Optional height attribute for the root SVG element.
 * @return string Sanitized SVG markup, or an empty string if unavailable.
 */
function cb_sanitise_svg( $svg_source, $classes = '', $width = '', $height = '' ) {
	$is_dimension = static function ( $value ) {
		if ( ! is_scalar( $value ) ) {
			return false;
		}

		$value = trim( (string) $value );

		if ( '' === $value ) {
			return false;
		}

		return 1 === preg_match( '/^\d+(?:\.\d+)?(?:px|em|rem|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in)?$/i', $value );
	};

	$sanitize_dimension = static function ( $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( 1 === preg_match( '/^\d+(?:\.\d+)?(?:px|em|rem|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in)?$/i', $value ) ) {
			return $value;
		}

		return '';
	};

	// Support shorthand calls without classes.
	// Numeric values in the classes position are treated as width and height.
	if ( ! is_array( $classes ) && $is_dimension( $classes ) ) {
		if ( '' === $width && '' === $height ) {
			$width   = $classes;
			$classes = '';
		} elseif ( '' === $height && $is_dimension( $width ) ) {
			$height  = $width;
			$width   = $classes;
			$classes = '';
		}
	}

	$width  = $sanitize_dimension( $width );
	$height = $sanitize_dimension( $height );

	if ( empty( $svg_source ) ) {
		return '';
	}

	$file_path = '';

	if ( is_numeric( $svg_source ) ) {
		$attachment_id = (int) $svg_source;

		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return '';
		}

		$file_path = get_attached_file( $attachment_id );
	} elseif ( is_string( $svg_source ) ) {
		$file_path = trim( $svg_source );

		$theme_uri = get_stylesheet_directory_uri();
		if ( 0 === strpos( $file_path, $theme_uri ) ) {
			$file_path = get_stylesheet_directory() . substr( $file_path, strlen( $theme_uri ) );
		}

		$parent_theme_uri = get_template_directory_uri();
		if ( 0 === strpos( $file_path, $parent_theme_uri ) ) {
			$file_path = get_template_directory() . substr( $file_path, strlen( $parent_theme_uri ) );
		}

		if ( ! preg_match( '#^([a-zA-Z]:[\\/]|/)#', $file_path ) ) {
			$theme_relative = ltrim( $file_path, '/\\' );
			$child_path     = trailingslashit( get_stylesheet_directory() ) . $theme_relative;
			$parent_path    = trailingslashit( get_template_directory() ) . $theme_relative;

			if ( file_exists( $child_path ) ) {
				$file_path = $child_path;
			} elseif ( file_exists( $parent_path ) ) {
				$file_path = $parent_path;
			}
		}

		if ( 'svg' !== strtolower( (string) pathinfo( $file_path, PATHINFO_EXTENSION ) ) ) {
			return '';
		}
	} else {
		return '';
	}

	if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
		return '';
	}

	$svg_markup = file_get_contents( $file_path );

	if ( false === $svg_markup || '' === trim( $svg_markup ) ) {
		return '';
	}

	$classes = is_array( $classes ) ? $classes : preg_split( '/\s+/', trim( (string) $classes ) );
	$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

	$svg_markup = preg_replace( '/<\?xml.*?\?>/i', '', $svg_markup );
	$svg_markup = preg_replace_callback(
		'/<svg\b([^>]*)>/i',
		static function ( $matches ) use ( $classes, $width, $height ) {
			$attributes = $matches[1];

			if ( preg_match( '/\bclass=("|\')(.*?)(\1)/i', $attributes, $class_matches ) ) {
				$existing_classes = preg_split( '/\s+/', trim( $class_matches[2] ) );
				$all_classes      = array_unique( array_filter( array_merge( $existing_classes, $classes ) ) );
				$attributes       = preg_replace( '/\bclass=("|\')(.*?)(\1)/i', 'class="' . esc_attr( implode( ' ', $all_classes ) ) . '"', $attributes, 1 );
			} elseif ( ! empty( $classes ) ) {
				$attributes .= ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
			}

			if ( ! preg_match( '/\baria-hidden=/i', $attributes ) ) {
				$attributes .= ' aria-hidden="true"';
			}

			if ( ! preg_match( '/\bfocusable=/i', $attributes ) ) {
				$attributes .= ' focusable="false"';
			}

			if ( '' !== $width ) {
				if ( preg_match( '/\bwidth=("|\')(.*?)(\1)/i', $attributes ) ) {
					$attributes = preg_replace( '/\bwidth=("|\')(.*?)(\1)/i', 'width="' . esc_attr( $width ) . '"', $attributes, 1 );
				} else {
					$attributes .= ' width="' . esc_attr( $width ) . '"';
				}
			}

			if ( '' !== $height ) {
				if ( preg_match( '/\bheight=("|\')(.*?)(\1)/i', $attributes ) ) {
					$attributes = preg_replace( '/\bheight=("|\')(.*?)(\1)/i', 'height="' . esc_attr( $height ) . '"', $attributes, 1 );
				} else {
					$attributes .= ' height="' . esc_attr( $height ) . '"';
				}
			}

			return '<svg' . $attributes . '>';
		},
		$svg_markup,
		1
	);

	$allowed_svg_tags = array(
		'svg'            => array(
			'aria-hidden'         => true,
			'class'               => true,
			'data-name'           => true,
			'fill'                => true,
			'focusable'           => true,
			'height'              => true,
			'id'                  => true,
			'preserveaspectratio' => true,
			'role'                => true,
			'stroke'              => true,
			'stroke-width'        => true,
			'viewbox'             => true,
			'width'               => true,
			'xmlns'               => true,
			'xmlns:xlink'         => true,
		),
		'g'              => array(
			'class'             => true,
			'clip-path'         => true,
			'data-name'         => true,
			'fill'              => true,
			'fill-rule'         => true,
			'id'                => true,
			'mask'              => true,
			'opacity'           => true,
			'stroke'            => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'stroke-width'      => true,
			'transform'         => true,
		),
		'path'           => array(
			'class'             => true,
			'clip-rule'         => true,
			'd'                 => true,
			'fill'              => true,
			'fill-rule'         => true,
			'opacity'           => true,
			'stroke'            => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'stroke-width'      => true,
			'transform'         => true,
		),
		'circle'         => array(
			'class'        => true,
			'cx'           => true,
			'cy'           => true,
			'fill'         => true,
			'opacity'      => true,
			'r'            => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'ellipse'        => array(
			'class'        => true,
			'cx'           => true,
			'cy'           => true,
			'fill'         => true,
			'opacity'      => true,
			'rx'           => true,
			'ry'           => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'line'           => array(
			'class'          => true,
			'stroke'         => true,
			'stroke-linecap' => true,
			'stroke-width'   => true,
			'x1'             => true,
			'x2'             => true,
			'y1'             => true,
			'y2'             => true,
		),
		'polygon'        => array(
			'class'        => true,
			'fill'         => true,
			'opacity'      => true,
			'points'       => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'polyline'       => array(
			'class'        => true,
			'fill'         => true,
			'opacity'      => true,
			'points'       => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'rect'           => array(
			'class'        => true,
			'fill'         => true,
			'height'       => true,
			'opacity'      => true,
			'rx'           => true,
			'ry'           => true,
			'stroke'       => true,
			'stroke-width' => true,
			'width'        => true,
			'x'            => true,
			'y'            => true,
		),
		'defs'           => array(),
		'style'          => array(
			'type' => true,
		),
		'clippath'       => array(
			'id' => true,
		),
		'mask'           => array(
			'id'        => true,
			'maskunits' => true,
			'x'         => true,
			'y'         => true,
			'width'     => true,
			'height'    => true,
		),
		'lineargradient' => array(
			'id' => true,
			'x1' => true,
			'x2' => true,
			'y1' => true,
			'y2' => true,
		),
		'radialgradient' => array(
			'cx' => true,
			'cy' => true,
			'fx' => true,
			'fy' => true,
			'id' => true,
			'r'  => true,
		),
		'stop'           => array(
			'offset'       => true,
			'stop-color'   => true,
			'stop-opacity' => true,
		),
		'title'          => array(),
		'desc'           => array(),
		'use'            => array(
			'href'       => true,
			'width'      => true,
			'height'     => true,
			'x'          => true,
			'xlink:href' => true,
			'y'          => true,
		),
	);

	return wp_kses( $svg_markup, $allowed_svg_tags );
}
