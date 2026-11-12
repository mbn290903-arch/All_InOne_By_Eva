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
            DemoSeeder::class,
            ModePaiementSeeder::class,
            PermissionSeeder::class,        // ajouté
            CategorieDepenseSeeder::class,  // ajouté
        ]);
    }
}
