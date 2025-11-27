<?php
session_start();
header('Content-Type: application/json');

if(!defined('DB_HOST')) define('DB_HOST','localhost');
if(!defined('DB_USER')) define('DB_USER','root');
if(!defined('DB_PASS')) define('DB_PASS','');
if(!defined('DB_NAME')) define('DB_NAME','test');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($mysqli->connect_error){
    echo json_encode(['ok'=>false, 'error'=>'DB connection failed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) $input = $_POST;

$username = isset($input['username']) ? trim($input['username']) : '';
$password = isset($input['password']) ? $input['password'] : '';

if($username === '' || $password === ''){
    echo json_encode(['ok'=>false, 'error'=>'Username and password required']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, username, password, isAdmin FROM users_table WHERE username = ? LIMIT 1');
if(!$stmt){
    echo json_encode(['ok'=>false, 'error'=>'Query prepare failed']);
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
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
$_SESSION['username'] = $row['username'];
$_SESSION['isAdmin'] = intval($row['isAdmin']);

echo json_encode(['ok'=>true, 'isAdmin' => intval($row['isAdmin'])]);
exit;

?>