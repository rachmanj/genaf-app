<?php

namespace App\Services;

use App\Exceptions\ArkFleetException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ArkFleetApiService
{
    protected string $baseUrl;

    protected int $timeout;

    /**
     * @var array<string, bool>
     */
    protected array $allowedFilters = [
        'project_code' => true,
        'status' => true,
        'plant_group' => true,
        'unitstatus_id' => true,
        'plant_group_id' => true,
        'current_project_id' => true,
    ];

    public function __construct(?string $baseUrl = null, ?int $timeout = null)
    {
        $configuredBaseUrl = $baseUrl ?? config('services.arkfleet.base_url');

        if (empty($configuredBaseUrl)) {
            throw ArkFleetException::missingConfiguration('ARKFLEET_API_URL');
        }

        $this->baseUrl = rtrim($configuredBaseUrl, '/');
        $this->timeout = $timeout ?? (int) config('services.arkfleet.timeout', 15);
    }

    public function listEquipments(array $filters = []): array
    {
        $response = $this->client()->get('/api/equipments', $this->sanitizeFilters($filters));

        if ($response->failed()) {
            throw ArkFleetException::apiError('Failed to fetch equipment list.', $response);
        }

        $data = $response->json();

        return [
            'count' => (int) Arr::get($data, 'count', count(Arr::get($data, 'data', []))),
            'data' => Arr::get($data, 'data', []),
        ];
    }

    public function getEquipment(string $unitNo): ?array
    {
        $response = $this->client()->get("/api/equipments/by-unit/{$unitNo}");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw ArkFleetException::apiError("Failed to fetch equipment {$unitNo}.", $response);
        }

        $payload = $response->json();

        if (is_array($payload) && isset($payload['data'])) {
            return $payload['data'];
        }

        return $payload;
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->retry(2, 200);
    }

    protected function sanitizeFilters(array $filters): array
    {
        return array_intersect_key($filters, $this->allowedFilters);
    }
}

