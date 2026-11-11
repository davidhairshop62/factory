<?php
// api_factory_info.php - 공장 기본 정보 조회 및 수정 API (관리자 전용)
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// 관리자 로그인 권한 검증
if (empty($_SESSION['user_id']) || empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => '관리자 권한이 필요합니다. 관리자로 로그인 후 이용해 주세요.'
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDbConnection();

    // 테이블 자동 생성 및 초기 데이터 보장
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS factory_info (
            id INT PRIMARY KEY AUTO_INCREMENT,
            company_name VARCHAR(150) NOT NULL,
            contact_person VARCHAR(100) NULL,
            address_1 VARCHAR(255) NULL,
            address_2 VARCHAR(255) NULL,
            phone VARCHAR(50) NULL,
            email VARCHAR(100) NULL,
            tax_id VARCHAR(50) NULL,
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 기본 레코드(id=1) 존재 확인
    $stmt = $pdo->prepare("SELECT * FROM factory_info WHERE id = 1 LIMIT 1");
    $stmt->execute();
    $factory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$factory) {
        $insertStmt = $pdo->prepare("
            INSERT INTO factory_info (id, company_name, contact_person, address_1, address_2, phone, email, notes)
            VALUES (1, :company_name, :contact_person, :address_1, :address_2, :phone, :email, :notes)
        ");
        $insertStmt->execute([
            ':company_name'   => 'DAVID HAIR (GUDANG RAMBUT)',
            ':contact_person' => 'NURHASANA TUNISA',
            ':address_1'      => 'KP. CISALADA RT02 RW05 DS CIAMPEA UDIK, KEL CIAMPEA KAB',
            ':address_2'      => 'BOGOR, JAWA BARAT, INDONESIA 16620',
            ':phone'          => '+62 88291335199',
            ':email'          => 'info@davidhairshop.com',
            ':notes'          => '인도네시아 본사 맞춤 가발/헤어피스 제조 및 국제 출하 총괄 기지'
        ]);

        $stmt = $pdo->prepare("SELECT * FROM factory_info WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $factory = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 1. 공장 정보 조회 (GET)
    if ($method === 'GET') {
        echo json_encode([
            'success' => true,
            'data'    => $factory
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2. 공장 정보 등록 및 수정 (POST)
    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!$input || !is_array($input)) {
            $input = $_POST;
        }

        $companyName   = trim($input['company_name'] ?? '');
        $contactPerson = trim($input['contact_person'] ?? '');
        $address1      = trim($input['address_1'] ?? '');
        $address2      = trim($input['address_2'] ?? '');
        $phone         = trim($input['phone'] ?? '');
        $email         = trim($input['email'] ?? '');
        $taxId         = trim($input['tax_id'] ?? '');
        $notes         = trim($input['notes'] ?? '');

        if (empty($companyName)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '회사명(공장명)은 필수 입력 항목입니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $updateStmt = $pdo->prepare("
            UPDATE factory_info SET
                company_name   = :company_name,
                contact_person = :contact_person,
                address_1      = :address_1,
                address_2      = :address_2,
                phone          = :phone,
                email          = :email,
                tax_id         = :tax_id,
                notes          = :notes,
                updated_at     = NOW()
            WHERE id = 1
        ");

        $updateStmt->execute([
            ':company_name'   => $companyName,
            ':contact_person' => $contactPerson,
            ':address_1'      => $address1,
            ':address_2'      => $address2,
            ':phone'          => $phone,
            ':email'          => $email,
            ':tax_id'         => $taxId,
            ':notes'          => $notes
        ]);

        // 업데이트된 최신 정보 조회
        $stmt = $pdo->prepare("SELECT * FROM factory_info WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $updatedFactory = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => '공장 기본 정보가 성공적으로 저장되었습니다.',
            'data'    => $updatedFactory
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '지원하지 않는 HTTP 메서드입니다.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
