<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'User',
            'guard_name' => 'User',
        ]);
        DB::table('roles')->insert([
            'name' => 'Manager',
            'guard_name' => 'Manager'
        ]);
        DB::table('roles')->insert([
            'name' => 'Admin',
            'guard_name' => 'Admin'
        ]);
    }
}
