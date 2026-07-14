<?php
/**
 * Button reference partial.
 *
 * Lists the theme's real button component variants. This used to regex-scan
 * compiled CSS for `.button*`, but that pulled in unrelated Bootstrap noise
 * (`.btn-outline-*`, `.accordion-button`, `.swiper-button-*`) and a stray
 * typography utility (`.button-text`, from _typography.scss — styling for
 * button-like text labels, not an actual button), while missing that
 * `.btn-id-outline-lime`/`-green` only render correctly paired with
 * Bootstrap's own `.btn` base class and `.id-button--sm` only as a modifier
 * on `.id-button` (confirmed against real usage in blocks/cb-work-index.php
 * and elsewhere, and the class rules themselves in _buttons.scss). A small
 * curated list, checked against real usage, is more correct than scraping.
 *
 * @package cb-identitygroup2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button_variants = array(
	array(
		'label' => '.id-button',
		'class' => 'id-button',
	),
	array(
		'label' => '.id-button.id-button--sm',
		'class' => 'id-button id-button--sm',
	),
	array(
		'label' => '.btn.btn-id-outline-lime',
		'class' => 'btn btn-id-outline-lime',
	),
	array(
		'label' => '.btn.btn-id-outline-green',
		'class' => 'btn btn-id-outline-green',
	),
);
?>

<style>
	.button-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 1.5rem;
		margin-top: 2rem;
	}
	.button-card {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
		font-family: monospace;
		align-items: start;
	}
	.button-preview {
		/* Real theme font for the actual button, not the card's monospace label styling. */
		font-family: var(--font-family);
	}
</style>

<div class="container-xl">
	<h2>Buttons</h2>
	<p>The theme's real button component variants (<code>.id-button</code>, <code>.btn-id-outline-*</code>), each shown with the base class it depends on.</p>

	<div class="button-grid">
		<?php
		foreach ( $button_variants as $button ) {
			?>
			<div class="button-card">
				<div class="button-preview">
					<a class="<?= esc_attr( $button['class'] ); ?>" href="#">Sample Button</a>
				</div>
				<code><?= esc_html( $button['label'] ); ?></code>
			</div>
			<?php
		}
		?>
	</div>
</div>
