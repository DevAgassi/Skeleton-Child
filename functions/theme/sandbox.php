<?php

/**
 * Media lightbox dialog — one per page, opened by any [data-sandbox-trigger].
 *
 * Hooked on wp_footer rather than shadowing views/base.twig: the dialog is a
 * project feature, and a whole page shell should not be forked to add one
 * element to it.
 *
 * Markup: ui/components/sandbox.twig
 * Logic:  resources/scripts/sandbox.js
 */

if (!defined('ABSPATH')) {
    die();
}

add_action('wp_footer', static function (): void {
    if (class_exists(\Timber\Timber::class)) {
        \Timber\Timber::render('ui/components/sandbox.twig');
    }
});
