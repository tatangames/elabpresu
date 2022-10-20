<?php

namespace App\Http\Controllers\Admin\Generar;

use App\Exports\ExportarConsolidadoExcel;
use App\Exports\ExportarPorUnidadesExcel;
use App\Exports\ExportarTotalesExcel;
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



    public function generarPdfTotales($idanio){

        // obtener todos los departamentos, que han creado el presupuesto
        $arrayPresupuestoUni = PresupUnidad::where('id_anio', $idanio)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $pilaArrayPresuUni = array();

        foreach ($arrayPresupuestoUni as $p){
            array_push($pilaArrayPresuUni, $p->id);
        }

        $dataArray = array();

        // listado
        $materiales = Material::orderBy('descripcion')->get();

        $fechaanio = Anio::where('id', $idanio)->pluck('nombre')->first();

        ini_set("pcre.backtrack_limit", "5000000");
        //ini_set('max_execution_time', 180); //3 minutes


        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $sumaCantidadGlobal = 0;

        $sumaTotalGlobal = 0;

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

                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila = $multiFila + $resultado;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);

                    // para colocar CANTIDAD TOTAL al final de la columna
                    $sumaCantidadGlobal = $sumaCantidadGlobal + $sumacantidad;
                }
            }

            // si es mayor a cero, es porque si hay cantidad * periodo
            if($sumacantidad > 0){

                $multiFila = number_format((float)($multiFila), 2, '.', ',');

                // para fila de columna CANTIDAD
                $sumacantidad = number_format((float)($sumacantidad), 2, '.', ',');

                $dataArray[] = [
                    'codigo' => $infoObj->numero,
                    'descripcion' => $mm->descripcion,
                    'sumacantidad' => $sumacantidad,
                    'total' => $multiFila,
                ];
            }
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        $sumaCantidadGlobal = number_format((float)($sumaCantidadGlobal), 2, '.', ',');
        $sumaTotalGlobal = number_format((float)($sumaTotalGlobal), 2, '.', ',');


        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO TOTALES
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
        <table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%'>COD. ESPEC.</th>
            <th style='text-align: center; font-size:13px; width: 20%'>NOMBRE</th>
            <th style='text-align: center; font-size:13px; width: 9%'>CANTIDAD</th>
            <th style='text-align: center; font-size:13px; width: 9%'>TOTAL</th>
        </tr>";

        foreach ($dataArray as $dd) {

            $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'>" . $dd['codigo'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['descripcion'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['sumacantidad'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['total'] . "</td>
            </tr>";
        }

        $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'>$sumaCantidadGlobal</td>
                <td style='font-size:11px; text-align: center'>$ $sumaTotalGlobal</td>
            </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }

    public function generarPdfPorUnidades($anio, $unidades){

        $porciones = explode("-", $unidades);

        // filtrado por x departamento y x año
        $arrayPresupUnidad = PresupUnidad::where('id_anio', $anio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataUnidades = Departamento::whereIn('id', $porciones)->orderBy('nombre')->get();

        $pilaArrayPresuUni = array();

        foreach ($arrayPresupUnidad as $p){
            array_push($pilaArrayPresuUni, $p->id);
        }

        $dataArray = array();

        // listado
        $materiales = Material::orderBy('descripcion')->get();

        $fechaanio = Anio::where('id', $anio)->pluck('nombre')->first();

        ini_set("pcre.backtrack_limit", "5000000");
        //ini_set('max_execution_time', 180); //3 minutes

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $sumaCantidadGlobal = 0;
        $sumaTotalGlobal = 0;

        // recorrer cada material
        foreach ($materiales as $mm) {

            // para suma de cantidad para cada fila. columna CANTIDAD
            $sumacantidad = 0;

            $infoObj = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // dinero fila columna TOTAL
            $multiFila = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupUnidad as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila = $multiFila + $resultado;
                    $sumaTotalGlobal += $multiFila;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);

                    // para colocar CANTIDAD TOTAL al final de la columna
                    $sumaCantidadGlobal = $sumaCantidadGlobal + $sumacantidad;
                }
            }

            if($sumacantidad > 0){

                $multiFila = number_format((float)($multiFila), 2, '.', ',');

                // para fila de columna CANTIDAD
                $sumacantidad = number_format((float)($sumacantidad), 2, '.', ',');

                //Para obtener la unidad de medida por id_unimedida
                $infoUniMedida = Unidad::where('id', $mm->id_unimedida)->first();

                $dataArray[] = [
                    'codigo' => $infoObj->numero,
                    'descripcion' => $mm->descripcion,
                    'unidad' => $infoUniMedida->simbolo,
                    'sumacantidad' => $sumacantidad,
                    'total' => $multiFila,
                ];
            }
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        $sumaCantidadGlobal = number_format((float)($sumaCantidadGlobal), 2, '.', ',');
        $sumaTotalGlobal = number_format((float)($sumaTotalGlobal), 2, '.', ',');

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

        $tabla .= "<table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%'>COD. ESPEC.</th>
            <th style='text-align: center; font-size:13px; width: 20%'>NOMBRE</th>
            <th style='text-align: center; font-size:13px; width: 9%'>U. MEDIDA</th>
            <th style='text-align: center; font-size:13px; width: 8%'>CANTIDAD</th>
            <th style='text-align: center; font-size:13px; width: 9%'>TOTAL</th>
        </tr>";

        foreach ($dataArray as $dd) {

            if($dd['sumacantidad'] > 0){
                $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'>" . $dd['codigo'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['descripcion'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['unidad'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['sumacantidad'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['total'] . "</td>
            </tr>";
            }
        }

        $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'>$sumaCantidadGlobal</td>
                <td style='font-size:11px; text-align: center'>$ $sumaTotalGlobal</td>
            </tr>";


        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
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

    // ?
    public function generarExcelPorUnidades($anio, $unidades){
        $nombre = 'unidades.xlsx';
        return Excel::download(new ExportarPorUnidadesExcel($anio, $unidades), $nombre);
    }



}
