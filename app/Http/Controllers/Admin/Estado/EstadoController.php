<?php

namespace App\Http\Controllers\Admin\Estado;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstadoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.estado.index');
    }

    public function tablaEstado(){
        $lista = Estado::orderBy('nombre')->get();
        return view('backend.admin.estado.tabla.tablaestado', compact('lista'));
    }

    public function nuevaEstado(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Estado();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionEstado(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Estado::where('id', $request->id)->first()){

            return ['success' => 1, 'estado' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarEstado(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Estado::where('id', $request->id)->first()){

            Estado::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}
