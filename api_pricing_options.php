<?php
// api_pricing_options.php - 가발 견적 옵션 금액 DB 관리 API
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = $conn;

    // 1. 테이블 자동 생성
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pricing_options (
            id INT PRIMARY KEY AUTO_INCREMENT,
            category VARCHAR(50) NOT NULL,
            item_key VARCHAR(50) NOT NULL UNIQUE,
            item_name VARCHAR(150) NOT NULL,
            item_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            unit VARCHAR(20) NOT NULL DEFAULT 'AUD',
            description VARCHAR(255) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. 초기 기본 데이터 정의
    $defaultItems = [
        // 맞춤 가발 기본가 (기존 349 * 1.3)
        ['custom_base', 'custom_base_price', '맞춤 가발 기초 시작가', 453.70, 'AUD', '표준 크기 ≤70 sq in, 기장 5~6인치, 일반 인모 포함', 10],

        // 베이스 크기 면적 추가금 (기존 * 1.3)
        ['area', 'area_70_80', '베이스 면적 70~80 sq in (라지)', 52.00, 'AUD', '가로×세로 면적 70 초과 80 이하', 20],
        ['area', 'area_80_100', '베이스 면적 80~100 sq in (와이드)', 130.00, 'AUD', '가로×세로 면적 80 초과 100 이하', 21],
        ['area', 'area_100_plus', '베이스 면적 100+ sq in (특대/풀캡)', 260.00, 'AUD', '가로×세로 면적 100 초과 전두형', 22],

        // 모발 기장 추가금 (기존 * 1.3)
        ['length', 'length_8', '모발 길이 8" (약 20cm)', 52.00, 'AUD', '표준 6인치 대비 2인치 연장', 30],
        ['length', 'length_10', '모발 길이 10" (약 25cm)', 91.00, 'AUD', '10인치 연장 기장', 31],
        ['length', 'length_12', '모발 길이 12" (약 30cm)', 143.00, 'AUD', '12인치 세미 롱 기장', 32],
        ['length', 'length_14', '모발 길이 14" (약 35cm)', 234.00, 'AUD', '14인치 롱 기장', 33],
        ['length', 'length_16', '모발 길이 16" (약 40cm)', 364.00, 'AUD', '16인치 엑스트라 롱 기장', 34],
        ['length', 'length_18', '모발 길이 18" (약 45cm)', 494.00, 'AUD', '18인치 특수 장모 기장', 35],
        ['length', 'length_20', '모발 길이 20" (약 50cm)', 624.00, 'AUD', '20인치 최고 장모 기장', 36],

        // 모질 할증율 (%)
        ['quality', 'quality_remy', '레미 인모 (Remy Hair)', 40.00, '%', '큐티클 결이 일치된 고급 인모 (+40%)', 40],
        ['quality', 'quality_european', '유럽산 인모 (European Hair)', 40.00, '%', '7인치 이하 전용 가는 고급 유럽모 (+40%)', 41],
        ['quality', 'quality_virgin', '버진 인모 (Virgin Hair)', 70.00, '%', '화학 처리 없는 최고급 생인모 (+70%)', 42],

        // 밀도 할증율 (%)
        ['density', 'density_med_heavy', 'Medium-Heavy 밀도 (120%)', 25.00, '%', '풍성한 숱 선호 시 (+25%)', 50],
        ['density', 'density_heavy', 'Heavy 밀도 (140%)', 35.00, '%', '최대 밀도 헤비 숱 (+35%)', 51],
        ['density', 'density_long_cond', '장모(12"+) 미디엄 조건부 할증', 15.00, '%', '12인치 이상 장모 선택 시 자동 적용 (+15%)', 52],

        // 특수 옵션 고정 금액 (기존 * 1.3)
        ['fixed_addon', 'addon_grey', '인모 새치 혼합', 26.00, 'AUD', '자연스러운 흰머리 비율 블렌딩', 60],
        ['fixed_addon', 'addon_root_color', '루트 컬러 (Root Color)', 26.00, 'AUD', '두피 근접 모근 톤 그라데이션', 61],
        ['fixed_addon', 'addon_highlight', '스팟 하이라이트', 19.50, 'AUD', '부분 포인트 컬러 염색', 62],
        ['fixed_addon', 'addon_afro', '특수 아프로 컬 (Afro Curls)', 39.00, 'AUD', '흑인 모발 특수 강한 텍스처 컬', 63],
        ['fixed_addon', 'addon_bleach', '전체 매듭 탈색 (Bleach Knots)', 24.70, 'AUD', '매듭점을 탈색하여 두피 모공 연출', 64],
        ['fixed_addon', 'addon_zyrosen', '항균 베이스 ZyroSen', 39.00, 'AUD', '의료용 나노 항균 및 냄새 방지 가공', 65],
        ['fixed_addon', 'addon_rush', '급행 제작 Rush Service', 76.70, 'AUD', '제작 소요 기간 6~7주로 단축 특급 배송', 66],

        // 부가 서비스 (살롱 컷트 & 부착 방식) (기존 * 1.3)
        ['service', 'service_haircut', '전문 미용사 컷트 & 스타일링', 130.00, 'AUD', '고객 두상/원하는 스타일에 맞춘 프로 컷팅', 70],
        ['service', 'service_basecut', '베이스 치수 맞춤 커팅 서비스', 19.50, 'AUD', '정밀 패턴에 맞춘 베이스 외곽선 가공', 71],
        ['service', 'service_bonding_glue', 'Bonding Glue 4-5 weeks', 130.00, 'AUD', '4~5주 강력 유지 방수 액상 접착 글루 패키지', 72],
        ['service', 'service_bonding_tape_glue', 'Bonding Tape & Glue 3-4 weeks', 104.00, 'AUD', '3~4주 유지 테이프 및 글루 복합 부착 패키지', 73],
        ['service', 'service_bonding_tape', 'Bonding Tape 2-3 weeks', 65.00, 'AUD', '2~3주 유지 저자극성 양면 테이프 패키지', 74],

        // 기성 가발 베이스 모델가 (기존 * 1.3)
        ['stock_model', 'stock_frontal', '프론탈 M자 전용 (SuperSkin-F)', 180.70, 'AUD', '앞머리 M자 탈모 집중 보완용 파셜 스킨', 80],
        ['stock_model', 'stock_uts', '0.03mm 초박막 스킨 UTS', 284.70, 'AUD', '1~2개월 수명, 피부처럼 밀착되는 초박막', 81],
        ['stock_model', 'stock_superskin_v', '0.06mm 씬스킨 SuperSkin-V', 297.70, 'AUD', '2~4개월 수명, 내구성과 밀착성 밸런스', 82],
        ['stock_model', 'stock_vigor_royal', '모노 + 스킨 테두리 Vigor/Royal', 323.70, 'AUD', '4~6개월+ 긴 수명, 튼튼한 모노 베이스', 83],
        ['stock_model', 'stock_champion', '풀 프렌치 레이스 Champion', 401.70, 'AUD', '전체 레이스 구조로 최고 수준의 통기성', 84],
        ['stock_model', 'stock_neo_combo', '하이브리드 레이스+스킨 Neo/Combo', 388.70, 'AUD', '앞이마 레이스 + 후두부 스킨 콤보', 85],

        // 기준 환율 (1 AUD 기준)
        ['exchange_rate', 'rate_usd', 'USD 환율 (1 AUD 당 미국달러)', 0.65, 'USD', '1 AUD = 0.65 USD', 90],
        ['exchange_rate', 'rate_krw', 'KRW 환율 (1 AUD 당 대한민국원)', 880.00, 'KRW', '1 AUD = 880 KRW', 91],
        ['exchange_rate', 'rate_eur', 'EUR 환율 (1 AUD 당 유럽유로)', 0.60, 'EUR', '1 AUD = 0.60 EUR', 92],
        ['exchange_rate', 'rate_idr', 'IDR 환율 (1 AUD 당 인도네시아루피아)', 10200.00, 'IDR', '1 AUD = 10,200 IDR', 93]
    ];

    // 누락된 기본 항목 삽입 (INSERT IGNORE)
    $insertStmt = $pdo->prepare("
        INSERT IGNORE INTO pricing_options 
        (category, item_key, item_name, item_price, unit, description, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($defaultItems as $item) {
        $insertStmt->execute($item);
    }

    // 3. GET: 모든 옵션 가격 조회 (공개 접근 가능 - 견적서 및 관리자 모두 사용)
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM pricing_options ORDER BY sort_order ASC, id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 빠른 매핑을 위한 key => price 맵 생성
        $priceMap = [];
        foreach ($rows as $r) {
            $priceMap[$r['item_key']] = (float)$r['item_price'];
        }

        echo json_encode([
            'success'  => true,
            'items'    => $rows,
            'priceMap' => $priceMap,
            'updated_at' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 4. POST: 옵션 가격 수정 저장 (관리자 권한 필수)
    if ($method === 'POST') {
        if (empty($_SESSION['user_id']) || empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => '옵션 금액 수정은 관리자 권한이 필요합니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!$input || !is_array($input)) {
            $input = $_POST;
        }

        // Action 분기 (개별 업데이트, 일괄 업데이트, 기본값 복원)
        $action = $input['action'] ?? 'update_batch';

        if ($action === 'reset_defaults') {
            // 초기 기본값으로 리셋
            $updateStmt = $pdo->prepare("UPDATE pricing_options SET item_price = ? WHERE item_key = ?");
            foreach ($defaultItems as $item) {
                $updateStmt->execute([$item[3], $item[1]]);
            }

            echo json_encode([
                'success' => true,
                'message' => '모든 옵션 금액이 기본값으로 초기화되었습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 일괄 업데이트: { prices: { custom_base_price: 349, ... } } 또는 { items: [ { item_key: '...', item_price: 350 } ] }
        $pdo->beginTransaction();
        $updateStmt = $pdo->prepare("UPDATE pricing_options SET item_price = :price WHERE item_key = :key");

        $updatedCount = 0;

        if (!empty($input['prices']) && is_array($input['prices'])) {
            foreach ($input['prices'] as $k => $v) {
                $p = floatval($v);
                $updateStmt->execute([':price' => $p, ':key' => $k]);
                $updatedCount++;
            }
        } elseif (!empty($input['items']) && is_array($input['items'])) {
            foreach ($input['items'] as $item) {
                if (isset($item['item_key']) && isset($item['item_price'])) {
                    $k = trim($item['item_key']);
                    $p = floatval($item['item_price']);
                    $updateStmt->execute([':price' => $p, ':key' => $k]);
                    $updatedCount++;
                }
            }
        }

        $pdo->commit();

        // 갱신된 데이터 재조회
        $stmt = $pdo->query("SELECT * FROM pricing_options ORDER BY sort_order ASC, id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $priceMap = [];
        foreach ($rows as $r) {
            $priceMap[$r['item_key']] = (float)$r['item_price'];
        }

        echo json_encode([
            'success'      => true,
            'message'      => "{$updatedCount}개 옵션 금액이 성공적으로 저장되었습니다.",
            'items'        => $rows,
            'priceMap'     => $priceMap,
            'updatedCount' => $updatedCount
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
