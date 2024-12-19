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
        // Get all files in the 'backup' folder
        $backupFiles = Storage::files('backup');

        // Sort files by last modified time, newest first
        usort($backupFiles, function ($a, $b) {
            return Storage::lastModified($b) <=> Storage::lastModified($a);
        });

        // Return the list of file names (basename only)
        $backupFileNames = array_map(function ($file) {
            return basename($file);
        }, $backupFiles);

        return response()->json($backupFileNames);
    }


    public function deleteBackup($fileName)
    {
        // Define the file path
        $filePath = "backup/{$fileName}";

        // Check if the file exists
        if (Storage::exists($filePath)) {
            // Delete the file
            Storage::delete($filePath);

            return response()->json([
                'message' => 'Backup file deleted successfully!',
                'file' => $fileName
            ]);
        } else {
            return response()->json(['error' => 'File not found!'], 404);
        }
    }

    public function restoreBackup($fileName)
    {
        $backupPath = storage_path("app/backup/{$fileName}");

        // Check if the backup file exists
        if (!file_exists($backupPath)) {
            return response()->json(['error' => 'Backup file not found!'], 404);
        }

        // Get database credentials from .env
        $databaseName = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        // Command to restore the database
        $command = "mysql --user={$username} --password={$password} --host={$host} {$databaseName} < {$backupPath}";

        $output = [];
        $returnVar = 0;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Database restore failed!'], 500);
        }

        return response()->json(['message' => 'Database restored successfully!', 'file' => $fileName]);
    }



}
