![ClassicPress Directory Integration Plugin logo](images/banner-772x250.png "ClassicPress Directory Integration Plugin")

# ClassicPress Directory integrator.

[![ClassicPress Directory Coding Standard checks.](https://github.com/ClassicPress/classicpress-directory-integration/actions/workflows/cpcs.yml/badge.svg)](https://github.com/ClassicPress/classicpress-directory-integration/actions/workflows/cpcs.yml)[![WPCS checks.](https://github.com/ClassicPress/classicpress-directory-integration/actions/workflows/wpcs.yml/badge.svg)](https://github.com/ClassicPress/classicpress-directory-integration/actions/workflows/wpcs.yml)

## Features

- Plugins and themes from [ClassicPress Directory](https://directory.classicpress.net/) now can update as WP.org plugins.
- Plugins from ClassicPress Directory now can be installed using the "Install CP Plugins" menu under "Plugins" menu.
- Themes from ClassicPress Directory now can be installed using the "Install CP Themes" menu under "Appearance" menu.

## WP-CLI commands

- Flush transients: `wp cpdi flush`

## Hooks

#### `apply_filters( "cpdi_images_folder_{$plugin}", string $folder )`
Filters the folder where we search for icons and banners.
The filtered path is relative to the plugin's directory.
Default is `/images`.

Example:
```php
add_filter(
	'cpdi_images_folder_' . basename( __DIR__ ),
	function ( $source ) {
		return '/assets/images';
	}
);
```
