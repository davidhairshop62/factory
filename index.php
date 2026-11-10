<?php
require_once __DIR__ . '/config.php';

$dbStatus = false;
$dbMessage = '';
$tableCount = 0;

try {
    if (isset($conn)) {
        $dbStatus = true;
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tableCount = count($tables);
        $dbMessage = "MySQL DB '{$dbname}' 정상 연결됨 (테이블 수: {$tableCount})";
    }
} catch (Exception $e) {
    $dbMessage = "연결 오류: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-200">
                F
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Factory Management</h1>
                <p class="text-sm text-slate-500">factory.davidhairshop.com</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="p-4 rounded-xl border <?= $dbStatus ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?> flex items-start space-x-3">
                <span class="inline-block w-2.5 h-2.5 rounded-full mt-1.5 <?= $dbStatus ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                <div>
                    <h2 class="font-semibold text-sm">데이터베이스 연결 상태</h2>
                    <p class="text-xs mt-0.5 opacity-90"><?= htmlspecialchars($dbMessage) ?></p>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-2 text-slate-600">
                <div class="flex justify-between">
                    <span class="font-medium text-slate-500">호스트:</span>
                    <span class="font-mono text-slate-700"><?= htmlspecialchars($servername) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-slate-500">데이터베이스:</span>
                    <span class="font-mono text-slate-700"><?= htmlspecialchars($dbname) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-slate-500">보유 테이블:</span>
                    <span class="font-mono text-slate-700"><?= $tableCount ?>개</span>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
            Factory System &copy; <?= date('Y') ?> David Hair Shop
        </div>
    </div>
</body>
</html>

