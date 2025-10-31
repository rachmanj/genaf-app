<?php

namespace Tests\Feature\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmsFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_buildings_page_loads_and_create_form_visible(): void
    {
        $this->seed();
        $user = User::first();

        $this->actingAs($user)
            ->get('/pms/buildings')
            ->assertStatus(200)
            ->assertSee('Buildings');

        $this->actingAs($user)
            ->get('/pms/buildings/create')
            ->assertStatus(200)
            ->assertSee('New Building');
    }

    public function test_rooms_list_and_filters_render(): void
    {
        $this->seed();
        $user = User::first();

        $this->actingAs($user)
            ->get('/pms/rooms')
            ->assertStatus(200)
            ->assertSee('Rooms')
            ->assertSee('Building')
            ->assertSee('Status');
    }

    public function test_reservation_create_dependent_selects(): void
    {
        $this->seed();
        $user = User::first();

        $this->actingAs($user)
            ->get('/pms/reservations/create')
            ->assertStatus(200)
            ->assertSee('Create Reservation')
            ->assertSee('Building')
            ->assertSee('Room');
    }
}


