# Project Vision & Constitution: Factory ERP

> **AGENT INSTRUCTION:** Read this file before every iteration. It serves as the project's "Long-Term Memory" and constitution for the Stitch Build Loop.

## 1. Core Identity
* **Project Name:** Factory ERP - 맞춤형 헤어제품(가발/투페/톱피스/익스텐션) 주문·생산·회계 통합 시스템
* **Stitch Project ID:** `12760513028391886822` (`projects/12760513028391886822`)
* **Mission:** 다수의 B2B 거래처(살롱, 도매점, 뷰티숍, 클리닉 등)의 고난도 맞춤 수주 접수부터 공장 MES 바코드 공정 추적, 작업지시서 발행, 인보이스 및 수금 정산까지 전 과정을 하나의 통제 시스템으로 일원화하고 완벽한 역할 기반 데이터 격리(RBAC)를 제공한다.
* **Target Audience:**
  1. **B2B 거래처 (Client Portal):** 살롱 원장, 미용실 발주 담당자, 클리닉 상담 실장
  2. **본사 관리자 (Admin Console):** 대표 및 운영 총괄팀 (수주 승인, 단가 책정, 납기 통제)
  3. **공장/외주 생산 작업자 (MES Operator):** 공정별 바코드 스캔, 작업지시서 확인, QC 검품원
  4. **회계 담당자 (Accountant):** 인보이스 발행, 미수금 대사, 거래처별 여신 관리
* **Voice:** Industrial Precision, Authoritative, High-Fidelity Data Dense, Bespoke Luxury & Clinical Reliability.

## 2. Visual Language
*Reference these descriptors when prompting Stitch.*

* **The "Vibe" (Adjectives):**
  * *Primary:* **Dark Industrial Precision** (짙은 슬레이트 캔버스 위에 기계적 라인과 데이터 밀도가 조화된 엔지니어링 인터페이스)
  * *Secondary:* **Bespoke Atelier Luxury** (호박색/골드 엠버 악센트로 맞춤 가발 장인 정신과 프리미엄 가치를 표현)
  * *Tertiary:* **Clinical Operational Reliability** (에메랄드/시안 상태 인디케이터, 바코드/QR 중심의 즉각적인 공정 피드백)

## 3. Architecture & File Structure
* **Stitch Staging:** `.stitch/designs/{page}.html` & `.stitch/designs/{page}.png`
* **Production Deployment Root:** `/var/www/factory/` (`https://factory.davidhairshop.com`)
* **Asset Flow:** Stitch MCP Generate (`.stitch/designs/`) → Code Review & Ast/Responsive Validation → Production Integration (`site/` or root web apps)
* **Navigation Strategy:**
  * **Unified Gateway (`index.html`):** 통합 로그인 및 역할(Role) 선택 게이트웨이
  * **Client Portal (`/portal/`):** 상단 전용 네비게이션 (대시보드 / 신규발주 / 주문추적 / 원클릭 재주문 / 인보이스 / 문의)
  * **Admin Console (`/admin/`):** 좌측 고밀도 사이드바 (통합 관제 / 수주 관리 / MES 바코드 공정 / 거래처·단가 / 인보이스·회계)

## 4. Live Sitemap (Current State)
*Update this when a new page is successfully merged.*

### Gateway & Auth
* [x] `index.html` - 통합 로그인 및 역할(Client / Admin / MES / Accountant) 분기 게이트웨이 (완료)

### Client Portal (거래처 전용)
* [x] `portal-dashboard.html` - 거래처 대시보드 (진행 중 주문 현황, 당월 발주/미수금 요약, 실시간 알림 피드) (완료)
* [ ] `portal-order-new.html` - 맞춤 수주 등록 폼 (4대 품목: 가발/투페/톱피스/익스텐션 정밀 규격 입력기)
* [ ] `portal-order-list.html` - 주문/배송 타임라인 조회 (접수 → 제작 → QC → 출고 배송 추적)
* [ ] `portal-reorder.html` - 원클릭 재주문 (이전 출고 데이터 복제 및 부분 옵션 빠른 수정 발주)
* [ ] `portal-invoices.html` - 정산 및 인보이스 내역 (PDF 다운로드, 월별 청구서, 입금 확인증 제출)

