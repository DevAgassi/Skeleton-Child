<?php

/**
 * Skeleton Child — project theme.
 *
 * The parent theme bootstraps everything (autoloader, container, providers),
 * and FileLoaderServiceProvider requires every file under this theme's
 * functions/ directory — so project hooks live there, not here.
 *
 * This file loads BEFORE the parent's functions.php, which makes it the right
 * place for the one thing that must run that early: the core extension points.
 *
 *   add_filter('skeleton/config',   fn($config)   => $config);
 *   add_filter('skeleton/services', fn($services) => $services);
 *   add_filter('skeleton/features', fn($features) => $features);
 *   add_filter('skeleton/roots',    fn($roots)    => $roots);
 *
 * Service override example (inheritance pattern):
 *
 *   add_filter('skeleton/services', function ($services) {
 *       $services['BlocksRegistry'] = fn($app) => new \Child\BlockRegistry($app->get('Vite'));
 *       return $services;
 *   });
 */

namespace Child;

// Composer autoload for project-only packages (added via `composer require`
// in this theme). Both this theme's composer.json and the parent's set an
// explicit, distinct `autoloader-suffix` — without that, Composer can
// generate the same ComposerAutoloaderInit{hash} class name for unrelated
// projects, and loading both vendor/autoload.php in one request fatals with
// "Cannot redeclare class". Do not remove either suffix.
$childAutoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($childAutoload)) {
    require_once $childAutoload;
}

// Intentionally minimal otherwise — project code lives in blocks/, views/,
// ui/, resources/ and functions/.
