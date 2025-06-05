<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name:  ClassicPress Directory Integration
 * Description:  Install and update plugins and themes from ClassicPress directory.
 * Version:      1.1.5
 * Author:       ClassicPress Contributors
 * Author URI:   https://www.classicpress.net
 * Plugin URI:   https://www.classicpress.net
 * Text Domain:  classicpress-directory-integration
 * Domain Path:  /languages
 * Requires PHP: 7.4
 * Requires CP:  2.0
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 */

// Declare the namespace.
namespace ClassicPress\Directory;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Bail if on WordPress
if ( ! function_exists( 'classicpress_version' ) ) {
	add_action( 'admin_init', 'ClassicPress\Directory\deactivate_plugin_now' );
	add_action( 'admin_notices', 'ClassicPress\Directory\error_is_wp' );
	unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return;
}

function deactivate_plugin_now() {
	if ( is_plugin_active( 'classicpress-directory-integration/classicpress-directory-integration.php' ) ) {
		deactivate_plugins( 'classicpress-directory-integration/classicpress-directory-integration.php' );
	}
}

function error_is_wp() {
	$class   = 'notice notice-error';
	$message = __( 'ClassicPress Directory integration is a plugin meant to only work on ClassicPress sites.', 'classicpress-directory-integration' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

const DB_VERSION = 1;

// Load non namespaced constants and functions
require_once 'includes/constants.php';
require_once 'includes/functions.php';

// Load Helpers trait.
require_once 'classes/trait-helpers.php';

// Load Plugin Update functionality class.
require_once 'classes/class-plugin-update.php';
$plugin_update = new PluginUpdate();

// Load Plugin Install functionality class.
require_once 'classes/class-plugin-install.php';
$plugin_install = new PluginInstall();

// Load Theme Update functionality class.
require_once 'classes/class-theme-update.php';
$theme_update = new ThemeUpdate();

// Load Theme Install functionality class.
require_once 'classes/class-theme-install.php';
$theme_install = new ThemeInstall();

// Register text domain
function register_text_domain() {
	load_plugin_textdomain( 'classicpress-directory-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', '\ClassicPress\Directory\register_text_domain' );

// Add commands to WP-CLI
require_once 'classes/class-wpcli.php';
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_command( 'cpdi', '\ClassicPress\Directory\CPDICLI' );
}
