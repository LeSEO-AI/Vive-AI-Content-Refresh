/**
 * Vive — Settings page JS (dirty tracking + auto-discover).
 */
( function() {
	'use strict';

	var api = window.viveApi || {};

	// Dirty tracking: Persona & Rules
	var form    = document.getElementById( 'vive-persona-rules-form' );
	if ( ! form ) return;

	var saveBtn = form.querySelector( 'button[type="submit"]' );
	var persona = document.getElementById( 'vive_persona' );
	var rules   = document.getElementById( 'vive_rules' );
	var lang    = document.getElementById( 'vive_language' );

	var initPersona = persona.value;
	var initRules   = rules.value;
	var initLang    = lang.value;

	function checkDirty() {
		var dirty = persona.value !== initPersona || rules.value !== initRules || lang.value !== initLang;
		saveBtn.disabled = !dirty;
	}

	persona.addEventListener( 'input', checkDirty );
	rules.addEventListener( 'input', checkDirty );
	lang.addEventListener( 'change', checkDirty );

	// API Key dirty tracking
	( function() {
		var apiForm  = document.getElementById( 'vive-api-form' );
		var apiSave  = apiForm.querySelector( 'button[type="submit"]' );
		var apiInput = document.getElementById( 'vive_api_key' );
		var initApi  = apiInput.value;

		apiInput.addEventListener( 'input', function() {
			apiSave.disabled = ( apiInput.value === initApi );
		} );
	} )();

	// Auto-Discover
	var discoverBtn = document.getElementById( 'vive-auto-discover' );

	discoverBtn.addEventListener( 'click', async function() {
		discoverBtn.disabled = true;
		discoverBtn.textContent = 'Analyzing\u2026';

		try {
			var resp = await fetch( api.restUrl + '/auto-discover', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': api.nonce,
				},
			} );

			var data = await resp.json();

			if ( data.error ) {
				showToast( data.error, 'error' );
			} else {
				persona.value = data.persona || '';
				rules.value = data.rules || '';
				checkDirty();
				showToast( 'Persona and rules filled from your posts.', 'success' );
			}
		} catch ( err ) {
			showToast( 'Request failed: ' + err.message, 'error' );
		} finally {
			discoverBtn.disabled = false;
			discoverBtn.textContent = 'Analyze from Posts';
		}
	} );
} )();
