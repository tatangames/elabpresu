<?php

namespace App\Http\Controllers\Admin\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{

    public function index(){
        return view('backend.admin.cuenta.index');
    }

    public function tablaCuenta(){
        $lista = Cuenta::orderBy('nombre')->get();
        return view('backend.admin.cuenta.tabla.tablacuenta', compact('lista'));
    }

    public function nuevaCuenta(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Cuenta();
        $dato->numero = $request->numero;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion del motorista
    public function informacionCuenta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Cuenta::where('id', $request->id)->first()){

            return ['success' => 1, 'cuenta' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar motorista
    public function editarCuenta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cuenta::where('id', $request->id)->first()){

            Cuenta::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'numero' => $request->numero
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


}
