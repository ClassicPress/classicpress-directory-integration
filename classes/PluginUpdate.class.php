<?php

namespace ClassicPress\Directory;

class PluginUpdate {

	private $cp_plugins_directory_data = false;
	private $cp_plugins = false;

	public function __construct() {

		// Add a test menu. ToDo: remove
		add_action('admin_menu', [$this, 'create_test_menu'], 100);

		// Hook to check for updates
		$update_plugins_hook = 'update_plugins_'.wp_parse_url(\CLASSICPRESS_DIRECTORY_INTEGRATION_URL, PHP_URL_HOST);
		add_filter($update_plugins_hook, [$this, 'update_uri_filter'], 10, 4);

		// Deal with row meta
		add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 100, 2);
		add_filter('after_plugin_row', [$this, 'after_plugin_row'], 100, 3);
		add_filter('plugins_api_result', [$this, 'plugin_information'], 100, 3);

		// Hooks to refresh directory data
		add_action('activated_plugin', [$this, 'refresh_cp_directory_data']);

	}

	public function plugin_information($result, $action, $args) {

		if ($action !== 'plugin_information') {
			return $result;
		}
		$dir_data = $this->get_directory_data(true);
		$slug = dirname($args->slug);
		if (!array_key_exists($slug, $dir_data)) {
			return $result;
		}

		// Query the directory
		$endpoint = \CLASSICPRESS_DIRECTORY_INTEGRATION_URL.'plugins?byslug='.$slug;
		$response = wp_remote_get($endpoint, ['user-agent' => classicpress_user_agent(true)]);

		if (is_wp_error($response) || empty($response['response']) || wp_remote_retrieve_response_code($response) !== 200) {
			return false;
		}

		$data_from_dir = json_decode(wp_remote_retrieve_body($response), true);
		$data = $data_from_dir[0];

		$result = [
			'active_installs'   => (int) $data['meta']['active_installations'],
			'author'            => $data['meta']['developer_name'],
			'banners'           => $this->get_plugin_images('banner', $slug),
			'description'       => 'false',
			'icons'             => $this->get_plugin_images('icon', $slug),
			'name'              => $data['title']['rendered'],
			'requires_php'      => $data['meta']['requires_php'],
			'screenshots'       => $this->get_plugin_images('screenshot', $slug),
			'sections'          => [
				'desctiption' => $data['content']['rendered'],
				//'faq'            => 'frequently asked questions',
				//'installation'   => 'installation',
				//'screenshots'    => 'screenshots',
				//'reviews'        => 'reviews',
				//'other_notes'    => 'other notes',
				//'changelog'      => 'changelog',
			],
			'short_description' => $data['excerpt']['rendered'],
			'slug'              => null,           // null so we don't point to WP.org
			'tags'              => explode(',', $data['meta']['category_names']),
			'version'           => $data['meta']['current_version'],
			//'added' => true,                     // date
			//'author_block_count' => true,        // int
			//'author_block_rating' => true,       // int
			//'author_profile' => true,            // url
			//'compatibility' => false,            // empty array?
			//'contributors' => true,              // array( array( [profile], [avatar], [display_name] )
			//'donate_link' => true,               // url
			//'download_link' => true,             // url
			//'downloaded' => false,               // int
			//'homepage' => true,                  // url
			//'last_updated' => true,              // datetime
			//'num_ratings' => 14,            	   // int how many ratings
			//'rating' => 50,                      // int rating x 100
			//'ratings' =>[						   // unuseful?
			//	5 => 10,
			//	4 => 4,
			//	3 => 0,
			//	2 => 0,
			//	1 => 0,
			//],                   // array( [5..0] )
			//'requires' => true,                  // version string
			//'support_threads_resolved' => true,  // int
			//'support_threads' => true,           // int
			//'tested' => true,                    // version string
			//'versions' => true,                  // array( [version] url )
		];

		return (object) $result;

	}

	private function get_plugin_images($type, $plugin) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		// From Update Manager

		// Initialize.
		$images = [];

		// Need argument missing? Bail.
		if (empty($plugin)) {
			return $images;
		}

		// Not a valid size passed in? Bail.
		if (!in_array($type, ['icon', 'banner', 'screenshot'], true)) {
			return $images;
		}

		// Set path and URL to this plugin's own images directory.
		$image_path = untrailingslashit(WP_PLUGIN_DIR).'/'.$plugin.'/images';
		$image_url  = untrailingslashit(WP_PLUGIN_URL).'/'.$plugin.'/images';

		// Banner and icon images are keyed differently; it's a core thing.
		$image_qualities = [
			'icon'   => ['default', '1x',  '2x'],
			'banner' => ['default', 'low', 'high'],
		];

		// Array of dimensions for bannes and icons.
		$image_dimensions = [
			'icon'   => ['default' => '128',     '1x' => '128',      '2x' => '256'],
			'banner' => ['default' => '772x250', 'low' => '772x250', 'high' => '1544x500'],
		];

		// Handle icon and banner requests.
		if ($type === 'icon' || $type === 'banner') {
			// For SVG banners/icons; one tiny loop handles both.
			if (file_exists($image_path.'/'.$type.'.svg')) {
				foreach ($image_qualities[$type] as $key) {
					$images[$key] = $image_url.'/'.$type.'.svg';
				}
			} else {
				// Ok, no svg. How about png or jpg?
				// This loop doesn't break early, so, it favors png.
				foreach (['jpg', 'png'] as $ext) {
					// Pop keys off the end of the $images_qualities array.
					$all_keys   = $image_qualities[$type];
					$last_key   = array_pop($all_keys);
					$middle_key = array_pop($all_keys);
					// Normal size images found? Add them.
					if (file_exists($image_path.'/'.$type.'-'.$image_dimensions[$type][$middle_key].'.'.$ext)) {
						foreach ($image_qualities[$type] as $key) {
							$images[$key] = $image_url.'/'.$type.'-'.$image_dimensions[$type][$middle_key].'.'.$ext;
						}
					}
					// Retina image found? Add it.
					if (file_exists($image_path.'/'.$type.'-'.$image_dimensions[$type][$last_key].'.'.$ext)) { // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
						$images[$last_key] = $image_url.'/'.$type.'-'.$image_dimensions[$type][$last_key].'.'.$ext;
					}

				} // foreach

			} // inner if/else

			// Return icon or banner URLs.
			return $images;

		}

		// Oh, banners? Note these are from current version, not new version.
		if ($type === 'screenshot') {

			// Does /images/ directory exists? Prevent notices.
			if (file_exists($image_path)) {

				// Scan the directory.
				$dir_contents = scandir($image_path);

				// Capture only the screenshot URLs.
				foreach ($dir_contents as $name) {
					if (strpos(strtolower($name), 'screenshot') === 0) { // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
						$start = strpos($name, '-') + 1;
						$for = strpos($name, '.') - $start;
						$screenshot_number = substr($name, $start, $for);
						$images[$screenshot_number] = $image_url.'/'.$name;
					}
				}

				// Proper the sort.
				ksort($images);

			}

		}

		// Return any screenshot URLs.
		return $images;

	}

	public function plugin_row_meta($links, $file) {

		$slug    = dirname($file);
		$plugins = $this->get_cp_plugins();

		if (!array_key_exists($slug, $plugins)) {
			return $links;
		}

		// Add Visit site
		array_push($links, '<a href="'.esc_url_raw($plugins[$slug]['PluginURI']).'">'.esc_html__('Visit plugin site').'</a>');

		return $links;

	}

	public function after_plugin_row($plugin_file, $plugin_data, $status) {

		$slug     = dirname($plugin_file);
		$plugins  = $this->get_cp_plugins();

		if (!array_key_exists($slug, $plugins)) {
			return;
		}

		$dir_data = $this->get_directory_data();
		$data     = $dir_data[$slug];
		$plugin   = $plugins[$slug];

		if (version_compare($plugin['Version'], $data['Version']) >= 0) {
			// No updates available
			return false;
		}

		$message = '';
		if (version_compare(classicpress_version(), $data['RequiresCP']) === -1) {
			// Higher CP version required
			// Translators: %1$s is the plugin latest version. %2$s is the ClassicPress version required by the plugin.
			$message .= sprintf (esc_html__('This plugin has not updated to version %1$s because it needs ClassicPress %2$s.', 'classicpress-directory-integration'), esc_html($data['Version']), esc_html($data['RequiresCP']));
		}
		if (version_compare(phpversion(), $data['RequiresPHP']) === -1) {
			if ($message !== '') {
				$message .= ' ';
			}
			// Translators: %1$s is the plugin latest version. %2$s is the PHP version required by the plugin.
			$message .= sprintf (esc_html__('This plugin has not updated to version %1$s because it needs PHP %2$s.', 'classicpress-directory-integration'), esc_html($data['Version']), esc_html($data['RequiresPHP']));
		}

		if ($message === '') {
			return;
		}

		echo '<tr class="plugin-update-tr active" id="'.esc_html($plugin_file).'-update" data-slug="'.esc_html($plugin_file).'" data-plugin="'.esc_html($plugin_file).'"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-alt notice-error"><p aria-label="Can not install a newer version.">';
		echo esc_html($message).'</p></div></td></tr>';

	}

	// Force a refresh of local ClassicPress directory data
	public function refresh_cp_directory_data() {
		$this->get_directory_data(true);
	}

	// Add a test menu. ToDo: remove
	public function create_test_menu() {
		if (!current_user_can('manage_options')) {
			return;
		}
		$page = add_menu_page(
			'Plugin Update tests',
			'Plugin Update tests',
			'manage_options',
			'classicpress-directory-integration-plugin-update-test',
			[$this, 'render_test_page'],
			'dashicons-pets'
		);
	}

	// Add a test menu. ToDo: remove
	public function render_test_page () {

		delete_transient('cpdi_directory_data');

		echo '<h1>Local CP plugins</h1>';
		echo '<pre>';
		$plugins = $this->get_cp_plugins();
		var_dump($plugins); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
		echo '</pre>';

		echo '<h1>Dir CP plugins</h1>';
		echo '<pre>';
		$dir = $this->get_directory_data();
		var_dump($dir); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
		echo '</pre>';

	}

	// Get all installed ClassicPress plugin
	private function get_cp_plugins() {

		if ($this->cp_plugins !== false) {
			return $this->cp_plugins;
		}

		$all_plugins = get_plugins();
		$cp_plugins  = [];
		foreach ($all_plugins as $slug => $plugin) {
			if (!array_key_exists('UpdateURI', $plugin)) {
				continue;
			}
			if (strpos($plugin['UpdateURI'], \CLASSICPRESS_DIRECTORY_INTEGRATION_URL) !== 0) {
				continue;
			}
			$cp_plugins[dirname($slug)] = [
				'WPSlug'      => $slug,
				'Version'     => $plugin['Version'],
				'RequiresPHP' => array_key_exists('RequiresPHP', $plugin) ? $plugin['RequiresPHP'] : null,
				'RequiresCP'  => array_key_exists('RequiresCP', $plugin) ? $plugin['RequiresCP'] : null,
				'PluginURI'   => array_key_exists('PluginURI', $plugin) ? $plugin['PluginURI'] : null,
			];
		}

		$this->cp_plugins = $cp_plugins;
		return $this->cp_plugins;

	}

	// Get data from the directory for all installed ClassicPress plugin
	private function get_directory_data($force = false) {

		// Try to get stored data
		if (!$force && $this->cp_plugins_directory_data !== false) {
			// We have it in memory
			return $this->cp_plugins_directory_data;
		}
		$this->cp_plugins_directory_data = get_transient('cpdi_directory_data');
		if (!$force && $this->cp_plugins_directory_data !== false) {
			// We have it in transient
			return $this->cp_plugins_directory_data;
		}

		// Query the directory
		$plugins  = $this->get_cp_plugins();
		$endpoint = \CLASSICPRESS_DIRECTORY_INTEGRATION_URL.'plugins?byslug='.implode(',', array_keys($plugins)).'&_fields=meta';
		$response = wp_remote_get($endpoint, ['user-agent' => classicpress_user_agent(true)]);

		if (is_wp_error($response) || empty($response['response']) || wp_remote_retrieve_response_code($response) !== 200) {
			return [];
		}

		$data_from_dir = json_decode(wp_remote_retrieve_body($response), true);
		$data = [];

		foreach ($data_from_dir as $single_data) {
			$data[$single_data['meta']['slug']] = [
				'Download'        => $single_data['meta']['download_link'],
				'Version'         => $single_data['meta']['current_version'],
				'RequiresPHP'     => $single_data['meta']['requires_php'],
				'RequiresCP'      => $single_data['meta']['requires_cp'],
				'active_installs' => $single_data['meta']['active_installations'],
			];
		}

		$this->cp_plugins_directory_data = $data;
		set_transient('cpdi_directory_data', $this->cp_plugins_directory_data, 3 * HOUR_IN_SECONDS);
		return $this->cp_plugins_directory_data;

	}

	// Filter to trigger updates using Update URI header
	public function update_uri_filter($update, $plugin_data, $plugin_file, $locales) {

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
		if (version_compare(classicpress_version(), $data['RequiresCP']) === -1) {
			// Higher CP version required
			return false;
		}
		if (version_compare(phpversion(), $data['RequiresPHP']) === -1) {
			// Higher PHP version required
			return false;
		}

		$update = [
			'slug'         => $plugin_file,
			'version'      => $data['Version'],
			'package'      => $data['Download'],
			'requires_php' => $data['RequiresPHP'],
			'banners'      => $this->get_plugin_images('banner', $slug),
			'icons'        => $this->get_plugin_images('icon', $slug),

		];

		return $update;

	}

}
