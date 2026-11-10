<?php
// api_login.php - 통합 인증 처리 API (ADMIN / CLIENT)
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST 요청만 허용됩니다.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// If not JSON, try standard form POST
if (!$data) {
    $data = $_POST;
}

$loginId = trim($data['username'] ?? $data['login_id'] ?? $data['email'] ?? '');
$password = trim($data['password'] ?? '');
$selectedRole = trim($data['user_type'] ?? ''); // Optional filter (ADMIN or CLIENT)

if (empty($loginId) || empty($password)) {
    echo json_encode(['success' => false, 'message' => '아이디(이메일)와 비밀번호를 모두 입력해 주세요.']);
    exit;
}

try {
    // Look up user by username or email
    $sql = "SELECT * FROM users WHERE (username = :loginId OR email = :loginId)";
    if (!empty($selectedRole) && in_array(strtoupper($selectedRole), ['ADMIN', 'CLIENT'])) {
        $sql .= " AND user_type = :selectedRole";
    }
    $sql .= " LIMIT 1";

    $stmt = $conn->prepare($sql);
    $params = [':loginId' => $loginId];
    if (!empty($selectedRole) && in_array(strtoupper($selectedRole), ['ADMIN', 'CLIENT'])) {
        $params[':selectedRole'] = strtoupper($selectedRole);
    }
    $stmt->execute($params);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => '존재하지 않는 계정이거나 정보가 일치하지 않습니다.']);
        exit;
    }

    // Check account status
    if ($user['status'] !== 'ACTIVE') {
        $statusMsg = $user['status'] === 'PENDING' ? '관리자 승인 대기 중인 계정입니다.' : '이용이 정지된 계정입니다. 본사에 문의하세요.';
        echo json_encode(['success' => false, 'message' => $statusMsg]);
        exit;
    }

    // Verify password (Bcrypt)
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => '비밀번호가 올바르지 않습니다.']);
        exit;
    }

    // Update last_login_at
    $updateStmt = $conn->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
    $updateStmt->execute([':id' => $user['id']]);

    // Store in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['company_name'] = $user['company_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['tier'] = $user['tier'];
    $_SESSION['logged_in_at'] = time();

    // Determine target redirect URL based on user_type
    $redirectUrl = ($user['user_type'] === 'ADMIN') ? 'admin-dashboard.html' : 'portal-dashboard.html';

    echo json_encode([
        'success' => true,
        'message' => '로그인 성공',
        'redirect_url' => $redirectUrl,
        'user' => [
            'id' => (int)$user['id'],
            'user_type' => $user['user_type'],
            'username' => $user['username'],
            'name' => $user['name'],
            'company_name' => $user['company_name'],
            'role' => $user['role'],
            'tier' => $user['tier']
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
