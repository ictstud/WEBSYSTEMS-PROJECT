<?php
// file_api.php - Handles file uploads and downloads
// POST ?action=upload - stores file with form data
// GET ?action=download&id=ID - serves stored file

// Suppress error display to prevent HTML in JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header immediately
header('Content-Type: application/json');

if(!defined('DB_HOST')) define('DB_HOST','localhost');
if(!defined('DB_USER')) define('DB_USER','root');
if(!defined('DB_PASS')) define('DB_PASS','');
if(!defined('DB_NAME')) define('DB_NAME','database');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($mysqli->connect_error){ 
    http_response_code(500); 
    echo json_encode(['ok'=>false,'error'=>'DB connect: '.$mysqli->connect_error]); 
    exit; 
}

// determines what action to take
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';


if($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Check if file was uploaded
    if(!isset($_FILES['file'])){
        echo json_encode(['ok'=>false,'error'=>'No file provided']); 
        exit;
    }
    
    // Check for upload errors
    if($_FILES['file']['error'] !== UPLOAD_ERR_OK){
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File upload incomplete',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by extension'
        ];
        $errorMsg = isset($uploadErrors[$_FILES['file']['error']]) 
            ? $uploadErrors[$_FILES['file']['error']] 
            : 'Unknown upload error';
        echo json_encode(['ok'=>false,'error'=>$errorMsg]); 
        exit;
    }

    // Reads the other form fields
    $last = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $first = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $fname = isset($_POST['file_name']) ? trim($_POST['file_name']) : '';
    $dateRaw = isset($_POST['date_issued']) ? trim($_POST['date_issued']) : '';

    // Convert date to MM/DD/YYYY format (same as Controller.php)
    $dateTimestamp = strtotime($dateRaw);
    if($dateTimestamp === false){
        echo json_encode(['ok'=>false,'error'=>'Invalid date format. Please enter a valid date.']); 
        exit;
    }
    $date = date("m/d/Y", $dateTimestamp);

    if(!$last || !$first || !$fname || !$date){
        echo json_encode(['ok'=>false,'error'=>'Missing required fields']); 
        exit;
    }

    $tmp = $_FILES['file']['tmp_name'];
    $orig = $_FILES['file']['name'];
    $mime = $_FILES['file']['type'] ?: 'application/octet-stream';
    $fileSize = $_FILES['file']['size'];
    
    // Check file size (warn if larger than typical LONGBLOB safe limit)
    if($fileSize > 16777215){ // 16MB limit for LONGBLOB practical purposes
        echo json_encode(['ok'=>false,'error'=>'File too large. Maximum 16MB allowed.']); 
        exit;
    }
    
    $data = file_get_contents($tmp);

    if($data === false){
        echo json_encode(['ok'=>false,'error'=>'Could not read uploaded file']); 
        exit;
    }
    
    if(strlen($data) === 0){
        echo json_encode(['ok'=>false,'error'=>'File is empty']); 
        exit;
    }

    // Prepares SQL insert query
    $stmt = $mysqli->prepare('INSERT INTO files_table (last_name, first_name, file_name, file_type, original_name, file_blob, date_issued) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if(!$stmt){ 
        echo json_encode(['ok'=>false,'error'=>'Database prepare failed: '.$mysqli->error]); 
        exit; 
    }
    
    // Use standard bind_param for all parameters - bind_param handles binary data
    $stmt->bind_param('sssssss', $last, $first, $fname, $mime, $orig, $data, $date);
    
    if($stmt->execute()){
        $id = $mysqli->insert_id;
        $stmt->close();
        echo json_encode(['ok'=>true,'id'=>$id]);
    } else {
        $error = $stmt->error;
        $stmt->close();
        
        // Provide more specific error messages
        if(strpos($error, 'Unknown column') !== false){
            echo json_encode(['ok'=>false,'error'=>'Database schema error. Missing required columns. Run migration: ALTER TABLE files_table ADD COLUMN file_blob LONGBLOB, ADD COLUMN original_name VARCHAR(255);']);
        } else if(strpos($error, 'Packet too large') !== false){
            echo json_encode(['ok'=>false,'error'=>'File is too large for database. Increase max_allowed_packet in MySQL config.']);
        } else {
            echo json_encode(['ok'=>false,'error'=>'Database error: '.$error]);
        }
    }
    exit;
}

// Fetch file by ID
if($action === 'download' && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare('SELECT file_blob, file_type, original_name FROM files_table WHERE ID = ? LIMIT 1');
    if(!$stmt){ 
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>'Query prepare failed']); 
        exit; 
    }
    $stmt->bind_param('i',$id);
    if(!$stmt->execute()){
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>'Query execute failed']);
        exit;
    }
    $stmt->store_result();
    
    if($stmt->num_rows === 0){ 
        http_response_code(404);
        echo json_encode(['ok'=>false,'error'=>'File not found']);
        exit; 
    }
    
    $stmt->bind_result($blob,$mime,$orig);
    $stmt->fetch();
    $stmt->close();
    
    if($blob === null){ 
        http_response_code(404);
        echo json_encode(['ok'=>false,'error'=>'No file data']); 
        exit; 
    }
    
    // Now send the file as binary
    header_remove();
    header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
    header('Content-Disposition: inline; filename="'.basename($orig).'"');
    echo $blob;
    exit;
}

// check if file exists from given ID
if($action === 'has' && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare('SELECT file_blob IS NOT NULL AS hasblob FROM files_table WHERE ID = ? LIMIT 1');
    if(!$stmt){ 
        echo json_encode(['ok'=>false,'error'=>'Prepare: '.$mysqli->error]); 
        exit; 
    }
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->bind_result($has);
    if($stmt->fetch()){
        echo json_encode(['ok'=>true,'has'=>boolval($has)]);
    } else {
        echo json_encode(['ok'=>false,'has'=>false]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['ok'=>false,'error'=>'Invalid action']);
exit;

?>
