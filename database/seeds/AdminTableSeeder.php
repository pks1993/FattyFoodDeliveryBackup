<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;



class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'FattyMainAdmin',
            'email' => 'fattymainadmin@gmail.com',
            'phone' => '09972213949',
            'password' => bcrypt('fattyadmin@2021'),
            'zone_id' => '0',
            'is_main_admin'=> '1',
        ]);

        $role = Role::create(['name' => 'Fatty Super Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

    }
}
