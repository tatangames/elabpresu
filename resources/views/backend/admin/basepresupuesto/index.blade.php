@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Base de Presupuesto</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Material
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaDatatable">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="800" class="form-control" id="descripcion" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <label>Costo</label>
                                    <input type="number" min="0.01" class="form-control" id="costo" placeholder="Costo">
                                </div>

                                <!-- OBJETO ESPECIFICO -->

                                <div class="form-group">
                                    <label style="color:#191818">Objeto Específico</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-objeto">
                                            @foreach($objeto as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Unidad de Medida</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-unidad">
                                            @foreach($unidad as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="800" class="form-control" id="descripcion-editar" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <label>Costo</label>
                                    <input type="number" min="0.01" class="form-control" id="costo-editar" placeholder="Costo">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Objeto Específico</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-objeto-editar">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Unidad de Medida</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-unidad-editar">
                                        </select>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>
        </div>
    </div>
</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/basepresupuesto/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/basepresupuesto/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("frm1").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var descripcion = document.getElementById('descripcion').value;
            var costo = document.getElementById('costo').value;
            var objeto = document.getElementById('select-objeto').value;
            var unidad = document.getElementById('select-unidad').value;

            if(objeto === ''){
                toastr.error('objeto específico es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('unidad de medida es requerido');
                return;
            }

            if(descripcion === ''){
                toastr.error('descripción es requerido');
                return;
            }

            if(descripcion.length > 800){
                toastr.error('descripción 800 caracteres');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!costo.match(reglaNumeroDecimal)) {
                toastr.error('costo debe ser un número');
                return;
            }

            if(costo < 0){
                toastr.error('No números negativos');
                return;
            }

            if(costo > 1000000){
                toastr.error('costo máximo 1 millón de límite');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('descripcion', descripcion);
            formData.append('costo', costo);
            formData.append('objeto', objeto);
            formData.append('unidad', unidad);

            axios.post('/admin/basepresupuesto/nuevo', formData, {
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

            axios.post('/admin/basepresupuesto/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.material.id);
                        $('#descripcion-editar').val(response.data.material.descripcion);
                        $('#costo-editar').val(response.data.material.costo);

                        document.getElementById("select-objeto-editar").options.length = 0;
                        document.getElementById("select-unidad-editar").options.length = 0;

                        $.each(response.data.objeto, function( key, val ){
                            if(response.data.idobj == val.id){
                                $('#select-objeto-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-objeto-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $.each(response.data.unidad, function( key, val ){
                            if(response.data.iduni == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

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
            var descripcion = document.getElementById('descripcion-editar').value;
            var costo = document.getElementById('costo-editar').value;
            var objeto = document.getElementById('select-objeto-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;


            if(objeto === ''){
                toastr.error('objeto específico es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('unidad de medida es requerido');
                return;
            }

            if(descripcion === ''){
                toastr.error('descripción es requerido');
                return;
            }

            if(descripcion.length > 800){
                toastr.error('descripción 800 caracteres');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!costo.match(reglaNumeroDecimal)) {
                toastr.error('costo debe ser un número');
                return;
            }

            if(costo < 0){
                toastr.error('No números negativos');
                return;
            }

            if(costo > 1000000){
                toastr.error('costo máximo 1 millón de límite');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('descripcion', descripcion);
            formData.append('costo', costo);
            formData.append('objeto', objeto);
            formData.append('unidad', unidad);

            axios.post('/admin/basepresupuesto/editar', formData, {
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
