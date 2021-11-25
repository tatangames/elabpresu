<?php

namespace App\Http\Controllers\Admin\Generar;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Models\Departamento;
use App\Models\PresupUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenerarController extends Controller
{
    public function index(){

        $anios = Anio::orderBy('nombre')->get();

        return view('backend.admin.generar.index', compact('anios'));
    }


    public function verificarAprobados(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // veirificar que todos los presupuestos este aprobados

        if(PresupUnidad::where('id_anio', $request->anio)
            ->where('id_estado', 1) // 1
            ->first()){

            // obtener lista de unidades de presupuesto nos aprobados
            $lista = PresupUnidad::where('id_anio', $request->anio)
                ->where('id_estado', 1)->get();

            // obtener nombre
            foreach ($lista as $l){
                $nombre = Departamento::where('id', $l->id_departamento)->pluck('nombre')->first();
                $l->departamento = $nombre;
            }

            return ['success' => 1, 'lista' => $lista];
        }


        return ['success' => 2];
    }

}
