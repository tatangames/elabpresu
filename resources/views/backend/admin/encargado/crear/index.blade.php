@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<style>



</style>

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

                                    <button type="button" onclick="crear()" class="btn btn-success col-form-label" style="margin-left: 10px">Crear</button>
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

                                                                                <p class="accordion-header">{{ $obj->numero }} - {{ $obj->nombre }}</p>
                                                                                <div class="accordion-body">

                                                                                    <table class="table" data-toggle="table">
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
                                                                                        <tbody id="myTbody">

                                                                                            <!-- foreach para material -->

                                                                                            @foreach($obj->material as $mm)

                                                                                                <tr id="{{ $mm->id }}">
                                                                                                    <td><input value="{{ $mm->descripcion }}" disabled class="form-control" type="text"></td>
                                                                                                    <td><input value="{{ $mm->unimedida }}" disabled class="form-control" type="text"></td>
                                                                                                    <td><input value="{{ $mm->costo }}" disabled class="form-control" style="max-width: 150px" ></td>
                                                                                                    <td><input name="unidades[]" class="form-control" type="number" maxlength="6"  style="max-width: 180px" ></td>
                                                                                                    <td><input name="periodo[]" class="form-control" min="1" type="number" maxlength="6"  style="max-width: 180px" ></td>
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

                                                        <table class="table" id="matriz" style="border: 80px" data-toggle="table">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 8%; text-align: center"># Fila</th>
                                                                <th style="width: 30%; text-align: center">Descripción</th>
                                                                <th style="width: 20%; text-align: left">Unidad de Medida</th>
                                                                <th style="width: 15%; margin-left: 100px">Costo</th>
                                                                <th style="width: 10%; text-align: center">Periodo</th>

                                                                <th style="width: 10%; text-align: center">Opciones</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="myTbody">

                                                            <tr id="0">
                                                                <td><p name="fila[]" disabled id="fila0" class="form-control" style='max-width: 65px'>1</td>
                                                                <td><input name="descripcion[]" maxlength="800" class="form-control" type="text"></td>
                                                                <td><select name="unidadmedida[]" class="form-control seleccion" style='max-width: 180px'>
                                                                        @foreach($unidad as $item)
                                                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                                        @endforeach
                                                                    </select></td>
                                                                <td><input name="costo[]" class="form-control" type="number" min="0.01" maxlength="9" style="max-width: 150px" ></td>
                                                                <td><input name="periodo[]" class="form-control" type="number" maxlength="6"  style="max-width: 180px" ></td>
                                                                <td><button type="button" class="btn btn-block btn-danger" id="btnBorrar" onclick="borrarFila(this)">Borrar</button></td>
                                                            </tr>

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
                                <button type="submit" class="btn btn-info">Sign in</button>
                                <button type="submit" class="btn btn-default float-right">Cancel</button>
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


        // filas de la tabla
        $(document).ready(function () {
            $("#btnAdd").on("click", function () {

                var nFilas = $('#matriz >tbody >tr').length;
                nFilas += 1;

                //agrega las filas dinamicamente

                var markup = "<tr id='"+(nFilas)+"'>"+

                    "<td>"+
                    "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                    "</td>"+

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
                    "<input name='costo[]' class='form-control' min='0.1' style='max-width: 150px' type='number' maxlength='9' value=''/>"+
                    "</td>"+

                    "<td>"+
                    "<input name='periodo[]' class='form-control' style='max-width: 180px' type='number' maxlength='6' value=''/>"+
                    "</td>"+

                    "<td>"+
                    "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                    "</td>"+

                    "</tr>";

                $("tbody").append(markup);


            });
        });

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFila();
        }

        // cambiar # de fila cada vez que se borre una fila
        function setearFila(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }





        function crear(){
            var anio = document.getElementById('select-anio').value;

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
            formData.append('nombre', nombre);

            axios.post('/admin/departamento/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
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
