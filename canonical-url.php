<?php
/**
 * Plugin Name: Canonical URL
 * Description: Checks if the current URL matches the canonical URL and redirects if they do not match (keeping the query parameters).
 * Requires at least: 6.6
 * Requires PHP: 7.2
 * Version: 1.0.0
 * Author: Javier Casares
 * Author URI: https://www.javiercasares.com/
 * License: GPL-2.0-or-later
 * License URI: https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain: canonical-url
 *
 * @package canonical-url
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Checks if the current URL matches the canonical URL and redirects if they do not match.
 *
 * @since 1.0.0
 *
 * @return void
 */
function canonical_url_matches() {

	// Get the canonical URL using wp_get_canonical_url().
	$canonical_url = wp_get_canonical_url();
	if ( ! $canonical_url ) {
		return;
	}

	// Sanitize the canonical URL.
	$canonical_url = esc_url_raw( $canonical_url );

	// Check if $_SERVER['HTTP_HOST'] is set.
	if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
		return;
	}

	// Check if $_SERVER['REQUEST_URI'] is set.
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	// Get the full current URL.
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . strtok( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '?' );

	// Check if the current URL matches the canonical URL.
	if ( untrailingslashit( $canonical_url ) !== untrailingslashit( $current_url ) ) {

		// Check if $_SERVER['QUERY_STRING'] is set.
		$query_string = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
		$query_string = $query_string ? '?' . $query_string : '';

		// Redirect to the canonical URL keeping the query parameters.
		wp_safe_redirect( esc_url_raw( $canonical_url . $query_string ), 301 );
		exit;
	}
}

// Hook to check the canonical URL after loading the main query.
add_action( 'wp', 'canonical_url_matches' );
