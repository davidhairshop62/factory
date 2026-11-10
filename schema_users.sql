-- ==============================================================================
-- Table: users (사용자 및 계정 관리)
-- 구분: 관리자(ADMIN) / 거래처(CLIENT)
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_type` ENUM('ADMIN', 'CLIENT') NOT NULL COMMENT '사용자 구분 (ADMIN: 본사 관리자, CLIENT: B2B 거래처)',
    `username` VARCHAR(50) NOT NULL UNIQUE COMMENT '로그인 아이디',
    `email` VARCHAR(100) NOT NULL UNIQUE COMMENT '이메일 주소',
    `password` VARCHAR(255) NOT NULL COMMENT '암호화된 비밀번호 (Bcrypt Hash)',
    `name` VARCHAR(50) NOT NULL COMMENT '담당자 이름',
    `company_name` VARCHAR(100) NULL COMMENT '상호명 / 살롱명 (거래처 필수)',
    `company_id` INT NULL COMMENT '연동 거래처 ID (Companies 확장용)',
    `business_number` VARCHAR(30) NULL COMMENT '사업자등록번호 / ABN',
    `phone` VARCHAR(30) NULL COMMENT '연락처',
    `role` VARCHAR(30) NOT NULL DEFAULT 'CLIENT' COMMENT '세부 역할 (SUPER_ADMIN, ADMIN, OPERATOR, ACCOUNTANT, CLIENT)',
    `tier` ENUM('TIER_1', 'TIER_2', 'TIER_3') DEFAULT 'TIER_1' COMMENT '거래처 공급가 티어 (CLIENT 전용)',
    `status` ENUM('ACTIVE', 'PENDING', 'SUSPENDED') NOT NULL DEFAULT 'ACTIVE' COMMENT '계정 상태 (ACTIVE: 정상, PENDING: 승인대기, SUSPENDED: 정지)',
    `last_login_at` DATETIME NULL COMMENT '최근 로그인 일시',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '가입/생성일시',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '정보 수정일시',

    INDEX `idx_user_type` (`user_type`),
    INDEX `idx_status` (`status`),
    INDEX `idx_role` (`role`),
    INDEX `idx_company_name` (`company_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='통합 사용자 계정 테이블';
