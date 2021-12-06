<?php

use Ibexa\CodeStyle\PhpCsFixer\InternalConfigFactory;

$factory = new InternalConfigFactory();
$factory->withRules([
    'declare_strict_types' => false,
]);

return $factory->buildConfig()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
            ->files()->name('*.php')
    );
