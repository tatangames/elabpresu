<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Admin\Perfil\PerfilController;
use App\Http\Controllers\Admin\Unidades\UnidadesController;
use App\Http\Controllers\Admin\Cuenta\CuentaController;
use App\Http\Controllers\Admin\Roles\RolesController;
use App\Http\Controllers\Admin\Roles\PermisosController;
use App\Http\Controllers\Admin\Rubro\RubroController;
use App\Http\Controllers\Admin\ObjEspecifico\ObjEspecificoController;
use App\Http\Controllers\Admin\Departamento\DepartamentoController;
use App\Http\Controllers\Admin\BasePresupuesto\BasePresupuestoController;
use App\Http\Controllers\Admin\Anio\AnioPresupuestoController;
use App\Http\Controllers\Admin\Estado\EstadoController;
use App\Http\Controllers\Admin\Encargado\EncargadoUnidadController;
use App\Http\Controllers\Admin\Encargado\EncargadoPresupuestoController;
use App\Http\Controllers\Admin\Generar\GenerarController;


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
    Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
    Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);


    // --- UNIDADES ---
    Route::get('/admin/unidades/index', [UnidadesController::class,'index'])->name('admin.unidades.index');
    Route::get('/admin/unidades/tabla', [UnidadesController::class,'tablaUnidades']);
    Route::post('/admin/unidades/nuevo', [UnidadesController::class, 'nuevaUnidad']);
    Route::post('/admin/unidades/informacion', [UnidadesController::class, 'informacionUnidad']);
    Route::post('/admin/unidades/editar', [UnidadesController::class, 'editarUnidad']);

    // --- RUBRO ---
    Route::get('/admin/rubro/index', [RubroController::class,'index'])->name('admin.rubro.index');
    Route::get('/admin/rubro/tabla', [RubroController::class,'tablaRubro']);
    Route::post('/admin/rubro/nuevo', [RubroController::class, 'nuevaRubro']);
    Route::post('/admin/rubro/informacion', [RubroController::class, 'informacionRubro']);
    Route::post('/admin/rubro/editar', [RubroController::class, 'editarRubro']);

    // --- CUENTA ---
    Route::get('/admin/cuenta/index', [CuentaController::class,'index'])->name('admin.cuenta.index');
    Route::get('/admin/cuenta/tabla', [CuentaController::class,'tablaCuenta']);
    Route::post('/admin/cuenta/nuevo', [CuentaController::class, 'nuevaCuenta']);
    Route::post('/admin/cuenta/informacion', [CuentaController::class, 'informacionCuenta']);
    Route::post('/admin/cuenta/editar', [CuentaController::class, 'editarCuenta']);

    // --- OBJETO ESPECIFICO ---
    Route::get('/admin/objespecifico/index', [ObjEspecificoController::class,'index'])->name('admin.objespecifico.index');
    Route::get('/admin/objespecifico/tabla', [ObjEspecificoController::class,'tablaObjEspecifico']);
    Route::post('/admin/objespecifico/nuevo', [ObjEspecificoController::class, 'nuevoObjEspecifico']);
    Route::post('/admin/objespecifico/informacion', [ObjEspecificoController::class, 'informacionObjEspecifico']);
    Route::post('/admin/objespecifico/editar', [ObjEspecificoController::class, 'editarObjEspecifico']);

    // --- DEPARTAMENTO ---
    Route::get('/admin/departamento/index', [DepartamentoController::class,'index'])->name('admin.departamento.index');
    Route::get('/admin/departamento/tabla', [DepartamentoController::class,'tablaDepartamento']);
    Route::post('/admin/departamento/nuevo', [DepartamentoController::class, 'nuevaDepartamento']);
    Route::post('/admin/departamento/informacion', [DepartamentoController::class, 'informacionDepartamento']);
    Route::post('/admin/departamento/editar', [DepartamentoController::class, 'editarDepartamento']);

    // --- BASE DE PRESUPUESTO ---
    Route::get('/admin/basepresupuesto/index', [BasePresupuestoController::class,'index'])->name('admin.basepresupuesto.index');
    Route::get('/admin/basepresupuesto/tabla', [BasePresupuestoController::class,'tablaPresupuesto']);

    Route::post('/admin/basepresupuesto/nuevo', [BasePresupuestoController::class, 'nuevaBasePresupuesto']);
    Route::post('/admin/basepresupuesto/informacion', [BasePresupuestoController::class, 'infoBasePresupuesto']);
    Route::post('/admin/basepresupuesto/editar', [BasePresupuestoController::class, 'editarBasePresupuesto']);

    // --- AÑO DE PRESUPUESTO ---
    Route::get('/admin/anio/index', [AnioPresupuestoController::class,'index'])->name('admin.anio.index');
    Route::get('/admin/anio/tabla', [AnioPresupuestoController::class,'tablaAnio']);
    Route::post('/admin/anio/nuevo', [AnioPresupuestoController::class, 'nuevaAnio']);
    Route::post('/admin/anio/informacion', [AnioPresupuestoController::class, 'informacionAnio']);
    Route::post('/admin/anio/editar', [AnioPresupuestoController::class, 'editarAnio']);

    // --- ESTADO ---
    Route::get('/admin/estado/index', [EstadoController::class,'index'])->name('admin.estado.index');
    Route::get('/admin/estado/tabla', [EstadoController::class,'tablaEstado']);
    Route::post('/admin/estado/nuevo', [EstadoController::class, 'nuevaEstado']);
    Route::post('/admin/estado/informacion', [EstadoController::class, 'informacionEstado']);
    Route::post('/admin/estado/editar', [EstadoController::class, 'editarEstado']);


    // --- NUEVO PRESUPUESTO - ROL ENCARGADO DE UNIDAD
    Route::get('/admin/nuevo/presupuesto/index', [EncargadoUnidadController::class,'index'])->name('admin.crear.presupuesto.index');
    Route::post('/admin/nuevo/presupuesto/crear', [EncargadoUnidadController::class,'crearPresupuesto']);

    // --- EDITAR PRESUPUESTO - ROL ENCARGADO DE UNIDAD
    Route::get('/admin/editar/presupuesto/index', [EncargadoUnidadController::class,'indexEditar'])->name('admin.editar.presupuesto.index');
    Route::get('/admin/editar/presupuesto/anio/{id}', [EncargadoUnidadController::class,'indexEditarAnio']);
    Route::post('/admin/nuevo/presupuesto/editar', [EncargadoUnidadController::class,'editarPresupuesto']);

    // --- PRESUPUESTOS REVISAR - ROL ENCARGADO DE PRESUPUESTO
    Route::get('/admin/departamento/presupuesto/index', [EncargadoPresupuestoController::class,'index'])->name('admin.ver.presupuestos.index');
    Route::get('/admin/departamento/presup/aniounidad/{unidad}/{anio}', [EncargadoPresupuestoController::class,'indexVerPresupuesto']);
    Route::post('/admin/departamento/presup/editar', [EncargadoPresupuestoController::class,'editarEstado']);
    Route::post('/admin/departamento/presup/transferir', [EncargadoPresupuestoController::class,'transferirMaterial']);


    // --- GENERADOR DE PRESUPUESTO - ROL ENCARGADO DE PRESUPUESTO
    Route::get('/admin/generador/presupuesto/index', [GenerarController::class,'index'])->name('admin.generar.presupuestos.index');
    Route::post('/admin/generador/verificar/presupuesto', [GenerarController::class,'verificarAprobados']);
    Route::get('/admin/generador/tabla/consolidado/{anio}', [GenerarController::class,'tablaConsolidado']);


    // PDF
    Route::get('/admin/generador/pdf/presupuesto/{id}', [GenerarController::class, 'generarPdf']);
    Route::get('/admin/generador/pdf/totales/{id}', [GenerarController::class, 'generarPdfTotales']);




    // --- SIN PERMISOS VISTA 403 ---
    Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');



