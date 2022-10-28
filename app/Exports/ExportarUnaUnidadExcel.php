<?php

namespace App\Exports;

use App\Models\Cuenta;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\PresupUnidad;
use App\Models\Rubro;
use App\Models\Unidad;
use App\Models\PresupUnidadDetalle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportarUnaUnidadExcel implements FromCollection, WithHeadings, WithStyles
{

    public function __construct($anio, $unidad){
        $this->anio = $anio;
        $this->unidad = $unidad;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        // filtrado por x departamento y x año
        $presupUnidad = PresupUnidad::where('id_anio', $this->anio)
            ->where('id_departamento', $this->unidad)
            //->where('id_estado', 2) // solo aprobados
            ->first();

        // listado de materiales
        //$materiales = Material::orderBy('descripcion')->get();

        $sumaGlobalUnidades = 0;

        $pilaArrayMateriales = array();
        $pilaArrayPresuUni = array();

        $infoPresuUniDeta = PresupUnidadDetalle::where('id_presup_unidad', $presupUnidad->id)
            //->where('id_material', $mm->id)
            ->get();

        $dataArray = array();

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

                            // PERIODO SERA COMO MÍNIMO 1
                            $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;

                            $sumaObjeto += $resultado;

                            $sumaGlobalUnidades += $resultado;

                            $subLista->cantidadpedi = $infoData->cantidad  * $infoData->periodo;

                            $subLista->precunitario = '$' . number_format((float)$infoData->precio, 2, '.', ',');

                            $subLista->total = '$' . number_format((float)$resultado, 2, '.', ',');
                        }
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');


        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                $dataArray[] = [
                    'codigo' => $dataRR->numero,
                    'descripcion' => $dataRR->nombre,
                    'medida' => "",
                    'precunitario' => "",
                    'cantidad' => "",
                    'total' => "$".$dataRR->sumarubro,
                ];

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                        $dataArray[] = [
                            'codigo' => $dataCC->numero,
                            'descripcion' => $dataCC->nombre,
                            'medida' => "",
                            'precunitario' => "",
                            'cantidad' => "",
                            'total' => "$". $dataCC->sumaobjetototal,
                        ];

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $dataArray[] = [
                                    'codigo' => $dataObj->numero,
                                    'descripcion' => $dataObj->nombre,
                                    'medida' => "",
                                    'precunitario' => "",
                                    'cantidad' => "",
                                    'total' => "$" . number_format((float)$dataObj->sumaobjeto, 2, '.', ',')
                                ];

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    $dataArray[] = [
                                        'codigo' => $dataObj->numero,
                                        'descripcion' => $dataMM->descripcion,
                                        'medida' => $dataMM->unimedida,
                                        'precunitario' => $dataMM->precunitario,
                                        'cantidad' => $dataMM->cantidadpedi,
                                        'total' => $dataMM->total,
                                    ];

                                }
                            }
                        }
                    }
                }
            }
        }

        $dataArray[] = [
            'codigo' => "",
            'descripcion' => "",
            'medida' => "",
            'precunitario' => "",
            'cantidad' => "",
            'total' => "",
        ];

        $dataArray[] = [
            'codigo' => "",
            'descripcion' => "TOTAL",
            'medida' => "",
            'precunitario' => "",
            'cantidad' => "",
            'total' => "$" . $sumaGlobalUnidades,
        ];

        return collect($dataArray);
    }

    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "U. MEDIDA", "PRECIO UNITARIO", "CANTIDAD", "TOTAL"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
        ];
    }
}
