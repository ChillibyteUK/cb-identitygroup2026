<?php
/**
 * Admin UI for running the one-off content migrations in /migrations/.
 *
 * These scripts were originally written for `wp eval-file ... [write]` -
 * this site's hosting does not allow WP-CLI/SSH access, so every migration
 * must be runnable entirely from wp-admin instead. Rather than rewrite each
 * migration script, this file provides a WP_CLI class polyfill (only
 * defined when the real WP-CLI isn't present) so the exact same
 * `WP_CLI::log()`/`::success()`/`::warning()`/`::error()` calls the scripts
 * already use just get captured into an on-screen report instead. The
 * scripts themselves are byte-identical whether run via `wp eval-file` or
 * from this admin page.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_CLI' ) ) {
	/**
	 * Minimal stand-in for the WP_CLI class, active only outside a real
	 * WP-CLI process. Buffers messages into cb_migrations_admin_log()
	 * instead of printing to a terminal.
	 */
	class WP_CLI {

		/**
		 * Log a plain message.
		 *
		 * @param string $message Message text.
		 * @return void
		 */
		public static function log( $message ) {
			cb_migrations_admin_log( 'log', $message );
		}

		/**
		 * Log a success message.
		 *
		 * @param string $message Message text.
		 * @return void
		 */
		public static function success( $message ) {
			cb_migrations_admin_log( 'success', $message );
		}

		/**
		 * Log a warning message.
		 *
		 * @param string $message Message text.
		 * @return void
		 */
		public static function warning( $message ) {
			cb_migrations_admin_log( 'warning', $message );
		}

		/**
		 * Log an error message. The migration scripts always pass
		 * $exit = false and follow this call with their own `return`, so
		 * this never needs to halt execution itself.
		 *
		 * @param string $message Message text.
		 * @param bool   $exit    Unused - scripts always pass false and return manually.
		 * @return void
		 */
		public static function error( $message, $exit = true ) {
			cb_migrations_admin_log( 'error', $message );
		}
	}
}

/**
 * Append a message to the current migration run's on-screen log.
 *
 * @param string $type    One of log|success|warning|error.
 * @param string $message Message text.
 * @return void
 */
function cb_migrations_admin_log( $type, $message ) {
	global $cb_migrations_admin_output;
	$cb_migrations_admin_output[] = array( 'type' => $type, 'message' => $message );
}

/**
 * Register the admin menu page under Tools.
 *
 * @return void
 */
function cb_migrations_admin_menu() {
	add_management_page(
		'CB Migrations',
		'CB Migrations',
		'manage_options',
		'cb-migrations',
		'cb_migrations_admin_page'
	);
}
add_action( 'admin_menu', 'cb_migrations_admin_menu' );

/**
 * List available migration scripts, sorted by filename (their numeric prefix).
 *
 * @return array List of ['file' => basename, 'path' => full path].
 */
function cb_migrations_admin_list_scripts() {
	$dir     = CB_THEME_DIR . '/migrations';
	$results = array();

	if ( ! is_dir( $dir ) ) {
		return $results;
	}

	$files = glob( $dir . '/*.php' );
	sort( $files );

	foreach ( $files as $file ) {
		$results[] = array(
			'file' => basename( $file ),
			'path' => $file,
		);
	}

	return $results;
}

/**
 * Extract the docblock summary (first paragraph) from a migration file, for display.
 *
 * @param string $path Full file path.
 * @return string Plain-text summary.
 */
function cb_migrations_admin_get_summary( $path ) {
	$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( ! preg_match( '#/\*\*(.*?)\*/#s', $contents, $matches ) ) {
		return '';
	}

	$lines = explode( "\n", $matches[1] );
	$out   = array();

	foreach ( $lines as $line ) {
		$line = trim( preg_replace( '#^\s*\*\s?#', '', $line ) );
		if ( '' === $line && ! empty( $out ) ) {
			break; // Stop at the first blank line - just want the summary paragraph.
		}
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}

	return implode( ' ', $out );
}

/**
 * Render the admin page and handle form submissions.
 *
 * @return void
 */
