<?php
// Check admins using Laravel's database connection
$pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');

echo "=== Checking Admins Table ===\n";

try {
    // Get all admins
    $stmt = $pdo->query("SELECT id, email, name, password FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($admins) . " admin users:\n";
    foreach ($admins as $admin) {
        echo "ID: {$admin['id']}\n";
        echo "Email: {$admin['email']}\n";
        echo "Name: {$admin['name']}\n";
        echo "Password starts with: " . substr($admin['password'], 0, 10) . "...\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Check Complete ===\n";
?>
