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

class Update {

	private $cp_directory_data = false;

	public function __construct() {

		// Load non namespaced constants and functions
		require_once 'includes/constants.php';
		require_once 'includes/functions.php';

		// Add a test menu. ToDo: remove
		add_action('admin_menu', [$this, 'create_test_menu'], 100);

		$update_plugins_hook = 'update_plugins_'.wp_parse_url(\CLASSICPRESS_DIRECTORY_INTEGRATION_URL, PHP_URL_HOST);
		add_filter($update_plugins_hook, [$this, 'update_uri_filter'], 10, 4);

		// Register hooks for activation, deactivation, and uninstallation.
		register_uninstall_hook(__FILE__,    [__CLASS__, 'uninstall_plugin']);
		register_activation_hook(__FILE__,   [$this, 'activate_plugin']);
		register_deactivation_hook(__FILE__, [$this, 'deactivate_plugin']);


	}

	// Add a test menu. ToDo: remove
	public function create_test_menu() {
		if (!current_user_can('manage_options')) {
			return;
		}
		$page = add_menu_page(
			'Directory tests',
			'Directory tests',
			'manage_options',
			'classicpress-directory-integration-test',
			[$this, 'render_test_page']
		);
	}

	// Add a test menu. ToDo: remove
	public function render_test_page () {
		echo '<h1>Welcome to the sandbox</h1>';
		echo '<pre>';
		// PLAY THERE
		$x=$this->get_directory_data();
		var_dump($x);
		$y=$this->get_cp_plugins();
		var_dump($y);
		// version_compare($plugin['Version'], $data['Version']) >= 0)
		$z=version_compare('1.2.0', '1.5');
		var_dump($z);
		echo wp_parse_url(\CLASSICPRESS_DIRECTORY_INTEGRATION_URL, PHP_URL_HOST);
		// END OF GAMES
		echo '</pre>';
	}

	// Get all installed ClassicPress plugin
	private function get_cp_plugins() {
		$all_plugins = get_plugins();
		$cp_plugins  = [];
		foreach ($all_plugins as $slug => $plugin) {
			if (!array_key_exists('UpdateURI',$plugin)) {
				continue;
			}
			if (strpos($plugin['UpdateURI'], \CLASSICPRESS_DIRECTORY_INTEGRATION_URL) !== 0) {
				continue;
			}
			$cp_plugins[dirname($slug)] = [
				'WPSlug'      => $slug,
				'Version'     => $plugin['Version'],
				'RequiresPHP' => array_key_exists('RequiresPHP',$plugin) ? $plugin['RequiresPHP'] : null,
				'RequiresCP'  => array_key_exists('RequiresCP',$plugin) ? $plugin['RequiresCP'] : null,
			];
		}
		return $cp_plugins;
	}

	// Get data from the directory for all installed ClassicPress plugin
	private function get_directory_data($force = false) {

		// Try to get stored data
		if (!$force && $this->cp_directory_data !== false) {
			// We have it in memory
			return $this->cp_directory_data;
		}
		$this->cp_directory_data = get_transient('cpdi_directory_data');
		if (!$force && $this->cp_directory_data !== false) {
			// We have it in transient
			return $this->cp_directory_data;
		}

		// Query the directory
		$plugins  = $this->get_cp_plugins();
		$endpoint = \CLASSICPRESS_DIRECTORY_INTEGRATION_URL.'plugins?byslug='.implode(',', array_keys($plugins));
		$response = wp_remote_get($endpoint, ['user-agent' => classicpress_user_agent(true)]);

		if (is_wp_error($response) || empty($response['response']) || $response['response']['code'] != '200') {
			return false;
		}

		$data_from_dir = json_decode(wp_remote_retrieve_body($response), true);
		$data = [];

		foreach ($data_from_dir as $single_data) {
			$data[$single_data['meta']['slug']] = [
				'Download'    => $single_data['meta']['download_link'],
				'Version'     => $single_data['meta']['current_version'],
				'RequiresPHP' => $single_data['meta']['requires_php'],
				'RequiresCP'  => $single_data['meta']['requires_cp'],
			];
		}

		$this->cp_directory_data = $data;
		set_transient('cpdi_directory_data', $this->cp_directory_data, 3 * HOUR_IN_SECONDS);
		return $this->cp_directory_data;

	}

	// Filter to trigger updates using Update URI header
	public function update_uri_filter($update, $plugin_data, $plugin_file, $locales) {

		// codepotent_php_error_log_viewer_log($plugin_data);
		/*
		Array
			(
				[Name] => ClassicPress Directory Integration
				[PluginURI] => https://www.classicpress.net
				[Version] => 0.1.0
				[Description] => Desc.
				[Author] => ClassicPress Contributors
				[AuthorURI] => https://www.classicpress.net
				[TextDomain] => classicpress-directory-integration
				[DomainPath] => /languages
				[Network] =>
				[RequiresWP] =>
				[RequiresCP] => 1.5
				[RequiresPHP] => 5.6
				[UpdateURI] => https://directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration
				[Title] => ClassicPress Directory Integration
				[AuthorName] => ClassicPress Contributors
			)
		*/
		// codepotent_php_error_log_viewer_log($plugin_file);
		/*
		classicpress-directory-integration/classicpress-directory-integration.php
		*/


		// https://developer.wordpress.org/reference/hooks/update_plugins_hostname/


		// Get the slug from Update URI
		if (preg_match('/plugins\?byslug=(.*)/', $plugin_data['UpdateURI'], $matches) !== 1) {
			return false;
		}

		// Check if the slug matches plugin file
		if (!isset($matches[1]) || dirname($plugin_file) !== $matches[1]) {
			return false;
		}
		$slug = $matches[1];

		// Check if we have that plugin in installed ones
		$plugins  = $this->get_cp_plugins();
		if (!array_key_exists($slug, $plugins)) {
			return false;
		}

		// Check if we have that plugin in directory ones
		$dir_data = $this->get_directory_data();
		if (!array_key_exists($slug, $dir_data)) {
			return false;
		}

		$plugin = $plugins[$slug];
		$data   = $dir_data[$slug];

		if (version_compare($plugin['Version'], $data['Version']) >= 0) {
			// No updates available
			return false;
		}

		$update = [
			'slug'         => $plugin_file,
			'version'      => $data['Version'],
			'package'      => $data['Download'],
			'requires_php' => $data['RequiresPHP'],
		];

		return $update;
	}

	public function activate_plugin() {

		// No permission to activate plugins? Bail.
		if (!current_user_can('activate_plugins')) {
			return;
		}

		// Refresh data from directory
		get_directory_data(true);

	}

	public function deactivate_plugin() {

		// No permission to activate plugins? None to deactivate either. Bail.
		if (!current_user_can('activate_plugins')) {
			return;
		}


	}

	public static function uninstall_plugin() {

		// No permission to delete plugins? Bail.
		if (!current_user_can('delete_plugins')) {
			return;
		}

		// Delete options related to the plugin.

	}

}

// Make awesome all the errors.
new Update;
