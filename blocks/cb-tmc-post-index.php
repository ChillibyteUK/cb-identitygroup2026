<?php
/**
 * Block template for CB TMC Post Index.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="insight-type">
	<div class="insight-type-grid tmc-index grid-type-1 id-container pb-5 px-4 px-md-5">
		<div class="row g-5">
		<?php
		$args = array(
			'post_type'      => 'post',
			'post_status'    => array( 'publish' ),
			'orderby'        => 'date',
			'order'          => 'DESC', // Descending order.
			'posts_per_page' => -1,    // Get all posts.
			// only in the tmc category.
			'tax_query'      => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => array( 'tmc' ),
				),
			),
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
				case 4:
					$col_class = 'col-md-6 insight-type-grid__card-4';
					break;
				case 5:
					$col_class = 'col-md-3 insight-type-grid__card-5';
					break;
				case 6:
					$col_class = 'col-md-3 insight-type-grid__card-6';
					break;
				case 7:
					$col_class = 'col-md-12 insight-type-grid__card-7';
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
					<div class="insight-type-grid__title">
						<?php the_title(); ?>
					</div>
					<div class="insight-type-grid__date d-flex align-items-center gap-2">
						<?= cb_sanitise_svg( get_stylesheet_directory_uri() . '/img/arrow-n600.svg', 'insight-type-grid__arrow', 14, 13 ) ?>
					</div>
				</div>
			</a>	
		</div>
			<?php
			if ( $counter >= 7 ) {
				$counter = 0;
			}
		}
		wp_reset_postdata();
		?>
		</div>
	</div>
</section>