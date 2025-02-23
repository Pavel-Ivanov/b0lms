<?php

namespace Database\Seeders;

use App\Models\CompanyDepartment;
use App\Models\CourseType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanyDepartmentSeeder::class,
            CompanyPositionSeeder::class,
            CourseType::class,
        ]);
    }
}
