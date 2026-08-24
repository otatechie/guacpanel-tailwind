<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminBackupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-backups|manage-backups'),
            new Middleware('permission:manage-backups', only: ['createBackup', 'download', 'destroy']),
        ];
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

        $message =
            $exitCode === 0
                ? 'Backup completed successfully'
                : 'Backup failed: ' . ($output ?: $artisanOutput ?: 'Unknown error occurred');

        session()->flash($exitCode === 0 ? 'success' : 'error', $message);

        return redirect()->back();
    }

    public function fetchBackupInfo()
    {
        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $backupName = config('backup.backup.name') ?? env('APP_NAME', 'laravel-backup');

        try {
            $files = collect($this->getDisk()->allFiles($backupName))->filter(
                fn($file) => str_ends_with($file, '.zip'),
            );
        } catch (\Throwable $e) {
            return [
                [
                    'name' => $backupName,
                    'disk' => $diskName,
                    'reachable' => false,
                    'count' => 0,
                    'storageSpace' => $this->formatBytes(0),
                    'backups' => [],
                ],
            ];
        }

        $disk = $this->getDisk();

        if ($files->isEmpty()) {
            return [
                [
                    'name' => $backupName,
                    'disk' => $diskName,
                    'reachable' => true,
                    'count' => 0,
                    'storageSpace' => $this->formatBytes(0),
                    'backups' => [],
                ],
            ];
        }

        $backups = $files
            ->map(function ($file) use ($disk) {
                $size = $disk->size($file);
                $lastModified = $disk->lastModified($file);

                return [
                    'path' => $file,
                    'date' => date('M d, Y g:i A', $lastModified),
                    'age' => Carbon::createFromTimestamp($lastModified)->diffForHumans(),
                    'size' => $this->formatBytes($size),
                    'raw_size' => $size,
                    'timestamp' => $lastModified,
                ];
            })
            ->sortByDesc(fn($backup) => $backup['timestamp'])
            ->values()
            ->toArray();

        $totalSize = collect($backups)->sum('raw_size');

        return [
            [
                'name' => $backupName,
                'disk' => $diskName,
                'reachable' => true,
                'count' => count($backups),
                'storageSpace' => $this->formatBytes($totalSize),
                'backups' => array_map(function ($backup) {
                    unset($backup['raw_size'], $backup['timestamp']);

                    return $backup;
                }, $backups),
            ],
        ];
    }

    private function validateBackupExists(string $path, bool $isBase64 = false): ?string
    {
        $disk = $this->getDisk();
        $decodedPath = $isBase64 ? base64_decode(strtr($path, '-_', '+/'), true) : urldecode($path);

        if ($decodedPath === false) {
            return null;
        }

        $backupName = config('backup.backup.name') ?? env('APP_NAME', 'laravel-backup');
        $normalizedPath = str_replace('\\', '/', $decodedPath);
        $normalizedPath = preg_replace('#/+#', '/', $normalizedPath);

        if (!str_starts_with($normalizedPath, $backupName . '/') && $normalizedPath !== $backupName) {
            return null;
        }

        if (str_contains($normalizedPath, '..')) {
            return null;
        }

        return $disk->exists($decodedPath) ? $decodedPath : null;
    }

    public function download(string $path)
    {
        $decodedPath = $this->validateBackupExists($path, true);

        if (!$decodedPath) {
            session()->flash('error', 'Unable to locate backup file.');

            return redirect()->back();
        }

        return $this->getDisk()->download($decodedPath, basename($decodedPath));
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
        $bytes /= 1 << 10 * $pow;

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
