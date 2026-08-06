<?php

/**
 * Server data for the marquee block's script module.
 *
 * Vite enqueues every entry under its own handle, so a block reads its own
 * payload with getModuleData('marquee') — no globals, no inline <script>, and
 * WordPress escapes the JSON itself.
 *
 * Reference: docs/guides/server-data.md in the engine.
 */

if (!defined('ABSPATH')) {
    die();
}

add_filter('script_module_data_marquee', static function (array $data): array {
    return array_merge($data, [
        // Pixels per second. An editorial decision, so it belongs on the
        // server; the travel distance is measured in the browser, because only
        // the browser knows how wide the rendered strip turned out.
        'speed' => 60,
    ]);
});
