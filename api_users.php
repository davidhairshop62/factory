<?php
// api_users.php - 관리자 전용 사용자 CRUD API
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// 관리자 권한 확인 (Session 검증)
if (empty($_SESSION['user_id']) || empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '관리자 권한이 필요합니다. 로그인 후 이용해 주세요.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// JSON 입력 파싱
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true) ?: [];

if ($method === 'POST' && empty($action) && isset($jsonInput['action'])) {
    $action = $jsonInput['action'];
}

try {
    // 1. 사용자 목록 조회 (GET 또는 action=list)
    if ($method === 'GET' || $action === 'list') {
        $filterType = $_GET['user_type'] ?? '';
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT id, user_type, username, email, name, name_en, company_name, company_name_en, business_number, phone, shipping_address, shipping_address_en, role, tier, notes, remarks, status, last_login_at, created_at, updated_at 
                FROM users WHERE 1=1";
        $params = [];

        if (!empty($filterType) && in_array($filterType, ['ADMIN', 'CLIENT'])) {
            $sql .= " AND user_type = :user_type";
            $params[':user_type'] = $filterType;
        }

        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR name LIKE :search OR name_en LIKE :search OR company_name LIKE :search OR company_name_en LIKE :search OR email LIKE :search OR phone LIKE :search OR shipping_address LIKE :search OR shipping_address_en LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'total' => count($users),
            'users' => $users
        ]);
        exit;
    }

    // 2. 사용자 신규 추가 (action=create)
    if ($action === 'create') {
        $userType = trim($jsonInput['user_type'] ?? '');
        $username = trim($jsonInput['username'] ?? '');
        $email = trim($jsonInput['email'] ?? '');
        $password = trim($jsonInput['password'] ?? '');
        $name = trim($jsonInput['name'] ?? '');
        $nameEn = trim($jsonInput['name_en'] ?? '');
        $companyName = trim($jsonInput['company_name'] ?? '');
        $companyNameEn = trim($jsonInput['company_name_en'] ?? '');
        $bizNumber = trim($jsonInput['business_number'] ?? '');
        $phone = trim($jsonInput['phone'] ?? '');
        $shippingAddress = trim($jsonInput['shipping_address'] ?? '');
        $shippingAddressEn = trim($jsonInput['shipping_address_en'] ?? '');
        $role = trim($jsonInput['role'] ?? '');
        $tier = trim($jsonInput['tier'] ?? 'TIER_1');
        $status = trim($jsonInput['status'] ?? 'ACTIVE');

        // 기타사항 및 비고사항은 오직 SUPER_ADMIN(총괄관리자)만 입력 가능
        $isSuperAdmin = !empty($_SESSION['role']) && $_SESSION['role'] === 'SUPER_ADMIN';
        $notes = $isSuperAdmin ? trim($jsonInput['notes'] ?? '') : null;
        $remarks = $isSuperAdmin ? trim($jsonInput['remarks'] ?? '') : null;

        if (!in_array($userType, ['ADMIN', 'CLIENT'])) {
            echo json_encode(['success' => false, 'message' => '구분(관리자/거래처)을 올바르게 선택해 주세요.']);
            exit;
        }

        if (empty($username) || empty($email) || empty($password) || empty($name)) {
            echo json_encode(['success' => false, 'message' => '아이디, 이메일, 비밀번호, 담당자 이름은 필수 항목입니다.']);
            exit;
        }

        if ($userType === 'CLIENT' && empty($companyName)) {
            echo json_encode(['success' => false, 'message' => '거래처의 경우 상호명(살롱명)은 필수입니다.']);
            exit;
        }

        // 중복 아이디/이메일 검사
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => '이미 사용 중인 아이디 또는 이메일입니다.']);
            exit;
        }

        // 비밀번호 Bcrypt 해시
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        if (empty($role)) {
            $role = ($userType === 'ADMIN') ? 'ADMIN' : 'CLIENT';
        }

        $insertSql = "INSERT INTO users (user_type, username, email, password, name, name_en, company_name, company_name_en, business_number, phone, shipping_address, shipping_address_en, role, tier, notes, remarks, status)
                      VALUES (:user_type, :username, :email, :password, :name, :name_en, :company_name, :company_name_en, :business_number, :phone, :shipping_address, :shipping_address_en, :role, :tier, :notes, :remarks, :status)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->execute([
            ':user_type' => $userType,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':name' => $name,
            ':name_en' => !empty($nameEn) ? $nameEn : null,
            ':company_name' => !empty($companyName) ? $companyName : null,
            ':company_name_en' => !empty($companyNameEn) ? $companyNameEn : null,
            ':business_number' => !empty($bizNumber) ? $bizNumber : null,
            ':phone' => !empty($phone) ? $phone : null,
            ':shipping_address' => !empty($shippingAddress) ? $shippingAddress : null,
            ':shipping_address_en' => !empty($shippingAddressEn) ? $shippingAddressEn : null,
            ':role' => $role,
            ':tier' => in_array($tier, ['TIER_1', 'TIER_2', 'TIER_3']) ? $tier : 'TIER_1',
            ':notes' => !empty($notes) ? $notes : null,
            ':remarks' => !empty($remarks) ? $remarks : null,
            ':status' => in_array($status, ['ACTIVE', 'PENDING', 'SUSPENDED']) ? $status : 'ACTIVE'
        ]);

        $newId = (int)$conn->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => '사용자가 성공적으로 등록되었습니다.',
            'user_id' => $newId
        ]);
        exit;
    }

    // 3. 사용자 정보 변경 / 수정 (action=update)
    if ($action === 'update') {
        $id = (int)($jsonInput['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 사용자 ID입니다.']);
            exit;
        }

        // 대상 사용자 존재 여부 확인
        $findStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $findStmt->execute([$id]);
        $existing = $findStmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => '수정할 사용자를 찾을 수 없습니다.']);
            exit;
        }

        $userType = trim($jsonInput['user_type'] ?? $existing['user_type']);
        $email = trim($jsonInput['email'] ?? $existing['email']);
        $name = trim($jsonInput['name'] ?? $existing['name']);
        $nameEn = trim($jsonInput['name_en'] ?? ($existing['name_en'] ?? ''));
        $companyName = trim($jsonInput['company_name'] ?? $existing['company_name']);
        $companyNameEn = trim($jsonInput['company_name_en'] ?? ($existing['company_name_en'] ?? ''));
        $bizNumber = trim($jsonInput['business_number'] ?? $existing['business_number']);
        $phone = trim($jsonInput['phone'] ?? $existing['phone']);
        $shippingAddress = trim($jsonInput['shipping_address'] ?? ($existing['shipping_address'] ?? ''));
        $shippingAddressEn = trim($jsonInput['shipping_address_en'] ?? ($existing['shipping_address_en'] ?? ''));
        $role = trim($jsonInput['role'] ?? $existing['role']);
        $tier = trim($jsonInput['tier'] ?? $existing['tier']);
        $status = trim($jsonInput['status'] ?? $existing['status']);
        $password = trim($jsonInput['password'] ?? '');

        // 기타사항(notes) 및 비고사항(remarks)은 오직 SUPER_ADMIN(총괄관리자)만 수정 가능
        $isSuperAdmin = !empty($_SESSION['role']) && $_SESSION['role'] === 'SUPER_ADMIN';
        $notes = $existing['notes'];
        $remarks = $existing['remarks'];

        if ($isSuperAdmin) {
            if (array_key_exists('notes', $jsonInput)) {
                $notes = trim($jsonInput['notes'] ?? '');
            }
            if (array_key_exists('remarks', $jsonInput)) {
                $remarks = trim($jsonInput['remarks'] ?? '');
            }
        }

        // 이메일 중복 체크 (타 사용자와 중복 방지)
        if ($email !== $existing['email']) {
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkEmail->execute([$email, $id]);
            if ($checkEmail->fetch()) {
                echo json_encode(['success' => false, 'message' => '이미 다른 계정에서 사용 중인 이메일입니다.']);
                exit;
            }
        }

        $updateFields = [
            'user_type' => $userType,
            'email' => $email,
            'name' => $name,
            'name_en' => !empty($nameEn) ? $nameEn : null,
            'company_name' => !empty($companyName) ? $companyName : null,
            'company_name_en' => !empty($companyNameEn) ? $companyNameEn : null,
            'business_number' => !empty($bizNumber) ? $bizNumber : null,
            'phone' => !empty($phone) ? $phone : null,
            'shipping_address' => !empty($shippingAddress) ? $shippingAddress : null,
            'shipping_address_en' => !empty($shippingAddressEn) ? $shippingAddressEn : null,
            'role' => $role,
            'tier' => in_array($tier, ['TIER_1', 'TIER_2', 'TIER_3']) ? $tier : 'TIER_1',
            'notes' => !empty($notes) ? $notes : null,
            'remarks' => !empty($remarks) ? $remarks : null,
            'status' => in_array($status, ['ACTIVE', 'PENDING', 'SUSPENDED']) ? $status : 'ACTIVE'
        ];

        // 비밀번호 입력이 있을 경우에만 해시하여 업데이트
        $sql = "UPDATE users SET 
                user_type = :user_type,
                email = :email,
                name = :name,
                name_en = :name_en,
                company_name = :company_name,
                company_name_en = :company_name_en,
                business_number = :business_number,
                phone = :phone,
                shipping_address = :shipping_address,
                shipping_address_en = :shipping_address_en,
                role = :role,
                tier = :tier,
                notes = :notes,
                remarks = :remarks,
                status = :status";

        $params = [];
        foreach ($updateFields as $key => $val) {
            $params[':' . $key] = $val;
        }

        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $params[':id'] = $id;

        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute($params);

        echo json_encode([
            'success' => true,
            'message' => '사용자 정보가 성공적으로 수정되었습니다.'
        ]);
        exit;
    }

    // 4. 사용자 삭제 (action=delete)
    if ($action === 'delete') {
        $id = (int)($jsonInput['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 사용자 ID입니다.']);
            exit;
        }

        // 현재 본인 계정 삭제 방지
        if ($id === (int)$_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => '현재 로그인 중인 본인 관리자 계정은 삭제할 수 없습니다.']);
            exit;
        }

        $delStmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $delStmt->execute([':id' => $id]);

        if ($delStmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => '사용자가 삭제되었습니다.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => '삭제할 사용자를 찾을 수 없습니다.']);
        }
        exit;
    }

    // 5. 거래처 가입 승인 (action=approve)
    if ($action === 'approve') {
        $id = (int)($jsonInput['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 사용자 ID입니다.']);
            exit;
        }

        $approveStmt = $conn->prepare("UPDATE users SET status = 'ACTIVE' WHERE id = :id");
        $approveStmt->execute([':id' => $id]);

        if ($approveStmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => '거래처 계정이 성공적으로 승인되었습니다. 이제 해당 계정으로 로그인이 가능합니다.'
            ]);
        } else {
            $checkStmt = $conn->prepare("SELECT status FROM users WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            $u = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$u) {
                echo json_encode(['success' => false, 'message' => '사용자를 찾을 수 없습니다.']);
            } else if ($u['status'] === 'ACTIVE') {
                echo json_encode(['success' => true, 'message' => '이미 활성화(승인)된 계정입니다.']);
            } else {
                echo json_encode(['success' => false, 'message' => '계정 상태를 승인할 수 없습니다.']);
            }
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => '지원하지 않는 액션입니다.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>

