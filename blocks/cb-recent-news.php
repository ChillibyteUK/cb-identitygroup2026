<?php
/**
 * CB Recent News Block Template
 *
 * @package  cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id   = $block['id'] ?? '';
$blog_type  = get_query_var( 'blog_type', '' );
$person     = get_query_var( 'person', '' );
$theme      = get_query_var( 'theme', '' );
$theme_field = get_field( 'theme' );
$blog_types = array();
$post_limit = is_front_page() ? 6 : 3;

if ( ! empty( $theme_field ) ) {
	$theme_term = get_term( $theme_field, 'theme' );
	if ( $theme_term && ! is_wp_error( $theme_term ) ) {
		$theme = $theme_term->slug;
	}
}

if ( is_object( $theme ) && ! empty( $theme->slug ) ) {
	$theme = $theme->slug;
}

if ( ! empty( $blog_type ) ) {
	$blog_types = is_array( $blog_type ) ? $blog_type : array( $blog_type );
}

// If blog_type is not set, use selected category or categories from the block field.
if ( empty( $blog_types ) ) {
	$cat_field = get_field( 'category' );
	$cat_ids   = is_array( $cat_field ) ? $cat_field : ( ! empty( $cat_field ) ? array( $cat_field ) : array() );

	foreach ( $cat_ids as $cat_id ) {
		$category_term = get_term( $cat_id );
		if ( $category_term && ! is_wp_error( $category_term ) ) {
			$blog_types[] = $category_term->slug;
		}
	}
}

$blog_types = array_values( array_unique( array_filter( $blog_types ) ) );
$primary_blog_type = $blog_types[0] ?? '';

$background    = 'has-primary-black-background-color';
$section_title = 'has-primary-black-background-color';
$arrow         = '/img/arrow-n600.svg';
$block_title   = 'PRESS';

switch ( $primary_blog_type ) {
	case 'news':
		$block_title   = 'NEWS';
		$background    = 'has-primary-black-background-color';
		$section_title = 'has-primary-black-background-color';
		break;
	case 'tmc':
		$block_title   = 'OTHER RELATED ARTICLES';
		$background    = 'has-ink-background-color';
		$section_title = 'has-neutral-800-background-color';
		break;
	case 'people':
		$block_title   = 'MORE FROM OUR PEOPLE';
		$background    = 'has-raspberry-background-color';
		$section_title = 'has-raspberry-450-background-color';
		break;
	default:
		$block_title   = 'LATEST NEWS AND INSIGHTS';
		$background    = 'has-primary-black-background-color';
		$section_title = 'has-primary-black-background-color';
		break;
}

$args = array(
	'post_type'      => 'post',
	'post_status'    => array( 'publish' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
	'posts_per_page' => $post_limit,
	'post__not_in'   => array( get_the_ID() ),
);

$tax_query = array();

if ( ! empty( $blog_types ) ) {
	$args['category_name'] = implode( ',', $blog_types );
}

if ( ! empty( $person ) ) {
	$tax_query[] = array(
		'taxonomy' => 'person',
		'field'    => 'slug',
		'terms'    => $person->slug,
	);
	$args['category_name'] = null;
}

if ( ! empty( $tax_query ) ) {
	$args['tax_query'] = $tax_query;
}

$posts = array();

if ( ! empty( $theme ) ) {
	$priority_args = $args;
	$priority_tax_query = $priority_args['tax_query'] ?? array();
	$priority_tax_query[] = array(
		'taxonomy' => 'theme',
		'field'    => 'slug',
		'terms'    => $theme,
	);
	if ( count( $priority_tax_query ) > 1 ) {
		$priority_tax_query['relation'] = 'AND';
	}
	$priority_args['tax_query'] = $priority_tax_query;

	$priority_query = new WP_Query( $priority_args );
	if ( $priority_query->have_posts() ) {
		$posts = $priority_query->posts;
	}
	wp_reset_postdata();

	if ( count( $posts ) < $post_limit ) {
		$fallback_args = $args;
		$fallback_args['posts_per_page'] = $post_limit - count( $posts );
		$fallback_args['post__not_in'] = array_merge(
			$fallback_args['post__not_in'],
			wp_list_pluck( $posts, 'ID' )
		);

		$fallback_query = new WP_Query( $fallback_args );
		if ( $fallback_query->have_posts() ) {
			$posts = array_merge( $posts, $fallback_query->posts );
		}
		wp_reset_postdata();
	}

	$q = new WP_Query(
		array(
			'post_type'      => 'post',
			'post__in'       => wp_list_pluck( $posts, 'ID' ),
			'orderby'        => 'post__in',
			'posts_per_page' => $post_limit,
		)
	);
} else {
	$q = new WP_Query( $args );
}

if ( ! $q->have_posts() ) {
	wp_reset_postdata();
	return;
}


?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-recent-news <?= esc_attr( $background ); ?>">
	<div class="cb-recent-news__pre-title <?= esc_attr( $section_title ); ?>">
		<div class="id-container px-4 px-md-5">
			<?= esc_html( $block_title ); ?>
		</div>
	</div>

	<div class="id-container">
		<div class="insight-type-grid grid-type-1 id-container px-4 px-md-5 py-5">
			<div class="row g-5" data-aos-stagger-group>
			<?php
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
						case 4:
							$col_class = 'col-md-6 insight-type-grid__card-4';
							break;
						case 5:
							$col_class = 'col-md-3 insight-type-grid__card-5';
							break;
						case 6:
							$col_class = 'col-md-3 insight-type-grid__card-6';
							break;
						default:
							$col_class = 'col-md-6';
							break;
				}

				?>
			<div class="<?php echo esc_attr( $col_class ); ?>" data-aos="fade">			
				<?php $card_themes = wp_get_post_terms( get_the_ID(), 'theme', array( 'fields' => 'slugs' ) ); ?>
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="insight-type-grid__card" data-theme="<?php echo esc_attr( implode( ' ', $card_themes ) ); ?>">
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
						<?php
						if ( in_array( 'news', $blog_types, true ) || in_array( 'insights', $blog_types, true ) ) {
							?>
						<div class="insight-type-grid__date d-flex align-items-center gap-2">
							<?php echo get_the_date( 'j F Y' ); ?> 
							<?= cb_sanitise_svg( get_stylesheet_directory_uri() . '/img/arrow-n600.svg', 'insight-type-grid__arrow', 14, 13 ) ?>
						</div>
							<?php
						}
						?>
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
