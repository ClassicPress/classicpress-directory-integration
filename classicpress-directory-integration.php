<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: ClassicPress Directory Integration
 * Description: Desc.
 * Version: 0.1.0
 * Author: ClassicPress Contributors
 * Author URI: https://www.classicpress.net
 * Plugin URI: https://www.classicpress.net
 * Text Domain: classicpress-directory-integration
 * Domain Path: /languages
 * Requires PHP: 5.6
 * Requires CP: 1.5
 * Update URI: https://directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration
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
if (!defined('ABSPATH')) {
	die();
}

// Load non namespaced constants and functions
require_once 'includes/constants.php';
require_once 'includes/functions.php';

// Load Plugin Update functionality class.
require_once 'classes/PluginUpdate.class.php';
$plugin_update = new PluginUpdate();

// Load Plugin Install functionality class.
require_once 'classes/PluginInstall.class.php';
$plugin_install = new PluginInstall();



