<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Owner'],
            ['id' => 2, 'name' => 'Manager'],
            ['id' => 3, 'name' => 'Staff'],
        ]);
    }
}
