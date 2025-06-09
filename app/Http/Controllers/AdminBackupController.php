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

        $backups = collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->map(fn($file) => [
                'path' => $file,
                'date' => date('M d, Y g:i A', $disk->lastModified($file)),
                'size' => $this->formatBytes($disk->size($file)),
            ])
            ->sortByDesc(fn($backup) => strtotime($backup['date']))
            ->values()
            ->toArray();

        $totalSize = collect($backups)->sum(fn($backup) => $disk->size($backup['path']));
        $storageType = config('filesystems.disks.local.driver') === 'local' ? 'local' : 'other';

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


    private function handleBackupError(\Exception $e, string $action = 'process backup')
    {
        report($e);
        session()->flash('error', "Failed to {$action}: " . $e->getMessage());
        return redirect()->back();
    }


    private function validateBackupExists(string $path, bool $isBase64 = false): ?string
    {
        $disk = $this->getDisk();
        $decodedPath = $isBase64 ? base64_decode($path) : urldecode($path);
        
        return $disk->exists($decodedPath) ? $decodedPath : null;
    }
    

    public function download(string $path)
    {
        try {
            $decodedPath = $this->validateBackupExists($path, true);
            
            if (!$decodedPath) {
                session()->flash('error', 'Unable to locate backup file.');
                return redirect()->back();
            }

            $filePath = storage_path('app/' . $decodedPath);
            
            if (!file_exists($filePath)) {
                session()->flash('error', 'Backup file not found on disk.');
                return redirect()->back();
            }

            return response()->download($filePath, basename($decodedPath));
        } catch (\Exception $e) {
            return $this->handleBackupError($e, 'download backup');
        }
    }


    public function destroy(string $path)
    {
        try {
            $decodedPath = $this->validateBackupExists($path, true);
            
            if (!$decodedPath) {
                session()->flash('error', 'Backup file not found.');
                return redirect()->back();
            }
            
            $this->getDisk()->delete($decodedPath);
            session()->flash('warning', 'Backup deleted successfully.');
            
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleBackupError($e, 'delete backup');
        }
    }


    private function formatBytes($bytes)
    {
        $units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
