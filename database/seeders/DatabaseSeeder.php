<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SalonSeeder::class,
            ModePaiementSeeder::class,
            PermissionSeeder::class,
            CategorieDepenseSeeder::class,
            DemoSeeder::class,
        ]);
    }
}