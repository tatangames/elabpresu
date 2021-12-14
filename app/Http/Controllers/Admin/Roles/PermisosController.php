<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Unidad;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $roles = Role::all()->pluck('name', 'id');

        $unidad = Departamento::orderBy('nombre')->get();

        return view('backend.admin.permisos.index', compact('roles', 'unidad'));
    }

    public function tablaUsuarios(){
        $usuarios = Usuario::orderBy('id', 'ASC')->get();

        foreach ($usuarios as $l){

            $l->departamento = Departamento::where('id',$l->id_departamento)->pluck('nombre')->first();
        }

        return view('backend.admin.permisos.tabla.tablapermisos', compact('usuarios'));
    }

    public function nuevoUsuario(Request $request){
        if(Usuario::where('usuario', $request->usuario)->first()){
            return ['success' => 1];
        }

        $u = new Usuario();
        $u->nombre = $request->nombre;
        $u->apellido = $request->apellido;
        $u->usuario = $request->usuario;
        $u->password = bcrypt($request->password);
        $u->id_departamento = $request->unidad;
        $u->activo = 1;

        if ($u->save()) {
            $u->assignRole($request->rol);
            return ['success' => 2];
        } else {
            return ['success' => 3];
        }
    }

    public function infoUsuario(Request $request){
        if($info = Usuario::where('id', $request->id)->first()){

            $roles = Role::all()->pluck('name', 'id');

            $idrol = $info->roles->pluck('id');

            $unidad = Departamento::orderBy('nombre')->get();

            return ['success' => 1,
                'info' => $info,
                'unidad' => $unidad,
                'roles' => $roles,
                'idrol' => $idrol,
                'idunidad' => $info->id_departamento];

        }else{
            return ['success' => 2];
        }
    }

    public function editarUsuario(Request $request){

        if(Usuario::where('id', $request->id)->first()){

            if(Usuario::where('usuario', $request->usuario)->where('id', '!=', $request->id)->first()){
                return ['success' => 1];
            }

            $usuario = Usuario::find($request->id);
            $usuario->nombre = $request->nombre;
            $usuario->apellido = $request->apellido;
            $usuario->usuario = $request->usuario;
            $usuario->activo = $request->toggle;
            $usuario->id_departamento = $request->unidad;

            if($request->password != null){
                $usuario->password = $request->password;
            }

            //$usuario->assignRole($request->rol); asigna un rol extra

            //elimina el rol existente y agrega el nuevo
            $usuario->syncRoles($request->rol);

            $usuario->save();

            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function nuevoRol(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $mensaje = array(
            'nombre.required' => 'Nombre es requerido',
        );

        $validar = Validator::make($request->all(), $regla, $mensaje);

        if ($validar->fails()){return ['success' => 0];}

        // verificar si existe el rol
        if(Role::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Role::create(['name' => $request->nombre]);

        return ['success' => 2];
    }

    public function nuevoPermisoExtra(Request $request){

        // verificar si existe el permiso
        if(Permission::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Permission::create(['name' => $request->nombre, 'description' => $request->descripcion]);

        return ['success' => 2];
    }

    public function borrarPermisoGlobal(Request $request){

        // buscamos el permiso el cual queremos eliminar
        $permission = Permission::findById($request->idpermiso)->delete();

        return ['success' => 1];
    }


}
