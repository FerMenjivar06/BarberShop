<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'ADMIN', 'guard_name' => 'api']);
        Role::create(['name' => 'CLIENTE', 'guard_name' => 'api']);
        Role::create(['name' => 'VENDEDOR', 'guard_name' => 'api']);
    }
}