<?php
header('Content-Type: application/json');

// Connection logic
// Just a fail safe in case these constants aren't defined yet
if(!defined('DB_HOST')) define('DB_HOST','localhost');
if(!defined('DB_USER')) define('DB_USER','root');
if(!defined('DB_PASS')) define('DB_PASS','');
if(!defined('DB_NAME')) define('DB_NAME','test');

// Connect to MysQL database and handle any connection error
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($mysqli->connect_error){
    echo json_encode(['ok'=>false, 'error'=>'Connection failed']);
    exit;
}

// Reads raw JSON data from request body 
$input = json_decode(file_get_contents('php://input'), true);
// Get from $_POST if there is no JSON input
if(!$input) $input = $_POST;

// Holds what actions to perform. If it is empty (no action input yet) the default value is check
$action = isset($input['action']) ? $input['action'] : 'check';

// helper: send json and exit (this is to maintain consistentcy in the response structures)
function json_response($arr){
    echo json_encode($arr);
    exit;
}

// helper: check if a value exists for a column in users_table
function value_exists($mysqli, $column, $value){
    $allowed = ['username','email','id'];
    if(!in_array($column, $allowed)) return false;

    $sql = "SELECT id FROM users_table WHERE {$column} = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);

    if(!$stmt) return false;

    $stmt->bind_param('s', $value);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}

if($action === 'check'){
    $username = isset($input['username']) ? trim($input['username']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';

    $username_exists = ($username !== '') ? value_exists($mysqli, 'username', $username) : false;
    $email_exists = ($email !== '') ? value_exists($mysqli, 'email', $email) : false;

    json_response([
        'ok' => true,
        'username_exists' => $username_exists,
        'email_exists' => $email_exists
    ]);
}

if($action === 'create'){
    $username = isset($input['username']) ? trim($input['username']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    // Error handling
    $errors = [];
    if(strlen($username) < 3) $errors[] = 'Username too short';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if(strlen($password) < 6) $errors[] = 'Password too short';

    if(!empty($errors)){
        json_response(['ok'=>false, 'error'=>'validation', 'messages'=>$errors]);
    }

    // Make sure username and email don't already exist in the user table database
    if(value_exists($mysqli, 'username', $username) || value_exists($mysqli, 'email', $email)){
        json_response(['ok'=>false, 'error'=>'Username or email already exists']);
    }

    // Insert matching actual `users_table` schema (no created_at column in provided SQL)
    $insert = $mysqli->prepare('INSERT INTO users_table (username, email, password, isAdmin) VALUES (?, ?, ?, 0)');
    if(!$insert){
        json_response(['ok'=>false, 'error'=>'Prepare failed: ' . $mysqli->error]);
    }
    $insert->bind_param('sss', $username, $email, $password);
    if($insert->execute()){
        json_response(['ok'=>true, 'message'=>'User created', 'redirect_to' => '../Frontend/login.php']);
    } else {
        json_response(['ok'=>false, 'error'=>'Insert failed: '.$insert->error]);
    }
    $insert->close();
}

json_response(['ok'=>false, 'error'=>'Invalid action']);

?>