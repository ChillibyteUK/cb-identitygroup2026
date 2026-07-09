<?php
/**
 * Block template for CB Recent News — identity's own real design.
 *
 * identity's real category taxonomy (press/insights/perspectives/
 * white-paper) and single-slug-filtered query are genuinely different from
 * idtravel's (theme/person priority query, news/tmc/people categories) —
 * the base cb-recent-news.php this was forked from. Included from
 * cb-recent-news.php when cb_site_template_suffix() === 'identity'.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id  = $block['id'] ?? '';
$blog_type = get_query_var( 'blog_type', '' );

// if blog_type is not set, check get_field('category') for a term id (single or array) and use that.
if ( ! $blog_type ) {
	$cat_field = get_field( 'category' );
	$first_cat = null;
	if ( is_array( $cat_field ) && count( $cat_field ) > 0 ) {
		$first_cat = $cat_field[0];
	} elseif ( ! empty( $cat_field ) ) {
		$first_cat = $cat_field;
	}
	if ( $first_cat ) {
		$category_term = get_term( $first_cat );
		if ( $category_term && ! is_wp_error( $category_term ) ) {
			$blog_type = $category_term->slug;
		}
	}
}

$background    = '';
$section_title = '';

switch ( $blog_type ) {
	case 'press':
		$background    = 'has-purple-900-background-color';
		$section_title = 'has-purple-800-background-color';
		$arrow         = '/img/arrow-p200.svg';
		$block_title   = 'RECENT NEWS';
		break;
	case 'insights':
		$background    = 'has-neutral-1100-background-color';
		$section_title = 'has-neutral-1000-background-color';
		$arrow         = '/img/arrow-n600.svg';
		$block_title   = 'RECENT INSIGHTS';
		break;
	case 'perspectives':
		$background    = 'has-neutral-1100-background-color';
		$section_title = 'has-neutral-1000-background-color';
		$arrow         = '/img/arrow-n600.svg';
		$block_title   = 'RECENT PERSPECTIVES';
		break;
	case 'white-paper':
		$background    = 'has-neutral-1100-background-color';
		$section_title = 'has-neutral-1000-background-color';
		$arrow         = '/img/arrow-n600.svg';
		$block_title   = 'RECENT WHITE PAPERS';
		break;
	default:
		$background    = 'has-primary-black-background-color';
		$section_title = 'has-primary-black-background-color';
		$arrow         = '/img/arrow-n600.svg';
		$block_title   = 'INSIGHTS &amp; PERSPECTIVES';
		break;
}

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-latest-insights cb-recent-news <?= esc_attr( $background ); ?>">
	<div class="cb-latest-insights__pre-title <?= esc_attr( $section_title ); ?>">
		<div class="id-container pt-2 pb-1 px-4 px-md-5">
			<?= esc_html( $block_title ); ?>
		</div>
	</div>
	<div class="id-container">
		<div class="insight-type-grid grid-type-1 id-container px-4 px-md-5 py-5">
			<div class="row g-5">
			<?php
			$args = array(
				'post_type'      => 'post',
				'post_status'    => array( 'publish' ),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'category_name'  => $blog_type,
			);
			$q = new WP_Query( $args );

			$counter = 0;
			while ( $q->have_posts() ) {
				$q->the_post();
				++$counter;
				switch ( $counter ) {
					case 1:
						$col_class = 'col-md-3 insight-type-grid__card-1';
						break;
					case 2:
						$col_class = 'col-md-6 insight-type-grid__card-2';
						break;
					case 3:
						$col_class = 'col-md-3 insight-type-grid__card-3';
						break;
					default:
						$col_class = 'col-md-6';
						break;
				}
				?>
			<div class="<?php echo esc_attr( $col_class ); ?>">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="insight-type-grid__card">
					<div class="insight-type-grid__image-wrapper">
						<?php
						if ( get_the_post_thumbnail( get_the_ID() ) ) {
							echo get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'insight-type-grid__image', 'alt' => get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) ) );
						} else {
							echo '<img src="' . esc_url( get_stylesheet_directory_uri() . '/img/default-post-image.png' ) . '" alt="" class="insight-type-grid__image" />';
						}
						?>
					</div>
					<div class="insight-type-grid__content">
						<div class="insight-type-grid__category">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								echo esc_html( $categories[0]->name );
							}
							?>
						</div>
						<div class="insight-type-grid__title">
							<?php the_title(); ?>
						</div>
						<div class="insight-type-grid__date d-flex align-items-center gap-2">
							<?php echo get_the_date( 'j F Y' ); ?>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . $arrow ); ?>" width="14" height="13" alt="" />
						</div>
					</div>
				</a>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
