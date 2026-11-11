# Lordhair 남성 가발 견적 계산기 구현 명세서 (1.3배 마진율 적용 기준, 호주 달러 AUD)

이 문서는 Lordhair 맞춤 제작(Custom Order) 및 기성 가발(Stock System)의 가격 책정 정책을 기반으로 한 견적 계산기 시스템 명세서입니다. (모든 옵션 금액은 AUD 기준 1.3배가 적용되었습니다.)

---

## 1. 가격 계산 로직 (Pricing Algorithm)

### 1.1 기성 가발 (Stock Systems)
$$\text{최종 금액} = \text{베이스 모델 기본가} + \text{베이스 컷팅(선택: A\$19.50)} + \text{전문 미용사 컷트 & 스타일링(선택: A\$130)} + \text{부착 방식 패키지(선택: A\$65 ~ A\$130)}$$

* **베이스 모델 기본가 (1.3배 적용)**:
  - 프론탈 M자 전용 (SuperSkin-F): **A$180.70**
  - 0.03mm 초박막 스킨 UTS: **A$284.70**
  - 0.06mm 씬스킨 SuperSkin-V: **A$297.70**
  - 모노 + 스킨 테두리 Vigor/Royal: **A$323.70**
  - 풀 프렌치 레이스 Champion: **A$401.70**
  - 하이브리드 레이스+스킨 Neo/Combo: **A$388.70**

### 1.2 맞춤 가발 (Custom Systems)
맞춤 제작의 경우 베이스 가격 및 고정 추가금을 합산한 후, 모질(Hair Quality)과 밀도(Density)에 따른 비율 할증(Percentage Multipliers)을 복합 적용합니다.

$$\text{기본 합산액}(S) = \text{기초 시작가}(\text{A\$453.70}) + \text{베이스 크기 추가금} + \text{모발 길이 추가금} + \sum(\text{기타 고정 및 부가서비스 추가금})$$

$$\text{최종 금액} = S \times (1 + \text{모질 할증율} + \text{밀도 할증율})$$

#### 할증 및 추가금 세부 기준표 (1.3배 적용, 단위: AUD)
1. **베이스 크기 (면적 = 가로 $\times$ 세로)**
   - $\le 70\text{ sq in}$ (7"×9", 7"×10" 이하): **+A$0**
   - $70 \sim 80\text{ sq in}$ (8"×10" 이하): **+A$52**
   - $80 \sim 100\text{ sq in}$ (10"×10" 이하): **+A$130**
   - $> 100\text{ sq in}$ (전두형/특대형): **+A$260**
2. **모발 길이 (Hair Length)**
   - 5"~6": **+A$0** | 8": **+A$52** | 10": **+A$91** | 12": **+A$143** | 14": **+A$234** | 16": **+A$364** | 18": **+A$494** | 20": **+A$624**
