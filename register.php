<?php
header('Content-Type: application/json');
require 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    echo json_encode(['success'=>false, 'message'=>'Invalid input']);
    exit;
}

$name = trim($input['name'] ?? '');
$mode = ($input['mode'] ?? '') === 'kids' ? 'kids' : 'adult';

if($name === '') {
    echo json_encode(['success'=>false, 'message'=>'Name is required']);
    exit;
}

if($mode === 'adult') {

    $email = filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $input['password'] ?? '';

    if(!$email) {
        echo json_encode(['success'=>false, 'message'=>'Valid email required']);
        exit;
    }

    // Password strength check
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/', $password)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Password must be at least 6 characters and include both letters and numbers'
        ]);
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if($stmt->fetch()) {
        echo json_encode(['success'=>false, 'message'=>'Email already registered']);
        exit;
    }

    // Insert adult user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password,mode) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hash, $mode]);

    echo json_encode(['success'=>true, 'message'=>'Registered successfully']);
    exit;

} else {

    // Kids mode registration
    $stmt = $pdo->prepare('INSERT INTO users (name, mode) VALUES (?, ?)');
    $stmt->execute([$name, $mode]);
    $userId = $pdo->lastInsertId();

    // Auto-login kids
    $_SESSION['user_id'] = $userId;
    $_SESSION['name'] = $name;
    $_SESSION['mode'] = 'kids';

    echo json_encode(['success'=>true, 'message'=>'Registered and logged in', 'autologin' => true]);
    exit;
}
?>
