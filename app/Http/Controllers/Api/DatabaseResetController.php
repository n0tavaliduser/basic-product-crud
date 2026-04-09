<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseResetController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $pidPath = storage_path('app/database-reset.pid');
        $logPath = storage_path('logs/database-reset.log');

        if ($this->isResetRunning($pidPath)) {
            return response()->json([
                'message' => 'Reset database sedang berjalan.',
                'log_path' => 'storage/logs/database-reset.log',
            ], 409);
        }

        try {
            $basePath = escapeshellarg(base_path());
            $phpBinary = escapeshellarg(PHP_BINARY);
            $artisanPath = escapeshellarg(base_path('artisan'));
            $pidPathArg = escapeshellarg($pidPath);
            $logPathArg = escapeshellarg($logPath);

            $resetCommand = sprintf(
                'echo $$ > %s; %s %s migrate:fresh --seed --no-interaction >> %s 2>&1; rm -f %s',
                $pidPathArg,
                $phpBinary,
                $artisanPath,
                $logPathArg,
                $pidPathArg
            );

            $command = sprintf(
                'cd %s && nohup sh -c %s >/dev/null 2>&1 & echo $!',
                $basePath,
                escapeshellarg($resetCommand)
            );

            $output = [];
            $exitCode = 0;

            exec($command, $output, $exitCode);

            if ($exitCode !== 0 || ! isset($output[0])) {
                Log::error('Failed to start database reset process.', [
                    'exit_code' => $exitCode,
                    'output' => $output,
                ]);

                return response()->json([
                    'message' => 'Gagal memulai proses reset database.',
                ], 500);
            }

            $processId = (int) $output[0];

            Log::info('Database reset process started.', [
                'pid' => $processId,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Reset database berhasil dijalankan di background.',
                'pid' => $processId,
                'log_path' => 'storage/logs/database-reset.log',
            ], 202);
        } catch (Throwable $exception) {
            Log::error('Database reset API failed.', [
                'exception' => $exception->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memulai reset database.',
            ], 500);
        }
    }

    private function isResetRunning(string $pidPath): bool
    {
        if (! is_file($pidPath)) {
            return false;
        }

        $pid = (int) trim((string) file_get_contents($pidPath));

        if ($pid < 1) {
            @unlink($pidPath);

            return false;
        }

        if (! function_exists('posix_kill')) {
            return true;
        }

        $isRunning = @posix_kill($pid, 0);

        if (! $isRunning) {
            @unlink($pidPath);
        }

        return $isRunning;
    }
}
