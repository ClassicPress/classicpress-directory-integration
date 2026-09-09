/**
 * @file Functionality for the ClassicPress install screens.
 */
document.addEventListener( 'DOMContentLoaded', function() {

	var openers = document.querySelectorAll( '.link-txt' ),
		width = window.innerWidth,
		height = window.innerHeight,
		dialog = document.createElement( 'dialog' ),
		{ __, _x, _n, _nx } = wp.i18n;	

	dialog.className = 'plugin-details-modal';
	document.body.append( dialog ); // append dialog element to page

	/**
	 * Open modal dialog
	 */
	openers.forEach( function( opener ) {
		opener.addEventListener( 'click', function( e ) {
			var divInfo = document.createElement( 'div' ),
				closeButton = document.createElement( 'button' ),
				span = document.createElement( 'span' ),
				scrollable = document.createElement( 'div' ),
				h2 = document.createElement( 'h2' ),
				div = document.createElement( 'div' ),
				infoFooter = document.createElement( 'div' ),
				header = opener.closest( 'article' ).querySelector( 'h3' ).textContent,
				content = opener.closest( 'footer' ).dataset.content,
				status = opener.nextElementSibling,
				title = opener.closest( 'article' ).querySelector( 'h3' ).textContent ?
					wp.i18n.sprintf(
						// translators: %s: Plugin name.
						wp.i18n.__( 'Plugin: %s' ),
						opener.closest( 'article' ).querySelector( 'h3' ).textContent
					) :
					wp.i18n.__( 'Plugin details' );

			e.preventDefault();
			e.stopPropagation();

			content = reduceheaders( content );
			status.id = 'plugin-install-from-modal';

			divInfo.id = 'plugin-information';
			divInfo.title = title;
			divInfo.style.width = parseInt( width * 9 / 10 ) + 'px';
			divInfo.style.height = parseInt( height * 9 / 10 ) + 'px';

			closeButton.id = 'dialog-close-button';
			closeButton.type = 'button';
			closeButton.setAttribute( 'autofocus', 'true' );

			span.className = 'screen-reader-text';
			span.textContent = wp.i18n.__( 'Close' );
			closeButton.append( span );
			
			scrollable.id = 'plugin-information-scrollable';
			scrollable.setHTML( content );
			h2.textContent = header;
			scrollable.prepend( h2 );
			scrollable.append( div );
			
			infoFooter.id = 'plugin-information-footer';
			infoFooter.setHTML( status.outerHTML, {
				sanitizer: {
					allowAttributes: ['id', 'class']
				}
			} );
			divInfo.append( closeButton, scrollable, infoFooter );
			dialog.replaceChildren( divInfo );
			dialog.showModal();

			// Set initial focus on the "Close" button
			closeButton.focus();

			// Remove modal contents using mouse
			closeButton.addEventListener( 'click', function() {
				dialog.close();
				dialog.querySelector( '#plugin-information' ).remove();
			} );

			// Keyboard interactions
			dialog.addEventListener( 'keydown', function( e ) {
				if ( e.key === 'Escape' ) { // Remove modal contents
					if ( dialog.querySelector( '#directory-item-content' ) !== null ) {
						dialog.querySelector( '#directory-item-content' ).remove();
					}
				}
				else if ( e.key === 'Enter' && e.target.id === 'dialog-close-button' ) { // Remove modal contents
					e.preventDefault();
					dialog.close();
					if ( dialog.querySelector( '#directory-item-content' ) !== null ) {
						dialog.querySelector( '#directory-item-content' ).remove();
					}
				}
				else if ( e.key === 'Tab' ) { // Prevent tabbing out of modal
					if ( e.target.id === status.id && ! e.shiftKey ) {
						e.preventDefault();
						closeButton.focus();
					} else if ( closeButton === e.target && e.shiftKey ) {
						e.preventDefault();
						dialog.querySelector( '#' + status.id ).focus();
					}
				}
			} );
		} );
	} );

	/**
	 * Helper function to reduce each element's header level by 1 so that modal header can be an `<h2>`.
	 */
	function reduceheaders( content ) {
		return content.replaceAll( '<h5', '<h6' ).replaceAll( '</h5>', '</h6>' ).replaceAll( '<h4', '<h5' ).replaceAll( '</h4>', '</h5>' ).replaceAll( '<h3', '<h4' ).replaceAll( '</h3>', '</h4>' ).replaceAll( '<h2', '<h3' ).replaceAll( '</h2>', '</h3>' );
	}
	
} );
