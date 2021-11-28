<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * usuario por defecto.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan',
            'apellido' => 'Moran',
            'usuario' => 'jonathan',
            'password' => bcrypt('admin'),
            'activo' => 1,
            'id_departamento' => 22 // departamento informatica
        ])->assignRole('Encargado-Administrador');
    }
}


