<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::findByName('Admin');

        $admin = new User();
        $admin->first_name = "";
        $admin->second_name = "";
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("defaultAdmin");
        $admin->save();

        $admin->assignRole($role);
    }
}
