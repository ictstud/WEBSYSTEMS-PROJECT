<?php
// initializes a session to store user data (like login state) across requests.
session_start();
// tell browser that the response is in a JSON format
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
    echo json_encode(['ok'=>false, 'error'=>'DB connection failed']);
    exit;
}

// Reads raw JSON data from request body 
$input = json_decode(file_get_contents('php://input'), true);
// Get from $_POST if there is no JSON input
if(!$input) $input = $_POST;

// extract email and password from input
$email = isset($input['email']) ? trim($input['email']) : '';
$password = isset($input['password']) ? $input['password'] : '';

// Error handling for empty fields
if($email === '' || $password === ''){
    echo json_encode(['ok'=>false, 'error'=>'Email and password required']);
    exit;
}

// Prepares a SQL query to fetch user details by email.
$stmt = $mysqli->prepare('SELECT id, email, password, isAdmin FROM users_table WHERE email = ? LIMIT 1');
if(!$stmt){
    echo json_encode(['ok'=>false, 'error'=>'Query prepare failed']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// If no user is found, return an error
if($result->num_rows === 0){
    echo json_encode(['ok'=>false, 'error'=>'Invalid credentials']);
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

// verify password (direct comparison since passwords are stored as plain text)
if(!hash_equals($row['password'], $password)){
    echo json_encode(['ok'=>false, 'error'=>'Invalid credentials']);
    exit;
}

// login successful: set session
$_SESSION['user_id'] = $row['id'];
$_SESSION['email'] = $row['email'];
$_SESSION['isAdmin'] = intval($row['isAdmin']);

echo json_encode(['ok'=>true, 'isAdmin' => intval($row['isAdmin'])]);
exit;

?>