<?php

namespace App\Jobs;

use App\Services\ArkFleetSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ArkFleetSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $mode,
        private readonly array $payload = []
    ) {
    }

    public function handle(ArkFleetSyncService $syncService): void
    {
        $result = match ($this->mode) {
            'selected' => $syncService->syncSelected($this->payload['unit_numbers'] ?? []),
            default => $syncService->syncAll($this->payload['filters'] ?? []),
        };

        Log::info('ArkFleet sync job completed', [
            'mode' => $this->mode,
            'payload' => $this->payload,
            'result' => $result,
        ]);
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
