# Draft plugin for ClassicPress Directory integrator.

Steps
- [X] Create plugin update system that pulls from the directory
- [X] Create plugin page
- [ ] Create theme update system that pulls from the directory (may need Changeset 53933 or ClassicPress v.2)
- [ ] Create theme page

## Plugin from the directory now can update as WP.org plugins.

They have to set a proper header field:

` * Update URI: https://directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration`

Use this header for testing:

` * Update URI: https://staging-directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration`

### Features

- Only one API call is sent to the directory
- Plugin having updates that are not compatible with current configuration are hilighted <img width="1086" alt="Schermata 2022-12-28 alle 14 57 37" src="https://user-images.githubusercontent.com/29772709/209845045-14921192-579c-42e0-8e89-e81716323dd5.png">

- View details now works <br /><img width="731" alt="Schermata 2023-01-02 alle 16 46 50" src="https://user-images.githubusercontent.com/29772709/210253850-369dbf62-e734-41b0-b257-968b47f14dbd.png"><br /><img width="765" alt="Schermata 2023-01-02 alle 16 46 39" src="https://user-images.githubusercontent.com/29772709/210253846-49821c1f-4b3e-43e7-be69-075ad29741fb.png">
- Images are pulled in (if they are in `images/` folder) <br /><img width="588" alt="Schermata 2023-01-02 alle 16 46 11" src="https://user-images.githubusercontent.com/29772709/210253842-b241321b-00dc-40ca-a7d0-e5b1c577935e.png">


## Plugin from the directory now can be installed using the "Install CP plugins" menu under "Plugins" menu.

## WP-CLI commands

- Flush transients: `wp cpdi flush`
