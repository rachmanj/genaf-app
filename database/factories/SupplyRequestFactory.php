<?php

namespace Database\Factories;

use App\Models\SupplyRequest;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplyRequestFactory extends Factory
{
    protected $model = SupplyRequest::class;

    public function definition(): array
    {
        return [
            'employee_id' => User::factory(),
            'department_id' => Department::factory(),
            'request_date' => now(),
            'status' => 'pending_dept_head',
            'department_head_approved_by' => null,
            'department_head_approved_at' => null,
            'ga_admin_approved_by' => null,
            'ga_admin_approved_at' => null,
            'notes' => $this->faker->sentence(),
        ];
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'department_head_approved_by' => User::factory(),
                'department_head_approved_at' => now(),
                'ga_admin_approved_by' => User::factory(),
                'ga_admin_approved_at' => now(),
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'department_head_approved_by' => User::factory(),
                'department_head_approved_at' => now(),
            ];
        });
    }
}

