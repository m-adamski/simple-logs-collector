<?php

/**
 * Returns the importmap for this application.
 *
 * - "Path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "Entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'application' => [
        'path'       => './assets/js/application.js',
        'entrypoint' => true,
    ],
    'preline'     => [
        'version' => '2.5.1',
    ],
    'clipboard'   => [
        'version' => '2.0.11',
    ],
];
