<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(DepartamentoSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsuariosSeeder::class);
        $this->call(EstadosSeeder::class);
        $this->call(RubroSeeder::class);
        $this->call(CuentaSeeder::class);
        $this->call(AnioSeeder::class);
        $this->call(ObjetoSeeder::class);
        $this->call(UnidadSeeder::class);
    }
}
