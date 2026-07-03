<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->map(fn($file) => [
                'name' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'date' => Storage::disk('local')->lastModified($file),
                'path' => $file,
            ])->sortByDesc('date')->values();

        return view('admin.backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            $filename = 'backup_' . date('Y_m_d_His') . '.sql';
            $dir      = storage_path('app/backups');

            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $sql = $this->generateSqlDump();

            file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $sql);

            return back()->with('success', "Backup created: {$filename}");
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download($file)
    {
        // Prevent path traversal
        $file = basename($file);
        $path = storage_path('app/backups/' . $file);

        if (!file_exists($path)) abort(404);

        return response()->download($path, $file, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $file . '"',
        ]);
    }

    public function destroy($file)
    {
        $file = basename($file);
        $path = 'backups/' . $file;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return back()->with('success', "Backup '{$file}' deleted.");
        }

        return back()->with('error', 'Backup file not found.');
    }

    // ── Pure-PHP SQL dump (no mysqldump binary required) ──────────────────────

    private function generateSqlDump(): string
    {
        $dbName = config('database.connections.mysql.database');
        $lines  = [];

        $lines[] = '-- Database Backup: ' . $dbName;
        $lines[] = '-- Generated: ' . now()->toDateTimeString();
        $lines[] = '-- Laravel College Management System';
        $lines[] = '';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $lines[] = 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';
        $lines[] = 'SET time_zone="+00:00";';
        $lines[] = '';

        $tables = DB::select('SHOW TABLES');
        $col    = 'Tables_in_' . $dbName;

        foreach ($tables as $tableRow) {
            $table = $tableRow->$col;

            // DROP + CREATE
            $lines[] = "-- Table: `{$table}`";
            $lines[] = "DROP TABLE IF EXISTS `{$table}`;";

            $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
            $lines[]   = $createRow[0]->{'Create Table'} . ';';
            $lines[]   = '';

            // Rows
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                $lines[] = "-- (no rows in `{$table}`)";
                $lines[] = '';
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $colList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

            $chunkSize = 100;
            foreach ($rows->chunk($chunkSize) as $chunk) {
                $valueSets = $chunk->map(function ($row) use ($columns) {
                    $vals = array_map(function ($col) use ($row) {
                        $v = ((array) $row)[$col];
                        if ($v === null) return 'NULL';
                        if (is_numeric($v)) return $v;
                        return "'" . addslashes((string) $v) . "'";
                    }, $columns);
                    return '(' . implode(', ', $vals) . ')';
                })->implode(",\n  ");

                $lines[] = "INSERT INTO `{$table}` ({$colList}) VALUES";
                $lines[] = "  " . $valueSets . ";";
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $lines);
    }
}
