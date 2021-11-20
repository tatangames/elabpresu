<?php

namespace App\Http\Controllers\Admin\ObjEspecifico;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObjEspecificoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $cuenta = Cuenta::orderBy('nombre')->get();

        return view('backend.admin.objespecifico.index', compact('cuenta'));
    }

    public function tablaObjEspecifico(){
        $lista = ObjEspecifico::orderBy('nombre')->get();

        foreach ($lista as $l){
            $cuenta = Cuenta::where('id', $l->id_cuenta)->pluck('nombre')->first();

            $l->cuenta = $cuenta;
        }

        return view('backend.admin.objespecifico.tabla.tablaobj', compact('lista'));
    }

    public function nuevoObjEspecifico(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required',
            'cuenta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new ObjEspecifico();
        $dato->numero = $request->numero;
        $dato->nombre = $request->nombre;
        $dato->id_cuenta = $request->cuenta;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionObjEspecifico(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = ObjEspecifico::where('id', $request->id)->first()){

            $cuenta = Cuenta::orderBy('nombre')->get();

            return ['success' => 1, 'objespecifico' => $lista, 'idcuenta' => $lista->id_cuenta, 'cuenta' => $cuenta];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarObjEspecifico(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required',
            'cuenta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(ObjEspecifico::where('id', $request->id)->first()){

            ObjEspecifico::where('id', $request->id)->update([
                'nombre' => $request -> nombre,
                'numero' => $request -> numero,
                'id_cuenta' => $request -> cuenta
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}
