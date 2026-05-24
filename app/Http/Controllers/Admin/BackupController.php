<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class BackupController extends Controller
{
    /**
     * List all database backups (ref: admin/myphpbackup.php).
     */
    public function index()
    {
        // Ensure backups directory exists
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        // Fetch all backup files
        $files = Storage::disk('local')->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'gz') {
                $name = basename($file);
                $sizeBytes = Storage::disk('local')->size($file);
                $createdAt = Storage::disk('local')->lastModified($file);

                $backups[] = [
                    'name' => $name,
                    'size' => $this->formatBytes($sizeBytes),
                    'created_at' => date('d-m-Y h:i A', $createdAt),
                    'timestamp' => $createdAt
                ];
            }
        }

        // Sort by newest backup first
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Get table counts for status indicators
        $dbName = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $tableCount = count($tables);

        return view('admin.backups.index', compact('backups', 'tableCount', 'dbName'));
    }

    /**
     * Create a new database backup.
     */
    public function create()
    {
        // Increase memory limit and execution time to prevent timeouts during backup
        ini_set('memory_limit', '512M');
        set_time_limit(900);

        try {
            $dbName = config('database.connections.mysql.database');
            $tables = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $dbName;
            $tableNames = [];

            foreach ($tables as $table) {
                if (isset($table->$key)) {
                    $tableNames[] = $table->$key;
                }
            }

            // Exporter logic replicating myphpbackup.php in pure PHP
            $sql = "-- MTLMart Database Backup Dump\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: `" . $dbName . "`\n\n";
            
            $sql .= "SET foreign_key_checks = 0;\n\n";

            foreach ($tableNames as $table) {
                // Drop Table
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";

                // Show Create Table
                $createResult = DB::select("SHOW CREATE TABLE `$table`")[0];
                $createTableSql = $createResult->{'Create Table'} ?? $createResult->{'create table'};
                $sql .= $createTableSql . ";\n\n";

                // Insert Rows in batches
                $rows = DB::select("SELECT * FROM `$table`");
                if (count($rows) > 0) {
                    $sql .= "INSERT INTO `$table` VALUES \n";
                    $valueRows = [];

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($val)) {
                                $values[] = $val;
                            } else {
                                // Escape strings securely
                                $escapedVal = addslashes($val);
                                $escapedVal = str_replace("\n", "\\n", $escapedVal);
                                $escapedVal = str_replace("\r", "\\r", $escapedVal);
                                $values[] = "'" . $escapedVal . "'";
                            }
                        }
                        $valueRows[] = "(" . implode(',', $values) . ")";
                    }
                    $sql .= implode(",\n", $valueRows) . ";\n\n";
                }
                $sql .= "\n";
            }

            $sql .= "SET foreign_key_checks = 1;\n";

            // Compress to gzip
            $gzipSql = gzencode($sql, 9);
            
            $fileName = 'backup-' . $dbName . '-' . date('dmY_His') . '.sql.gz';
            Storage::disk('local')->put('backups/' . $fileName, $gzipSql);

            return redirect()->route('admin.backups.index')->with('success', 'Database backup successfully created!');
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Error generating database backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific backup file.
     */
    public function download($fileName)
    {
        $filePath = 'backups/' . $fileName;

        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }

        return redirect()->route('admin.backups.index')->with('error', 'Backup file not found.');
    }

    /**
     * Delete a specific backup file.
     */
    public function destroy($fileName)
    {
        $filePath = 'backups/' . $fileName;

        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
            return redirect()->route('admin.backups.index')->with('success', 'Backup file successfully deleted!');
        }

        return redirect()->route('admin.backups.index')->with('error', 'Backup file not found.');
    }

    /**
     * Restore database from a stored backup file.
     */
    public function restore($fileName)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(900);

        $filePath = 'backups/' . $fileName;

        if (!Storage::disk('local')->exists($filePath)) {
            return redirect()->route('admin.backups.index')->with('error', 'Backup file not found.');
        }

        try {
            $compressedSql = Storage::disk('local')->get($filePath);
            
            // Decompress gzipped content
            $sql = gzdecode($compressedSql);
            if ($sql === false) {
                throw new Exception('Gzip decompression failed.');
            }

            // Disable foreign key checks, run raw unprepared SQL script, and re-enable
            DB::unprepared("SET foreign_key_checks = 0;");
            DB::unprepared($sql);
            DB::unprepared("SET foreign_key_checks = 1;");

            return redirect()->route('admin.backups.index')->with('success', 'Database successfully restored from backup: ' . $fileName);
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Error restoring database: ' . $e->getMessage());
        }
    }

    /**
     * Upload an SQL or SQL.GZ file and restore database.
     */
    public function uploadRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200' // Limit upload to 50MB
        ]);

        ini_set('memory_limit', '512M');
        set_time_limit(900);

        try {
            $file = $request->file('backup_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $tempPath = $file->getRealPath();

            $sql = '';

            if ($extension === 'gz') {
                $compressedSql = file_get_contents($tempPath);
                $sql = gzdecode($compressedSql);
                if ($sql === false) {
                    throw new Exception('Gzip decompression failed.');
                }
            } elseif ($extension === 'sql') {
                $sql = file_get_contents($tempPath);
            } else {
                return redirect()->route('admin.backups.index')->with('error', 'Invalid file type. Only .sql and .sql.gz files are allowed.');
            }

            // Disable foreign key checks, run raw SQL script, and re-enable
            DB::unprepared("SET foreign_key_checks = 0;");
            DB::unprepared($sql);
            DB::unprepared("SET foreign_key_checks = 1;");

            return redirect()->route('admin.backups.index')->with('success', 'Database successfully restored from uploaded file!');
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Error restoring database: ' . $e->getMessage());
        }
    }

    /**
     * Helper to format file sizes nicely.
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
