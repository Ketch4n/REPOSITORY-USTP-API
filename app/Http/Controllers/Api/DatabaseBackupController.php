<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
<<<<<<< HEAD
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
=======
>>>>>>> 3412113aea32b2f9a010a4588e568553c26bc661
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

<<<<<<< HEAD
        // Create a MySQLi connection
        $mysqli = new \mysqli($host, $username, $password, $databaseName);

        if ($mysqli->connect_error) {
            return response()->json(['error' => 'Database connection failed: ' . $mysqli->connect_error], 500);
        }

        // Open a file to save the backup
        $handle = fopen($backupFilePath, 'w');
        if (!$handle) {
            return response()->json(['error' => 'Failed to create backup file'], 500);
        }

        // Get all tables
        $tables = $mysqli->query('SHOW TABLES');
        while ($table = $tables->fetch_array()) {
            $tableName = $table[0];

            // Get table creation SQL
            $createTable = $mysqli->query("SHOW CREATE TABLE $tableName")->fetch_array();
            fwrite($handle, $createTable[1] . ";\n\n");

            // Get table data
            $rows = $mysqli->query("SELECT * FROM $tableName");
            while ($row = $rows->fetch_assoc()) {
                $rowValues = array_map([$mysqli, 'real_escape_string'], array_values($row));
                $rowValues = implode("', '", $rowValues);
                fwrite($handle, "INSERT INTO $tableName VALUES ('$rowValues');\n");
            }
            fwrite($handle, "\n");
        }

        fclose($handle);
        $mysqli->close();
        
        chmod($backupFilePath, 0777);

        // Check if the backup file was created
        if (!file_exists($backupFilePath)) {
            return response()->json(['error' => 'Database backup creation failed!'], 500);
        }

        // Upload the backup file to Firebase Storage
        try {
            $firebase = (new Factory)
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
=======
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
>>>>>>> 3412113aea32b2f9a010a4588e568553c26bc661
    }
}

