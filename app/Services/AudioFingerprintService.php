<?php

namespace App\Services;

use App\Models\BeatFile;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class AudioFingerprintService
{
    private string $fpcalcPath = 'C:\chromaprint-fpcalc\fpcalc.exe';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.acoustid.key');
    }

    public function generateFingerprint(string $audioPath): array
    {
        Log::debug('Generating fingerprint for file: ' . $audioPath);
        Log::debug('File exists: ' . (file_exists($audioPath) ? 'yes' : 'no'));
        $process = new Process([$this->fpcalcPath, '-json', $audioPath]);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return json_decode($process->getOutput(), true) ?: ['fingerprint' => null, 'duration' => null];
    }

    public function checkDuplicates(array $fingerprint): bool
    {
        // First check our local database for duplicates
        $existingBeat = BeatFile::where('fingerprint', $fingerprint['fingerprint'])->first();

        if ($existingBeat) {
            return true;
        }

        // If no local duplicates, check AcoustID
        $response = Http::get('https://api.acoustid.org/v2/lookup', [
            'client' => $this->apiKey,
            'format' => 'json',
            'meta' => 'recordings,releases,tracks',
            'duration' => $fingerprint['duration'],
            'fingerprint' => $fingerprint['fingerprint']
        ]);

        return $response->successful() && !empty($response->json()['results']);
    }
}
