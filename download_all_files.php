<?php
// download_all_files.php
$dir = __DIR__ . '/uploads';
if (!is_dir($dir)) {
    die('Không có tệp.');
}
$zipname = 'all_files_' . time() . '.zip';
$zipPath = sys_get_temp_dir() . '/' . $zipname;
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
    die('Không thể tạo file zip.');
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $localName = substr($filePath, strlen($dir) + 1);
        $zip->addFile($filePath, $localName);
    }
}
$zip->close();
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipname . '"');
readfile($zipPath);
unlink($zipPath);
exit;
