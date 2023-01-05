<?php

namespace ClassicPress\Directory;

class PluginInstall {

	public function __construct() {

		// Add menu under plugins.
		add_action('admin_menu', [$this, 'create_menu'], 100);

	}

	// Add a test menu. ToDo: remove
	public function create_menu() {

		if (!current_user_can('install_plugins')) {
			return;
		}

		$page = add_submenu_page(
			'plugins.php',
			esc_html__('Install ClassicPress plugins', 'classicpress-directory-integration'),
			esc_html__('Install CP plugins', 'classicpress-directory-integration'),
			'install_plugins',
			'classicpress-directory-integration-plugin-install',
			[$this, 'render_menu']
		);

	}

	public static function sanitize_args($args) {
		foreach ($args as $key => $value) {
			$sanitized = false;
			switch ($key) {
				case 'per_page':
				case 'page':
					$args[$key] = (int) $value;
					$sanitized = true;
					break;
				case 'by_slug':
					$args[$key] = preg_replace('[^A-Za-z0-9\-_]', '', $value);
					$sanitized = true;
					break;
				case 'search':
					$args[$key] = sanitize_text_field($value);
					$sanitized = true;
					break;

			}
			if ($sanitized) {
				continue;
			}
			unset($args[$key]);
		}
		return $args;
	}


	public static function do_directory_request($args = [], $type = 'plugins') {

		$result['success'] = false;

		if (!in_array($type, ['plugins', 'themes'])) {
			$result['error'] = $type.' is not a supported type';
			return $result;
		}

		$args = self::sanitize_args($args);
		$endpoint = \CLASSICPRESS_DIRECTORY_INTEGRATION_URL.$type;
		$endpoint = add_query_arg($args, $endpoint);
		$response = wp_remote_get($endpoint, ['user-agent' => classicpress_user_agent()]);

		if (is_wp_error($response)) {
			$result['error'] = rtrim(implode(',', $response->get_error_messages()), '.');
			return $result;
		}

		$e = wp_remote_retrieve_response_code($response);
		if ($e !== 200) {
			$result['error'] = $response['response']['message'];
			$result['code']  = $response['response']['code'];
			return $result;
		}

		if (!isset($response['headers'])) {
			$result['error'] = 'No headers found';
			return $result;
		}

		$headers = $response['headers']->getAll();
		if (!isset($headers['x-wp-total']) || !isset($headers['x-wp-totalpages'])) {
			$result['error'] = 'No pagination headers found';
			return $result;
		}

		$data_from_dir = json_decode(wp_remote_retrieve_body($response), true);
		if ($data_from_dir === null) {
			$result['error'] = 'Failed decoding response';
			return $result;
		}

		$result['success']     = true;
		$result['total-pages'] = $headers['x-wp-totalpages'];
		$result['total-items'] = $headers['x-wp-total'];
		$result['response']    = $data_from_dir;

		return $result;

	}

	// Add a test menu. ToDo: remove
	public function render_menu () {
		echo '<h1>Directory</h1>';

		echo '<pre>';
echo sanitize_text_field('sort=alpha%3Cimg+src=xyz+onerror=alert(99)%3E%3Cxss/%3E');


$test = [
	'by_slug' => '(ciao)',
	'fuck' => 'you',
	'per_page' => '39',
	'page' => 'simo',
];

$test = self::sanitize_args($test);

//$x = self::do_directory_request(['per_page'=>'2', '_fields' => 'title'], 'plugins');
var_dump($test);
echo "\n" . (int) 'simo';
//var_dump($x);


		echo '</pre>';

	}

}
