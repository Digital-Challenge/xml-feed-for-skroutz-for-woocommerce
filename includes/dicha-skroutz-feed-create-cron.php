<?php

if ( PHP_SAPI !== 'cli' ) {
	die( "Nice try, but this script can only be run from the command line." );
}

$flags        = getopt( '', [ 'type::' ] );
$feed_type    = $flags['type'] ?? 'skroutz';
$wp_load_path = dicha_skroutz_locate_wp_load_location( __DIR__ );

if ( empty( $wp_load_path ) ) {
	die( "Error: Could not locate wp-load.php. Ensure the WordPress root folder is accessible." );
}

require_once $wp_load_path;

$skroutz_creator = new Dicha_Skroutz_Feed_Creator( $feed_type );
$skroutz_creator->create_feed();

echo "XML file generated successfully for '$feed_type'.";


/**
 * Locate wp-load.php dynamically by traversing up the directory tree.
 *
 * @param string $start_dir Path to start looking from.
 *
 * @return string|null Returns the full path to wp-load.php, or null if it cannot be found.
 */
function dicha_skroutz_locate_wp_load_location( string $start_dir ): ?string {

	$directory = $start_dir;

	// Traverse up the directory tree looking for wp-load.php
	while ( $directory ) {
		if ( file_exists( $directory . DIRECTORY_SEPARATOR . 'wp-load.php' ) ) {
			return $directory . DIRECTORY_SEPARATOR . 'wp-load.php';
		}

		// Move one level up
		$parent = dirname( $directory );

		// If the parent directory is the same as the current, we've reached the root
		if ( $parent === $directory ) {
			break;
		}

		$directory = $parent;
	}

	// If we get here, wp-load.php was not found
	return null;
}