function cb_migrations_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'cb-identitygroup2026' ) );
	}

	global $cb_migrations_admin_output;
	$cb_migrations_admin_output = array();

	$ran_file = '';
	$ran_mode = '';

	if ( ! empty( $_POST['cb_migration_file'] ) && check_admin_referer( 'cb_run_migration' ) ) {
		$requested_file = basename( sanitize_text_field( wp_unslash( $_POST['cb_migration_file'] ) ) );
		$path           = CB_THEME_DIR . '/migrations/' . $requested_file;

		// Only ever allow files that are actually in the migrations directory - no path traversal.
		$allowed = wp_list_pluck( cb_migrations_admin_list_scripts(), 'file' );

		if ( ! in_array( $requested_file, $allowed, true ) ) {
			cb_migrations_admin_log( 'error', 'Refused to run an unrecognised file.' );
		} else {
			$write = ! empty( $_POST['cb_migration_write'] ) && '1' === $_POST['cb_migration_write'];

			if ( $write && empty( $_POST['cb_migration_confirm_backup'] ) ) {
				cb_migrations_admin_log( 'error', 'You must confirm a database backup has been taken before running in write mode. Nothing was changed.' );
			} else {
				$args = $write ? array( 'write' ) : array();
				ob_start();
				include $path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
				$stray_output = ob_get_clean();
				if ( '' !== trim( $stray_output ) ) {
					cb_migrations_admin_log( 'log', $stray_output );
				}
				$ran_file = $requested_file;
				$ran_mode = $write ? 'write' : 'dry-run';
			}
		}
	}

	$scripts = cb_migrations_admin_list_scripts();
	?>
	<div class="wrap">
		<h1>CB Migrations</h1>
		<p>One-off content migrations for the theme consolidation. Always run <strong>Dry Run</strong> first and read its output before running <strong>Run (write)</strong> - dry run never changes the database. Take a full database backup (your host's backup/export tool) before any write run.</p>

		<?php if ( $ran_file ) : ?>
			<div class="notice notice-<?= 'write' === $ran_mode ? 'success' : 'info'; ?>">
				<p><strong><?= esc_html( $ran_file ); ?></strong> ran in <strong><?= esc_html( $ran_mode ); ?></strong> mode.</p>
			</div>
			<h2>Output</h2>
			<div style="background:#1d2327; color:#f0f0f1; padding:1rem; font-family:monospace; white-space:pre-wrap; max-height:500px; overflow:auto; border-radius:4px;">
				<?php foreach ( $cb_migrations_admin_output as $entry ) : ?>
					<?php
					$color = array(
						'log'     => '#f0f0f1',
						'success' => '#68de7c',
						'warning' => '#f0c33c',
						'error'   => '#ff6b6b',
					)[ $entry['type'] ] ?? '#f0f0f1';
					?>
					<div style="color: <?= esc_attr( $color ); ?>;"><?= esc_html( strtoupper( $entry['type'] ) . ': ' . $entry['message'] ); ?></div>
				<?php endforeach; ?>
				<?php if ( empty( $cb_migrations_admin_output ) ) : ?>
					<div>(no output)</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<h2>Available migrations</h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="width:20%;">File</th>
					<th>Summary</th>
					<th style="width:22%;">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $scripts as $script ) : ?>
					<tr>
						<td><code><?= esc_html( $script['file'] ); ?></code></td>
						<td><?= esc_html( cb_migrations_admin_get_summary( $script['path'] ) ); ?></td>
						<td>
							<form method="post" style="display:inline-block; margin-right:0.5rem;">
								<?php wp_nonce_field( 'cb_run_migration' ); ?>
								<input type="hidden" name="cb_migration_file" value="<?= esc_attr( $script['file'] ); ?>" />
								<input type="hidden" name="cb_migration_write" value="0" />
								<button type="submit" class="button">Dry Run</button>
							</form>
							<form method="post" style="display:inline-block;" onsubmit="return confirm('This will WRITE to the database. Have you taken a backup? This cannot be undone automatically.');">
								<?php wp_nonce_field( 'cb_run_migration' ); ?>
								<input type="hidden" name="cb_migration_file" value="<?= esc_attr( $script['file'] ); ?>" />
								<input type="hidden" name="cb_migration_write" value="1" />
								<label style="display:block; font-size:11px; margin-bottom:2px;">
									<input type="checkbox" name="cb_migration_confirm_backup" value="1" required /> I've backed up the database
								</label>
								<button type="submit" class="button button-primary">Run (write)</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if ( empty( $scripts ) ) : ?>
					<tr><td colspan="3">No migration scripts found in /migrations.</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
