<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    try {
        // Use mysqli to backup the database
        $mysqli = new \mysqli($host, $username, $password, $databaseName);
        if ($mysqli->connect_error) {
            throw new \Exception('Connection error: ' . $mysqli->connect_error);
        }

        $tables = $mysqli->query('SHOW TABLES');
        $backupData = '';

        while ($table = $tables->fetch_row()) {
            $backupData .= "DROP TABLE IF EXISTS `{$table[0]}`;\n";
            $createTableQuery = $mysqli->query("SHOW CREATE TABLE `{$table[0]}`")->fetch_row();
            $backupData .= $createTableQuery[1] . ";\n";

            $rows = $mysqli->query("SELECT * FROM `{$table[0]}`");
            while ($row = $rows->fetch_assoc()) {
                $backupData .= "INSERT INTO `{$table[0]}` (" . implode(",", array_keys($row)) . ") VALUES ('" . implode("','", array_values($row)) . "');\n";
            }
        }

        // Save the backup data to a file
        file_put_contents($backupFilePath, $backupData);

        // Set full permissions (read, write, execute for all users)
        chmod($backupFilePath, 0777);

        $mysqli->close();

        return response()->json(['message' => 'Database backup created successfully with full permissions!', 'backup_file' => basename($backupFilePath)]);
    } catch (\Exception $e) {
        Log::error("Database backup failed using mysqli", ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Database backup failed!'], 500);
    }
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

    try {
        // Establish connection with the database
        $mysqli = new \mysqli($host, $username, $password, $databaseName);
        if ($mysqli->connect_error) {
            throw new \Exception('Connection error: ' . $mysqli->connect_error);
        }

        // Disable foreign key checks
        $mysqli->query("SET foreign_key_checks = 0;");

        // Read the backup file and clean up unnecessary whitespace
        $backupData = file_get_contents($backupPath);

        // Trim the data and replace multiple newlines with a single one
        $backupData = trim(preg_replace('/\n+/', "\n", $backupData));

        // Split the backup data into individual SQL statements
        $queries = explode(";\n", $backupData);

        // Log the number of queries for debugging
        Log::info('Total queries found in the backup file: ' . count($queries));

        // Loop through each query and execute it
        foreach ($queries as $index => $query) {
            $query = trim($query);
            if ($query) {
                // Log each query for debugging purposes (optional)
                Log::info('Executing query #' . ($index + 1) . ': ' . $query);

                // Execute the query
                if (!$mysqli->query($query)) {
                    throw new \Exception("Error executing query: " . $mysqli->error . " Query: " . $query);
                }
            }
        }

        // Re-enable foreign key checks
        $mysqli->query("SET foreign_key_checks = 1;");

        $mysqli->close();

        return response()->json(['message' => 'Database restored successfully from backup!', 'file' => $fileName]);

    } catch (\Exception $e) {
        // Log the error and return the response
        Log::error("Database restore failed using mysqli", ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Database restore failed! ' . $e->getMessage()], 500);
    }
}


}