### Admin Console (본사 관리자 및 MES)
* [x] `admin-dashboard.html` - 본사 통합 관제 대시보드 (신규 수주, D-Day 지연 위험, 공정 병목, 매출·미수금 현황) (완료)
* [ ] `admin-orders.html` - 통합 수주 관리 (전 거래처 주문 목록, 스펙 검토 승인/반려, 긴급 오더 할증 적용)
* [ ] `admin-mes-scanner.html` - 공정 관리 & 바코드/QR 스캐너 (작업지시서 출력, 공정 통과 바코드 스캔, QC 검품 기록)
* [ ] `admin-clients.html` - 거래처 및 단가 티어 관리 (Tier 1~3 공급가 정책, 여신 한도, 담당자 정보)
* [ ] `admin-accounting.html` - 인보이스 및 회계 원장 (미수금 A/R 관리, 입금 수동/자동 대사, 세금계산서 연동)

## 5. The Roadmap (Backlog)

### Phase 1: High Priority (Core Workflows)
- [x] **Task 1.1:** `index.html` - 통합 인증 & 2FA 역할 분기 화면 (완료)
- [x] **Task 1.2:** `admin-dashboard.html` - 관리자 통합 관제 대시보드 (병목 공정 & 핵심 KPI 헤드업 디스플레이) (완료)
- [x] **Task 1.3:** `portal-dashboard.html` - 거래처 전용 대시보드 (주문 상태 카드 & 직관적 액션) (완료)
- [ ] **Task 1.4:** `portal-order-new.html` - 품목별 정밀 사양 맞춤 주문 폼 (가발 치수, 베이스, 모발 밀도/컬 선택기)
- [ ] **Task 1.5:** `admin-mes-scanner.html` - MES 공정 작업지시서 및 현장 바코드/QR 스캐너 인터페이스

### Phase 2: Medium Priority (Management & Logistics)
- [ ] **Task 2.1:** `admin-orders.html` - 전체 수주 승인/검토 및 작업지시서 일괄 발행 콘솔
- [ ] **Task 2.2:** `portal-order-list.html` - 고객 관점 실시간 공정 진행 타임라인 및 송장 추적
- [ ] **Task 2.3:** `admin-clients.html` - B2B 거래처별 단가 티어(Tier) 및 여신 설정
- [ ] **Task 2.4:** `admin-accounting.html` - 인보이스 발행 및 미수금 자동 대사 원장
- [ ] **Task 2.5:** `portal-reorder.html` - 과거 주문 데이터 기반 1-Click 재발주 시스템

### Phase 3: Low Priority (Extensions & Advanced Analytics)
- [ ] **Task 3.1:** `print-job-order.html` - 공장 부착용 표준 2D DataMatrix/QR 작업지시서 인쇄 양식
- [ ] **Task 3.2:** `portal-invoices.html` - 거래처용 인터랙티브 거래명세서 및 입금 확인증 업로더
- [ ] **Task 3.3:** `admin-analytics.html` - 생산 효율, 리드타임 분석, 불량률 통계 분석 리포트

## 6. Creative Freedom Guidelines
*When the backlog is empty, follow these guidelines to innovate.*

1. **Stay On-Brand:** 항상 어두운 슬레이트(#0B1326, #0F172A)와 앰버 골드(#F59E0B), 정밀 에메랄드(#10B981) 컬러를 기본으로 기계적 신뢰성을 유지합니다.
2. **Enhance the Core:** 가발 및 모발 제품의 물리적 사양(치수, 밀도, 컬러 셰이드, 매듭 방식 등)을 직관적으로 시각화하는 부품/인터랙션 위젯을 추가합니다.
3. **Naming Convention:** 소문자 케밥 케이스(`kebab-case.html`)를 준수합니다.

### Ideas to Explore
- [ ] `spec-3d-visualizer.html` - 두상 6대 치수 및 가발 캡 3D 인터랙티브 와이어프레임 매핑기
- [ ] `color-ring-selector.html` - 인모/인조모 혼방 컬러 링(#1B, #02, #60 등) 및 흰머리 혼합율 정밀 시뮬레이터
- [ ] `mes-kiosk.html` - 공장 벽면 터치스크린용 초간편 대형 바코드 스캔 키오스크 모드

## 7. Rules of Engagement
1. Section 4의 완료된 페이지를 중복 생성하지 않고 업데이트합니다.
2. 매 작업 완료 시 반드시 `.stitch/next-prompt.md`의 바톤을 다음 작업으로 갱신합니다.
3. Section 6의 아이디어를 구현한 경우 목록에서 해당 항목을 체크하거나 정리합니다.

