<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'department_name' => $name,
            'slug' => Str::slug($name),
            'status' => true,
        ];
    }
}

