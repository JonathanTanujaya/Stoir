<?php

/**
 * Script to update PHPUnit doc-comments to attributes
 * This will fix all deprecated @test annotations
 */

$testDirectories = [
    __DIR__ . '/tests/Unit',
    __DIR__ . '/tests/Feature'
];

function updateTestFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Add PHPUnit\Framework\Attributes import if not exists
    if (!str_contains($content, 'use PHPUnit\Framework\Attributes\Test;')) {
        $content = str_replace(
            'namespace Tests',
            "use PHPUnit\Framework\Attributes\Test;\n\nnamespace Tests",
            $content
        );
    }
    
    // Replace @test doc-comments with #[Test] attributes
    $content = preg_replace('/\s*\/\*\*\s*@test\s*\*\/\s*\n(\s*)public function/', "\n$1#[Test]\n$1public function", $content);
    
    file_put_contents($filePath, $content);
    echo "Updated: $filePath\n";
}

function scanDirectory($directory) {
    $files = glob($directory . '/*.php');
    foreach ($files as $file) {
        updateTestFile($file);
    }
    
    $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        scanDirectory($subdir);
    }
}

foreach ($testDirectories as $directory) {
    if (is_dir($directory)) {
        echo "Processing directory: $directory\n";
        scanDirectory($directory);
    }
}

echo "Done! All test files have been updated to use PHPUnit attributes.\n";
