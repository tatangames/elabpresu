<?php

namespace Database\Seeders;

use App\Models\Anio;
use Illuminate\Database\Seeder;

class AnioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Anio::create([
            'nombre' => '2021'
        ]);
    }
}
