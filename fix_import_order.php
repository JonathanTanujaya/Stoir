<?php

/**
 * Fix PHPUnit import order - namespace must come before use statements
 */

$testDirectories = [
    __DIR__ . '/tests/Unit',
    __DIR__ . '/tests/Feature'
];

function fixImportOrder($filePath) {
    $content = file_get_contents($filePath);
    
    // Find the position of namespace and use statements
    if (preg_match('/(<\?php\s*)(use PHPUnit\\\\Framework\\\\Attributes\\\\Test;\s*)(namespace\s+[^;]+;)/', $content, $matches)) {
        // Reorder: <?php, namespace, then use statements
        $newContent = str_replace(
            $matches[0],
            $matches[1] . $matches[3] . "\n\n" . $matches[2],
            $content
        );
        
        file_put_contents($filePath, $newContent);
        echo "Fixed import order: $filePath\n";
    }
}

function scanDirectory($directory) {
    $files = glob($directory . '/*.php');
    foreach ($files as $file) {
        fixImportOrder($file);
    }
    
    $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        scanDirectory($subdir);
    }
}

foreach ($testDirectories as $directory) {
    if (is_dir($directory)) {
        echo "Fixing directory: $directory\n";
        scanDirectory($directory);
    }
}

echo "Import order fixed!\n";
