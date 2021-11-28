<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- CREAR ROLES ---

        // encargado administrador
        $role1 = Role::create(['name' => 'Encargado-Administrador']);

        // encargado de la unidad o departamento
        $role2 = Role::create(['name' => 'Encargado-Unidad']);

        // encargado del presupuesto
        $role3 = Role::create(['name' => 'Encargado-Presupuesto']);


        // --- CREAR PERMISOS ---

        // visualizar roles y permisos
        Permission::create(['name' => 'seccion.roles.y.permisos', 'description' => 'Cuando hace login, se podra visualizar roles y permisos'])->syncRoles($role1);

        // redireccionamiento a url - encargado de unidad
        Permission::create(['name' => 'url.presupuesto.crear.index', 'description' => 'Cuando hace login, se redirigirá la vista Mi Presupuesto - Crear'])->syncRoles($role1, $role2);

        // redireccionamiento a url - encargado de presupuesto
        Permission::create(['name' => 'url.encargada.presupuesto.index', 'description' => 'Cuando hace login, se redirigirá la vista lista de presupuesto'])->syncRoles($role3);


    }
}
