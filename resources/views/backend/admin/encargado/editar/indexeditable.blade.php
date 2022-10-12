@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper" id="divcontenedor" style="display: none">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <form class="form-vertical">
                            <div class="card-body">
                                <div class="form-group">
                                    <label style="margin-left: 15px;">Presupuesto Año: {{ $preanio }}</label>
                                </div>

                                <div class="form-group">
                                    <label style="margin-left: 15px;">Estado: {{ $estado->nombre }}</label>
                                </div>
                            </div>


                            <div style="margin-left: 20px">
                                <label style="color: darkgreen; font-size: 20px; font-family: arial">Total: ${{$totalvalor}}</label>
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

                                                                    <label class="accordion-header">{{ $item->numero }} - {{ $item->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $item->sumarubro }}</label>

                                                                    <!-- foreach para cuenta -->
                                                                    <div class="accordion-body">

                                                                        @foreach($item->cuenta as $cc)

                                                                            <div class="accordion-group" data-behavior="accordion" data-multiple="true">
                                                                                <p class="accordion-header">{{ $cc->numero }} - {{ $cc->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $cc->sumaobjetototal }}</p>

                                                                                <div class="accordion-body">
                                                                                    <div class="accordion-group" data-behavior="accordion" data-multiple="true">

                                                                                        <!-- foreach para objetos -->
                                                                                        @foreach($cc->objeto as $obj)

                                                                                            <p class="accordion-header">{{ $obj->numero }} | {{ $obj->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $obj->sumaobjeto }}</p>
                                                                                            <div class="accordion-body">

                                                                                                <table data-toggle="table">
                                                                                                    <thead>
                                                                                                    <tr>
                                                                                                        <th style="width: 30%; text-align: center">Descripción</th>
                                                                                                        <th style="width: 20%; text-align: center">U/M</th>
                                                                                                        <th style="width: 15%; text-align: center">Costo</th>
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
                                                                                                            <input type="hidden" name="idmaterial[]" value='{{ $mm->id }}'>
                                                                                                            <input value="{{ $mm->descripcion }}" disabled class="form-control"  type="text">
                                                                                                        </td>
                                                                                                        <td><input value="{{ $mm->unimedida }}" disabled class="form-control"  type="text"></td>
                                                                                                        <td><input value="{{ $mm->precio }}" disabled class="form-control" style="max-width: 150px" ></td>
                                                                                                        <td><input value="{{ $mm->cantidad }}" name="unidades[]" class="form-control" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->periodo }}" name="periodo[]" class="form-control" min="1" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->total }}" disabled name="total[]" class="form-control" type="text" style="max-width: 180px"></td>

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

                                                                    @endforeach
                                                                    <!-- fin foreach para cuenta -->
                                                                    </div>
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

                                                                @foreach($listado as $ll)

                                                                    <tr>
                                                                        <td>
                                                                            <input name="descripcion[]" value="{{ $ll->descripcion }}" maxlength="800" class="form-control" type="text"></td>
                                                                        <td><select name="unidadmedida[]" class="form-control seleccion" style="max-width: 180px">
                                                                                @foreach($unidad as $item)
                                                                                    @if($item->id == $ll->id_unidad)
                                                                                        <option value="{{$item->id}}" selected="selected">{{$item->simbolo}}</option>
                                                                                        @else
                                                                                        <option value="{{$item->id}}">{{$item->simbolo}}</option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select></td>
                                                                        <td><input name="costoextra[]" value="{{ $ll->costo }}" class="form-control" min="0.1" type="number" style="max-width: 85px"></td>
                                                                        <td><input name="cantidadextra[]" value="{{ $ll->cantidad }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 180px"></td>
                                                                        <td><input name="periodoextra[]" value="{{ $ll->periodo }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 180px"></td>

                                                                        <td>
                                                                            @if($estado->id == 1)
                                                                            <button type="button" class="btn btn-block btn-danger" id="btnBorrar" onclick="borrarFila(this)">Borrar</button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>

                                                                @endforeach

                                                            </tbody>
                                                        </table>

                                                        @if($estado->id == 1)
                                                            <br>
                                                            <button type="button" class="btn btn-block btn-success" id="btnAdd">Agregar Fila</button>
                                                            <br>
                                                        @endif

                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($estado->id == 1)
                                <div class="card-footer">
                                    <button type="button" onclick="verificar()" class="btn btn-success float-right">Guardar</button>
                                </div>
                            @endif
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

    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

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
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(unidades.value.length > 0) {
                // validar

                if(!unidades.value.match(reglaNumeroDecimal)) {
                    modalMensaje('Error', 'unidades debe ser número decimal');
                    return;
                }

                if(unidades.value <= 0){
                    modalMensaje('Error', 'unidades no debe ser negativo o cero');
                    return;
                }

                if(unidades.value > 1000000){
                    modalMensaje('Error', 'unidades máximo 1 millón');
                    return;
                }

                boolUnidades = true;
            }

            if(periodo.value.length > 0) {
                // validar

                if(!periodo.value.match(reglaNumeroEntero)) {
                    modalMensaje('Error', 'periodo debe ser número entero');
                    return;
                }

                if(periodo.value <= 0){
                    modalMensaje('Error', 'periodo no debe ser negativo o cero');
                    return;
                }

                if(periodo.value > 1000000){
                    modalMensaje('Error', 'periodo máximo 1 millón');
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

                total.value = '$' + Number(valTotal).toFixed(2);
            }else{
                total.value = '';
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
                    "<option value='{{ $data->id }}'>{{ $data->simbolo }}</option>"+
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


        function verificar(){

            Swal.fire({
                title: 'Editar Presupuesto?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    editar();
                }
            })
        }


        function editar(){

            var idmaterial = $("input[name='idmaterial[]']").map(function(){return $(this).val();}).get();
            var unidades = $("input[name='unidades[]']").map(function(){return $(this).val();}).get();
            var periodo = $("input[name='periodo[]']").map(function(){return $(this).val();}).get();

            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            // verificar que todos las unidades y periodos ingresados sean validos

            for(var a = 0; a < unidades.length; a++){

                var datoUnidades = unidades[a];

                if(datoUnidades.length > 0){

                    // revisar si es decimal

                    if(!datoUnidades.match(reglaNumeroDecimal)) {
                        modalMensaje('Presupuesto Base','unidades ingresada no es valido');
                        return;
                    }

                    if(datoUnidades <= 0){
                        modalMensaje('Presupuesto Base', 'unidades no debe ser negativos o cero');
                        return;
                    }

                    if(datoUnidades > 1000000){
                        modalMensaje('Presupuesto Base', 'unidades máximo 1 millón');
                        return;
                    }

                }
            }

            for(var b = 0; b < periodo.length; b++){

                var datoPeriodo = periodo[b];

                if(datoPeriodo.length > 0){

                    // revisar si es decimal

                    if(!datoPeriodo.match(reglaNumeroEntero)) {
                        modalMensaje('Presupuesto Base', 'periodo ingresada no es valido');
                        return;
                    }

                    if(datoPeriodo <= 0){
                        modalMensaje('Presupuesto Base', 'periodo no debe ser negativos o cero');
                        return;
                    }

                    if(datoPeriodo > 1000000){
                        modalMensaje('Presupuesto Base', 'periodo máximo 1 millón');
                        return;
                    }

                }
            }

            let formData = new FormData();

            // verificar ingreso de materiales extras


            var nRegistro = $('#matrizMateriales >tbody >tr').length;
            if (nRegistro > 0){

                var descripcion = $("input[name='descripcion[]']").map(function(){return $(this).val();}).get();
                var costoextra = $("input[name='costoextra[]']").map(function(){return $(this).val();}).get();
                var cantidadextra = $("input[name='cantidadextra[]']").map(function(){return $(this).val();}).get();
                var periodoextra = $("input[name='periodoextra[]']").map(function(){return $(this).val();}).get();


                for(var c = 0; c < descripcion.length; c++){

                    var datoDescripcion = descripcion[c];

                    if(datoDescripcion === ''){
                        modalMensaje('Nuevos Materiales', 'un material le falta su descripción');
                        return;
                    }

                    if(datoDescripcion.length > 800){
                        modalMensaje('Nuevos Materiales', 'máximo 800 caracteres para descripción');
                        return;
                    }

                }

                for(var d = 0; d < costoextra.length; d++){

                    var datoCostoExtra = costoextra[d];

                    if(datoCostoExtra === ''){
                        modalMensaje('Nuevos Materiales', 'costo es requerido');
                        return;
                    }

                    if(!datoCostoExtra.match(reglaNumeroDecimal)) {
                        modalMensaje('Nuevos Materiales', 'costo debe ser decimal');
                        return;
                    }

                    if(datoCostoExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'costo no debe ser negativo o cero');
                        return;
                    }

                    if(datoCostoExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'costo máximo es 1 millón');
                        return;
                    }

                }

                for(var t = 0; t < cantidadextra.length; t++){

                    var datoCantidadExtra = cantidadextra[t];

                    if(datoCantidadExtra === ''){
                        modalMensaje('Nuevos Materiales', 'cantidad es requerido');
                        return;
                    }

                    if(!datoCantidadExtra.match(reglaNumeroDecimal)) {
                        modalMensaje('Nuevos Materiales', 'cantidad debe ser decimal');
                        return;
                    }

                    if(datoCantidadExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'cantidad no debe ser negativo o cero');
                        return;
                    }

                    if(datoCantidadExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'cantidad máximo es 1 millón');
                        return;
                    }

                }

                for(var e = 0; e < periodoextra.length; e++){

                    var datoPeriodoExtra = periodoextra[e];

                    if(datoPeriodoExtra === ''){
                        modalMensaje('Nuevos Materiales', 'periodo es requerido');
                        return;
                    }

                    if(!datoPeriodoExtra.match(reglaNumeroDecimal)) {
                        modalMensaje('Nuevos Materiales', 'periodo debe ser número entero');
                        return;
                    }

                    if(datoPeriodoExtra <= 0){
                        modalMensaje('Nuevos Materiales', 'periodo no debe ser negativo o cero');
                        return;
                    }

                    if(datoPeriodoExtra > 1000000){
                        modalMensaje('Nuevos Materiales', 'periodo máximo es 1 millón');
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
                    formData.append('idmaterial[]', idmaterial[z]);
                    formData.append('unidades[]', unidades[z]);
                    formData.append('periodo[]', periodo[z]);
                }
            }

            var idanio = {{ $anio }};
            var idpresupuesto = {{ $idpresupuesto }};

            formData.append('anio', idanio);
            formData.append('idpresupuesto', idpresupuesto);

            axios.post(url+'/nuevo/presupuesto/editar', formData, {
            })
                .then((response) => {
                    if(response.data.success === 1){
                        preAprobado();
                    }
                    else if(response.data.success === 2){
                        msjActualizado();
                    }
                    else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al registrar');
                    closeLoading();
                });
        }

        function preAprobado(){
            Swal.fire({
                title: 'Información',
                text: "El presupuesto ya esta aprobado",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                closeOnClickOutside: false,
                allowOutsideClick: false,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }

        function msjActualizado(){

            Swal.fire({
                title: 'Presupuesto modificado',
                text: "",
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                closeOnClickOutside: false,
                allowOutsideClick: false,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }

        function modalMensaje(titulo, mensaje){
            Swal.fire({
                title: titulo,
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }


    </script>


@endsection
