<?php
require_once __DIR__ . '/config.php';

// Reset admin password to admin1234!
$adminHash = password_hash('admin1234!', PASSWORD_BCRYPT);
$conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$adminHash]);
echo "1. Admin password set to admin1234!\n";

// Check user salon_test_01
$stmt = $conn->query("SELECT id, username, company_name, status FROM users WHERE username = 'salon_test_01'");
$salon = $stmt->fetch(PDO::FETCH_ASSOC);
echo "2. Applied salon status: " . ($salon ? json_encode($salon, JSON_UNESCAPED_UNICODE) : "NOT FOUND") . "\n";

// Approve salon_test_01
if ($salon) {
    $conn->prepare("UPDATE users SET status = 'ACTIVE' WHERE id = ?")->execute([$salon['id']]);
    echo "3. Approved salon_test_01! Status updated to ACTIVE.\n";
}

// Verify approval
$salonAfter = $conn->query("SELECT id, username, status FROM users WHERE username = 'salon_test_01'")->fetch(PDO::FETCH_ASSOC);
echo "4. After approval: " . json_encode($salonAfter) . "\n";
