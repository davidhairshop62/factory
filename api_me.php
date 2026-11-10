<?php
// api_me.php - 현재 로그인 세션 확인 API
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => false,
        'user' => null
    ]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'user_type' => $_SESSION['user_type'],
        'username' => $_SESSION['username'],
        'name' => $_SESSION['name'],
        'email' => $_SESSION['email'],
        'company_name' => $_SESSION['company_name'],
        'role' => $_SESSION['role'],
        'tier' => $_SESSION['tier'] ?? null
    ]
]);
?>
