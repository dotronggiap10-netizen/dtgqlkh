<?php
// functions.php
function human_filesize($bytes, $decimals = 2)
{
    $sz = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sz[$factor];
}

function save_uploaded_files($filesArray, $uploadDir = __DIR__ . '/uploads')
{
    // $filesArray is like $_FILES['someField']
    $saved = [];
    if (!isset($filesArray['name'])) return $saved;
    for ($i = 0; $i < count($filesArray['name']); $i++) {
        if ($filesArray['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp = $filesArray['tmp_name'][$i];
        $name = basename($filesArray['name'][$i]);
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($name, PATHINFO_FILENAME));
        $finalName = $safeName . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $dest = $uploadDir . '/' . $finalName;
        if (move_uploaded_file($tmp, $dest)) {
            $saved[] = [
                'original' => $name,
                'path' => 'uploads/' . $finalName,
                'size' => filesize($dest)
            ];
        }
    }
    return $saved;
}