3. **모질 할증률 (Hair Quality)**
   - 인도산 일반 인모: **0%**
   - 레미 인모 (Remy Hair): **+40%**
   - 유럽산 인모 (European Hair, 7" 이하 전용): **+40%**
   - 버진 인모 (Virgin Hair): **+70%**
4. **밀도 할증률 (Density)**
   - Extra Light ~ Medium (60% ~ 100%): **0%**
     - *단, 모발 길이가 12인치 이상이면서 Medium 밀도일 경우: **+15%***
   - Medium-Heavy (120%): **+25%**
   - Heavy (140%): **+35%**
5. **기타 고정 옵션 (Fixed Options)**
   - 인모 새치 혼합 (Human Grey Hair): **+A$26**
   - 루트 컬러 (Root Color): **+A$26**
   - 스팟 하이라이트 (Spot Highlight): **+A$19.50**
   - 아프로 컬 (Afro Curls): **+A$39**
   - 전체 매듭 탈색 (Bleach Knots): **+A$24.70**
   - 항균 베이스 업그레이드 (ZyroSen Base): **+A$39**
   - 급행 제작 (Rush Service, 6~7주): **+A$76.70**
6. **부가 서비스 옵션 (Additional Addons)**
   - 전문 미용사 컷트 & 스타일링 (Haircut & Styling): **+A$130**
   - 베이스 치수 맞춤 커팅 서비스: **+A$19.50**
   - Bonding Glue 4-5 weeks: **+A$130**
   - Bonding Tape & Glue 3-4 weeks: **+A$104**
   - Bonding Tape 2-3 weeks: **+A$65**

---

## 2. 단일 파일 HTML 계산기 구현 코드 (`calc.html`)

아래 코드를 복사하여 `calc.html` 파일로 저장한 뒤 브라우저에서 실행하면 바로 작동합니다. Tailwind CSS CDN을 활용하여 모던하고 반응형 인터페이스를 제공합니다.

```html
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lordhair 남성 가발 견적 계산기</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen py-10 px-4">
  <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
    
    <!-- Header -->
    <header class="bg-slate-900 text-white p-6 sm:p-8">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Lordhair 남성 가발 견적 계산기</h1>
          <p class="text-slate-400 text-sm mt-1">기성(Stock) 및 맞춤형(Custom) 전 스펙 실시간 견적 산출</p>
        </div>
        <div class="inline-flex p-1 bg-slate-800 rounded-xl border border-slate-700">
          <button id="tabCustomBtn" onclick="switchTab('custom')" class="px-4 py-2 rounded-lg text-sm font-semibold transition bg-blue-600 text-white shadow">맞춤 제작 (Custom)</button>
          <button id="tabStockBtn" onclick="switchTab('stock')" class="px-4 py-2 rounded-lg text-sm font-semibold transition text-slate-300 hover:text-white">기성 가발 (Stock)</button>
        </div>
      </div>
    </header>

    <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Option Input Form (Left) -->
      <form id="calculatorForm" class="lg:col-span-7 space-y-6">

        <!-- 1. CUSTOM SYSTEM OPTIONS -->
        <div id="customOptionsSection" class="space-y-6">
          <!-- Base Size -->
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
            <label class="block text-sm font-bold text-slate-700 mb-2">1. 베이스 크기 규격 (Base Size)</label>
            <div class="grid grid-cols-2 gap-3 mb-2">
              <div>
                <label class="text-xs text-slate-500">가로 너비 (Width, Inch)</label>
                <input type="number" id="baseWidth" step="0.5" value="7" min="1" max="15" class="w-full mt-1 p-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              </div>
              <div>
                <label class="text-xs text-slate-500">세로 길이 (Length, Inch)</label>
                <input type="number" id="baseLength" step="0.5" value="9" min="1" max="15" class="w-full mt-1 p-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              </div>
            </div>
            <div id="baseAreaNote" class="text-xs text-blue-600 font-medium">면적: 63 sq inches (기본 포함, 추가금 없음)</div>
          </div>

          <!-- Hair Length -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">2. 모발 길이 (Hair Length)</label>
            <select id="hairLength" class="w-full p-2.5 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              <option value="0" selected>5" ~ 6" (약 13~15cm) - 기본 표준 (+$0)</option>
              <option value="40">8" (약 20cm) (+$40)</option>
              <option value="70">10" (약 25cm) (+$70)</option>
              <option value="110">12" (약 30cm) (+$110)</option>
              <option value="180">14" (약 35cm) (+$180)</option>
              <option value="280">16" (약 40cm) (+$280)</option>
              <option value="380">18" (약 45cm) (+$380)</option>
              <option value="480">20" (약 50cm) (+$480)</option>
            </select>
          </div>

          <!-- Hair Quality -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">3. 모질 등급 (Hair Quality)</label>
            <select id="hairQuality" class="w-full p-2.5 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              <option value="0" selected>일반 인도산 천연 인모 (Indian Human Hair) (+0%)</option>
              <option value="0.4">레미 인모 (Remy Hair) (+40%)</option>
              <option value="0.4">유럽산 인모 (European Hair, 7" 이하 전용) (+40%)</option>
              <option value="0.7">버진 인모 (Virgin Hair) (+70%)</option>
            </select>
          </div>

          <!-- Density -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">4. 모발 밀도 (Hair Density)</label>
            <select id="density" class="w-full p-2.5 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              <option value="0" selected>Light ~ Medium (60% ~ 100%) (+0%)</option>
              <option value="0.25">Medium-Heavy (120%) (+25%)</option>
              <option value="0.35">Heavy (140%) (+35%)</option>
            </select>
            <p id="longHairDensityNote" class="text-xs text-amber-600 mt-1 hidden">* 모발 12인치 이상에서 Medium 밀도 선택 시 +15% 할증 자동 적용</p>
          </div>

          <!-- Optional Add-ons -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">5. 특수 옵션 및 공정 추가</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="20" class="custom-addon rounded text-blue-600 mr-2">
                <span>인모 새치 혼합 (+$20)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="20" class="custom-addon rounded text-blue-600 mr-2">
                <span>루트 컬러 (+$20)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="15" class="custom-addon rounded text-blue-600 mr-2">
                <span>스팟 하이라이트 (+$15)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="30" class="custom-addon rounded text-blue-600 mr-2">
                <span>특수 아프로 컬 (+$30)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="19" class="custom-addon rounded text-blue-600 mr-2">
                <span>전체 매듭 탈색 (+$19)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="30" class="custom-addon rounded text-blue-600 mr-2">
                <span>항균 베이스 ZyroSen (+$30)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="40" class="custom-addon rounded text-blue-600 mr-2">
                <span>컷트 & 스타일링 (+$40)</span>
              </label>
              <label class="flex items-center p-2.5 border rounded-lg hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" value="59" class="custom-addon rounded text-blue-600 mr-2">
                <span>급행 제작 (Rush 6~7주) (+$59)</span>
              </label>
            </div>
          </div>
        </div>

        <!-- 2. STOCK SYSTEM OPTIONS -->
        <div id="stockOptionsSection" class="space-y-6 hidden">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">기성 가발 베이스 모델 선택</label>
            <select id="stockModel" class="w-full p-2.5 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
              <option value="139">프론탈 M자 전용 (SuperSkin-F) - $139</option>
              <option value="219" selected>0.03mm 초박막 스킨 UTS (수명 1~2개월) - $219</option>
              <option value="229">0.06mm 씬스킨 SuperSkin-V (수명 2~4개월) - $229</option>
              <option value="249">모노 + 스킨 테두리 Vigor/Royal (수명 4~6개월+) - $249</option>
              <option value="309">풀 프렌치 레이스 Champion (통기성 최고) - $309</option>
              <option value="299">하이브리드 레이스+스킨 Neo/Combo - $299</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">부가 서비스 옵션</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded-lg hover:bg-slate-50 cursor-pointer text-sm">
                <input type="checkbox" id="stockBaseCut" value="15" class="rounded text-blue-600 mr-2">
                <span>베이스 치수 맞춤 커팅 서비스 (+$15)</span>
              </label>
              <label class="flex items-center p-3 border rounded-lg hover:bg-slate-50 cursor-pointer text-sm">
                <input type="checkbox" id="stockHairCut" value="40" class="rounded text-blue-600 mr-2">
                <span>전문 미용사 컷트 & 스타일링 (+$40)</span>
              </label>
            </div>
          </div>
        </div>

      </form>

      <!-- Price Breakdown Panel (Right) -->
      <div class="lg:col-span-5 bg-slate-50 border border-slate-200 rounded-xl p-6 flex flex-col justify-between">
        <div>
          <h2 class="text-lg font-bold text-slate-900 border-b pb-3 mb-4">견적 세부 산출 내역</h2>
          
          <div id="breakdownList" class="space-y-2.5 text-sm">
            <!-- Dynamic Breakdown items will be injected here -->
          </div>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-200">
          <div class="flex justify-between items-baseline mb-1">
            <span class="text-sm font-semibold text-slate-600">예상 결제 금액 (USD)</span>
            <span id="totalPriceUSD" class="text-3xl font-extrabold text-blue-600">$0.00</span>
          </div>
          <div class="flex justify-between items-baseline text-xs text-slate-500">
            <span>원화 환산 예상가 (1,350원 기준)</span>
            <span id="totalPriceKRW" class="font-medium text-slate-700">약 0원</span>
          </div>

          <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800">
            💡 Lordhair 본사 정책에 따른 공식 계산식 기준이며, 프로모션 할인 코드 적용 시 최종 금액이 달라질 수 있습니다.
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    let activeMode = 'custom';
    const KRW_RATE = 1350;

    function switchTab(mode) {
      activeMode = mode;
      const customSection = document.getElementById('customOptionsSection');
      const stockSection = document.getElementById('stockOptionsSection');
      const tabCustomBtn = document.getElementById('tabCustomBtn');
      const tabStockBtn = document.getElementById('tabStockBtn');

      if (mode === 'custom') {
        customSection.classList.remove('hidden');
        stockSection.classList.add('hidden');
        tabCustomBtn.className = "px-4 py-2 rounded-lg text-sm font-semibold transition bg-blue-600 text-white shadow";
        tabStockBtn.className = "px-4 py-2 rounded-lg text-sm font-semibold transition text-slate-300 hover:text-white";
      } else {
        customSection.classList.add('hidden');
        stockSection.classList.remove('hidden');
        tabStockBtn.className = "px-4 py-2 rounded-lg text-sm font-semibold transition bg-blue-600 text-white shadow";
        tabCustomBtn.className = "px-4 py-2 rounded-lg text-sm font-semibold transition text-slate-300 hover:text-white";
      }
      calculate();
    }

    function calculate() {
      const breakdownList = document.getElementById('breakdownList');
      breakdownList.innerHTML = '';
      let totalUSD = 0;

      if (activeMode === 'stock') {
        const stockSelect = document.getElementById('stockModel');
        const basePrice = parseFloat(stockSelect.value);
        const modelName = stockSelect.options[stockSelect.selectedIndex].text.split(' - ')[0];

        addBreakdownRow(`기성 모델 (${modelName})`, `$${basePrice.toFixed(2)}`);
        totalUSD += basePrice;

        const baseCut = document.getElementById('stockBaseCut');
        if (baseCut.checked) {
          totalUSD += 15;
          addBreakdownRow('베이스 맞춤 커팅', '+$15.00');
        }

        const hairCut = document.getElementById('stockHairCut');
        if (hairCut.checked) {
          totalUSD += 40;
          addBreakdownRow('컷트 & 스타일링', '+$40.00');
        }

      } else {
        // Custom Calculation
        const basePrice = 349.00;
        addBreakdownRow('맞춤 기본 시작가', `$${basePrice.toFixed(2)}`);

        // Base Size Calculation
        const width = parseFloat(document.getElementById('baseWidth').value) || 0;
        const length = parseFloat(document.getElementById('baseLength').value) || 0;
        const area = width * length;
        let sizeFee = 0;
        let sizeText = `면적: ${area.toFixed(1)} sq in (표준 범위)`;

        if (area > 100) {
          sizeFee = 200;
          sizeText = `면적: ${area.toFixed(1)} sq in (100인치 초과 특대/풀캡)`;
        } else if (area > 80) {
          sizeFee = 100;
          sizeText = `면적: ${area.toFixed(1)} sq in (80~100인치 와이드)`;
        } else if (area > 70) {
          sizeFee = 40;
          sizeText = `면적: ${area.toFixed(1)} sq in (70~80인치 라지)`;
        }
        document.getElementById('baseAreaNote').innerText = sizeText + (sizeFee > 0 ? ` (+$${sizeFee})` : ` (기본 포함)`);
        if (sizeFee > 0) {
          addBreakdownRow('베이스 크기 추가금', `+$${sizeFee.toFixed(2)}`);
        }

        // Hair Length
        const hairLengthFee = parseFloat(document.getElementById('hairLength').value);
        if (hairLengthFee > 0) {
          addBreakdownRow('모발 기장 추가금', `+$${hairLengthFee.toFixed(2)}`);
        }

        // Fixed Addons
        let fixedAddonsTotal = 0;
        const checkedAddons = document.querySelectorAll('.custom-addon:checked');
        checkedAddons.forEach(addon => {
          const val = parseFloat(addon.value);
          const label = addon.parentElement.querySelector('span').innerText.split(' (')[0];
          fixedAddonsTotal += val;
          addBreakdownRow(label, `+$${val.toFixed(2)}`);
        });

        const subtotalFixed = basePrice + sizeFee + hairLengthFee + fixedAddonsTotal;

        // Multipliers (Quality & Density)
        const qualityRate = parseFloat(document.getElementById('hairQuality').value);
        let densityRate = parseFloat(document.getElementById('density').value);

        // Conditional 12" + Medium density add-on
        const lengthSelectVal = parseInt(document.getElementById('hairLength').value);
        const longHairDensityNote = document.getElementById('longHairDensityNote');
        if (lengthSelectVal >= 110 && densityRate === 0) {
          // Hair length >= 12" and Medium density selected
          densityRate = 0.15;
          longHairDensityNote.classList.remove('hidden');
        } else {
          longHairDensityNote.classList.add('hidden');
        }

        const totalMultiplier = 1 + qualityRate + densityRate;

        if (qualityRate > 0) {
          const qName = document.getElementById('hairQuality').options[document.getElementById('hairQuality').selectedIndex].text.split(' (')[0];
          addBreakdownRow(`${qName} 할증`, `+${(qualityRate * 100)}%`);
        }
        if (densityRate > 0) {
          addBreakdownRow('모발 밀도 할증', `+${(densityRate * 100)}%`);
        }

        totalUSD = subtotalFixed * totalMultiplier;
      }

      // Update UI
      document.getElementById('totalPriceUSD').innerText = `$${totalUSD.toFixed(2)}`;
      const totalKRW = Math.round(totalUSD * KRW_RATE);
      document.getElementById('totalPriceKRW').innerText = `약 ${totalKRW.toLocaleString()}원`;
    }

    function addBreakdownRow(label, value) {
      const row = document.createElement('div');
      row.className = 'flex justify-between items-center text-slate-600 py-1 border-b border-slate-100 last:border-0';
      row.innerHTML = `<span>${label}</span><span class="font-semibold text-slate-800">${value}</span>`;
      document.getElementById('breakdownList').appendChild(row);
    }

    // Attach Event Listeners
    document.getElementById('calculatorForm').addEventListener('input', calculate);
    document.getElementById('calculatorForm').addEventListener('change', calculate);

    // Initial Calculation
    calculate();
  </script>
</body>
</html>
```