<?php

namespace App\Http\Controllers\Admin\Rubro;

use App\Http\Controllers\Controller;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RubroController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.rubro.index');
    }

    public function tablaRubro(){
        $lista = Rubro::orderBy('nombre')->get();
        return view('backend.admin.rubro.tabla.tablarubro', compact('lista'));
    }

    public function nuevaRubro(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Rubro();
        $dato->nombre = $request->nombre;
        $dato->numero = $request->numero;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionRubro(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Rubro::where('id', $request->id)->first()){

            return ['success' => 1, 'rubro' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarRubro(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Rubro::where('id', $request->id)->first()){

            Rubro::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'numero' => $request->numero
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



}
