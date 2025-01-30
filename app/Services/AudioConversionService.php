<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AudioConversionService
{
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => 'C:\ProgramData\chocolatey\bin\ffmpeg.exe',
            'ffprobe.binaries' => 'C:\ProgramData\chocolatey\bin\ffprobe.exe',
            'timeout'          => 3600,
            'ffmpeg.threads'   => 12,
        ]);
    }

    public function convertToOgg($inputPath, $quality = 192)
    {
        try {
            // Create temporary output path
            $outputPath = storage_path('app/temp/' . uniqid() . '.ogg');

            // Ensure temp directory exists
            if (!file_exists(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            // Use Process to run FFmpeg directly
            $process = new Process([
                'C:\ProgramData\chocolatey\bin\ffmpeg.exe',
                '-i', $inputPath,
                '-c:a', 'libvorbis',
                '-q:a', '4',
                $outputPath
            ]);

            $process->setTimeout(3600);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('FFmpeg conversion failed: ' . $process->getErrorOutput());
                return null;
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('FFmpeg conversion failed: ' . $e->getMessage());
            return null;
        }
    }
}
