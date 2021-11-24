@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper">


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

                                <div class="form-group col-md-3">
                                    <label style="color:#191818">Estado</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-estado" onchange="actualizarEstado()">
                                            @foreach($estado as $item)

                                                @if($idestado == $item->id)
                                                    <option value="{{$item->id}}" selected="selected">{{$item->nombre}}</option>
                                                @else
                                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                @endif

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
                                                                                                                <input type="hidden" name="idmaterial[]" value='{{ $mm->id }}'>
                                                                                                                <input value="{{ $mm->descripcion }}" disabled class="form-control"  type="text">
                                                                                                            </td>
                                                                                                            <td><input value="{{ $mm->unimedida }}" disabled class="form-control"  type="text"></td>
                                                                                                            <td><input value="{{ $mm->costo }}" disabled class="form-control" style="max-width: 150px" ></td>
                                                                                                            <td><input value="{{ $mm->cantidad }}" name="unidades[]" class="form-control" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                            <td><input value="{{ $mm->periodo }}" name="periodo[]" class="form-control" min="1" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                            <td><input value="{{ $mm->total }}" name="total[]" class="form-control" type="text" style="max-width: 180px"></td>
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
                                                                <th style="width: 30%; text-align: center">Cod</th>
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
                                                                    <td><select class="form-control seleccion" style="max-width: 180px">
                                                                            @foreach($objeto as $item)
                                                                                <option value="{{$item->id}}">{{$item->numero}}</option>
                                                                            @endforeach
                                                                        </select></td>
                                                                    <td>
                                                                        <input name="idfila[]" value="{{ $ll->id }}" type="hidden">
                                                                        <input name="descripcion[]" value="{{ $ll->descripcion }}" maxlength="800" class="form-control" type="text">
                                                                    </td>
                                                                    <td><select name="unidadmedida[]" class="form-control seleccion" style="max-width: 180px">
                                                                            @foreach($unidad as $item)
                                                                                @if($item->id == $ll->id_unidad)
                                                                                    <option value="{{$item->id}}" selected="selected">{{$item->nombre}}</option>
                                                                                @else
                                                                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select></td>
                                                                    <td><input name="costoextra[]" value="{{ $ll->costo }}" class="form-control" min="0.1" type="number" style="max-width: 85px"></td>
                                                                    <td><input name="cantidadextra[]" value="{{ $ll->cantidad }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 180px"></td>
                                                                    <td><input name="periodoextra[]" value="{{ $ll->periodo }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 180px"></td>

                                                                    <td><button type="button" class="btn btn-block btn-success" id="btnTransferir" onclick="verificarTransferir(this)">Transferir</button></td>
                                                                </tr>

                                                            @endforeach

                                                            </tbody>

                                                        </table>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>
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

        function actualizarEstado(){

            var estado = document.getElementById('select-estado').value;

            var idpresupuesto = {{ $idpresupuesto }};

            let formData = new FormData();
            formData.append('idpresupuesto', idpresupuesto);
            formData.append('idestado',estado);

            axios.post('/admin/departamento/presup/editar', formData, {
            })
                .then((response) => {
                    if(response.data.success === 1){
                        toastr.success('estado actualizado');
                    }else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function verificarTransferir(e){
            Swal.fire({
                title: 'Transferir?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    transferir(e);
                }
            })
        }

        function transferir(e){

            var table = e.parentNode.parentNode;
            var objeto = table.cells[0].childNodes[0].value;
            var idpresupuesto = {{ $idpresupuesto }};
            var idfila = table.cells[1].children[0].value;


            // guardar en base de materiales de la unidad
            // guardar en base de materiales

            let formData = new FormData();
            formData.append('objeto', objeto);
            formData.append('idpresupuesto', idpresupuesto);
            formData.append('idfila', idfila);

            axios.post('/admin/departamento/presup/transferir', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1){
                        toastr.success('agregado correctamente');
                        table.parentNode.removeChild(table);
                    }else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }








    </script>


@endsection
