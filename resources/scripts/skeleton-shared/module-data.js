/**
 * Server data for script modules.
 *
 * WordPress prints a JSON payload next to every enqueued script module:
 *
 *   <script type="application/json" id="wp-script-module-data-app">…</script>
 *
 * PHP fills it through the `script_module_data_{handle}` filter — see
 * functions/theme/vars.php, which exposes home, isHome, nonce and root for the
 * "app" handle. Reading it here is the whole contract: no inline bootstrap
 * script, no globals hung off window, and the payload is escaped by core.
 *
 * A block adds its own data by filtering `script_module_data_{blockName}`,
 * since Vite enqueues every block entry under its own handle.
 */

/**
 * Read the payload WordPress printed for a script module handle.
 *
 * @param {string} handle Script module handle, e.g. "app" or a block name.
 * @returns {Object} Decoded payload, or an empty object when absent/invalid.
 */
export function getModuleData(handle) {
	const el = document.getElementById(`wp-script-module-data-${handle}`);

	if (!el) {
		return {};
	}

	try {
		return JSON.parse(el.textContent) ?? {};
	} catch {
		return {};
	}
}

/**
 * Site-wide payload for the "app" module: { home, isHome, nonce, root }.
 *
 * Called rather than exported as a constant so it reads the DOM at use time —
 * an import-time constant would be empty for anything evaluated before the
 * payload is parsed.
 *
 * @returns {{home?: string, isHome?: boolean, nonce?: string, root?: string}}
 */
export function getAppData() {
	return getModuleData("app");
}
