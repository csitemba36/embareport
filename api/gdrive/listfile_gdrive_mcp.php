<?php
error_reporting(E_ERROR | E_PARSE);

require __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// ================================
// GOOGLE CLIENT
// ================================
$client = new Client();
$client->setAuthConfig(__DIR__ . '/gdrive-sales-mcp-4b12ba4f68e4.json');
$client->addScope(Drive::DRIVE_READONLY);

$drive = new Drive($client);

// ================================
// FOLDER GOOGLE DRIVE ID
// ================================
$folderId = '1tUwJUeT7fU-nDCj7iqqdSTSIHpH2kW7Y';

// ================================
// LIST FILE
// ================================
$response = $drive->files->listFiles([
    'q' => "'$folderId' in parents and trashed=false",
    'fields' => 'files(name,size,modifiedTime)',
    'pageSize' => 1000
]);

$fileList = [];

foreach ($response->files as $file) {
    $fileList[] = [
        'name' => $file->name,
        'size' => (int) $file->size,
        'date' => date('d/m/Y H:i', strtotime($file->modifiedTime))
    ];
}

// ================================
// OUTPUT JSON
// ================================
header('Content-Type: application/json');
echo json_encode($fileList, JSON_PRETTY_PRINT);
