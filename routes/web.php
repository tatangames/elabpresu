<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Admin\Perfil\PerfilController;
use App\Http\Controllers\Admin\Unidades\UnidadesController;
use App\Http\Controllers\Admin\Cuenta\CuentaController;
use App\Http\Controllers\Admin\Roles\RolesController;
use App\Http\Controllers\Admin\Roles\PermisosController;


Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');


    Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
    Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
    Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
    Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
    Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
    Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
    Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
    Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
    Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

    // --- PERMISOS ---
    Route::get('/admin/permisos/index', [PermisosController::class,'index'])->name('admin.permisos.index');
    Route::get('/admin/permisos/tabla', [PermisosController::class,'tablaUsuarios']);
    Route::post('/admin/permisos/nuevo-usuario', [PermisosController::class, 'nuevoUsuario']);
    Route::post('/admin/permisos/info-usuario', [PermisosController::class, 'infoUsuario']);
    Route::post('/admin/permisos/editar-usuario', [PermisosController::class, 'editarUsuario']);
    Route::post('/admin/permisos/nuevo-rol', [PermisosController::class, 'nuevoRol']);
    Route::post('/admin/permisos/extra-nuevo', [PermisosController::class, 'nuevoPermisoExtra']);
    Route::post('/admin/permisos/extra-borrar', [PermisosController::class, 'borrarPermisoGlobal']);

    // --- PERFIL ---
    Route::get('/admin/editar-perfil/index', [PerfilController::class,'index'])->name('admin.perfil');
    Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

    // --- UNIDADES ---
    Route::get('/admin/unidades/index', [UnidadesController::class,'index'])->name('admin.unidades.index');
    Route::get('/admin/unidades/tabla', [UnidadesController::class,'tablaUnidades']);
    Route::post('/admin/unidades/nuevo', [UnidadesController::class, 'nuevaUnidad']);
    Route::post('/admin/unidades/informacion', [UnidadesController::class, 'informacionUnidad']);
    Route::post('/admin/unidades/editar', [UnidadesController::class, 'editarUnidad']);

    // --- CUENTA ---
    Route::get('/admin/cuenta/index', [CuentaController::class,'index'])->name('admin.cuenta.index');
    Route::get('/admin/cuenta/tabla', [CuentaController::class,'tablaCuenta']);
    Route::post('/admin/cuenta/nuevo', [CuentaController::class, 'nuevaCuenta']);
    Route::post('/admin/cuenta/informacion', [CuentaController::class, 'informacionCuenta']);
    Route::post('/admin/cuenta/editar', [CuentaController::class, 'editarCuenta']);










