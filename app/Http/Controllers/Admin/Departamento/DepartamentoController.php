<?php

namespace App\Http\Controllers\Admin\Departamento;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartamentoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.departamento.index');
    }

    public function tablaDepartamento(){
        $lista = Departamento::orderBy('nombre')->get();
        return view('backend.admin.departamento.tabla.tabladepartamento', compact('lista'));
    }

    public function nuevaDepartamento(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Departamento();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionDepartamento(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Departamento::where('id', $request->id)->first()){

            return ['success' => 1, 'departamento' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarDepartamento(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Departamento::where('id', $request->id)->first()){

            Departamento::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}
