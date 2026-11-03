<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insert([
            ['name' => 'admin',          'label' => 'Administratrice',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'receptionniste', 'label' => 'Réceptionniste',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'professionnel',  'label' => 'Professionnel(le)', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
        ]);
    }
}