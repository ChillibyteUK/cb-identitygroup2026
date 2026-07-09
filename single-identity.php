<?php
/**
 * Template for displaying single posts — identity's own real design.
 *
 * identity's real category taxonomy is press/insights/perspectives, not
 * idtravel's news/people/tmc (the shared single.php this was forked from).
 * Included from single.php when cb_site_template_suffix() === 'identity'.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;
get_header( cb_site_template_suffix() );

// get categories.
$categories     = get_the_category();
$first_category = null;
if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
	$first_category = $categories[0];
}

$category_slug = $first_category instanceof WP_Term ? $first_category->slug : 'press';
$post_style    = $category_slug;

switch ( $post_style ) {
	case 'press':
		$post_style = 'post-press';
		break;
	case 'insights':
	case 'perspectives':
		$post_style = 'post-insight';
		break;
	default:
		$post_style = 'post-press';
		break;
}

?>
<main id="main" class="single-blog <?= esc_attr( $post_style ); ?>">
	<div class="id-container pt-5 pb-4">
		<div class="post-hero-clip-group">
		<?php
		if ( get_the_post_thumbnail( get_the_ID() ) ) {
			?>
			<?=
			get_the_post_thumbnail(
				get_the_ID(),
				'full',
				array(
					'class' => 'post-hero-image',
					'alt'   => get_post_meta(
						get_post_thumbnail_id( get_the_ID() ),
						'_wp_attachment_image_alt',
						true
					),
				)
			);
			?>
			<?php
		} else {
			?>
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/default-post-image.png' ); ?>" alt="" class="post-hero-image" />
			<?php
		}
		?>
		</div>
	</div>
	<div class="category-wrapper">
		<div class="id-container px-4 px-md-5">
			<div class="category <?= esc_attr( $category_slug ); ?>"><?= esc_html( $first_category instanceof WP_Term ? $first_category->name : 'Press' ); ?></div>
		</div>
	</div>
	<div class="post-title">
		<div class="id-container px-4 px-md-5">
			<div class="row">
				<div class="col-md-9">
					<h1 class="pt-1"><?= esc_html( get_the_title() ); ?></h1>
				</div>
			</div>
		</div>
	</div>
	<div class="id-container">
		<div class="row post-content-row mb-5">
			<div class="col-md-3"></div>
			<div class="col-md-9 post-content px-4 px-md-5 ps-md-0 pe-md-5">
				<?php
				echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
	</div>
	<div class="post-date-row">
		<div class="id-container">
			<div class="row post-content-row">
				<div class="col-md-3"></div>
				<div class="col-md-9 post-content px-4 px-md-5 ps-md-0 pe-md-5">
					<div class="container post-date pt-3">
						<?= get_the_date( 'j F Y' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	set_query_var( 'blog_type', $category_slug );
	?>
	<section class="recent-news">
		<?php
		get_template_part( 'blocks/cb-recent-news' );
		?>
	</section>
	<?php

	switch ( $category_slug ) {
		case 'press':
			$cta = get_field( 'press_cta', 'option' );
			break;
		case 'insights':
		case 'perspectives':
			$cta = get_field( 'insight_cta', 'option' );
			break;
		default:
			$cta = get_field( 'press_cta', 'option' );
			break;
	}
	set_query_var( 'cta_choice', $cta );
	get_template_part( 'blocks/cb-cta' );

	?>
</main>
<?php
get_footer( cb_site_template_suffix() );
?>
