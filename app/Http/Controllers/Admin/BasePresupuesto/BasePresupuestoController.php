<?php

namespace App\Http\Controllers\Admin\BasePresupuesto;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\Material;
use App\Models\ObjEspecifico;
use App\Models\Rubro;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BasePresupuestoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $objeto = ObjEspecifico::orderBy('nombre')->get();
        $unidad = Unidad::orderBy('nombre')->get();

        return view('backend.admin.basepresupuesto.index', compact('objeto', 'unidad'));
    }

    public function tablaPresupuesto(){
        $lista = Material::orderBy('descripcion')->get();

        foreach ($lista as $l){
            $unidad = Unidad::where('id', $l->id_unimedida)->pluck('simbolo')->first();
            $objeto = ObjEspecifico::where('id', $l->id_objespecifico)->pluck('nombre')->first();

            $l->unidad = $unidad;
            $l->objeto = $objeto;
        }

        return view('backend.admin.basepresupuesto.tabla.tablapresupuesto', compact('lista'));
    }

    public function nuevaBasePresupuesto(Request $request){

        $regla = array(
            'descripcion' => 'required',
            'costo' => 'required',
            'objeto' => 'required',
            'unidad' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Material();
        $dato->descripcion = $request->descripcion;
        $dato->id_unimedida = $request->unidad;
        $dato->id_objespecifico = $request->objeto;
        $dato->costo = $request->costo;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function infoBasePresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Material::where('id', $request->id)->first()){

            $objeto = ObjEspecifico::orderBy('nombre')->get();
            $unidad = Unidad::orderBy('nombre')->get();

            return ['success' => 1,
                'material' => $lista,
                'idobj' => $lista->id_objespecifico,
                'iduni' => $lista->id_unimedida,
                'objeto' => $objeto,
                'unidad' => $unidad];
        }else{
            return ['success' => 2];
        }
    }


    public function editarBasePresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
            'descripcion' => 'required',
            'costo' => 'required',
            'objeto' => 'required',
            'unidad' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Material::where('id', $request->id)->first()){

            Material::where('id', $request->id)->update([
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'id_unimedida' => $request->unidad,
                'id_objespecifico' => $request->objeto
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




}
