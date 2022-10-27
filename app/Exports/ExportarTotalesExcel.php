<?php

namespace App\Exports;

use App\Models\Cuenta;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\PresupUnidad;
use App\Models\PresupUnidadDetalle;
use App\Models\Rubro;
use App\Models\Unidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportarTotalesExcel implements FromCollection, WithHeadings, WithStyles
{
    public function __construct($anio){
        $this->anio = $anio;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){

        // obtener todos los departamentos, que han creado el presupuesto
        $arrayPresupuestoUni = PresupUnidad::where('id_anio', $this->anio)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

        // listado
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
            if ($sumacantidad > 0) {

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

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $rubro = Rubro::orderBy('numero')->get();

        $pilaIdMaterial = array();
        foreach ($dataArray as $dd) {
            array_push($pilaIdMaterial, $dd['idmaterial']);
        }

        // agregar cuentas
        foreach ($rubro as $secciones) {

            array_push($resultsBloque, $secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('numero', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista) {

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('numero', 'ASC')
                    ->get();

                $sumaObjetoTotal = 0; // total dinero por fila

                // agregar materiales
                foreach ($subSecciones2 as $ll) {

                    array_push($resultsBloque3, $ll);

                    if ($ll->numero == 61109) {
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $sumaObjeto = 0;

                    $subSecciones3Materiales = Material::whereIn('id', $pilaIdMaterial)
                        ->where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    foreach ($subSecciones3Materiales as $subLista) {

                        foreach ($dataArray as $dda) {

                            if ($dda['idmaterial'] == $subLista->id) {

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

        $dataArrayFinal = array();

        foreach ($rubro as $dataRR) {
            if ($dataRR->sumarubroDecimal > 0) {

                $dataArrayFinal[] = [
                    'codigo' => $dataRR->numero,
                    'descripcion' => $dataRR->nombre,
                    'unidadmedida' => '',
                    'sumacantidad' => '',
                    'total' => $dataRR->sumarubro
                ];

                foreach ($dataRR->cuenta as $dataCC) {

                    if ($dataCC->sumaobjetoDecimal > 0) {

                        // CUENTAS

                        $dataArrayFinal[] = [
                            'codigo' => $dataCC->numero,
                            'descripcion' => $dataCC->nombre,
                            'unidadmedida' => '',
                            'sumacantidad' => '',
                            'total' => $dataCC->sumaobjetototal
                        ];

                        foreach ($dataCC->objeto as $dataObj) {

                            if ($dataObj->sumaobjetoDeci > 0) {

                                $dataArrayFinal[] = [
                                    'codigo' => $dataObj->numero,
                                    'descripcion' => $dataObj->nombre,
                                    'unidadmedida' => '',
                                    'sumacantidad' => '',
                                    'total' => $dataObj->sumaobjeto
                                ];

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM) {

                                    $dataArrayFinal[] = [
                                        'codigo' => $dataObj->numero,
                                        'descripcion' => $dataMM->descripcion,
                                        'unidadmedida' => $dataMM->sumacantidad,
                                        'sumacantidad' => $dataMM->unidadmedida,
                                        'total' => $dataMM->totalfila
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        $dataArrayFinal[] = [
            'codigo' => '',
            'descripcion' => '',
            'unidadmedida' => '',
            'sumacantidad' => '',
            'total' =>  '',
        ];

        $dataArrayFinal[] = [
            'codigo' => '',
            'descripcion' => 'TOTALES',
            'unidadmedida' => '',
            'sumacantidad' => number_format((float)($totalColumnaCantidad), 2, '.', ','),
            'total' =>  number_format((float)($totalColumnaGlobal), 2, '.', ',')
        ];

        return collect($dataArrayFinal);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "UNIDAD MEDIDA", "CANTIDAD", "TOTAL"];
    }

}

