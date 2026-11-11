<?php
// api_profile.php - 로그인된 거래처 본인 정보 조회 및 수정 API
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// 로그인 여부 확인
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다. 로그인 후 이용해 주세요.']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    // 1. GET: 본인 상세 정보 조회
    if ($method === 'GET') {
        $stmt = $conn->prepare("SELECT id, user_type, username, email, name, name_en, company_name, company_name_en, business_number, phone, shipping_address, shipping_address_en, role, tier, status, created_at 
                                FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => '사용자 정보를 찾을 수 없습니다.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
        exit;
    }

    // 2. POST: 본인 정보 수정
    if ($method === 'POST') {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        $nameEn = trim($input['name_en'] ?? '');
        $companyName = trim($input['company_name'] ?? '');
        $companyNameEn = trim($input['company_name_en'] ?? '');
        $businessNumber = trim($input['business_number'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $shippingAddress = trim($input['shipping_address'] ?? '');
        $shippingAddressEn = trim($input['shipping_address_en'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => '담당자 성명을 입력해 주세요.']);
            exit;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => '올바른 이메일 주소를 입력해 주세요.']);
            exit;
        }

        // 이메일 중복 확인 (다른 계정과 중복 방지)
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkStmt->execute([$email, $userId]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => '이미 다른 계정에서 사용 중인 이메일입니다.']);
            exit;
        }

        // 거래처 본인이 수정 가능한 필드만 엄격히 적용
        // (user_type, role, tier, status, notes, remarks는 거래처가 변경 불가!)
        $sql = "UPDATE users SET 
                name = :name,
                name_en = :name_en,
                company_name = :company_name,
                company_name_en = :company_name_en,
                business_number = :business_number,
                phone = :phone,
                shipping_address = :shipping_address,
                shipping_address_en = :shipping_address_en,
                email = :email";

        $params = [
            ':name' => $name,
            ':name_en' => !empty($nameEn) ? $nameEn : null,
            ':company_name' => !empty($companyName) ? $companyName : null,
            ':company_name_en' => !empty($companyNameEn) ? $companyNameEn : null,
            ':business_number' => !empty($businessNumber) ? $businessNumber : null,
            ':phone' => !empty($phone) ? $phone : null,
            ':shipping_address' => !empty($shippingAddress) ? $shippingAddress : null,
            ':shipping_address_en' => !empty($shippingAddressEn) ? $shippingAddressEn : null,
            ':email' => $email,
            ':id' => $userId
        ];

        // 비밀번호 변경 입력이 있는 경우
        if (!empty($password)) {
            if (strlen($password) < 4) {
                echo json_encode(['success' => false, 'message' => '비밀번호는 최소 4자 이상 입력해 주세요.']);
                exit;
            }
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute($params);

        // 세션 정보 동기화
        $_SESSION['name'] = $name;
        $_SESSION['company_name'] = $companyName;
        $_SESSION['email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => '거래처 정보가 성공적으로 수정되었습니다.',
            'user' => [
                'name' => $name,
                'name_en' => $nameEn,
                'company_name' => $companyName,
                'company_name_en' => $companyNameEn,
                'business_number' => $businessNumber,
                'phone' => $phone,
                'shipping_address' => $shippingAddress,
                'shipping_address_en' => $shippingAddressEn,
                'email' => $email
            ]
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => '지원하지 않는 요청 방식입니다.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}

