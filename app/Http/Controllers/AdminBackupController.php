<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Illuminate\Filesystem\FilesystemAdapter;

class AdminBackupController extends Controller
{
    private function getDisk()
    {
        return Storage::disk('local');
    }


    public function index()
    {
        return Inertia::render('Admin/IndexBackupPage', [
            'backupInfo' => $this->fetchBackupInfo(),
        ]);
    }


    public function createBackup()
    {
        $exitCode = Artisan::call('backup:run', ['--quiet' => true]);

        $message = $exitCode === 0
            ? 'Backup completed successfully'
            : 'Backup failed: ' . Artisan::output();

        session()->flash($exitCode === 0 ? 'success' : 'error', $message);

        return redirect()->back();
    }


    public function fetchBackupInfo()
    {
        $disk = $this->getDisk();
        $backupName = config('backup.backup.name') ?? env('APP_NAME', 'laravel-backup');
        $files = $disk->allFiles($backupName);

        $backups = [];
        foreach ($files as $file) {
            if (substr($file, -4) === '.zip') {
                $backups[] = [
                    'path' => $file,
                    'date' => date('M d, Y g:i A', $disk->lastModified($file)),
                    'size' => $this->formatBytes($disk->size($file)),
                ];
            }
        }

        usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        $totalSize = array_reduce($backups, fn($carry, $backup) => $carry + $disk->size($backup['path']), 0);

        // Determine storage type more safely
        $storageType = 'other';
        try {
            $diskConfig = config('filesystems.disks.local.driver');
            if ($diskConfig === 'local') {
                $storageType = 'local';
            }
        } catch (\Exception $e) {
            // If we can't determine the adapter, just use the default value
        }

        return [[
            'name' => $backupName,
            'disk' => config('backup.backup.destination.disks')[0] ?? 'local',
            'storageType' => $storageType,
            'reachable' => true,
            'healthy' => true,
            'count' => count($backups),
            'storageSpace' => $this->formatBytes($totalSize),
            'backups' => $backups,
        ]];
    }


    public function download(string $path)
    {
        $disk = $this->getDisk();
        $path = urldecode($path);

        if (!$disk->exists($path)) {
            session()->flash('error', 'Unable to locate backup file');
            return redirect()->back();
        }

        return response()->streamDownload(function() use ($disk, $path) {
            echo $disk->get($path);
        }, basename($path));
    }


    public function delete(string $path)
    {
        try {
            $disk = $this->getDisk();
            $path = urldecode($path);
            
            if (!$disk->exists($path)) {
                return response()->json(['message' => 'Backup file not found'], 404);
            }
            
            $disk->delete($path);
            session()->flash('success', 'Backup deleted successfully');
            
            return redirect()->back();
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to delete: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(string $path)
    {
        try {
            $disk = $this->getDisk();
            $path = urldecode($path);
            
            if (!$disk->exists($path)) {
                return response()->json(['message' => 'Backup file not found'], 404);
            }
                    
            $disk->delete($path);
            session()->flash('success', 'Backup deleted successfully');

            return redirect()->back();
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to delete backup'], 500);
        }
    }


    private function formatBytes($bytes)
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
