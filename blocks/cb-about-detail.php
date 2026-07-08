<?php
/**
 * Block template for CB About Detail.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-about-detail">
    <?php
    if ( get_field( 'pre_title' ) ) {
        ?>
    <div class="cb-about-detail__pre-title">
        <div class="id-container pt-4 pb-3 px-4 px-md-5">
			<?= esc_html( get_field( 'pre_title' ) ); ?>
		</div>
	</div>
        <?php
    }
    ?>
	<div class="id-container px-4 px-md-5">
		<?php
		if ( have_rows( 'details' ) ) {
			$c = 0;
			while ( have_rows( 'details' ) ) {
				the_row();
				$item_title  = get_sub_field( 'title' );
				$description = get_sub_field( 'description' );
				$has_description = '' !== trim( wp_strip_all_tags( (string) $description ) );
				$is_large_intro  = 0 === $c && ! $has_description;
				?>
			<div class="row cb-about-detail__row pb-5<?= $is_large_intro ? ' cb-about-detail__row--intro' : ''; ?>" data-aos="fade-up" data-aos-delay="<?= esc_attr( $c ); ?>">
				<div class="col-md-6">
					<h2 class="cb-about-detail__title"><?= wp_kses_post( $item_title ); ?></h2>
				</div>
				<div class="col-md-1"></div>
				<div class="col-md-5">
					<?php if ( $has_description ) { ?>
					<div class="cb-about-detail__description">
						<?= wp_kses_post( $description ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
				<?php
				$c += 100;
			}
		}
		?>
	</div>
</section>
