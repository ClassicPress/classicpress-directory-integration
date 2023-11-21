<?php

namespace ClassicPress\Directory;

class PluginPage
{

	private $page = null;

	public function __construct()
	{
		add_action('admin_init', [$this, 'remove_menus'], 100);
		add_action('admin_menu', [$this, 'create_menu'], 200);
		add_action('admin_enqueue_scripts', [$this, 'styles']);
	}

	public function styles($hook)
	{
		if ($hook !== $this->page) {
			return;
		}
		wp_enqueue_style('classicpress-directory-integration-css', plugins_url('../styles/plugin-new.css', __FILE__), []);
	}

	public function remove_menus() {
		remove_submenu_page('plugins.php', 'plugin-install.php');
		remove_submenu_page('plugins.php', 'plugin-install.php?tab=upload');
		remove_submenu_page('plugins.php', 'classicpress-directory-integration-plugin-install');

	}

	public function create_menu()
	{

		if (!current_user_can('install_plugins')) {
			return;
		}

		$this->page = add_submenu_page(
			'plugins.php',
											   // Let's pick the translation from core.
			esc_html(_x('Add New', 'plugin')), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
			esc_html(_x('Add New', 'plugin')), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
			'install_plugins',
			'add-plugin-cpdi',
			[$this, 'render_menu'],
			2
		);

	}

	public function render_menu()
	{
?>

		<div class="wrap">
			<a href="<?php echo esc_url(admin_url('plugin-install.php')); ?>"><?php esc_html_e('Browse WordPress Plugins', 'classicpress-directory-integration'); ?></a>
			<a href="<?php echo esc_url(admin_url('plugins.php?page=classicpress-directory-integration-plugin-install')); ?>"><?php esc_html_e('Browse ClassicPress Plugins', 'classicpress-directory-integration'); ?></a>
			<a href="<?php echo esc_url(admin_url('plugin-install.php?tab=upload')); ?>"><?php esc_html_e('Upload Zip File', 'classicpress-directory-integration'); ?></a>
		</div>

<?php
	}

}

