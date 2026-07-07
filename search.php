<?php
/**
 * Template for displaying search results.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

get_header( cb_site_template_suffix() );

$search_query = get_search_query();
?>
<main id="main">
	<section class="search-results py-5">
		<div class="container">
			<h1 class="mb-4">Search Results for: "<?= esc_html( $search_query ); ?>"</h1>
			<div class="row">
				<div class="col-lg-3">
					<div class="position-sticky pt-4">
						<a href="<?= esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn--mid-blue">Back to All Insights</a>
					</div>
				</div>
				<div class="col-lg-9">
			<?php if ( have_posts() ) : ?>
				<p class="mb-4">Found <?= esc_html( $wp_query->found_posts ); ?> result<?= 1 !== $wp_query->found_posts ? 's' : ''; ?></p>
				
				<div class="search-results__list">
					<?php
					while ( have_posts() ) :
						the_post();

						// Skip the posts index page.
						if ( get_the_ID() === (int) get_option( 'page_for_posts' ) ) {
							continue;
						}

						// Get post type and category.
						$current_post_type = get_post_type();
						$post_type_obj     = get_post_type_object( $current_post_type );
						$post_type_name    = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst( $current_post_type );

						// Get categories for posts.
						$categories     = get_the_category();
						$first_category = ! empty( $categories ) ? $categories[0] : null;
						$catname        = $first_category ? str_replace( ' PDF', '', $first_category->name ) : $post_type_name;
						$cat_slug       = $first_category ? $first_category->slug : strtolower( $current_post_type );

						// Determine link and target.
						$plink  = get_permalink();
						$target = '_self';

						if ( $first_category ) {
							if ( 'research' === $first_category->slug ) {
								$pdf = get_field( 'pdf', get_the_ID() );
								if ( $pdf ) {
									$plink  = wp_get_attachment_url( $pdf );
									$target = '_blank';
								}
							}
							if ( 'video' === $first_category->slug || 'podcast' === $first_category->slug ) {
								$video_link = get_field( 'video_link', get_the_ID() );
								if ( $video_link && ! ( str_contains( $video_link, 'youtube.com' ) || str_contains( $video_link, 'vimeo.com' ) ) ) {
									$plink  = $video_link;
									$target = '_blank';
								}
							}
						}

						// Get excerpt with search term highlighted.
						$excerpt     = get_the_excerpt();
						$raw_content = get_the_content();

						// Get all searchable content.
						$all_content = '';

						// Add post content.
						if ( ! empty( $raw_content ) ) {
							$all_content .= wp_strip_all_tags( strip_shortcodes( apply_filters( 'the_content', $raw_content ) ) );
						}

						// Add post title to searchable content.
						$all_content .= ' ' . get_the_title();

						// Build the excerpt.
						if ( empty( $excerpt ) ) {
							if ( ! empty( $all_content ) ) {
								$excerpt = wp_trim_words( $all_content, 30, '...' );
							} else {
								$excerpt = 'No content available';
							}
						}

						// Find and highlight search term.
						if ( ! empty( $search_query ) && ! empty( $all_content ) ) {
							$pos = stripos( $all_content, $search_query );

							if ( false !== $pos ) {
								// Extract context around match.
								$start   = max( 0, $pos - 100 );
								$length  = 200 + strlen( $search_query );
								$excerpt = substr( $all_content, $start, $length );

								if ( $start > 0 ) {
									$excerpt = '...' . ltrim( $excerpt );
								}
								if ( strlen( $all_content ) > $start + $length ) {
									$excerpt = rtrim( $excerpt ) . '...';
								}
							}
						}

						$highlighted = preg_replace( '/(' . preg_quote( $search_query, '/' ) . ')/i', '<mark>$1</mark>', $excerpt );

						// Also highlight in title.
						$post_title        = get_the_title();
						$highlighted_title = preg_replace( '/(' . preg_quote( $search_query, '/' ) . ')/i', '<mark>$1</mark>', $post_title );

						// Add search term as URL parameter for JS highlighting.
						$url         = $plink;
						$link_target = $target;
						if ( ! empty( $search_query ) ) {
							$separator = strpos( $url, '?' ) !== false ? '&' : '?';
							$url      .= $separator . 'highlight=' . rawurlencode( $search_query );
						}
						?>
						<article class="search-result-item">
							<a href="<?= esc_url( $url ); ?>" target="<?= esc_attr( $link_target ); ?>" class="search-result-item__link">
								<div class="search-result-item__meta">
									<span class="category <?= esc_attr( $cat_slug ); ?>"><?= esc_html( $catname ); ?></span>
								</div>
								<div class="search-result-item__content">
									<h2 class="search-result-item__title">
										<?= wp_kses_post( $highlighted_title ); ?>
									</h2>
									<div class="search-result-item__excerpt"><?= wp_kses_post( $highlighted ); ?></div>
								</div>
							</a>
						</article>
						<?php
					endwhile;
					?>
				</div>
				
				<?php
				// Pagination.
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( '&laquo; Previous', 'cb-identitygroup2026' ),
						'next_text' => __( 'Next &raquo;', 'cb-identitygroup2026' ),
					)
				);
				?>
				
			<?php else : ?>
				<p>No results found for "<?= esc_html( $search_query ); ?>". Please try a different search term.</p>
			<?php endif; ?>
		
			</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer( cb_site_template_suffix() );
