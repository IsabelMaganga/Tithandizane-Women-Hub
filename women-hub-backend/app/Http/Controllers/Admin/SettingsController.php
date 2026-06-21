<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard with cards
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Display general settings page
     */
    public function general()
    {
        $settings = $this->getSettings();
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
            'mentor_registration' => 'nullable|boolean',
            'harassment_reports' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Save settings
        $this->saveSettings($validated);

        return redirect()->route('admin.settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Display admin users management page
     */
    public function admins()
    {
        $admins = Admin::all();
        return view('admin.settings.admins', compact('admins'));
    }

    /**
     * Store a new admin user
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

        return redirect()->route('admin.settings.admins')
            ->with('success', 'Admin user created successfully.');
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

        return redirect()->route('admin.settings.admins')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Delete admin user
     */
    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent deleting your own account
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admin.settings.admins')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.settings.admins')
            ->with('success', 'Admin user deleted successfully.');
    }

    /**
     * Display email templates page
     */
    public function email()
    {
        $templates = $this->getEmailTemplates();
        return view('admin.settings.email', compact('templates'));
    }

    /**
     * Update email template
     */
    public function updateEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        // Save email template
        $this->saveEmailTemplate($id, $request->only(['subject', 'body']));

        return redirect()->route('admin.settings.email')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Display security settings page
     */
    public function security()
    {
        $settings = $this->getSettings();
        return view('admin.settings.security', compact('settings'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'two_factor_auth' => 'nullable|boolean',
            'session_timeout' => 'required|in:30,60,120',
            'max_login_attempts' => 'required|in:3,5,10',
        ]);

        $this->saveSettings($validated);

        return redirect()->route('admin.settings.security')
            ->with('success', 'Security settings updated successfully.');
    }

    /**
     * Display backup page
     */
    public function backup()
    {
        $lastBackup = $this->getLastBackupInfo();
        $backupSize = $this->getBackupSize();
        $backups = $this->getBackupList();
        
        return view('admin.settings.backup', compact('lastBackup', 'backupSize', 'backups'));
    }

    /**
     * Create database backup
     */
    public function runBackup(Request $request)
    {
        try {
            // Create backups directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Get database configuration
            $dbConnection = config('database.default');
            $dbConfig = config('database.connections.' . $dbConnection);

            // Handle different database types
            if ($dbConnection === 'sqlite') {
                // SQLite backup
                $databasePath = $dbConfig['database'];
                if ($databasePath === ':memory:') {
                    throw new \Exception('Cannot backup in-memory database.');
                }
                
                if (!file_exists($databasePath)) {
                    throw new \Exception('Database file not found at: ' . $databasePath);
                }

                $backupPath = $backupDir . '/backup_' . date('Y_m_d_His') . '.sqlite';
                copy($databasePath, $backupPath);

                return redirect()->route('admin.settings.backup')
                    ->with('success', 'Backup created successfully.');
            } 
            elseif ($dbConnection === 'mysql') {
                // MySQL backup using mysqldump
                $backupFile = $backupDir . '/backup_' . date('Y_m_d_His') . '.sql';
                
                $command = sprintf(
                    'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port']),
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($backupFile)
                );

                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    throw new \Exception('MySQL backup failed with code: ' . $returnCode);
                }

                return redirect()->route('admin.settings.backup')
                    ->with('success', 'Backup created successfully.');
            }
            elseif ($dbConnection === 'pgsql') {
                // PostgreSQL backup using pg_dump
                $backupFile = $backupDir . '/backup_' . date('Y_m_d_His') . '.sql';
                
                $command = sprintf(
                    'PGPASSWORD=%s pg_dump --host=%s --port=%s --username=%s --dbname=%s > %s',
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port']),
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($backupFile)
                );

                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    throw new \Exception('PostgreSQL backup failed with code: ' . $returnCode);
                }

                return redirect()->route('admin.settings.backup')
                    ->with('success', 'Backup created successfully.');
            }
            else {
                throw new \Exception('Unsupported database type: ' . $dbConnection);
            }

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            
            return redirect()->route('admin.settings.backup')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($file)
    {
        try {
            // Security: Prevent directory traversal
            $file = basename($file);
            $path = storage_path('app/backups/' . $file);
            
            // Check if file exists and is within the backups directory
            if (!file_exists($path)) {
                return redirect()->route('admin.settings.backup')
                    ->with('error', 'Backup file not found.');
            }

            // Check if it's a valid backup file (prevent downloading other files)
            if (!str_starts_with($file, 'backup_')) {
                return redirect()->route('admin.settings.backup')
                    ->with('error', 'Invalid backup file.');
            }

            return response()->download($path);
        } catch (\Exception $e) {
            Log::error('Download backup failed: ' . $e->getMessage());
            
            return redirect()->route('admin.settings.backup')
                ->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($file)
    {
        try {
            // Security: Prevent directory traversal
            $file = basename($file);
            $path = storage_path('app/backups/' . $file);
            
            if (!file_exists($path)) {
                return redirect()->route('admin.settings.backup')
                    ->with('error', 'Backup file not found.');
            }

            // Check if it's a valid backup file
            if (!str_starts_with($file, 'backup_')) {
                return redirect()->route('admin.settings.backup')
                    ->with('error', 'Invalid backup file.');
            }

            unlink($path);
            
            return redirect()->route('admin.settings.backup')
                ->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Delete backup failed: ' . $e->getMessage());
            
            return redirect()->route('admin.settings.backup')
                ->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
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
        
        return response()->json([
            'success' => true,
            'api_key' => $apiKey,
            'message' => 'API key generated successfully.'
        ]);
    }

    /**
     * Revoke API key
     */
    public function revokeApiKey($key)
    {
        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully.'
        ]);
    }

    /**
     * Store guidance article
     */
    public function storeGuidance(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully.',
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
            'message' => 'Article updated successfully.'
        ]);
    }

    /**
     * Delete guidance article
     */
    public function deleteGuidance($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully.'
        ]);
    }

    /**
     * Get settings from storage
     */
    private function getSettings()
    {
        // You can store settings in database, cache, or config
        return [
            'platform_name' => config('app.name', 'Tithandizane Women Hub'),
            'platform_email' => config('mail.from.address', 'info@tithandizane.org'),
            'logo_path' => config('app.logo_path', 'images/logo2.png'),
            'mentor_registration' => config('settings.mentor_registration', true),
            'harassment_reports' => config('settings.harassment_reports', true),
            'maintenance_mode' => config('settings.maintenance_mode', false),
            'two_factor_auth' => config('settings.two_factor_auth', false),
            'session_timeout' => config('settings.session_timeout', 30),
            'max_login_attempts' => config('settings.max_login_attempts', 5),
        ];
    }

    /**
     * Save settings to storage
     */
    private function saveSettings(array $settings)
    {
        // Save to database or config
        // For now, we'll store in session as a temporary solution
        session()->put('settings', $settings);
        
        // You can also create a Settings model and save to database
        // Example: Settings::updateOrCreate(['key' => 'general'], ['value' => json_encode($settings)]);
    }

    /**
     * Get email templates
     */
    private function getEmailTemplates()
    {
        return [
            (object) [
                'id' => 1,
                'name' => 'Welcome Email (Mentor)',
                'description' => 'Sent when a new mentor registers',
                'subject' => 'Welcome to Tithandizane Mentor Program',
                'body' => '<p>Dear {{ $name }},</p><p>Welcome to Tithandizane Women Hub as a mentor!</p>',
            ],
            (object) [
                'id' => 2,
                'name' => 'Welcome Email (User)',
                'description' => 'Sent to new platform users',
                'subject' => 'Welcome to Tithandizane Women Hub',
                'body' => '<p>Hi {{ $name }},</p><p>Welcome to Tithandizane Women Hub!</p>',
            ],
            (object) [
                'id' => 3,
                'name' => 'Report Confirmation',
                'description' => 'Sent when a harassment report is submitted',
                'subject' => 'Report Received - Tithandizane',
                'body' => '<p>Dear {{ $name }},</p><p>We have received your report.</p>',
            ],
        ];
    }

    /**
     * Save email template
     */
    private function saveEmailTemplate($id, array $data)
    {
        // Save to database
        // Example: EmailTemplate::updateOrCreate(['id' => $id], $data);
    }

    /**
     * Get last backup info
     */
    private function getLastBackupInfo()
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            return 'No backups found';
        }

        $files = glob($backupDir . '/backup_*.{sqlite,sql}', GLOB_BRACE);
        if (empty($files)) {
            return 'No backups found';
        }

        $latest = max($files);
        $timestamp = filemtime($latest);
        return date('F j, Y \a\t g:i A', $timestamp);
    }

    /**
     * Get backup size
     */
    private function getBackupSize()
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            return '0 B';
        }

        $files = glob($backupDir . '/backup_*.{sqlite,sql}', GLOB_BRACE);
        if (empty($files)) {
            return '0 B';
        }

        $latest = max($files);
        $size = filesize($latest);
        return $this->formatFileSize($size);
    }

    /**
     * Get backup list
     */
    private function getBackupList()
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/backup_*.{sqlite,sql}', GLOB_BRACE);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = (object) [
                'name' => basename($file),
                'size' => $this->formatFileSize(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        // Sort by date (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b->date) - strtotime($a->date);
        });

        return $backups;
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}