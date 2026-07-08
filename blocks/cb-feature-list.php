<?php
/**
 * Block template for CB Feature List.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="cb-feature-list id-container pb-5">
	<div class="mx-4 mx-md-5 cb-feature-list__inner py-3">
		<div class="row g-5">
			<div class="col-md-6">
				<h2 class="cb-feature-list__title"><?= esc_html( get_field( 'title' ) ); ?></h2>
			</div>
			<div class="col-md-6">
				<ul class="cb-feature-list__list">
					<?= wp_kses_post( cb_list( get_field( 'features' ) ) ); ?>
				</ul>
			</div>
		</div>
	</div>
</section>
