@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Perfil</h1>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-green">
                    <div class="card-header">
                        <h3 class="card-title">Formulario</h3>
                    </div>
                    <form>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Usuario</label>
                                <input type="text" class="form-control" disabled value="{{ $usuario->usuario }}">
                            </div>

                            <div class="form-group">
                                <label>Contraseña Actual</label>
                                <input type="password" maxlength="16" class="form-control" id="password" placeholder="Contraseña">
                            </div>

                            <div class="form-group">
                                <label>Contraseña Nueva</label>
                                <input type="password" maxlength="16" class="form-control" id="password1" placeholder="Contraseña">
                            </div>

                        </div>

                        <div class="card-footer" style="float: right;">
                            <button type="button" class="btn btn-success" onclick="actualizar()">Actualizar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function actualizar(){
            var passwordNueva = document.getElementById('password').value;
            var passwordRepetida = document.getElementById('password1').value;

            if(passwordNueva === ''){
                toastr.error('contraseña nueva es requerida');
                return;
            }

            if(passwordRepetida === ''){
                toastr.error('contraseña repetida es requerida');
                return;
            }

            if(passwordNueva.length > 16){
                toastr.error('máximo 16 caracteres para contraseña nueva');
                return;
            }

            if(passwordNueva.length < 4){
                toastr.error('mínimo 4 caracteres para contraseña nueva');
                return;
            }

            if(passwordRepetida.length > 16){
                toastr.error('máximo 16 caracteres para contraseña repetida');
                return;
            }

            if(passwordRepetida.length < 4){
                toastr.error('mínimo 4 caracteres para contraseña repetida');
                return;
            }

            if(passwordNueva !== passwordRepetida){
                toastr.error('las contraseñas no coinciden');
                return;
            }

            openLoading()
            var formData = new FormData();
            formData.append('password', passwordNueva);

            axios.post(url+'/editar-perfil/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {
                        toastr.success('Contraseña Actualizada');
                        $('#modalEditar').modal('hide');
                        document.getElementById('password').value = '';
                        document.getElementById('password1').value = '';
                    }
                    else {
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('error al actualizar');
                });
        }


    </script>



@stop
