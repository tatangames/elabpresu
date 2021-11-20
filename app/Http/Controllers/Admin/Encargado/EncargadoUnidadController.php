<?php

namespace App\Http\Controllers\Admin\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Models\Cuenta;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\Rubro;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class EncargadoUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $anio = Anio::orderBy('nombre')->get();
        $unidad = Unidad::orderBy('nombre')->get();

        $rubro = Rubro::orderBy('nombre')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('nombre', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('nombre', 'ASC')
                    ->get();

                // agregar materiales
                foreach ($subSecciones2 as $ll){
                    array_push($resultsBloque3, $ll);

                    $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    foreach ($subSecciones3 as $subLista){

                        $uni = Unidad::where('id', $subLista->id_unimedida)->first();
                        $unimedida = $uni->simbolo;

                        $subLista->unimedida = $unimedida;
                    }

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        return view('backend.admin.encargado.crear.index', compact('anio', 'unidad', 'rubro'));
    }


    public function crearPresupuesto(Request $request){

        $regla = array(
            'idanio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $user = Auth::user();

        // devolver lista de base presupuesto para listado tipo acordeon

        return ['success' => 1];
    }

}
