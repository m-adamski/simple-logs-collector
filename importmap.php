<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'application'                                 => [
        'path'       => './assets/js/application.js',
        'entrypoint' => true,
    ],
    'tabulator'                                   => [
        'path'       => './assets/js/plugins/tabulator.js',
        'entrypoint' => true,
    ],
    'module.dashboard'                            => [
        'path'       => './assets/js/modules/dashboard.js',
        'entrypoint' => true,
    ],
    'preline'                                     => [
        'version' => '2.5.1',
    ],
    'clipboard'                                   => [
        'version' => '2.0.11',
    ],
    'lodash'                                      => [
        'version' => '4.17.21',
    ],
    'tabulator-tables'                            => [
        'version' => '6.3.0',
    ],
    'tabulator-tables/dist/css/tabulator.min.css' => [
        'version' => '6.3.0',
        'type'    => 'css',
    ],
    'moment'                                      => [
        'version' => '2.30.1',
    ],
    'moment-timezone'                             => [
        'version' => '0.5.46',
    ],
];
