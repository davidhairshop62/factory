<?php
// api_register.php - 신규 거래처(B2B) 가입 신청 엔드포인트
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '허용되지 않는 메소드입니다. POST 요청을 사용하세요.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: $_POST;

$companyName = trim($input['company_name'] ?? '');
$businessNumber = trim($input['business_number'] ?? '');
$name = trim($input['name'] ?? '');
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$phone = trim($input['phone'] ?? '');
$shippingAddress = trim($input['shipping_address'] ?? '');

// 필수 항목 검증
if (empty($companyName)) {
    echo json_encode(['success' => false, 'message' => '상호명(살롱명)을 입력해 주세요.']);
    exit;
}

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => '담당자 성명을 입력해 주세요.']);
    exit;
}

if (empty($username) || strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => '아이디는 최소 3자 이상 입력해 주세요.']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    echo json_encode(['success' => false, 'message' => '아이디는 영문, 숫자, 밑줄(_), 하이픈(-)만 사용할 수 있습니다.']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => '올바른 이메일 주소를 입력해 주세요.']);
    exit;
}

if (empty($password) || strlen($password) < 4) {
    echo json_encode(['success' => false, 'message' => '비밀번호는 최소 4자 이상 입력해 주세요.']);
    exit;
}

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => '연락처(전화번호)를 입력해 주세요.']);
    exit;
}

try {
    // 1. 중복 아이디 확인
    $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmtUser->execute([$username]);
    if ($stmtUser->fetch()) {
        echo json_encode(['success' => false, 'message' => '이미 사용 중인 아이디입니다. 다른 아이디를 입력해 주세요.']);
        exit;
    }

    // 2. 중복 이메일 확인
    $stmtEmail = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmtEmail->execute([$email]);
    if ($stmtEmail->fetch()) {
        echo json_encode(['success' => false, 'message' => '이미 등록된 이메일 주소입니다.']);
        exit;
    }

    // 3. 비밀번호 암호화 (Bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 4. 신규 거래처 등록 (status = PENDING 으로 등록)
    $insertSql = "INSERT INTO users (
                    user_type, 
                    username, 
                    email, 
                    password, 
                    name, 
                    company_name, 
                    business_number, 
                    phone, 
                    shipping_address, 
                    role, 
                    tier, 
                    status
                  ) VALUES (
                    'CLIENT', 
                    :username, 
                    :email, 
                    :password, 
                    :name, 
                    :company_name, 
                    :business_number, 
                    :phone, 
                    :shipping_address, 
                    'CLIENT', 
                    'TIER_1', 
                    'PENDING'
                  )";

    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':name' => $name,
        ':company_name' => $companyName,
        ':business_number' => !empty($businessNumber) ? $businessNumber : null,
        ':phone' => $phone,
        ':shipping_address' => !empty($shippingAddress) ? $shippingAddress : null
    ]);

    $newId = (int)$conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => '거래처 가입 신청이 성공적으로 접수되었습니다. 관리자 승인 후 시스템을 이용하실 수 있습니다.',
        'user_id' => $newId,
        'status' => 'PENDING'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 처리 중 오류가 발생했습니다: ' . $e->getMessage()
    ]);
}

