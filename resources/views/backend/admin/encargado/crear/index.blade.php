@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Formulario</h1>
                </div>

            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label style="margin: 8px">Año</label>
                                    <div style="margin-left: 6px" class="col-sm-3">
                                        <select class="form-control" id="select-anio">
                                            @foreach($anio as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>


                            <div class="col-12">
                                <!-- Custom Tabs -->
                                <div class="card">
                                    <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3"></h3>
                                        <ul class="nav nav-pills ml-auto p-2">
                                            <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Base Presupuesto</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Nuevos Materiales</a></li>

                                        </ul>
                                    </div><!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_1">

                                                <!-- inicio -->
                                                <div>

                                                    <form>
                                                        <div class="card-body">

                                                            <!-- foreach para rubro -->

                                                            @foreach($rubro as $item)

                                                            <div class="accordion-group" data-behavior="accordion">


                                                                <label class="accordion-header">{{ $item->numero }} - {{ $item->nombre }}</label>

                                                                <!-- foreach para cuenta -->

                                                                @foreach($item->cuenta as $cc)

                                                                    <div class="accordion-body">

                                                                    <div class="accordion-group" data-behavior="accordion" data-multiple="true">
                                                                        <p class="accordion-header">{{ $cc->numero }} - {{ $cc->nombre }}</p>

                                                                        <div class="accordion-body">
                                                                            <div class="accordion-group" data-behavior="accordion" data-multiple="true">

                                                                                <!-- foreach para objetos -->
                                                                                @foreach($cc->objeto as $obj)

                                                                                <p class="accordion-header">{{ $obj->contador }} - {{ $obj->nombre }}</p>
                                                                                <div class="accordion-body">

                                                                                    <table data-toggle="table">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th style="width: 30%; text-align: center">Descripción</th>
                                                                                            <th style="width: 20%; text-align: left">U/M</th>
                                                                                            <th style="width: 15%; margin-left: 100px">Costo</th>
                                                                                            <th style="width: 10%; text-align: center">Unidades</th>
                                                                                            <th style="width: 10%; text-align: center">Periodo</th>
                                                                                            <th style="width: 10%; text-align: center">Total</th>

                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>

                                                                                            <!-- foreach para material -->

                                                                                            @foreach($obj->material as $mm)

                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <input type="hidden" name="idMaterial[]" value='{{ $mm->id }}'>
                                                                                                        <input value="{{ $mm->descripcion }}" disabled class="form-control"  type="text">
                                                                                                    </td>
                                                                                                    <td><input value="{{ $mm->unimedida }}" disabled class="form-control"  type="text"></td>
                                                                                                    <td><input value="{{ $mm->costo }}" disabled class="form-control" style="max-width: 150px" ></td>
                                                                                                    <td><input name="unidades[]" class="form-control" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                    <td><input name="periodo[]" class="form-control" min="1" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                    <td><input name="total[]" class="form-control" type="text" style="max-width: 180px"></td>
                                                                                                </tr>

                                                                                            <!-- fin foreach material -->
                                                                                            @endforeach

                                                                                        </tbody>

                                                                                    </table>

                                                                                </div>

                                                                                @endforeach
                                                                                <!-- finaliza foreach para objetos-->

                                                                            </div>
                                                                        </div>


                                                                    </div>


                                                                </div>

                                                                @endforeach
                                                                <!-- fin foreach para cuenta -->

                                                            </div>

                                                            @endforeach
                                                            <!-- fin foreach para rubro -->

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>


                                            <!-- LISTA DE NUEVOS MATERIALES - TABS 2 -->
                                            <div class="tab-pane" id="tab_2">

                                                <form>
                                                    <div class="card-body">

                                                        <table class="table" id="matrizMateriales" style="border: 80px" data-toggle="table">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 30%; text-align: center">Descripción</th>
                                                                <th style="width: 20%; text-align: left">Unidad de Medida</th>
                                                                <th style="width: 15%; text-align: center">Costo</th>
                                                                <th style="width: 15%; text-align: center">Cantidad</th>
                                                                <th style="width: 10%; text-align: center">Periodo</th>

                                                                <th style="width: 10%; text-align: center">Opciones</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="myTbodyMateriales">



                                                            </tbody>

                                                        </table>

                                                        <br>
                                                        <button type="button" class="btn btn-block btn-success" id="btnAdd">Agregar Fila</button>
                                                        <br>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="button" onclick="verificar()" class="btn btn-success float-right">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/jquery.simpleaccordion.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('[data-behavior=accordion]').simpleAccordion({cbOpen:accOpen, cbClose:accClose});
        });


        function accClose(e, $this) {
            $this.find('span').fadeIn(200);
        }

        function accOpen(e, $this) {
            $this.find('span').fadeOut(200)
        }

    </script>

    <script>

        function multiplicar(e){

            var table = e.parentNode.parentNode; // fila de la tabla
            var costo = table.cells[2].children[0]; //
            var unidades = table.cells[3].children[0]; //
            var periodo = table.cells[4].children[0];
            var total = table.cells[5].children[0];

            var boolUnidades = false;
            var boolPeriodo = false;

            // validar que unidades y periodo existan para calcular total
            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(unidades.value.length > 0) {
                // validar

                if(!unidades.value.match(reglaNumeroEntero)) {
                    toastr.error('Unidades debe ser número entero');
                    return;
                }

                if(unidades.value <= 0){
                    toastr.error('unidades no debe ser negativo');
                    return;
                }

                if(unidades.value > 1000000){
                    toastr.error('unidades maximo 1 millon')
                    return;
                }

                boolUnidades = true;
            }


            if(periodo.value.length > 0) {
                // validar

                if(!periodo.value.match(reglaNumeroEntero)) {
                    toastr.error('periodo debe ser número entero');
                    return;
                }

                if(periodo.value <= 0){
                    toastr.error('periodo no debe ser negativo');
                    return;
                }

                if(periodo.value > 1000000){
                    toastr.error('periodo maximo 1 millon');
                    return;
                }

                boolPeriodo = true;
            }

            if(boolUnidades && boolPeriodo){

                // costo x unidades

                var val1 = costo.value;
                var val2 = unidades.value;
                var val3 = periodo.value;
                var valTotal = (val1 * val2) * val3;

                total.value = Number(valTotal).toFixed(2);
            }
        }


        // filas de la tabla
        $(document).ready(function () {
            $("#btnAdd").on("click", function () {

                //agrega las filas dinamicamente

                var markup = "<tr>"+

                    "<td>"+
                    "<input name='descripcion[]' maxlength='800' class='form-control' type='text'>"+
                    "</td>"+

                    "<td>"+
                    "<select class='form-control seleccion' style='max-width: 180px' name='unidadmedida[]'"+
                    "<option value='0'>Seleccionar Unidad</option>"+
                    "@foreach($unidad as $data)"+
                    "<option value='{{ $data->id }}'>{{ $data->nombre }}</option>"+
                    "@endforeach>"+
                    "</select>"+
                    "</td>"+

                    "<td>"+
                    "<input name='costoextra[]' class='form-control' min='0.1' style='max-width: 150px' type='number' value=''/>"+
                    "</td>"+

                    "<td>"+
                    "<input name='cantidadextra[]' class='form-control' onkeypress='if ( isNaN( String.fromCharCode(event.keyCode) )) return false;' min='1' style='max-width: 180px' type='number' value=''/>"+
                    "</td>"+

                    "<td>"+
                    "<input name='periodoextra[]' class='form-control' onkeypress='if ( isNaN( String.fromCharCode(event.keyCode) )) return false;' style='max-width: 180px' type='number' value=''/>"+
                    "</td>"+

                    "<td>"+
                    "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                    "</td>"+

                    "</tr>";

               // $("tbody").append(markup);
                $("#matrizMateriales tbody").append(markup);

            });
        });

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
        }

        // verificar datos ingresados
        function verificar(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('año de presupuesto es requerido');
                return;
            }




            var idMaterial = $("input[name='idMaterial[]']").map(function(){return $(this).val();}).get();
            var unidades = $("input[name='unidades[]']").map(function(){return $(this).val();}).get();
            var periodo = $("input[name='periodo[]']").map(function(){return $(this).val();}).get();

            var reglaNumeroEntero = /^[0-9]\d*$/;

            // verificar que todos las unidades y periodos ingresados sean validos

            for(var a = 0; a < unidades.length; a++){

                var datoUnidades = unidades[a];

                if(datoUnidades.length > 0){

                    // revisar si es decimal

                    if(!datoUnidades.match(reglaNumeroEntero)) {
                        toastr.error('unidades ingresada no es valido');
                        return;
                    }

                    if(datoUnidades <= 0){
                        toastr.error('unidades no debe ser negativos o cero');
                        return;
                    }

                    if(datoUnidades > 1000000){
                        toastr.error('unidades máximo 1 millón');
                        return;
                    }
                }
            }

            for(var b = 0; b < periodo.length; b++){

                var datoPeriodo = periodo[b];

                if(datoPeriodo.length > 0){

                    // revisar si es decimal

                    if(!datoPeriodo.match(reglaNumeroEntero)) {
                        toastr.error('periodo ingresada no es valido');
                        return;
                    }

                    if(datoPeriodo <= 0){
                        toastr.error('periodo no debe ser negativos o cero');
                        return;
                    }

                    if(datoPeriodo > 1000000){
                        toastr.error('periodo máximo 1 millón');
                        return;
                    }
                }
            }

            let formData = new FormData();

            // verificar ingreso de materiales extras



            var nRegistro = $('#matrizMateriales >tbody >tr').length;
            if (nRegistro > 0){

                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

                var descripcion = $("input[name='descripcion[]']").map(function(){return $(this).val();}).get();
                var costoextra = $("input[name='costoextra[]']").map(function(){return $(this).val();}).get();
                var cantidadextra = $("input[name='cantidadextra[]']").map(function(){return $(this).val();}).get();
                var periodoextra = $("input[name='periodoextra[]']").map(function(){return $(this).val();}).get();


                for(var c = 0; c < descripcion.length; c++){

                    var datoDescripcion = descripcion[c];

                    if(datoDescripcion === ''){
                        toastr.error('un material extra falta su descripcion');
                        return;
                    }

                    if(datoDescripcion.length > 800){
                        toastr.error('maximo 800 caracteres para descripcion');
                        return;
                    }
                }

                for(var d = 0; d < costoextra.length; d++){

                    var datoCostoExtra = costoextra[d];

                    if(datoCostoExtra === ''){
                        toastr.error('Costo en materiales extra es requerido');
                        return;
                    }

                    if(!datoCostoExtra.match(reglaNumeroDecimal)) {
                        toastr.error('costo en materiales extra debe ser decimal')
                        return;
                    }

                    if(datoCostoExtra <= 0){
                        toastr.error('costo en materiales extra no debe ser negativo o cero')
                        return;
                    }

                    if(datoCostoExtra > 1000000){
                        toastr.error('costo maximo es 1 millon')
                        return;
                    }
                }

                for(var t = 0; t < cantidadextra.length; t++){

                    var datoCantidadExtra = cantidadextra[t];

                    if(datoCantidadExtra === ''){
                        toastr.error('cantidad en materiales extra es requerido');
                        return;
                    }

                    if(!datoCantidadExtra.match(reglaNumeroEntero)) {
                        toastr.error('cantidad en materiales extra debe ser decimal')
                        return;
                    }

                    if(datoCantidadExtra <= 0){
                        toastr.error('cantidad en materiales extra no debe ser negativo o cero')
                        return;
                    }

                    if(datoCantidadExtra > 1000000){
                        toastr.error('cantidad maximo es 1 millon')
                        return;
                    }
                }

                for(var e = 0; e < periodoextra.length; e++){

                    var datoPeriodoExtra = periodoextra[e];

                    if(datoPeriodoExtra === ''){
                        toastr.error('Costo en materiales extra es requerido');
                        return;
                    }

                    if(!datoPeriodoExtra.match(reglaNumeroDecimal)) {
                        toastr.error('costo en materiales extra debe ser decimal')
                        return;
                    }

                    if(datoPeriodoExtra <= 0){
                        toastr.error('costo en materiales extra no debe ser negativo o cero')
                        return;
                    }

                    if(datoPeriodoExtra > 1000000){
                        toastr.error('costo maximo es 1 millon')
                        return;
                    }
                }


                for(var p = 0; p < descripcion.length; p++){
                    formData.append('descripcion[]', descripcion[p]);
                    formData.append('costoextra[]', costoextra[p]);
                    formData.append('cantidadextra[]', cantidadextra[p]);
                    formData.append('periodoextra[]', periodoextra[p]);
                }

                var row = $('table').find('tr');
                $(row).each(function (index, element) {
                    var unidad = $(this).find('.seleccion').val();

                    if(unidad !== undefined && unidad != null){
                        formData.append('unidadmedida[]', unidad);
                    }
                });
            }
            // fin validacion



            // llenar array para enviar
            for(var z = 0; z < unidades.length; z++){

                if(unidades[z].length > 0 && periodo[z].length > 0){
                    formData.append('idmaterial[]', idMaterial[z]);
                    formData.append('unidades[]', unidades[z]);
                    formData.append('periodo[]', periodo[z]);
                }
            }
            formData.append('anio', anio);

            axios.post('/admin/nuevo/presupuesto/crear', formData, {
            })
                .then((response) => {
                   console.log(response);

                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });


        }



        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/departamento/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.departamento.id);
                        $('#nombre-editar').val(response.data.departamento.nombre);
                    }else{
                        toastr.error('Información no encontrada');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editar(){
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;

            if(nombre === ''){
                toastr.error('nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);

            axios.post('/admin/departamento/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }


    </script>


@endsection
