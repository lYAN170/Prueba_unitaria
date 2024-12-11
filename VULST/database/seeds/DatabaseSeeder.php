<?php


use Database\Seeders\ComprobanteSeeder;
use Database\Seeders\DocumentoSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(ComprobanteSeeder::class);
        $this->call(DocumentoSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(RolePermissionSeeder::class);





    }
}
