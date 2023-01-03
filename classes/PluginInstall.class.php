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
			[$this, 'render_menu'],
		);

	}

	// Add a test menu. ToDo: remove
	public function render_menu () {
		echo '<h1>Directory</h1>';
		echo '<a href="'.add_query_arg(['x'=>'5']).'">5</a>';
		echo '<pre>';
		echo '</pre>';

	}

}
