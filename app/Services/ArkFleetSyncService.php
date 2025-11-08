<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Services\Concerns\MapsArkFleetVehicles;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArkFleetSyncService
{
    use MapsArkFleetVehicles;

    public function __construct(
        private readonly ArkFleetApiService $apiService
    ) {
    }

    public function syncAll(array $filters = []): array
    {
        $response = $this->apiService->listEquipments($filters);

        return $this->syncPayloads(collect($response['data']), true);
    }

    public function syncSelected(array $unitNumbers): array
    {
        $payloads = collect($unitNumbers)
            ->unique()
            ->mapWithKeys(function (string $unitNo) {
                $payload = $this->apiService->getEquipment($unitNo);

                if ($payload === null) {
                    return [$unitNo => null];
                }

                return [$unitNo => $payload];
            });

        return $this->syncPayloads($payloads->filter()->values(), false, $payloads->keys()->all());
    }

    protected function syncPayloads(Collection $payloads, bool $markMissingInactive = false, array $requestedUnits = []): array
    {
        $result = [
            'updated' => 0,
            'skipped' => 0,
            'deactivated' => 0,
            'missing' => [],
        ];

        $unitNumbers = $payloads->pluck('unit_no')->filter()->all();

        DB::transaction(function () use ($payloads, &$result, $markMissingInactive, $unitNumbers, $requestedUnits) {
            $payloads->each(function (array $payload) use (&$result) {
                $unitNo = Arr::get($payload, 'unit_no');

                if (empty($unitNo)) {
                    $result['skipped']++;
                    return;
                }

                $vehicle = Vehicle::query()->where('unit_no', $unitNo)->first();

                if ($vehicle === null) {
                    $result['missing'][] = $unitNo;
                    return;
                }

                $attributes = $this->mapPayloadToVehicleAttributes($payload);
                $attributes['arkfleet_sync_status'] = 'synced';

                $vehicle->fill(Arr::except($attributes, ['unit_no']));

                if ($vehicle->isDirty()) {
                    $vehicle->save();
                    $result['updated']++;
                    return;
                }

                $result['skipped']++;
            });

            if ($markMissingInactive) {
                if (empty($unitNumbers)) {
                    return;
                }

                $result['deactivated'] = Vehicle::query()
                    ->whereNotNull('unit_no')
                    ->whereNotIn('unit_no', $unitNumbers)
                    ->update([
                        'is_active' => false,
                        'arkfleet_sync_status' => 'missing',
                        'arkfleet_sync_message' => 'Unit not returned by latest ARKFleet sync.',
                        'arkfleet_synced_at' => now(),
                    ]);
            }

            if ($requestedUnits !== []) {
                $missingUnits = array_diff($requestedUnits, $unitNumbers);
                $result['missing'] = array_values(array_unique(array_merge($result['missing'], $missingUnits)));
            }
        });

        return $result;
    }
}

