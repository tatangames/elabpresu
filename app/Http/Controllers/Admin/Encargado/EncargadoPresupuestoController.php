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

            $rubro = Rubro::orderBy('numero', 'ASC')->get();
            $objeto = ObjEspecifico::orderBy('numero', 'ASC')->get();

            $resultsBloque = array();
            $index = 0;
            $resultsBloque2 = array();
            $index2 = 0;
            $resultsBloque3 = array();
            $index3 = 0;

            $totalvalor = 0;

            $listadoPresupuesto = PresupUnidad::where('id_departamento', $depar)
                ->where('id_anio', $anio)->get();

            $pila = array();

            foreach ($listadoPresupuesto as $lp){
                array_push($pila, $lp->id);
            }

            // agregar cuentas
            foreach($rubro as $secciones){
                array_push($resultsBloque,$secciones);

                $sumaRubro = 0;

                $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                    ->orderBy('numero', 'ASC')
                    ->get();

                // agregar objetos
                foreach ($subSecciones as $lista){

                    array_push($resultsBloque2, $lista);

                    $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                        ->orderBy('numero', 'ASC')
                        ->get();

                    $sumaObjetoTotal = 0;

                    // agregar materiales
                    foreach ($subSecciones2 as $ll){

                        array_push($resultsBloque3, $ll);

                        if($ll->numero == 61109){
                            $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                        }

                        $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                            ->orderBy('descripcion', 'ASC')
                            ->get();

                        $sumaObjeto = 0;

                        foreach ($subSecciones3 as $subLista){

                            $uni = Unidad::where('id', $subLista->id_unimedida)->first();

                            $subLista->unimedida = $uni->simbolo;

                            // ingresar los datos a editar
                            if($data = PresupUnidadDetalle::where('id_presup_unidad', $presupuesto->id)
                                ->where('id_material', $subLista->id)->first()){

                                $subLista->cantidad = $data->cantidad;
                                $subLista->periodo = $data->periodo;
                                $total = ($subLista->costo * $data->cantidad) * $data->periodo;
                                $subLista->total = number_format((float)$total, 2, '.', '');

                                $sumaObjeto = $sumaObjeto + $total;

                            }else{
                                $subLista->cantidad = '';
                                $subLista->periodo = '';
                                $subLista->total = '';
                            }
                        }

                        $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                        $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', '');

                        $resultsBloque3[$index3]->material = $subSecciones3;
                        $index3++;
                    }

                    $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                    $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', '');

                    $resultsBloque2[$index2]->objeto = $subSecciones2;
                    $index2++;
                }
                $totalvalor = $totalvalor + $sumaRubro;
                $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', '');

                $resultsBloque[$index]->cuenta = $subSecciones;
                $index++;
            }

            // obtener listado de materiales extra
            $listado = MaterialExtraDetalle::where('id_presup_unidad', $presupuesto->id)->get();

            foreach ($listado as $lista){
                $uni = Unidad::where('id', $lista->id_unidad)->first();
                $lista->simbolo = $uni->simbolo;
            }

            $idpresupuesto = $presupuesto->id;

            $totalvalor = number_format((float)$totalvalor, 2, '.', '');

            return view('backend.admin.encargado.ver.verpresupuesto', compact( 'estado', 'idestado', 'totalvalor', 'objeto', 'listado', 'idpresupuesto', 'preanio', 'rubro'));
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

        // el presupuesto debe estar aprobado primeramente
        if(PresupUnidad::where('id', $request->idpresupuesto)
            ->where('id_estado', 1) // presupuesto no aprobado aun
            ->first()){
            return ['success' => 1];
        }

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
            $base->visible = 1;
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
            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();

            return ['success' => 3];
        }
    }


}
