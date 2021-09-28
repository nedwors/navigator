<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->append(['.php-cs-fixer.dist.php']);

$rules = [
    '@Symfony' => true,
    'phpdoc_no_empty_return' => false,
    'array_syntax' => ['syntax' => 'short'],
    'yoda_style' => false,
    'concat_space' => ['spacing' => 'one'],
    'not_operator_with_space' => false,
    'increment_style' => ['style' => 'post'],
    'phpdoc_no_alias_tag' => false,
    'phpdoc_align' => [
        'align' => 'vertical',
        'tags' => [
            'param',
            'property',
            'property-read',
            'property-write',
            'return',
            'throws',
            'type',
            'var',
            'method',
        ],
    ],
    'global_namespace_import' => [
        'import_classes' => true,
    ],
];

return (new PhpCsFixer\Config())->setFinder($finder)->setRules($rules);
