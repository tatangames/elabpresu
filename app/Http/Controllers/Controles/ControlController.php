<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ControlController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexRedireccionamiento(){

        $user = Auth::user();

        // $permiso = $user->getAllPermissions()->pluck('name');

        // Rol 1: Encargado-Unidad
        if($user->hasPermissionTo('url.presupuesto.crear.index')){
            $ruta = 'admin.crear.presupuesto.index';
        }

        // Rol 2: Encargado-Presupuesto
        // vista informatico -> redirigir a nuevas solicitudes
        else  if($user->hasPermissionTo('url.encargada.presupuesto.index')){
            $ruta = 'admin.anio.index';
        }

        else{
            // no tiene ningun permiso de vista, redirigir a pantalla sin permisos
            $ruta = 'no.permisos.index';
        }

        $departamento = Departamento::where('id', $user->id_departamento)->pluck('nombre')->first();

        return view('backend.index', compact( 'ruta', 'user', 'departamento'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }

}



