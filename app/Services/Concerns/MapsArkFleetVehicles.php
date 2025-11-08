<?php

namespace App\Services\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait MapsArkFleetVehicles
{
    protected function mapPayloadToVehicleAttributes(array $payload): array
    {
        $unitNo = Arr::get($payload, 'unit_no') ?? Arr::get($payload, 'unitNo');
        $description = trim((string) Arr::get($payload, 'description', ''));

        $modelValue = Arr::get($payload, 'model');
        if (is_array($modelValue)) {
            $modelValue = Arr::get($payload, 'model.model_no') ?? Arr::get($payload, 'model.name');
        }

        [$brand, $model] = $this->splitDescriptionIntoBrandModel($description, is_string($modelValue) ? $modelValue : null);

        $plantGroup = Arr::get($payload, 'plant_group');
        if (is_array($plantGroup)) {
            $plantGroup = Arr::get($payload, 'plant_group.name');
        }

        $projectCode = Arr::get($payload, 'project_code');
        if ($projectCode === null) {
            $projectCode = Arr::get($payload, 'project.project_code');
        }

        $remarks = Arr::get($payload, 'remarks');
        if ($remarks === null) {
            $remarks = Arr::get($payload, 'project.remarks');
        }

        $statusSource = Arr::get($payload, 'unitstatus');
        if (is_array($statusSource)) {
            $statusSource = Arr::get($payload, 'unitstatus.name');
        }

        $status = $this->mapArkFleetStatus((string) $statusSource);

        $year = $this->deriveYear(Arr::get($payload, 'active_date'));

        if ($year === null) {
            $year = $this->normalizeYear(Arr::get($payload, 'year'));
        }

        $licensePlate = Arr::get($payload, 'nomor_polisi');
        if ($licensePlate === null) {
            $licensePlate = Arr::get($payload, 'nomorPolisi');
        }
        $licensePlate = $licensePlate !== null ? trim((string) $licensePlate) : null;

        return [
            'unit_no' => $unitNo ?: null,
            'nomor_polisi' => $licensePlate,
            'brand' => $brand ?: Arr::get($payload, 'brand', ''),
            'model' => $model ?: ($modelValue ?? $description),
            'year' => $year,
            'plant_group' => $plantGroup ?: null,
            'status' => $status,
            'current_project_code' => $projectCode ?: null,
            'remarks' => $remarks ?: null,
            'arkfleet_last_payload' => $payload,
            'arkfleet_synced_at' => now(),
            'arkfleet_sync_status' => 'success',
            'arkfleet_sync_message' => null,
            'is_active' => $this->determineIsActive($status),
        ];
    }

    protected function normalizeYear(mixed $value): ?int
    {
        if (is_numeric($value)) {
            $year = (int) $value;

            return $year > 1900 ? $year : null;
        }

        if (is_string($value) && $value !== '') {
            $numeric = preg_replace('/\D/', '', $value);

            if ($numeric === '') {
                return null;
            }

            $year = (int) $numeric;

            return $year > 1900 ? $year : null;
        }

        return null;
    }

    protected function mapArkFleetStatus(string $rawStatus): string
    {
        return match (strtoupper(trim($rawStatus))) {
            'RFU', 'ACTIVE' => 'active',
            'RFM', 'MAINTENANCE' => 'maintenance',
            'IN-ACTIVE', 'INACTIVE' => 'inactive',
            'SCRAP' => 'scrap',
            'SOLD' => 'sold',
            default => 'retired',
        };
    }

    protected function deriveYear(?string $activeDate): ?int
    {
        if (empty($activeDate)) {
            return null;
        }

        try {
            return Carbon::parse($activeDate)->year;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function determineIsActive(string $mappedStatus): bool
    {
        return in_array($mappedStatus, ['active', 'maintenance'], true);
    }

    protected function splitDescriptionIntoBrandModel(string $description, ?string $fallbackModel = null): array
    {
        if ($description === '') {
            return ['', $fallbackModel ?? ''];
        }

        $segments = preg_split('/\s+/', $description, 2);

        if ($segments === false || count($segments) < 2) {
            return [$segments[0] ?? '', $fallbackModel ?? $description];
        }

        return [$segments[0], $segments[1]];
    }
}

