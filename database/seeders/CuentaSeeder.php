<?php

namespace Database\Seeders;

use App\Models\Cuenta;
use Illuminate\Database\Seeder;

class CuentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '511',
            'nombre' => 'REMUNERACIONES PERMANENTES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '512',
            'nombre' => 'REMUNERACIONES EVENTUALES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '513',
            'nombre' => 'REMUNERACIONES EXTRAORDINARIAS'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '514',
            'nombre' => 'CONTRIBUCIONES PATRONALES A'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '515',
            'nombre' => 'CONTRIBUCIONES PATRONALES A'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '517',
            'nombre' => 'INDEMNIZACIONES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '518',
            'nombre' => 'COMISIONES POR SERVICIOS PERSONALES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'numero' => '519',
            'nombre' => 'REMUNERACIONES DIVERSAS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '541',
            'nombre' => 'BIENES DE USO Y CONSUMO'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '542',
            'nombre' => 'SERVICIOS BASICOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '543',
            'nombre' => 'SERVICIOS GENERALES Y ARRENDAMIENTOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '544',
            'nombre' => 'PASAJES Y VIATICOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '545',
            'nombre' => 'CONSULTORIAS, ESTUDIOS E'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'numero' => '546',
            'nombre' => 'TRATAMIENTO DE DESECHOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'numero' => '555',
            'nombre' => 'IMPUESTOS, TASAS Y DERECHOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'numero' => '556',
            'nombre' => 'SEGUROS, COMISIONES Y GASTOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'numero' => '557',
            'nombre' => 'OTROS GASTOS NO CLASIFICADOS'
        ]);

        Cuenta::create([
            'id_rubro' => '4',
            'numero' => '562',
            'nombre' => 'TRANSFERENCIAS CORRIENTES AL SECTOR'
        ]);

        Cuenta::create([
            'id_rubro' => '4',
            'numero' => '563',
            'nombre' => 'TRANSFERENCIAS CORRIENTES AL SECTOR'
        ]);

        Cuenta::create([
            'id_rubro' => '5',
            'numero' => '611',
            'nombre' => 'BIENES MUEBLES'
        ]);

        Cuenta::create([  // 21
            'id_rubro' => '5',
            'numero' => '612',
            'nombre' => 'BIENES INMUEBLES'
        ]);

        Cuenta::create([  // 22
            'id_rubro' => '5',
            'numero' => '614',
            'nombre' => 'INTANGIBLES'
        ]);

        Cuenta::create([  // 23
            'id_rubro' => '5',
            'numero' => '721',
            'nombre' => 'CUENTAS POR PAGAR DE AÑOS ANTERIORES'
        ]);

    }
}
