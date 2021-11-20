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
            <h1>Lista de Objetos Específicos</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Objeto Específico
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
                <h4 class="modal-title">Nuevo Objeto Específico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" class="form-control" id="numero" placeholder="Número">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="800" class="form-control" id="nombre" placeholder="Nombre">
                                </div>

                                <div class="form-group row" style="margin-top: 30px">
                                    <label class="control-label">Cuenta: </label>
                                    <select id="select-cuenta" class="form-control">
                                        @foreach($cuenta as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
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
                <h4 class="modal-title">Editar Objeto Específico</h4>
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
                                    <label>Número</label>
                                    <input type="text" class="form-control" id="numero-editar" placeholder="Número">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="800" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Cuenta</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="cuenta-editar">
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
            var ruta = "{{ URL::to('/admin/objespecifico/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/objespecifico/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var nombre = document.getElementById('nombre').value;
            var numero = document.getElementById('numero').value;
            var cuenta = document.getElementById('select-cuenta').value;

            if(numero === ''){
                toastr.error('Número es requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!numero.match(reglaNumeroEntero)) {
                toastr.error('Debe ser número Entero');
                return;
            }

            if(numero < 0){
                toastr.error('No números negativos');
                return;
            }

            if(numero > 1000000){
                toastr.error('Máximo 1 millón de límite');
                return;
            }

            if(nombre === ''){
                toastr.error('nombre es requerido');
                return;
            }

            if(nombre.length > 800){
                toastr.error('Nombre máximo 800 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('numero', numero);
            formData.append('cuenta', cuenta);

            axios.post('/admin/objespecifico/nuevo', formData, {
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

            axios.post('/admin/objespecifico/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.objespecifico.id);
                        $('#numero-editar').val(response.data.objespecifico.numero);
                        $('#nombre-editar').val(response.data.objespecifico.nombre);

                        document.getElementById("cuenta-editar").options.length = 0;

                        $.each(response.data.cuenta, function( key, val ){
                            if(response.data.idcuenta == val.id){
                                $('#cuenta-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#cuenta-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
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
            var nombre = document.getElementById('nombre-editar').value;
            var numero = document.getElementById('numero-editar').value;
            var cuenta = document.getElementById('cuenta-editar').value;

            if(numero === ''){
                toastr.error('Número es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!numero.match(reglaNumeroEntero)) {
                toastr.error('Debe ser número Entero');
                return;
            }

            if(numero < 0){
                toastr.error('No números negativos');
                return;
            }

            if(numero > 1000000){
                toastr.error('Máximo 1 millón de límite');
                return;
            }

            if(nombre === ''){
                toastr.error('nombre es requerido');
                return;
            }

            if(nombre.length > 800){
                toastr.error('Nombre máximo 800 caracteres');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('numero', numero);
            formData.append('cuenta', cuenta);

            axios.post('/admin/objespecifico/editar', formData, {
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
