<?php
// app/libraries/barcode/autoload.php
// Autoloader PSR-4 simple para Picqer\Barcode\*

spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'Picqer\\Barcode\\';

    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/';

    // Normalize leading backslash (if any)
    $class = ltrim($class, '\\');

    // If the class does not use the namespace prefix, move to next autoloader
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Get the relative class name
    $relativeClass = substr($class, strlen($prefix));

    // Replace namespace separators with directory separators, append .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        return;
    }

    // Fallback: try also lowercase-first-letter filenames (rare, but safe)
    $parts = explode('/', $relativeClass);
    $parts[count($parts)-1] = ucfirst($parts[count($parts)-1]);
    $fileAlt = $baseDir . implode('/', $parts) . '.php';
    if (file_exists($fileAlt)) {
        require $fileAlt;
    }
});
