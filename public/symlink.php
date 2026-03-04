<?php
declare(strict_types=1);

$publicDir = __DIR__;
$target = realpath($publicDir . '/../storage/app/public');
$link = $publicDir . '/storage';

if ($target === false) {
    exit("Target directory not found: {$publicDir}/../storage/app/public\n");
}

if (is_link($link)) {
    if (realpath($link) === $target) {
        exit("The 'storage' symlink already points to {$target}.\n");
    }

    if (! unlink($link)) {
        exit("Existing 'storage' symlink could not be removed.\n");
    }
}

if (file_exists($link)) {
    exit("A file or directory named 'storage' already exists in public/. Remove it manually and rerun this script.\n");
}

if (@symlink($target, $link)) {
    echo "Success! 'storage' now points to {$target}.\n";
    exit(0);
}

$error = error_get_last();
$message = $error['message'] ?? 'Unknown error creating symlink.';
exit("Failed to create symlink: {$message}\n");
?>