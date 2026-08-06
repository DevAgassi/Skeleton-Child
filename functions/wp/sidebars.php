<?php

/**
 * Project widget zones.
 *
 * Widget zones are project entities — the engine ships only the layout slots
 * they hang from. Each zone is owned end to end here: the register_sidebar()
 * call sits next to whatever draws it.
 *
 * Two ways a zone reaches the page, both represented below:
 *
 *  - Layout slot — the zone appears on every route. Hook it into a slot from
 *    views/base.twig (skeleton/layout/after_header, skeleton/layout/pre_footer).
 *    Consumers stack, so adding a zone never means removing another.
 *  - Route context — the zone belongs to one template. The route controller
 *    collects it (see views/single/index.php) and its view draws it.
 */

use Timber\Timber;

if (!defined('ABSPATH')) {
    die();
}

add_action('after_setup_theme', static function (): void {
    add_action('widgets_init', static function (): void {
        register_sidebar([
            'name'          => __('Pre-footer Widget Zone', 'skeleton'),
            'id'            => 'pre_footer_widget_zone',
            'description'   => __('Rendered above the footer on every page that has widgets in it.', 'skeleton'),
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => '',
        ]);

        register_sidebar([
            'name'           => __('Single Post — Share Zone', 'skeleton'),
            'id'             => 'single_share_zone',
            'description'    => __('Rendered before the author bio on every single post. Add the Social Share block here.', 'skeleton'),
            'before_sidebar' => '<aside class="container-content is-layout-constrained">',
            'after_sidebar'  => '</aside>',
            'before_widget'  => '',
            'after_widget'   => '',
            'before_title'   => '',
            'after_title'    => '',
        ]);
    });
});

/**
 * Draw the pre-footer zone into the layout slot.
 *
 * Named rather than a closure so a child theme or plugin can unhook it.
 * The widgets are collected here instead of on the global Timber context:
 * a page whose slot is silenced never pays for the lookup.
 *
 * @return void
 */
function skeleton_render_pre_footer_zone(): void
{
    Timber::render('ui/widgets/pre-footer.twig', [
        'pre_footer_sidebar' => Timber::get_widgets('pre_footer_widget_zone'),
    ]);
}

add_action('skeleton/layout/pre_footer', 'skeleton_render_pre_footer_zone');
