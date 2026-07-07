<?php
/**
 * Template Name: Page
 *
 * @package cb-identitygroup2026
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header( cb_site_template_suffix() );

?>
<main id="main" class="blog">
    <?php

    the_post();
    the_content();
	// phpcs:disable
    // $block_names = get_all_block_names_from_content(get_the_ID());
    // print_r($block_names);
	// phpcs:enable
    ?>
</main>
<?php
get_footer( cb_site_template_suffix() );
