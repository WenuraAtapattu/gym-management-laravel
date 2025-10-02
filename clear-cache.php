<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([
        'command' => 'config:clear',
    ]),
    new Symfony\Component\Console\Output\ConsoleOutput
);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([
        'command' => 'view:clear',
    ]),
    new Symfony\Component\Console\Output\ConsoleOutput
);

// Clear file cache
$cachePath = __DIR__.'/storage/framework/cache/data';
if (is_dir($cachePath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}

// Clear compiled views
$viewsPath = __DIR__.'/storage/framework/views';
if (is_dir($viewsPath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}

echo "Cache cleared successfully!\n";
