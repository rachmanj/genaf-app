<?php

namespace App\Http\Controllers\Vehicle;

use App\Exceptions\ArkFleetException;
use App\Http\Controllers\Controller;
use App\Jobs\ArkFleetSyncJob;
use App\Services\ArkFleetApiService;
use App\Services\ArkFleetImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VehicleImportController extends Controller
{
    public function __construct(
        private readonly ArkFleetApiService $arkFleetApiService,
        private readonly ArkFleetImportService $arkFleetImportService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = Arr::where(
            $request->only(['project_code', 'status', 'plant_group']),
            static fn ($value) => filled($value)
        );

        $vehicles = [];
        $count = 0;
        $errorMessage = null;

        try {
            $response = $this->arkFleetApiService->listEquipments($filters);
            $vehicles = $response['data'] ?? [];
            $count = $response['count'] ?? count($vehicles);
        } catch (ArkFleetException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Throwable $exception) {
            Log::error('ArkFleet import index failed', [
                'filters' => $filters,
                'message' => $exception->getMessage(),
            ]);

            $errorMessage = 'Unable to reach ARKFleet API. Please try again later.';
        }

        return view('vehicle.import.index', [
            'vehicles' => $vehicles,
            'count' => $count,
            'filters' => [
                'project_code' => $request->string('project_code')->toString(),
                'status' => $request->string('status')->toString(),
                'plant_group' => $request->string('plant_group')->toString(),
            ],
            'errorMessage' => $errorMessage,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_numbers' => ['array'],
            'unit_numbers.*' => ['string'],
            'unit_numbers_text' => ['nullable', 'string'],
        ]);

        $unitNumbers = $this->extractUnitNumbers($request);

        if ($unitNumbers === []) {
            return back()->with('error', 'Select at least one unit to import.');
        }

        try {
            $result = $this->arkFleetImportService->importUnitNumbers($unitNumbers);
        } catch (ArkFleetException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::error('ArkFleet import store failed', [
                'unit_numbers' => $unitNumbers,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Unexpected error while importing vehicles.');
        }

        $messages = [];

        if ($result['created'] > 0) {
            $messages[] = "{$result['created']} new vehicle(s) created.";
        }

        if ($result['updated'] > 0) {
            $messages[] = "{$result['updated']} vehicle(s) updated.";
        }

        if (!empty($result['missing'])) {
            $messages[] = 'Some units were not found in ARKFleet: ' . implode(', ', $result['missing']);
        }

        if (!empty($result['errors'])) {
            $messages[] = 'Errors: ' . implode('; ', $result['errors']);
        }

        if (empty($messages)) {
            $messages[] = 'No changes were applied.';
        }

        return back()->with('success', implode(' ', $messages));
    }

    public function syncSelected(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_numbers' => ['array'],
            'unit_numbers.*' => ['string'],
            'unit_numbers_text' => ['nullable', 'string'],
        ]);

        $unitNumbers = $this->extractUnitNumbers($request);

        if ($unitNumbers === []) {
            return back()->with('error', 'Select at least one unit to sync.');
        }

        ArkFleetSyncJob::dispatch('selected', ['unit_numbers' => $unitNumbers]);

        return back()->with('success', 'Selected units queued for syncing. Check logs for completion details.');
    }

    public function syncAll(Request $request): RedirectResponse
    {
        $filters = Arr::where(
            $request->only(['project_code', 'status', 'plant_group']),
            static fn ($value) => filled($value)
        );

        ArkFleetSyncJob::dispatch('all', ['filters' => $filters]);

        return back()->with('success', 'Sync for filtered vehicles has been queued. Check logs for completion details.');
    }

    private function extractUnitNumbers(Request $request): array
    {
        $unitNumbers = array_filter((array) $request->input('unit_numbers', []));

        $text = trim((string) $request->input('unit_numbers_text', ''));
        if ($text !== '') {
            $textUnits = preg_split('/[\s,]+/', $text);
            if (is_array($textUnits)) {
                $unitNumbers = array_merge($unitNumbers, $textUnits);
            }
        }

        $unitNumbers = array_map(static fn ($unit) => trim((string) $unit), $unitNumbers);

        return array_values(array_unique(array_filter($unitNumbers)));
    }
}
