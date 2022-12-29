# Draft plugin for ClassicPress Directory integrator.

Steps
- [X] Create plugin update system that pulls from the directory
- [ ] Create plugin page
- [ ] Create theme update system that pulls from the directory (may need Changeset 53933)
- [ ] Create theme page

## Plugin from the directory now can update as WP.org plugins.

They have to set a proper header field:

` * Update URI: https://directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration`

Use this header for testing:

` * Update URI: https://staging-directory.classicpress.net/wp-json/wp/v2/plugins?byslug=classicpress-directory-integration`

- Only one API call is sent to the directory
- Plugin having updates that are not compatible with current configuration are hilighted

<img width="1086" alt="Schermata 2022-12-28 alle 14 57 37" src="https://user-images.githubusercontent.com/29772709/209845045-14921192-579c-42e0-8e89-e81716323dd5.png">

