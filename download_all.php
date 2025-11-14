<?php
$dir = 'uploads';

if (!is_dir($dir)) {
    die("Không có thư mục uploads!");
}

$zipFile = "all_files.zip";
$zip = new ZipArchive;

if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $f) {
        if (!$f->isDir()) {
            $filePath = $f->getRealPath();
            $relative = substr($filePath, strlen($dir) + 1);
            $zip->addFile($filePath, $relative);
        }
    }

    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="all_files.zip"');
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);

    unlink($zipFile);
} else {
    echo "Không thể tạo file ZIP!";
}
