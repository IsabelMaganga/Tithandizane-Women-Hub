<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    $adminCount = \App\Models\Admin::count();
    echo "Admin count: " . $adminCount . "\n";
    
    if ($adminCount == 0) {
        echo "No admin users found. Creating default admin...\n";
        
        \App\Models\Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@tithandizane.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        
        echo "Default admin created: admin@tithandizane.com / password\n";
    } else {
        echo "Admin users exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
