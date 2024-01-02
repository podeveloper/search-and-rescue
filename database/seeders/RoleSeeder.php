<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $base_roles = [
            'candidate',
            'official',
        ];

        $additional_roles = [
            'coordinator',
            'network operator',
            'stock operator',
            'reference operator',
        ];

        $allRoles = array_merge($base_roles,$additional_roles);

        foreach ($allRoles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }
}
