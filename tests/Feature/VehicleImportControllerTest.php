<?php

namespace Tests\Feature;

use App\Jobs\ArkFleetSyncJob;
use App\Models\User;
use App\Services\ArkFleetImportService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class VehicleImportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_imports_selected_units(): void
    {
        $this->actingAsAdmin();

        $service = Mockery::mock(ArkFleetImportService::class);
        $service->shouldReceive('importUnitNumbers')
            ->once()
            ->with(['EX-001', 'DT-002'])
            ->andReturn([
                'created' => 1,
                'updated' => 1,
                'skipped' => 0,
                'errors' => [],
                'missing' => [],
            ]);

        $this->app->instance(ArkFleetImportService::class, $service);

        $response = $this->post(route('vehicles.import.store'), [
            'unit_numbers' => ['EX-001', 'DT-002'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', function (string $value) {
            return str_contains($value, 'new vehicle') && str_contains($value, 'updated');
        });
    }

    public function test_sync_selected_dispatches_job(): void
    {
        $this->actingAsAdmin();
        Queue::fake();

        $response = $this->post(route('vehicles.import.sync-selected'), [
            'unit_numbers' => ['EX-001', 'DT-002'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(ArkFleetSyncJob::class, function (ArkFleetSyncJob $job) {
            return $job->mode() === 'selected'
                && $job->payload() === ['unit_numbers' => ['EX-001', 'DT-002']];
        });
    }

    public function test_sync_all_dispatches_job_with_filters(): void
    {
        $this->actingAsAdmin();
        Queue::fake();

        $filters = [
            'project_code' => '000H',
            'status' => 'ACTIVE',
            'plant_group' => 'Excavator',
        ];

        $response = $this->post(route('vehicles.import.sync-all'), $filters);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(ArkFleetSyncJob::class, function (ArkFleetSyncJob $job) use ($filters) {
            return $job->mode() === 'all'
                && $job->payload() === ['filters' => $filters];
        });
    }

    public function test_sync_endpoints_require_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('vehicles.import.sync-all'))->assertForbidden();
        $this->post(route('vehicles.import.sync-selected'), [
            'unit_numbers' => ['EX-001'],
        ])->assertForbidden();
    }

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        return $user;
    }
}
