<?php

namespace Database\Seeders;

use App\Models\Rubro;
use Illuminate\Database\Seeder;

class RubroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rubro::create([
            'numero' => '51',
            'nombre' => 'REMUNERACIONES',
        ]);

        Rubro::create([
            'numero' => '54',
            'nombre' => 'ADQUISICIONES DE BIENES Y SERVICIOS',
        ]);

        Rubro::create([
            'numero' => '55',
            'nombre' => 'GASTOS FINANCIEROS Y OTROS',
        ]);

        Rubro::create([
            'numero' => '56',
            'nombre' => 'TRANSFERENCIAS CORRIENTES',
        ]);

        Rubro::create([
            'numero' => '61',
            'nombre' => 'INVERSIONES EN ACTIVOS FIJOS',
        ]);

        Rubro::create([
            'numero' => '72',
            'nombre' => 'SALDOS DE AÑOS ANTERIORES',
        ]);

    }
}
