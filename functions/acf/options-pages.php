<?php

/**
 * Project options sub-pages under the engine's "Skeleton" menu.
 *
 * The engine registers the parent page and keeps only what it owns
 * (Appearance → schema toggle). Editorial settings — the header CTA, the
 * footer tagline — are project content, so they are declared here and vanish
 * with the project instead of appearing in every site that updates the engine.
 */

if (!defined('ABSPATH')) {
    die();
}

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title'  => __('Header Settings', 'skeleton'),
        'menu_title'  => __('Header', 'skeleton'),
        'parent_slug' => 'skeleton-settings',
        'menu_slug'   => 'skeleton-header',
        'capability'  => 'manage_options',
    ]);

    acf_add_options_sub_page([
        'page_title'  => __('Footer Settings', 'skeleton'),
        'menu_title'  => __('Footer', 'skeleton'),
        'parent_slug' => 'skeleton-settings',
        'menu_slug'   => 'skeleton-footer',
        'capability'  => 'manage_options',
    ]);
});
