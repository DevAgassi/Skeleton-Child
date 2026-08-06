<?php

/**
 * ACF field group: Footer Settings
 * Location: Skeleton → Footer options sub-page.
 *
 * footer_tagline — textarea — tagline text displayed below logo
 */

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'   => 'group_footer_settings',
        'title' => __('Footer Settings', 'skeleton'),
        'fields' => [
            [
                'key'          => 'field_footer_tagline',
                'label'        => __('Tagline', 'skeleton'),
                'instructions' => __('Short text displayed below the logo in the footer.', 'skeleton'),
                'name'         => 'footer_tagline',
                'type'         => 'textarea',
                'rows'         => 3,
            ],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'skeleton-footer']]],
        'menu_order' => 0,
    ]);
});
