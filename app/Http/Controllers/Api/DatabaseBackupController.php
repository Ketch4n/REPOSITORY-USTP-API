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
    }
}

