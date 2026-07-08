<?php
/**
 * Block template for CB Gradient Intro.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="cb-gradient-intro py-5">
	<div class="id-container px-4 px-md-5">
		<div class="row">
			<div class="col-md-8">
				<div class="cb-gradient-intro__content"><?= wp_kses_post( get_field( 'content' ) ); ?></div>
			</div>
		</div>
	</div>
</section>
