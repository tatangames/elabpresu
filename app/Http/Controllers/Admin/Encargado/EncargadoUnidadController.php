<?php

namespace App\Http\Controllers\Admin\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Models\Cuenta;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class EncargadoUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        // verificar si hay presupuesto pendiente por crear

        $idusuario = Auth::id();
        $infouser = Usuario::where('id', $idusuario)->first();

        // solo sera necesario verificar con tabla presub_unidad

        // obtener lista de anios del departamento
        $listaAnios = PresupUnidad::where('id_departamento', $infouser->id_departamento)->get();

        $pila = array();

        foreach ($listaAnios as $p){
            array_push($pila, $p->id_anio);
        }

        $listado = Anio::whereNotIn('id', $pila)->get();

        // redireccionar a vista si ya no hay presupuesto por crear
        if($listado->isEmpty()){
            return view('backend.admin.encargado.crear.indexvacio');
        }

        $unidad = Unidad::orderBy('nombre')->get();

        $rubro = Rubro::orderBy('numero', 'ASC')->get();

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
                ->orderBy('numero', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('numero', 'ASC')
                    ->get();

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->numero == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->where('visible', 1)
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

        return view('backend.admin.encargado.crear.index', compact('listado', 'unidad', 'rubro'));
    }


    public function crearPresupuesto(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $idusuario = Auth::id();
        $userData = Usuario::where('id', $idusuario)->first();

        // verificar que aun no exista el presupuesto
        if(PresupUnidad::where('id_anio', $request->anio)
        ->where('id_departamento', $userData->id_departamento)
        ->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

            $pr = new PresupUnidad();
            $pr->id_anio = $request->anio;
            $pr->id_departamento = $userData->id_departamento;
            $pr->id_estado = 1; // editable
            $pr->save();

            if($request->idmaterial != null) {
                for ($i = 0; $i < count($request->idmaterial); $i++) {

                    $infoMaterial = Material::where('id', $request->idmaterial[$i])->first();

                    $prDetalle = new PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $pr->id;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->precio = $infoMaterial->costo;
                    $prDetalle->save();
                }
            }

            // ingreso de materiales extra
            if($request->descripcion != null) {
                for ($j = 0; $j < count($request->descripcion); $j++) {

                    $mtrDetalle = new MaterialExtraDetalle();
                    $mtrDetalle->id_presup_unidad = $pr->id;
                    $mtrDetalle->id_unidad = $request->unidadmedida[$j];
                    $mtrDetalle->descripcion = $request->descripcion[$j];
                    $mtrDetalle->costo = $request->costoextra[$j];
                    $mtrDetalle->cantidad = $request->cantidadextra[$j];
                    $mtrDetalle->periodo = $request->periodoextra[$j];
                    $mtrDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            Log::info('ee' . $e);
            return ['success' => 3];
        }
    }

    public function indexEditar(){

        // visualizar solo los años de presupuestos ya creados

        $idusuario = Auth::id();
        $infouser = Usuario::where('id', $idusuario)->first();

        // solo sera necesario verificar con tabla presub_unidad

        // obtener lista de anios del departamento
        $listaAnios = PresupUnidad::where('id_departamento', $infouser->id_departamento)->get();

        $pila = array();

        foreach ($listaAnios as $p){
            array_push($pila, $p->id_anio);
        }

        $listado = Anio::whereIn('id', $pila)->get();

        return view('backend.admin.encargado.editar.index', compact('listado'));
    }


    public function indexEditarAnio($anio){

        $idusuario = Auth::id();
        $infouser = Usuario::where('id', $idusuario)->first();

        // siempre habra un registro
        $presupuesto = PresupUnidad::where('id_departamento', $infouser->id_departamento)
            ->where('id_anio', $anio)->first();

        // listado de presupuesto por anio y departamento
        $listadoPresupuesto = PresupUnidad::where('id_departamento', $infouser->id_departamento)
            ->where('id_anio', $anio)->get();

        $pila = array();

        foreach ($listadoPresupuesto as $lp){
            array_push($pila, $lp->id);
        }

        $idpresupuesto = $presupuesto->id;
        $estado = Estado::where('id', $presupuesto->id_estado)->first();
        $preanio = Anio::where('id', $anio)->pluck('nombre')->first();

        $unidad = Unidad::orderBy('nombre')->get();
        $rubro = Rubro::orderBy('numero')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $totalvalor = 0;

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
                        ->where('visible', 1)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = Unidad::where('id', $subLista->id_unimedida)->first();
                        $unimedida = $uni->simbolo;

                        $subLista->unimedida = $unimedida;

                        // ingresar los datos a editar
                        if($data = PresupUnidadDetalle::where('id_presup_unidad', $presupuesto->id)
                        ->where('id_material', $subLista->id)->first()){

                            $subLista->precio = $data->precio;

                            $subLista->cantidad = $data->cantidad;
                            $subLista->periodo = $data->periodo;
                            $total = ($data->precio * $data->cantidad) * $data->periodo;
                            $subLista->total = '$' . number_format((float)$total, 2, '.', '');

                            $sumaObjeto = $sumaObjeto + $total;
                        }else{
                            $subLista->cantidad = '';
                            $subLista->periodo = '';
                            $subLista->total = '';
                            $subLista->precio = '';
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

        $totalvalor = number_format((float)$totalvalor, 2, '.', '');

        // obtener listado de materiales extra
        $listado = MaterialExtraDetalle::where('id_presup_unidad', $presupuesto->id)->get();

        return view('backend.admin.encargado.editar.indexeditable', compact( 'estado', 'totalvalor', 'listado', 'anio', 'idpresupuesto', 'preanio', 'unidad', 'rubro'));
    }

    public function editarPresupuesto(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $idusuario = Auth::id();
        $infouser = Usuario::where('id', $idusuario)->first();

        // siempre habra un registro
        if(PresupUnidad::where('id_departamento', $infouser->id_departamento)
            ->where('id_anio', $request->anio)
            ->where('id_estado', 2) // ya esta aprobado
            ->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

            // borrar todos el presupuesto base
            PresupUnidadDetalle::where('id_presup_unidad', $request->idpresupuesto)
                ->delete();

            // borrar materiales extra
            MaterialExtraDetalle::where('id_presup_unidad', $request->idpresupuesto)
                ->delete();

            if($request->unidades != null) {
                // crear de nuevo presupuesto base
                for ($i = 0; $i < count($request->unidades); $i++) {

                    $infoMaterial = Material::where('id', $request->idmaterial[$i])->first();

                    $prDetalle = new PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $request->idpresupuesto;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->precio = $infoMaterial->costo;
                    $prDetalle->save();
                }
            }

            // ingresar materiales extra

            if($request->descripcion != null) {
                for ($j = 0; $j < count($request->descripcion); $j++) {

                    $mtrDetalle = new MaterialExtraDetalle();
                    $mtrDetalle->id_presup_unidad = $request->idpresupuesto;
                    $mtrDetalle->id_unidad = $request->unidadmedida[$j];
                    $mtrDetalle->descripcion = $request->descripcion[$j];
                    $mtrDetalle->costo = $request->costoextra[$j];
                    $mtrDetalle->cantidad = $request->cantidadextra[$j];
                    $mtrDetalle->periodo = $request->periodoextra[$j];
                    $mtrDetalle->save();
                }
            }

            DB::commit();

            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 3];
        }
    }

}
