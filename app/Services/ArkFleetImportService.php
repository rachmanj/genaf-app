<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Services\Concerns\MapsArkFleetVehicles;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArkFleetImportService
{
    use MapsArkFleetVehicles;

    public function __construct(private readonly ArkFleetApiService $apiService)
    {
    }

    public function importFromApi(array $filters = []): array
    {
        $response = $this->apiService->listEquipments($filters);

        return $this->importPayloads(collect($response['data']));
    }

    public function importUnitNumbers(array $unitNumbers): array
    {
        $missing = [];

        $payloads = collect($unitNumbers)
            ->filter()
            ->unique()
            ->map(function (string $unitNo) use (&$missing) {
                $payload = $this->apiService->getEquipment($unitNo);

                if ($payload === null) {
                    $missing[] = $unitNo;
                    return null;
                }

                return $payload;
            })
            ->filter()
            ->values();

        $result = $this->importPayloads($payloads);
        $result['missing'] = $missing;

        return $result;
    }

    public function importPayloads(Collection $payloads): array
    {
        $result = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use ($payloads, &$result) {
            $payloads->each(function (array $payload) use (&$result) {
                $unitNo = Arr::get($payload, 'unit_no');

                if (empty($unitNo)) {
                    $result['skipped']++;
                    $result['errors'][] = "Payload missing unit_no: " . json_encode(Arr::only($payload, ['description', 'nomor_polisi']));
                    return;
                }

                $attributes = $this->mapPayloadToVehicleAttributes($payload);
                $attributes['arkfleet_sync_status'] = 'imported';

                $vehicle = Vehicle::query()->where('unit_no', $unitNo)->first();

                if ($vehicle === null) {
                    $attributes['arkfleet_sync_message'] = null;
                    $vehicle = Vehicle::create($attributes);
                    $result['created']++;
                    return;
                }

                $dirtyAttributes = Arr::except($attributes, ['unit_no']);

                $vehicle->fill($dirtyAttributes);

                if ($vehicle->isDirty()) {
                    $vehicle->save();
                    $result['updated']++;
                    return;
                }

                $result['skipped']++;
            });
        });

        return $result;
    }

    public function hydrateFromDescription(string $description): array
    {
        [$brand, $model] = $this->splitDescriptionIntoBrandModel($description, null);

        return [
            'brand' => Str::upper($brand),
            'model' => $model,
        ];
    }
}

