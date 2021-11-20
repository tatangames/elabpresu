<?php

namespace App\Http\Controllers\Admin\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $rubro = Rubro::orderBy('nombre')->get();

        return view('backend.admin.cuenta.index', compact('rubro'));
    }

    public function tablaCuenta(){
        $lista = Cuenta::orderBy('nombre')->get();

        foreach ($lista as $l){
            $rubro = Rubro::where('id', $l->id_rubro)->pluck('nombre')->first();

            $l->rubro = $rubro;
        }

        return view('backend.admin.cuenta.tabla.tablacuenta', compact('lista'));
    }

    public function nuevaCuenta(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required',
            'rubro' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Cuenta();
        $dato->numero = $request->numero;
        $dato->nombre = $request->nombre;
        $dato->id_rubro = $request->rubro;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionCuenta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Cuenta::where('id', $request->id)->first()){

            $rubro = Rubro::orderBy('nombre')->get();

            return ['success' => 1, 'cuenta' => $lista, 'idrr' => $lista->id_rubro,'rr' => $rubro];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarCuenta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required',
            'rubro' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cuenta::where('id', $request->id)->first()){

            Cuenta::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'numero' => $request->numero,
                'id_rubro' => $request->rubro
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


}
