-- schema_factory_info.sql
-- 인도네시아 본사 제조공장 (Gudang Rambut) 기본 정보 테이블

CREATE TABLE IF NOT EXISTS factory_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(150) NOT NULL COMMENT '회사명 / 공장명',
    contact_person VARCHAR(100) NULL COMMENT '공장 담당자 성명',
    address_1 VARCHAR(255) NULL COMMENT '공장 상세주소 1 (마을, 리, 면, 구 등)',
    address_2 VARCHAR(255) NULL COMMENT '공장 상세주소 2 (도시, 주, 국가, 우편번호)',
    phone VARCHAR(50) NULL COMMENT '공장 대표 연락처 (TEL/WhatsApp)',
    email VARCHAR(100) NULL COMMENT '공장 대표 이메일',
    tax_id VARCHAR(50) NULL COMMENT '사업자등록/세무번호 (NPWP 등)',
    notes TEXT NULL COMMENT '공장 특이사항 및 생산 비고',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 초기 기본 공장 데이터 등록 (DAVID HAIR - GUDANG RAMBUT)
INSERT INTO factory_info (id, company_name, contact_person, address_1, address_2, phone, email, notes)
VALUES (
    1,
    'DAVID HAIR (GUDANG RAMBUT)',
    'NURHASANA TUNISA',
    'KP. CISALADA RT02 RW05 DS CIAMPEA UDIK, KEL CIAMPEA KAB',
    'BOGOR, JAWA BARAT, INDONESIA 16620',
    '+62 88291335199',
    'info@davidhairshop.com',
    '인도네시아 본사 맞춤 가발/헤어피스 제조 및 국제 출하 총괄 기지'
)
ON DUPLICATE KEY UPDATE
    company_name = VALUES(company_name),
    contact_person = VALUES(contact_person),
    address_1 = VALUES(address_1),
    address_2 = VALUES(address_2),
    phone = VALUES(phone);
