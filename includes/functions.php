<?php

/**
 * -----------------------------------------------------------------------------
 * Purpose: Declare non namespaced functions for this plugin.
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	die();
}

/**
 * Get all substrings within text that are found between two other, specified strings
 *
 * Avoids parsing HTML with regex
 *
 * Returns an array
 *
 * See https://stackoverflow.com/a/27078384
 */
function cp_get_markdown_contents( $str, $startDelimiter, $endDelimiter ) {
	$contents = [];
	$startDelimiterLength = strlen( $startDelimiter );
	$endDelimiterLength = strlen( $endDelimiter );
	$startFrom = $contentStart = $contentEnd = 0;

	while ( $contentStart = strpos( $str, $startDelimiter, $startFrom ) ) {
		$contentStart += $startDelimiterLength;
		$contentEnd = strpos( $str, $endDelimiter, $contentStart );
		if ( $contentEnd === false ) {
			break;
		}
		$contents[] = substr( $str, $contentStart, $contentEnd - $contentStart );
		$startFrom = $contentEnd + $endDelimiterLength;
	}

	return $contents;
}
