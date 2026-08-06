<?php

use Timber\Timber;

/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 2.0
 *
 */

// Reuse the posts collection context() already builds for is_home() instead
// of wrapping the main query a second time. pagination() must run after the
// last Timber::get_posts() call: it writes the PostQuery wrapper back into
// wp_query->posts, and re-wrapping that trips a PHP 8.5 ArrayObject
// deprecation (Timber 2.5.1).
$context = Timber::context();

$context['pagination'] = $context['posts']->pagination( [ 'mid_size' => 2, 'end_size' => 1 ] );

Timber::render( 'home/view.twig', $context );
