<?php

declare(strict_types=1);

$header = <<<EOT
This file is part of the `kminek/marklink` codebase.
EOT;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__,
    ])
    ->exclude([
        '.infrastructure',
        'vendor',
        'node_modules',
        'storage',
    ])
    ->files()->name('*.php')->name('artisan')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'ordered_imports' => [
            'sortAlgorithm' => 'alpha',
        ],
        'header_comment' => [
            'header' => $header,
        ],
    ])
    ->setFinder($finder)
;
