<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'single_quote' => false,
        'concat_space' => ['spacing' => 'one'],
        'single_line_empty_body' => true,
    ])
    ->setFinder($finder)
;
