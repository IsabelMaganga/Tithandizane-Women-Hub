<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create a default admin user';

    public function handle()
    {
        $adminCount = Admin::count();
        
        if ($adminCount > 0) {
            $this->info('Admin users already exist.');
            return 0;
        }

        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@tithandizane.com',
            'password' => Hash::make('password'),
        ]);

        $this->info('Default admin user created successfully.');
        $this->info('Email: admin@tithandizane.com');
        $this->info('Password: password');
        
        return 0;
    }
}
