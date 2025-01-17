<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude('vendor');

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,                // Follow PSR-12 standards
        'line_ending' => true,           // Ensure correct line endings
        'array_syntax' => ['syntax' => 'short'], // Use short array syntax
        'binary_operator_spaces' => ['default' => 'align_single_space'],
        'blank_line_after_namespace' => true,
        'braces' => ['position_after_functions_and_oop_constructs' => 'next'],
        'single_quote' => true,          // Enforce single quotes
        'no_unused_imports' => true,    // Remove unused imports
        'phpdoc_align' => true,         // Align PHPDoc comments
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'phpdoc_separation' => true,
        'phpdoc_order' => true,
        'line_length' => 120,
    ])
    ->setFinder($finder);