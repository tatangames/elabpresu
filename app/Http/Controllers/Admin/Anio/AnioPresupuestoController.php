<?php

namespace App\Http\Controllers\Admin\Anio;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AnioPresupuestoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.anio.index');
    }

    public function tablaAnio(){
        $lista = Anio::orderBy('nombre')->get();
        return view('backend.admin.anio.tabla.tablaanio', compact('lista'));
    }

    public function nuevaAnio(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Anio();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionAnio(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Anio::where('id', $request->id)->first()){

            return ['success' => 1, 'departamento' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarAnio(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Anio::where('id', $request->id)->first()){

            Anio::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}
