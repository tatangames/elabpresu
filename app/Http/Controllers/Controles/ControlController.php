<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ControlController extends Controller
{

    public function __construct(){
        //$this->middleware('auth');
    }

    public function indexRedireccionamiento(){

       // $user = Auth::user();

        // $permiso = $user->getAllPermissions()->pluck('name');

        // Rol: Super-Admin
        /*if($user->hasPermissionTo('rol.superadmin.inicio')){
            // $ruta = 'admin.roles.index';
            $ruta = 'index.estadisticas';
        }

        // Rol: Admin-Informativo
        // vista informatico -> redirigir a nuevas solicitudes
        else  if($user->hasPermissionTo('rol.informativo.nuevas-solicitudes')){
            $ruta = 'admin2.nuevas.solicitudes.index';
        }*/


       // else{
            // no tiene ningun permiso de vista, redirigir a pantalla sin permisos
            $ruta = 'admin.roles.index';
        //}

        return view('backend.index', compact( 'ruta'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }

}



