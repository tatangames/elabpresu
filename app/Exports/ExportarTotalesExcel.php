<?php

namespace App\Exports;

use App\Models\Anio;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\PresupUnidad;
use App\Models\PresupUnidadDetalle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportarTotalesExcel implements FromCollection, WithHeadings
{
    public function __construct($anio)
    {
        $this->anio = $anio;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // obtener todos los departamentos, que han creado el presupuesto
        $presupuesto = PresupUnidad::where('id_anio', $this->anio)
            ->where('id_estado', 2) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

        $materiales = Material::orderBy('descripcion')->get();

        // recorrer cada material
        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            $codigo = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // recorrer cada departamento y buscar
            foreach ($presupuesto as $pp) {

                if ($info = PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {
                    $multip = $info->cantidad * $info->periodo;
                    $sumacantidad = $sumacantidad + $multip;
                }
            }

            $total = number_format((float)($sumacantidad * $mm->costo), 2, '.', ',');

            $dataArray[] = [
                'codigo' => $codigo->numero,
                'descripcion' => $mm->descripcion,
                'sumacantidad' => $sumacantidad,
                'costo' => $mm->costo,
                'total' => $total,
            ];
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        return collect($dataArray);
    }

    public function headings() :array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "CANTIDAD", "PRECIO UNITARIO", "TOTAL"];
    }















}
