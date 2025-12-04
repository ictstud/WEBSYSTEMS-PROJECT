<?php
header('Content-Type: application/json');

// Connection logic
// Just a fail safe in case these constants aren't defined yet
if(!defined('DB_HOST')) define('DB_HOST','localhost');
if(!defined('DB_USER')) define('DB_USER','root');
if(!defined('DB_PASS')) define('DB_PASS','');
if(!defined('DB_NAME')) define('DB_NAME','database');

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
// The parameters are, in order, the database connection, the name of the column, and the value itself that will be checked
function value_exists($mysqli, $column, $value){
    // list of columns we are checking (excluding password for security reasons)
    $allowed = ['username','email','id'];
    // makes sure that the column we are checking is allowed (a.k.a. not the password); return false if it's not allowed
    if(!in_array($column, $allowed)) return false;

    // Select ID of the row relative to the column given as argument
    $sql = "SELECT id FROM users_table WHERE {$column} = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);

    if(!$stmt) return false;

    $stmt->bind_param('s', $value);
    $stmt->execute();
    $stmt->store_result();

    // check to see if any rows exist; if none, pass in false
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}

// Just check if email or username already exists in the database
if($action === 'check'){
    // error handling in case of empty user inputs (set username and email to an empty string if so)
    $username = isset($input['username']) ? trim($input['username']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';

    // Actually compare if username or email exists; if it is empty, return false
    $username_exists = ($username !== '') ? value_exists($mysqli, 'username', $username) : false;
    $email_exists = ($email !== '') ? value_exists($mysqli, 'email', $email) : false;

    // sends a JSON object response with the results
    json_response([
        'ok' => true,
        'username_exists' => $username_exists,
        'email_exists' => $email_exists
    ]);
}

// create a user account and add into database
if($action === 'create'){
    // get user inputs and set to empty if it is empty
    $username = isset($input['username']) ? trim($input['username']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    // Setting validation for input fields and adding them to an array of errors
    $errors = [];
    if(strlen($username) < 3) $errors[] = 'Username too short';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if(strlen($password) < 6) $errors[] = 'Password too short';

    // If there are errors, return them as a JSON response with the array of errors
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