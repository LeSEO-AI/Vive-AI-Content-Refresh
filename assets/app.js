/**
 * Leseo — Shared JS for Create & Revive pages.
 */
( function() {
	'use strict';

	const api = window.leseoApi || {};

	/**
	 * Generic Worker call via PHP REST endpoint.
	 */
	async function seorSubmit( endpoint, data ) {
		const resp = await fetch( api.restUrl + endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': api.nonce,
			},
			body: JSON.stringify( data ),
		} );

		if ( ! resp.ok ) {
			throw new Error( 'Network error: ' + resp.status );
		}

		return resp.json();
	}

	/**
	 * Show loading state on button.
	 */
	function setLoading( btn, loading ) {
		if ( loading ) {
			btn.dataset.origText = btn.textContent;
			btn.disabled = true;
			btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Working…';
		} else {
			btn.disabled = false;
			btn.textContent = btn.dataset.origText || 'Generate';
		}
	}

	/**
	 * Render result into output container.
	 */
	function renderResult( container, html ) {
		container.innerHTML = '<div class="leseo-generated-content">' + html + '</div>';
	}

	/**
	 * Render error — uses toast.
	 */
	function renderError( container, msg ) {
		showToast( msg, 'error' );
		container.innerHTML = '';
	}

	function escHtml( str ) {
		const d = document.createElement( 'div' );
		d.textContent = str;
		return d.innerHTML;
	}

	/**
	 * Show toast notification — fixed top-right corner of screen.
	 */
	function showToast( msg, type ) {
		type = type || 'success';

		// Get or create container
		let container = document.querySelector( '.leseo-toast-container' );
		if ( ! container ) {
			container = document.createElement( 'div' );
			container.className = 'leseo-toast-container';
			document.body.appendChild( container );
		}

		const toast = document.createElement( 'div' );
		toast.className = 'leseo-toast leseo-toast-' + type;
		toast.textContent = msg;

		container.appendChild( toast );

		setTimeout( function() {
			toast.classList.add( 'leseo-toast-exit' );
			setTimeout( function() { toast.remove(); }, 300 );
		}, 4000 );
	}

	// Expose globally for inline scripts (Settings page)
	window.showToast = showToast;

	/**
	 * Wire up Create Post page.
	 */
	function initCreatePage() {
		const form = document.getElementById( 'leseo-create-form' );
		if ( ! form ) return;

		const btn    = form.querySelector( 'button[type="submit"]' );
		const output = document.getElementById( 'leseo-create-output' );

		form.addEventListener( 'submit', async function( e ) {
			e.preventDefault();

			const topic    = form.querySelector( '[name="topic"]' ).value.trim();
			const keywords = form.querySelector( '[name="keywords"]' ).value.trim();
			const tone     = form.querySelector( '[name="tone"]' ).value;

			if ( ! topic ) return;

			setLoading( btn, true );

			try {
				const result = await seorSubmit( '/create', { topic, keywords, tone } );
				output.innerHTML = '';

				if ( result.error ) {
					renderError( output, result.error );
				} else {
					renderResult( output, result.content );
					showPostActions( output, result.content, topic, 0 );
					showToast( 'Generated successfully. Check below.', 'success' );
				}
			} catch ( err ) {
				output.innerHTML = '';
				renderError( output, err.message );
			} finally {
				setLoading( btn, false );
			}
		} );
	}

	/**
	 * Strip HTML tags for text-only diffing.
	 */
	function stripHtml( html ) {
		const tmp = document.createElement( 'div' );
		tmp.innerHTML = html;
		return tmp.textContent || tmp.innerText || '';
	}

	/**
	 * Render diff view (red removed, green added) and store full HTML.
	 */
	function renderDiff( container, oldText, newText, fullHtml ) {
		const diff = Diff.diffWords( oldText, newText );
		let diffHtml = '<div class="leseo-diff-view">';

		diff.forEach( function( part ) {
			if ( part.removed ) {
				diffHtml += '<span class="leseo-diff-removed">' + escHtml( part.value ) + '</span>';
			} else if ( part.added ) {
				diffHtml += '<span class="leseo-diff-added">' + escHtml( part.value ) + '</span>';
			} else {
				diffHtml += '<span class="leseo-diff-same">' + escHtml( part.value ) + '</span>';
			}
		} );

		diffHtml += '</div>';

		// Store both views
		container.dataset.diffHtml = diffHtml;
		container.dataset.fullHtml = fullHtml;

		// Default to rendered HTML preview
		container.innerHTML = fullHtml;
		container.dataset.viewMode = 'preview';
	}

	/**
	 * Wire up Revive Post page.
	 */
	function initRevivePage() {
		const form = document.getElementById( 'leseo-revive-form' );
		if ( ! form ) return;

		const btn    = form.querySelector( 'button[type="submit"]' );
		const output = document.getElementById( 'leseo-revive-output' );

		form.addEventListener( 'submit', async function( e ) {
			e.preventDefault();

			const topic           = form.querySelector( '[name="topic"]' ).value.trim();
			const keywords        = form.querySelector( '[name="keywords"]' ).value.trim();
			const tone            = form.querySelector( '[name="tone"]' ).value;
			const originalContent = form.querySelector( '[name="original_content"]' ).value;

			if ( ! originalContent ) return;

			const originalText = stripHtml( originalContent );

			setLoading( btn, true );

			try {
				const result = await seorSubmit( '/revive', { topic, keywords, tone, original_content: originalContent } );
				output.innerHTML = '';

				if ( result.error ) {
					renderError( output, result.error );
				} else {
					const newText = stripHtml( result.content );
					renderDiff( output, originalText, newText, result.content );
					showPostActions( output, result.content, '', api.postId || 0 );
					showToast( 'Generated successfully. Check below.', 'success' );
				}
			} catch ( err ) {
				output.innerHTML = '';
				renderError( output, err.message );
			} finally {
				setLoading( btn, false );
			}
		} );
	}

	/**
	 * Bind click events to Publish/Draft buttons.
	 */
	function bindActionEvents( pubBtn, draftBtn, content, title, postId ) {
		postId = postId || 0;

		function reloadWithToast( msg ) {
			showToast( msg, 'success' );
			setTimeout( function() {
				window.location.reload();
			}, 2000 );
		}

		pubBtn.addEventListener( 'click', async function() {
			setLoading( pubBtn, true );
			try {
				const result = await seorSubmit( '/publish', { post_id: postId, content: content, title: title } );
				if ( result.error ) {
					showToast( 'Publish failed: ' + result.error, 'error' );
				} else {
					reloadWithToast( 'Successfully published!' );
				}
			} catch ( err ) {
				showToast( 'Publish failed: ' + err.message, 'error' );
			} finally {
				setLoading( pubBtn, false );
			}
		} );

		draftBtn.addEventListener( 'click', async function() {
			setLoading( draftBtn, true );
			try {
				const result = await seorSubmit( '/save-draft', { post_id: postId, content: content, title: title } );
				if ( result.error ) {
					showToast( 'Draft failed: ' + result.error, 'error' );
				} else {
					reloadWithToast( 'Saved as draft!' );
				}
			} catch ( err ) {
				showToast( 'Draft failed: ' + err.message, 'error' );
			} finally {
				setLoading( draftBtn, false );
			}
		} );
	}

	/**
	 * Wire up Diff toggle button.
	 */
	function bindDiffToggle( toggleBtn, container ) {
		toggleBtn.addEventListener( 'click', function() {
			const mode = container.dataset.viewMode;
			if ( mode === 'preview' ) {
				container.innerHTML = container.dataset.diffHtml;
				container.dataset.viewMode = 'diff';
				toggleBtn.textContent = 'View Preview';
				toggleBtn.classList.remove( 'btn-outline-secondary' );
				toggleBtn.classList.add( 'btn-secondary' );
			} else {
				container.innerHTML = container.dataset.fullHtml;
				container.dataset.viewMode = 'preview';
				toggleBtn.textContent = 'View Diff';
				toggleBtn.classList.remove( 'btn-secondary' );
				toggleBtn.classList.add( 'btn-outline-secondary' );
			}
		} );
	}

	/**
	 * Show Publish / Save Draft buttons after generation.
	 */
	function showPostActions( container, content, title, postId ) {
		postId = postId || 0;

		// Check for footer buttons (Revive page layout)
		const footerPubBtn = document.getElementById( 'leseo-revive-publish' );
		const footerDraftBtn = document.getElementById( 'leseo-revive-save-draft' );
		const toggleBtn = document.getElementById( 'leseo-revive-toggle-diff' );

		if ( footerPubBtn && footerDraftBtn ) {
			// Enable footer buttons and bind events
			footerPubBtn.disabled = false;
			footerDraftBtn.disabled = false;
			bindActionEvents( footerPubBtn, footerDraftBtn, content, title, postId );

			// Enable diff toggle
			if ( toggleBtn ) {
				toggleBtn.disabled = false;
				bindDiffToggle( toggleBtn, container );
			}
			return;
		}

		// Fallback: Create buttons inline (Create page or legacy layout)
		const actions = document.createElement( 'div' );
		actions.className = 'd-flex gap-2 mt-3';

		const pubBtn = document.createElement( 'button' );
		pubBtn.className = 'btn btn-success';
		pubBtn.textContent = 'Publish';

		const draftBtn = document.createElement( 'button' );
		draftBtn.className = 'btn btn-primary';
		draftBtn.textContent = 'Save as Draft';

		actions.appendChild( pubBtn );
		actions.appendChild( draftBtn );
		container.appendChild( actions );

		bindActionEvents( pubBtn, draftBtn, content, title, postId );
	}

	// Init on DOM ready
	document.addEventListener( 'DOMContentLoaded', function() {
		initCreatePage();
		initRevivePage();
	} );
} )();
