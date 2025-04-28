<?php

namespace ClassicPress\Directory;

trait Helpers {

	/**
	 * Get all substrings within text that are found between two other, specified strings
	 *
	 * Avoids parsing HTML with regex
	 *
	 * Returns an array
	 *
	 * See https://stackoverflow.com/a/27078384
	 */
	private function get_markdown_contents( $str, $startDelimiter, $endDelimiter ) {
		$contents             = array();
		$startDelimiterLength = strlen( $startDelimiter );
		$endDelimiterLength   = strlen( $endDelimiter );
		$startFrom            = $contentStart = $contentEnd = 0;

		while ( $contentStart = strpos( $str, $startDelimiter, $startFrom ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
			$contentStart += $startDelimiterLength;
			$contentEnd    = strpos( $str, $endDelimiter, $contentStart );
			if ( $contentEnd === false ) {
				break;
			}
			$contents[] = substr( $str, $contentStart, $contentEnd - $contentStart );
			$startFrom  = $contentEnd + $endDelimiterLength;
		}

		return $contents;
	}

	/**
	 * Polyfill for json_validate
	 * The function is defined only in PHP 8 >= 8.3.0
	 */
	private static function json_validate( $json ) {
		if ( function_exists( 'json_validate') ) {
			return json_validate( $json );
		}
		return json_decode( $json ) !== null;
	}

}
