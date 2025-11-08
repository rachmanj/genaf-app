<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\VehicleDocumentType;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\VehicleDocumentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleDocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(VehicleDocumentTypeSeeder::class);
    }

    public function test_store_creates_document_and_revision(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo([
            'create vehicle documents',
            'view vehicle documents',
            'download vehicle documents',
        ]);

        $vehicle = Vehicle::factory()->create();
        $type = VehicleDocumentType::where('slug', 'stnk')->firstOrFail();

        $payload = [
            'vehicle_id' => $vehicle->id,
            'vehicle_document_type_id' => $type->id,
            'document_number' => 'DOC-2025-001',
            'document_date' => now()->subMonth()->toDateString(),
            'due_date' => now()->addMonths(11)->toDateString(),
            'supplier' => 'Samsat Balikpapan',
            'amount' => 1250000,
            'notes' => 'Initial registration',
            'file' => UploadedFile::fake()->create('stnk.pdf', 120, 'application/pdf'),
        ];

        $response = $this->actingAs($user)
            ->post(route('vehicle-documents.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $document = VehicleDocument::first();

        $this->assertNotNull($document);
        $this->assertEquals($vehicle->id, $document->vehicle_id);
        $this->assertEquals($type->id, $document->vehicle_document_type_id);
        $this->assertEquals('DOC-2025-001', $document->document_number);
        $this->assertEquals('Samsat Balikpapan', $document->supplier);
        $this->assertEquals(1250000.00, (float) $document->amount);
        Storage::disk('public')->assertExists($document->file_path);

        $revision = $document->revisions()->latest()->first();
        $this->assertNotNull($revision);
        $this->assertEquals($document->document_number, $revision->document_number);
        $this->assertEquals($document->due_date, $revision->due_date);
        $this->assertEquals($document->amount, $revision->amount);
    }

    public function test_expiring_within_scope_finds_due_documents(): void
    {
        $vehicle = Vehicle::factory()->create();
        $type = VehicleDocumentType::factory()->create();

        VehicleDocument::factory()->create([
            'vehicle_id' => $vehicle->id,
            'vehicle_document_type_id' => $type->id,
            'document_number' => 'DOC-EXP-1',
            'document_date' => now()->subYear(),
            'due_date' => now()->addDays(10),
        ]);

        VehicleDocument::factory()->create([
            'vehicle_id' => $vehicle->id,
            'vehicle_document_type_id' => $type->id,
            'document_number' => 'DOC-LATE',
            'due_date' => now()->addDays(40),
        ]);

        $expiring = VehicleDocument::query()->expiringWithin(30)->pluck('document_number')->all();

        $this->assertContains('DOC-EXP-1', $expiring);
        $this->assertNotContains('DOC-LATE', $expiring);
    }

    public function test_revision_is_created_when_document_is_updated(): void
    {
        $document = VehicleDocument::factory()->create([
            'document_number' => 'DOC-REV-1',
            'document_date' => now()->subMonths(8),
            'due_date' => now()->addMonths(3),
        ]);

        $initialRevisionCount = $document->revisions()->count();

        $document->update([
            'due_date' => now()->addMonths(6),
            'notes' => 'Extended after inspection',
        ]);

        $document->refresh();

        $this->assertEquals($initialRevisionCount + 1, $document->revisions()->count());
        $latestRevision = $document->revisions()->latest('id')->first();
        $this->assertEquals('Extended after inspection', $latestRevision->notes);
    }

    public function test_update_endpoint_updates_document_and_replaces_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo('edit vehicle documents');

        $document = VehicleDocument::factory()->create([
            'document_number' => 'DOC-001',
            'document_date' => now()->subMonths(6),
            'due_date' => now()->addMonths(3),
            'notes' => 'Original notes',
        ]);

        $response = $this->actingAs($user)->put(
            route('vehicle-documents.update', $document),
            [
                'vehicle_document_type_id' => $document->vehicle_document_type_id,
                'document_number' => 'DOC-UPDATED',
                'document_date' => now()->subMonths(5)->toDateString(),
                'due_date' => now()->addMonths(6)->toDateString(),
                'supplier' => 'Update Supplier',
                'amount' => 2000000,
                'notes' => 'Updated via endpoint',
                'file' => UploadedFile::fake()->create('updated.pdf', 150, 'application/pdf'),
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $document->refresh();

        $this->assertEquals('DOC-UPDATED', $document->document_number);
        $this->assertEquals('Update Supplier', $document->supplier);
        $this->assertEquals(2000000.00, (float) $document->amount);
        Storage::disk('public')->assertExists($document->file_path);
        $this->assertEquals('Updated via endpoint', $document->revisions()->latest('id')->first()->notes);
    }
}
