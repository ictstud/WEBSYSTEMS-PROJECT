<?php
// test_upload.php - Diagnostic tool for file upload testing
// Visit: http://localhost/Repository/WEBSYSTEMS-PROJECT/Backend/test_upload.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli('localhost', 'root', '', 'database');

if($mysqli->connect_error){
    die(json_encode(['ok'=>false,'error'=>'DB Connection failed: '.$mysqli->connect_error]));
}

// Check if files_table exists and has required columns
$stmt = $mysqli->prepare('DESCRIBE files_table');
$stmt->execute();
$result = $stmt->get_result();
$columns = [];

while($row = $result->fetch_assoc()){
    $columns[] = $row['Field'];
}
$stmt->close();

// Verify required columns exist
$required = ['ID', 'last_name', 'first_name', 'file_name', 'date_issued', 'file_blob', 'original_name', 'file_type'];
$missing = [];

foreach($required as $col){
    if(!in_array($col, $columns)){
        $missing[] = $col;
    }
}

$response = [
    'ok' => true,
    'database' => 'database',
    'table' => 'files_table',
    'columns_found' => $columns,
    'required_columns' => $required,
    'missing_columns' => $missing,
    'message' => count($missing) === 0 ? 'All columns exist!' : 'Missing columns: '.implode(', ', $missing)
];

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

$mysqli->close();
?>
