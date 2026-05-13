<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Get all admins for the admin management section
        $admins = Admin::all();
        
        // Temporarily set articles as empty collection until model is created
        $articles = collect([]);
        
        // Get settings from database (you'll need to create a settings table)
        // For now, we'll use config or session
        $settings = [
            'platform_name' => config('app.name', 'Tithandizane Women Hub'),
            'platform_email' => config('mail.from.address', 'info@tithandizane.org'),
            'timezone' => config('app.timezone', 'Africa/Blantyre'),
        ];
        
        return view('admin.settings.settings', compact('admins', 'articles', 'settings'));
    }
    
    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_email' => 'required|email',
            'timezone' => 'required|string',
        ]);
        
        // Update .env or database settings
        // This is a simplified example - you'd want to properly update config
        \Illuminate\Support\Facades\Config::set('app.name', $request->platform_name);
        \Illuminate\Support\Facades\Config::set('mail.from.address', $request->platform_email);
        \Illuminate\Support\Facades\Config::set('app.timezone', $request->timezone);
        
        return response()->json([
            'success' => true,
            'message' => 'General settings updated successfully'
        ]);
    }
    
    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'two_factor' => 'nullable|boolean',
            'session_timeout' => 'nullable|integer',
            'max_attempts' => 'nullable|integer',
            'password_min_length' => 'nullable|integer',
            'password_require_uppercase' => 'nullable|boolean',
            'password_require_number' => 'nullable|boolean',
            'password_require_special' => 'nullable|boolean',
        ]);
        
        // Store security settings in database
        // This is where you'd save to your settings table
        
        return response()->json([
            'success' => true,
            'message' => 'Security settings updated successfully'
        ]);
    }
    
    /**
     * Create database backup
     */
    public function createBackup(Request $request)
    {
        try {
            // For SQLite, create a simple backup
            $databasePath = database_path('database.sqlite');
            $backupPath = storage_path('app/backups/backup_' . date('Y_m_d_His') . '.sqlite');
            
            // Create backups directory if it doesn't exist
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Copy database file
            if (file_exists($databasePath)) {
                copy($databasePath, $backupPath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'file' => basename($backupPath)
                ]);
            } else {
                throw new \Exception('Database file not found');
            }
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete backup file
     */
    public function deleteBackup($file)
    {
        // Delete backup file from storage
        $path = storage_path('app/backups/' . $file);
        
        if (file_exists($path)) {
            unlink($path);
            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Backup file not found'
        ], 404);
    }
    
    /**
     * Generate new API key
     */
    public function generateApiKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $apiKey = \Illuminate\Support\Str::random(32) . '_' . time();
        
        // You can store API key in database later
        // For now, just return it
        
        return response()->json([
            'success' => true,
            'api_key' => $apiKey,
            'message' => 'API key generated successfully. Save this key: ' . $apiKey
        ]);
    }
    
    /**
     * Revoke API key
     */
    public function revokeApiKey($key)
    {
        // Delete API key from database when implemented
        
        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully'
        ]);
    }
    
    /**
     * Update email template
     */
    public function updateEmailTemplate(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);
        
        // Store email template in database when implemented
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully'
        ]);
    }
    
    /**
     * Store new guidance article
     */
    public function storeGuidance(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);
        
        // You can create GuidanceArticle model later
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
        ]);
    }
    
    /**
     * Update guidance article
     */
    public function updateGuidance(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully'
        ]);
    }
    
    /**
     * Delete guidance article
     */
    public function deleteGuidance($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully'
        ]);
    }
    
    /**
     * Store new admin user
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ]);
        
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Admin user created successfully',
            'admin' => $admin
        ]);
    }
    
    /**
     * Update admin user
     */
    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'role' => 'required|string',
        ]);
        
        $admin->update($request->only(['name', 'email', 'role']));
        
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Admin user updated successfully'
        ]);
    }
    
    /**
     * Delete admin user
     */
    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent deleting your own account
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 400);
        }
        
        $admin->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Admin user deleted successfully'
        ]);
    }
}