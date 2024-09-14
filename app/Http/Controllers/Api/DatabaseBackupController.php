<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use App\Http\Controllers\Controller;

class DatabaseBackupController extends Controller
{
    public function backupDatabaseToFirebase()
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
        // Log error or handle failure
        return response()->json(['error' => 'Database backup failed!'], 500);
    }
        try {
            $firebase = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
                ->createStorage();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to initialize Firebase storage!'], 500);
        }

            $bucket = $firebase->getBucket();

        // Upload the SQL file to Firebase Storage
        try {
            $bucket->upload(file_get_contents($backupFilePath), [
                'name' => 'backups/' . basename($backupFilePath),
        ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload backup to Firebase storage!'], 500);
        }

        // Return a success response
        return response()->json(['message' => 'Database backup uploaded successfully!']);
    }
}
