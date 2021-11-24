<?php

namespace App\Http\Controllers\Admin\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Models\Cuenta;
use App\Models\Departamento;
use App\Models\Estado;
use App\Models\Material;
use App\Models\MaterialExtraDetalle;
use App\Models\ObjEspecifico;
use App\Models\PresupUnidad;
use App\Models\PresupUnidadDetalle;
use App\Models\Rubro;
use App\Models\Unidad;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EncargadoPresupuestoController extends Controller
{
    public function index(){

        $departamentos = Departamento::orderBy('nombre')->get();
        $anios = Anio::orderBy('nombre')->get();

        return view('backend.admin.encargado.ver.index', compact('departamentos', 'anios'));
    }


    public function indexVerPresupuesto($depar, $anio){

        // buscar presupuesto
        if($presupuesto = PresupUnidad::where('id_anio', $anio)->where('id_departamento', $depar)->first()){

            $estado = Estado::orderBy('id', 'ASC')->get();
            $preanio = Anio::where('id', $anio)->pluck('nombre')->first();

            $idestado = $presupuesto->id_estado;


            $unidad = Unidad::orderBy('nombre')->get();
            $rubro = Rubro::orderBy('nombre')->get();
            $objeto = ObjEspecifico::orderBy('nombre')->get();

            $resultsBloque = array();
            $index = 0;
            $resultsBloque2 = array();
            $index2 = 0;
            $resultsBloque3 = array();
            $index3 = 0;

            $contador = 0;

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

                        $contador = $contador + 1;
                        $ll->contador = $contador;

                        array_push($resultsBloque3, $ll);

                        $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                            ->orderBy('descripcion', 'ASC')
                            ->get();

                        foreach ($subSecciones3 as $subLista){

                            $uni = Unidad::where('id', $subLista->id_unimedida)->first();
                            $unimedida = $uni->simbolo;

                            $subLista->unimedida = $unimedida;

                            // ingresar los datos a editar
                            if($data = PresupUnidadDetalle::where('id_presup_unidad', $presupuesto->id)
                                ->where('id_material', $subLista->id)->first()){

                                $subLista->cantidad = $data->cantidad;
                                $subLista->periodo = $data->periodo;
                                $total = ($subLista->costo * $data->cantidad) * $data->periodo;
                                $subLista->total = number_format((float)$total, 2, '.', '');

                            }else{
                                $subLista->cantidad = '';
                                $subLista->periodo = '';
                                $subLista->total = '';
                            }
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

            // obtener listado de materiales extra
            $listado = MaterialExtraDetalle::where('id_presup_unidad', $presupuesto->id)->get();

            $idpresupuesto = $presupuesto->id;

            return view('backend.admin.encargado.ver.verpresupuesto', compact( 'estado', 'idestado', 'objeto', 'listado', 'idpresupuesto', 'preanio', 'unidad', 'rubro'));
        }else{
            // presupuesto no encontrado
            return view('backend.admin.encargado.ver.noencontrado');
        }
    }

    public function editarEstado(Request $request){

        $regla = array(
            'idpresupuesto' => 'required',
            'idestado' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(PresupUnidad::where('id', $request->idpresupuesto)->first()){

            PresupUnidad::where('id', $request->idpresupuesto)->update([
                'id_estado' => $request->idestado
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function transferirMaterial(Request $request){

        $regla = array(
            'objeto' => 'required',
            'idpresupuesto' => 'required',
            'idfila' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            // obtener informacion del material extra detalle
            $info = MaterialExtraDetalle::where('id', $request->idfila)->first();

            // agregar a materiales base
            $base = new Material();
            $base->descripcion = strtoupper($info->descripcion);
            $base->id_unimedida = $info->id_unidad;
            $base->id_objespecifico = $request->objeto;
            $base->costo = $info->costo;
            $base->save();

            // agregar material a la unidad detalle
            $prDetalle = new PresupUnidadDetalle();
            $prDetalle->id_presup_unidad = $request->idpresupuesto;
            $prDetalle->id_material = $base->id;
            $prDetalle->cantidad = $info->cantidad;
            $prDetalle->periodo = $info->periodo;
            $prDetalle->save();

            // borrar el material extra
            MaterialExtraDetalle::where('id', $request->idfila)->delete();

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            DB::rollback();

            Log::info('info' . $e);

            return ['success' => 2];
        }
    }


}
