<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-backups');
    }

    private function getDisk()
    {
        return Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
    }

    public function index()
    {
        return Inertia::render('Admin/IndexBackupPage', [
            'backupInfo' => $this->fetchBackupInfo(),
        ]);
    }

    public function createBackup()
    {
        ob_start();

        $exitCode = Artisan::call('backup:run', ['--quiet' => false]);

        $output = ob_get_clean();

        $artisanOutput = Artisan::output();

        $message = $exitCode === 0
            ? 'Backup completed successfully'
            : 'Backup failed: '.($output ?: $artisanOutput ?: 'Unknown error occurred');

        session()->flash($exitCode === 0 ? 'success' : 'error', $message);

        return redirect()->back();
    }

    public function fetchBackupInfo()
    {
        $disk = $this->getDisk();
        $backupName = config('backup.backup.name') ?? env('APP_NAME', 'laravel-backup');

        $files = collect($disk->allFiles($backupName))
            ->filter(fn ($file) => str_ends_with($file, '.zip'));

        if ($files->isEmpty()) {
            return [[
                'name'         => $backupName,
                'disk'         => config('backup.backup.destination.disks')[0] ?? 'local',
                'storageType'  => config('filesystems.disks.local.driver') === 'local' ? 'local' : 'other',
                'reachable'    => true,
                'healthy'      => true,
                'count'        => 0,
                'storageSpace' => $this->formatBytes(0),
                'backups'      => [],
            ]];
        }

        $backups = $files
            ->map(function ($file) use ($disk) {
                $size = $disk->size($file);
                $lastModified = $disk->lastModified($file);

                return [
                    'path'     => $file,
                    'date'     => date('M d, Y g:i A', $lastModified),
                    'size'     => $this->formatBytes($size),
                    'raw_size' => $size,
                ];
            })
            ->sortByDesc(fn ($backup) => strtotime($backup['date']))
            ->values()
            ->toArray();

        $totalSize = collect($backups)->sum('raw_size');

        return [[
            'name'         => $backupName,
            'disk'         => config('backup.backup.destination.disks')[0] ?? 'local',
            'storageType'  => config('filesystems.disks.local.driver') === 'local' ? 'local' : 'other',
            'reachable'    => true,
            'healthy'      => true,
            'count'        => count($backups),
            'storageSpace' => $this->formatBytes($totalSize),
            'backups'      => array_map(function ($backup) {
                unset($backup['raw_size']);

                return $backup;
            }, $backups),
        ]];
    }

    private function validateBackupExists(string $path, bool $isBase64 = false): ?string
    {
        $disk = $this->getDisk();
        $decodedPath = $isBase64 ? base64_decode($path) : urldecode($path);

        return $disk->exists($decodedPath) ? $decodedPath : null;
    }

    public function download(string $path)
    {
        $decodedPath = $this->validateBackupExists($path, true);

        if (!$decodedPath) {
            session()->flash('error', 'Unable to locate backup file.');

            return redirect()->back();
        }

        $filePath = storage_path('app/'.$decodedPath);

        if (!file_exists($filePath)) {
            session()->flash('error', 'Backup file not found on disk.');

            return redirect()->back();
        }

        return response()->download($filePath, basename($decodedPath));
    }

    public function destroy(string $path)
    {
        $decodedPath = $this->validateBackupExists($path, true);

        if (!$decodedPath) {
            session()->flash('error', 'Backup file not found.');

            return redirect()->back();
        }

        $this->getDisk()->delete($decodedPath);
        session()->flash('warning', 'Backup deleted successfully.');

        return redirect()->back();
    }

    private function formatBytes($bytes)
    {
        $units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }
}
