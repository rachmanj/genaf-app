<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_name' => 'Management / BOD', 'slug' => 'management-bod'],
            ['department_name' => 'Internal Audit & System', 'slug' => 'internal-audit-system'],
            ['department_name' => 'Corporate Secretary', 'slug' => 'corporate-secretary'],
            ['department_name' => 'APS - Arka Project Support', 'slug' => 'aps-arka-project-support'],
            ['department_name' => 'Relationship & Coordination', 'slug' => 'relationship-coordination'],
            ['department_name' => 'Design & Construction', 'slug' => 'design-construction'],
            ['department_name' => 'Finance', 'slug' => 'finance'],
            ['department_name' => 'Human Capital & Support', 'slug' => 'human-capital-support'],
            ['department_name' => 'Asset Management & Logistic', 'slug' => 'asset-management-logistic'],
            ['department_name' => 'Accounting', 'slug' => 'accounting'],
            ['department_name' => 'Plant', 'slug' => 'plant'],
            ['department_name' => 'Procurement', 'slug' => 'procurement'],
            ['department_name' => 'Marketing', 'slug' => 'marketing'],
            ['department_name' => 'Operation & Production', 'slug' => 'operation-production'],
            ['department_name' => 'Safety', 'slug' => 'safety'],
            ['department_name' => 'Information Technology', 'slug' => 'information-technology'],
            ['department_name' => 'Research & Development', 'slug' => 'research-development'],
        ];

        foreach ($departments as $department) {
            $existing = DB::table('departments')->where('slug', $department['slug'])->first();

            DB::table('departments')->updateOrInsert(
                ['slug' => $department['slug']],
                [
                    'department_name' => $department['department_name'],
                    'status' => true,
                    'updated_at' => Carbon::now(),
                    'created_at' => $existing->created_at ?? Carbon::now(),
                ]
            );
        }
    }
}
