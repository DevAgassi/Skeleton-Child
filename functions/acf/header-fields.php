<?php

/**
 * ACF field group: Header Settings
 * Location: Skeleton → Header options sub-page.
 *
 * header_cta — link — CTA button in the desktop navbar (url, title, target)
 */

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'   => 'group_header_settings',
        'title' => __('Header Settings', 'skeleton'),
        'fields' => [
            [
                'key'          => 'field_header_cta',
                'label'        => __('CTA Button', 'skeleton'),
                'instructions' => __('Desktop navbar call-to-action button. Leave empty to hide the button.', 'skeleton'),
                'name'         => 'header_cta',
                'type'         => 'link',
                'return_format' => 'array',
            ],
        ],
        'location'   => [[['param' => 'options_page', 'operator' => '==', 'value' => 'skeleton-header']]],
        'menu_order' => 0,
    ]);
});
