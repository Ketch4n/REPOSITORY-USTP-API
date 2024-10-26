<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        $databaseName = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        // Define backup file path
        $backupFilePath = storage_path("app/backup/{$databaseName}_" . date('Y-m-d_H-i-s') . ".sql");

        // Run mysqldump command to back up the database
        $command = "mysqldump --user={$username} --password={$password} --host={$host} {$databaseName} > {$backupFilePath}";

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Database backup failed!'], 500);
        }

        return response()->json(['message' => 'Database backup created successfully!', 'backup_file' => basename($backupFilePath)]);
    }

    public function listBackups()
    {
        $backupPath = storage_path('app/backup');
        $files = [];

        // Check if the backup directory exists
        if (is_dir($backupPath)) {
            // Scan the directory for files
            $files = array_diff(scandir($backupPath), ['.', '..']);
        }

        // Return the list of backup files
        return response()->json($files);
    }
}
