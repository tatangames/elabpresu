@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper" id="divcc" style="display: none">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Generar Consolidado</h5>
                        <div class="card">
                            <form class="form-horizontal">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label style="margin: 8px; margin-left: 20px">Año</label>
                                        <div style="margin-left: 6px" class="col-sm-2">
                                            <select class="form-control" id="select-anio">
                                                @foreach($anios as $item)
                                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="button" onclick="verificar()" class="btn btn-success" style="margin-left: 15px">Buscar</button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="content" id="divcontenedor" style="display: none">
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


    <div class="modal fade" id="modalPendiente">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Presupuestos aun sin Aprobar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <select class="form-control" id="select-departamento">
                                    </select>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
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
    <script src="{{ asset('js/jquery.simpleaccordion.js') }}"></script>

    <script>
        $(document).ready(function() {
            document.getElementById("divcc").style.display = "block";
        });

    </script>

    <script>

        function verificar(){

            var anio = document.getElementById('select-anio').value;

            if(anio === ''){
                toastr.error('año es requerido');
                return;
            }

            let formData = new FormData();
            formData.append('anio', anio);

            axios.post('/admin/generador/verificar/presupuesto', formData, {
            })
                .then((response) => {

                    if(response.data.success === 1){

                        // generar tabla
                        cargarTabla(anio);
                    }

                    else if(response.data.success === 2){
                        // departamentos si aprobar aun
                        $('#modalPendiente').modal('show');

                        document.getElementById("select-departamento").options.length = 0;

                        $.each(response.data.lista, function( key, val ){
                            $('#select-departamento').append('<option value="0">'+val.nombre+'</option>');
                        });

                    }
                    else{
                        toastr.error('error');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al generar');
                    closeLoading();
                });
        }

        function cargarTabla(anio){

            document.getElementById("divcontenedor").style.display = "block";

            var ruta = "{{ url('/admin/generador/tabla/consolidado') }}/"+anio;
            $('#tablaDatatable').load(ruta);
        }


        function msjActualizado(){
            Swal.fire({
                title: 'Actualizado',
                text: 'Nuevo Material transferido correctamente',
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }

    </script>


@endsection
