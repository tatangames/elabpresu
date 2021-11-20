<?php

namespace App\Http\Controllers\Admin\Unidades;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unidad;
use Illuminate\Support\Facades\Validator;

class UnidadesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.unidad.index');
    }

    public function tablaUnidades(){
        $lista = Unidad::orderBy('nombre')->get();
        return view('backend.admin.unidad.tabla.tablaunidad', compact('lista'));
    }

    public function nuevaUnidad(Request $request){

        $regla = array(
            'nombre' => 'required',
            'simbolo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Unidad();
        $dato->nombre = $request->nombre;
        $dato->simbolo = $request->simbolo;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionUnidad(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Unidad::where('id', $request->id)->first()){

            return ['success' => 1, 'unidad' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarUnidad(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'simbolo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Unidad::where('id', $request->id)->first()){

            Unidad::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'simbolo' => $request->simbolo
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}
