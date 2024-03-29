<?php

namespace App\Http\Controllers\Admin\Generar;

use App\Exports\ExportarConsolidadoExcel;
use App\Exports\ExportarPorUnidadesExcel;
use App\Exports\ExportarTotalesExcel;
use App\Exports\ExportarUnaUnidadExcel;
use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Models\Cuenta;
use App\Models\Departamento;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\PresupUnidad;
use App\Models\PresupUnidadDetalle;
use App\Models\Rubro;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class GenerarController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $anios = Anio::orderBy('nombre')->get();

        $unidad = Departamento::orderBy('nombre')->get();

        return view('backend.admin.generar.index', compact('anios', 'unidad'));
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

        // obtener listado de departamentos
        $depar = Departamento::all();
        $pila = array();

        foreach ($depar as $de){

            if($pre = PresupUnidad::where('id_anio', $request->anio)
                ->where('id_departamento', $de->id)->first()){

                if($pre->id_estado == 1){
                    array_push($pila, $de->id);
                }

            }else{
                // no esta creado aun
                array_push($pila, $de->id);
            }
        }

        $lista = Departamento::whereIn('id', $pila)
            ->orderBy('nombre', 'ASC')
            ->get();

        if($lista->isEmpty()){
            return ['success' => 1];
        }

        return ['success' => 2, 'lista' => $lista];
    }

    public function tablaConsolidado($anio){

        $rubro = Rubro::orderBy('nombre')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // listado de presupuesto por anio
        $listadoPresupuesto = PresupUnidad::where('id_anio', $anio)->get();

        $pila = array();

        foreach ($listadoPresupuesto as $lp){
            array_push($pila, $lp->id);
        }

        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('nombre', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $sumaObjetoTotal = 0;

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('nombre', 'ASC')
                    ->get();

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $sumaunidades = 0;
                        $sumaperiodos = 0;
                        $multiunidades = 0;

                        $listaMateriales = PresupUnidadDetalle::whereIn('id_presup_unidad', $pila)
                            ->where('id_material', $subLista->id)
                            ->get();

                        foreach ($listaMateriales as $lm){
                            $sumaunidades = $sumaunidades + $lm->cantidad;
                            $sumaperiodos = $sumaperiodos + $lm->periodo;
                            $multiunidades = $multiunidades + (($lm->cantidad * $lm->precio) * $lm->periodo);
                        }

                        $sumaObjeto = $sumaObjeto + $multiunidades;

                        $subLista->sumaunidades = number_format((float)$sumaunidades, 2, '.', ',');
                        $subLista->sumaperiodos = number_format((float)$sumaperiodos, 2, '.', ',');
                        $subLista->multiunidad = number_format((float)$multiunidades, 2, '.', ',');
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        return view('backend.admin.generar.tabla.tablagenerar', compact('rubro', 'anio'));
    }

    // generar pdf para consolidado
    public function generarPdfConsolidado($anio){

        $rubro = Rubro::orderBy('numero')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        //ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        // listado de presupuesto por anio
        $arrayPresupUnidad = PresupUnidad::where('id_anio', $anio)->get();
        $fechaanio = Anio::where('id', $anio)->pluck('nombre')->first();

        $pilaIdPresupUnidad = array();

        $totalobj = 0;
        $totalcuenta = 0;
        $totalrubro = 0;

        foreach ($arrayPresupUnidad as $lp){
            array_push($pilaIdPresupUnidad, $lp->id);
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

                $sumaObjetoTotal = 0;

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('numero', 'ASC')
                    ->get();

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    // todos los materiales del mismo objeto, ya filtrado por año
                    $subSecciones3 = Material::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $sumaunidades = 0;
                        $sumaperiodos = 0;
                        $multiunidades = 0;

                        // pila (id presup unidad) ya filtrado por año
                        $listaMateriales = PresupUnidadDetalle::whereIn('id_presup_unidad', $pilaIdPresupUnidad)
                            ->where('id_material', $subLista->id)
                            ->get();

                        foreach ($listaMateriales as $lm){
                            $sumaunidades = $sumaunidades + $lm->cantidad;
                            $sumaperiodos = $sumaperiodos + $lm->periodo;
                            $multiunidades = $multiunidades + (($lm->cantidad * $lm->precio) * $lm->periodo);
                        }

                        $sumaObjeto = $sumaObjeto + $multiunidades;

                        $subLista->sumaunidades = number_format((float)$sumaunidades, 2, '.', ',');
                        $subLista->sumaperiodos = number_format((float)$sumaperiodos, 2, '.', ',');
                        $subLista->multiunidad = number_format((float)$multiunidades, 2, '.', ',');
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $totalobj = $totalobj + $sumaObjeto;

                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $totalcuenta = $totalcuenta + $sumaObjetoTotal;

                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalrubro = $totalrubro + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $totalobj = number_format((float)$totalobj, 2, '.', ',');
        $totalcuenta = number_format((float)$totalcuenta, 2, '.', ',');
        $totalrubro = number_format((float)$totalrubro, 2, '.', ',');


        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;
        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
        <table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 11%'>COD.</th>
            <th style='text-align: center; font-size:13px; width: 30%'>ESPECIFICO</th>
            <th style='text-align: center; font-size:13px; width: 14%'>OBJ.ESPECIFICO</th>
            <th style='text-align: center; font-size:13px; width: 14%'>CUENTA</th>
            <th style='text-align: center; font-size:13px; width: 14%'>RUBRO</th>
        </tr>";

        foreach($rubro as $item){
            $tabla .= "
            <tr>
                <td style='font-size:11px; text-align: left'>$item->numero</td>
                <td style='font-size:11px; text-align: left'>$item->nombre</td>
                <td></td>
                <td></td>
                <td style='font-size:11px; text-align: right'>$ $item->sumarubro</td>
            </tr>";

            foreach($item->cuenta as $cc){

                $tabla .= "<tr>
                    <td style='font-size:11px; text-align: left'>$cc->numero</td>
                    <td style='font-size:11px; text-align: left'>$cc->nombre</td>
                    <td></td>
                    <td style='font-size:11px; text-align: right'>$ $cc->sumaobjetototal</td>
                    <td></td>
                </tr>";

                foreach($cc->objeto as $obj){

                    $tabla .= "<tr>
                        <td style='font-size:11px; text-align: left'>$obj->numero</td>
                        <td style='font-size:11px; text-align: left'>$obj->nombre</td>
                        <td style='font-size:11px; text-align: right'>$ $obj->sumaobjeto</td>
                        <td></td>
                        <td></td>
                    </tr>";

                }
            }
        }

        $tabla .= "<tr>
            <td style='border: none'></td>
            <td style='font-size:13px; text-align: center; font-weight: bold; border: none'>TOTAL</td>
            <td style='font-size:13px; text-align: right'>$ $totalobj</td>
            <td style='font-size:13px; text-align: right'>$ $totalcuenta</td>
            <td style='font-size:13px; text-align: right'>$ $totalrubro</td>
        </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssconsolidado.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla,2);
        $mpdf->Output();
    }


    // GENERAR TOTALES EN PDF
    public function generarPdfTotales($idanio){

        // obtener todos los departamentos, que han creado el presupuesto
        $arrayPresupuestoUni = PresupUnidad::where('id_anio', $idanio)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

        // listado
        $fechaanio = Anio::where('id', $idanio)->pluck('nombre')->first();
        ini_set("pcre.backtrack_limit", "5000000");

        // COLUMNA TOTAL GLOBAL
        $totalColumnaGlobal = 0;
        // COLUMNA TOTAL CANTIDAD
        $totalColumnaCantidad = 0;


        $materiales = Material::orderBy('descripcion')->get();
        // recorrer cada material
        foreach ($materiales as $mm) {

            // para suma de cantidad para cada fila. columna CANTIDAD
            $sumacantidad = 0;

            $infoObj = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // dinero fila columna TOTAL
            $multiFila = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupuestoUni as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    // PERIODO SIEMPRE SERA 1 COMO MÍNIMO
                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila = $multiFila + $resultado;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);
                }
            }

            // si es mayor a cero, es porque si hay cantidad * periodo
            if($sumacantidad > 0){

                $totalColumnaGlobal += $multiFila;
                $totalColumnaCantidad += $sumacantidad;

                $infoUnidadMedida = Unidad::where('id', $mm->id_unimedida)->first();

                $dataArray[] = [
                    'idmaterial' => $mm->id,
                    'codigo' => $infoObj->numero,
                    'descripcion' => $mm->descripcion,
                    'sumacantidad' => number_format((float)($sumacantidad), 2, '.', ','),
                    'sumacantidadDeci' => $sumacantidad,
                    'unidadmedida' => $infoUnidadMedida->nombre,
                    'total' => number_format((float)($multiFila), 2, '.', ','), // dinero
                    'totalDecimal' => $multiFila
                ];
            }
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        $totalColumnaCantidad = number_format((float)($totalColumnaCantidad), 2, '.', ',');
        $totalColumnaGlobal = number_format((float)($totalColumnaGlobal), 2, '.', ',');

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $rubro = Rubro::orderBy('numero')->get();

        $pilaIdMaterial = array();
        foreach ($dataArray as $dd){
            array_push($pilaIdMaterial, $dd['idmaterial']);
        }

        // agregar cuentas
        foreach($rubro as $secciones){

            array_push($resultsBloque, $secciones);

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

                $sumaObjetoTotal = 0; // total dinero por fila

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->numero == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $sumaObjeto = 0;

                    $subSecciones3Materiales = Material::whereIn('id', $pilaIdMaterial)
                        ->where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    foreach ($subSecciones3Materiales as $subLista){

                        foreach ($dataArray as $dda){

                            if($dda['idmaterial'] == $subLista->id){

                                $subLista->codigo = $ll->numero;
                                $subLista->sumacantidad = $dda['sumacantidad'];
                                $subLista->totalfila = $dda['total'];
                                $subLista->unidadmedida = $dda['unidadmedida'];

                                $sumaObjeto += $dda['totalDecimal'];

                                break;
                            }
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;

                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3Materiales;
                    $index3++;
                }

                $sumaRubro += $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO TOTALES
            </p>
            </div>";


        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>";

        // recorrer rubros que tenga dinero

        $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNI. MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->numero</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                        $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                            </tr>";

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->numero</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unidadmedida</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->sumacantidad</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$$dataMM->totalfila</td>
                                </tr>";

                                }
                            }
                        }
                    }
                }
            }
        }

        $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>TOTALES</td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>$totalColumnaCantidad</td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>$$totalColumnaGlobal</td>
                </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }

    // GENERAR PDF POR UNIDADES
    public function generarPdfPorUnidades($anio, $unidades){
        $porciones = explode("-", $unidades);

        // filtrado por x departamento y x año
        $arrayPresupUnidad = PresupUnidad::where('id_anio', $anio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        // solo para obtener los nombres
        $dataUnidades = Departamento::whereIn('id', $porciones)->orderBy('nombre')->get();

        $fechaanio = Anio::where('id', $anio)->pluck('nombre')->first();

        // listado de materiales
        $materiales = Material::orderBy('descripcion')->get();

        $sumaGlobalUnidades = 0;

        $pilaArrayMateriales = array();
        $pilaArrayPresuUni = array();

        // PRIMERO OBTENER LOS ID DE MATERIALES QUE TIENE ESTA UNIDAD, UN ARRAY DE ID Y AHI SE BUSCARA
        // A CUAL RUBRO PERTENECE

        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupUnidad as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    array_push($pilaArrayPresuUni, $info->id);

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);
                }
            }

            if($sumacantidad > 0){
                array_push($pilaArrayMateriales, $mm->id);
            }
        }

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
                    $subSecciones3 = Material::whereIn('id', $pilaArrayMateriales)
                        ->where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = Unidad::where('id', $subLista->id_unimedida)->first();
                        $subLista->unimedida = $uni->simbolo;

                        // buscar
                        $dataArrayPresu = PresupUnidadDetalle::whereIn('id', $pilaArrayPresuUni)
                            ->where('id_material', $subLista->id)->get();

                        foreach ($dataArrayPresu as $infoData){

                            // PERIODO SIEMPRE SERA MÍNIMO 1
                            $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;
                            $sumaObjeto += $resultado;

                            $sumaGlobalUnidades += $resultado;

                            $subLista->cantidadpedi = $infoData->cantidad  * $infoData->periodo;

                            $subLista->total = '$' . number_format((float)$resultado, 2, '.', ',');
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro += $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalvalor += $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');

        ini_set("pcre.backtrack_limit", "5000000");
        $logoalcaldia = 'images/logo.png';

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE PRESUPUESTO POR UNIDAD
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
                <p>Unidades.</p>";

        foreach ($dataUnidades as $dd) {
            $tabla .= "<label>$dd->nombre, </label>";
        }

        // recorrer rubros que tenga dinero

        $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNIDAD MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                    $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->numero</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                                $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                            </tr>";

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->numero</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unimedida</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->cantidadpedi</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->total</td>
                                </tr>";

                                }
                            }
                        }
                    }
                }
            }
        }

        $tabla .= "</tbody></table>";

        $tabla .= "<table id='tablaFor' style='width: 100%; margin-top: 30px'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL UNIDAD: $$sumaGlobalUnidades</th>
                </tr>";
        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }


    public function generarPdfSoloUnaUnidad($anio, $unidad){

        if($presupUnidad = PresupUnidad::where('id_anio', $anio)
            ->where('id_departamento', $unidad)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->first()){

            // solo para obtener los nombres
            $dataUnidades = Departamento::where('id', $presupUnidad->id_departamento)->first();
            $fechaanio = Anio::where('id', $anio)->pluck('nombre')->first();

            // listado de materiales
            //$materiales = Material::orderBy('descripcion')->get();

            $sumaGlobalUnidades = 0;

            $pilaArrayMateriales = array();
            $pilaArrayPresuUni = array();


            $infoPresuUniDeta = PresupUnidadDetalle::where('id_presup_unidad', $presupUnidad->id)
                //->where('id_material', $mm->id)
                ->get();

            $sumacantidad = 0;

            foreach ($infoPresuUniDeta as $dd){
                array_push($pilaArrayPresuUni, $dd->id);
                // solo obtener fila de columna CANTIDAD
                $sumacantidad += ($dd->cantidad * $dd->periodo);

                array_push($pilaArrayMateriales, $dd->id_material);
            }

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
                        $subSecciones3 = Material::whereIn('id', $pilaArrayMateriales)
                            ->where('id_objespecifico', $ll->id)
                            ->orderBy('descripcion', 'ASC')
                            ->get();

                        $sumaObjeto = 0;

                        foreach ($subSecciones3 as $subLista){

                            $uni = Unidad::where('id', $subLista->id_unimedida)->first();
                            $subLista->unimedida = $uni->simbolo;

                            // buscar
                            $dataArrayPresu = PresupUnidadDetalle::whereIn('id', $pilaArrayPresuUni)
                                ->where('id_material', $subLista->id)->get();

                            foreach ($dataArrayPresu as $infoData){

                                // PERIODO SIEMPRE SERA MÍNIMO 1
                                $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;
                                $sumaObjeto += $resultado;

                                $sumaGlobalUnidades += $resultado;

                                $subLista->cantidadpedi = $infoData->cantidad  * $infoData->periodo;

                                $subLista->precunitario = '$' . number_format((float)$infoData->precio, 2, '.', ',');

                                $subLista->total = '$' . number_format((float)$resultado, 2, '.', ',');
                            }
                        }

                        $sumaObjetoTotal += $sumaObjeto;
                        $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                        $ll->sumaobjetoDeci = $sumaObjeto;

                        $resultsBloque3[$index3]->material = $subSecciones3;
                        $index3++;
                    }

                    $sumaRubro += $sumaObjetoTotal;
                    $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                    $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                    $resultsBloque2[$index2]->objeto = $subSecciones2;
                    $index2++;
                }

                $totalvalor += $sumaRubro;
                $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
                $secciones->sumarubroDecimal = $sumaRubro;

                $resultsBloque[$index]->cuenta = $subSecciones;
                $index++;
            }

            $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');

            ini_set("pcre.backtrack_limit", "5000000");
            $logoalcaldia = 'images/logo.png';

            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf->SetTitle('Consolidado Totales');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE PRESUPUESTO POR UNIDAD
            </p>
            </div>";

            $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
                <p>Unidad.</p>";


            $tabla .= "<label>$dataUnidades->nombre</label>";

            // recorrer rubros que tenga dinero

            $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNIDAD MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>PRECIO UNI.</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

            foreach ($rubro as $dataRR){
                if($dataRR->sumarubroDecimal > 0){

                    $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->numero</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                    foreach ($dataRR->cuenta as $dataCC){

                        if($dataCC->sumaobjetoDecimal > 0){

                            // CUENTAS

                            $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                            foreach ($dataCC->objeto as $dataObj){

                                if($dataObj->sumaobjetoDeci > 0){

                                    $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->numero</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                            </tr>";

                                    // MATERIALES

                                    foreach ($dataObj->material as $dataMM){

                                        $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->numero</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unimedida</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->precunitario</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->cantidadpedi</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->total</td>
                                </tr>";

                                    }
                                }
                            }
                        }
                    }
                }
            }

            $tabla .= "</tbody></table>";

            $tabla .= "<table id='tablaFor' style='width: 100%; margin-top: 30px'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL UNIDAD: $$sumaGlobalUnidades</th>
                </tr>";
            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/csspdftotales.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

            $mpdf->WriteHTML($tabla, 2);
            $mpdf->Output();
        }else{
            return "Presupuesto no aprobado";
        }
    }


    // Generar Excel consolidado
    public function generarExcelConsolidado($anio){
        $nombre = 'consolidado.xlsx';
        return Excel::download(new ExportarConsolidadoExcel($anio), $nombre);
    }

    public function generarExcelTotales($anio){
        $nombre = 'totales.xlsx';
        return Excel::download(new ExportarTotalesExcel($anio), $nombre);
    }

    public function generarExcelPorUnidades($anio, $unidades){
        $nombre = 'unidades.xlsx';
        return Excel::download(new ExportarPorUnidadesExcel($anio, $unidades), $nombre);
    }

    public function generarExcelSoloUnidad($anio, $unidad){
        $nombre = 'unidad.xlsx';
        return Excel::download(new ExportarUnaUnidadExcel($anio, $unidad), $nombre);
    }

}
