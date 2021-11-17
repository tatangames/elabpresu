<!DOCTYPE html>
<html lang="es">

<head>
    <title>Alcaldía Metapán</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/login/bootstrap.min.css') }}">

    <link href="{{ asset('images/icono-sistema.png') }}" rel="icon">
    <!-- libreria -->
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" type="text/css" rel="stylesheet" />
    <!-- comprimido de librerias -->
    <link href="{{ asset('css/login/login.css') }}" type="text/css" rel="stylesheet" />
    <!-- libreria para alertas -->
    <link href="{{ asset('css/login/alertify.css') }}" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('css/login/styleLogin.css')}}">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        h3 {
            font-size: 1.5em;
        }
    </style>
</head>

<body style="background-image: url({{ asset('images/fondo3.jpg') }});">
    <div class="container">
        <div class="d-flex justify-content-center h-100">

            <div class="card " style="height: 450px;">
                <div class="card-header text-center">

                <div class="row text-center d-flex" style="position: relative; top: -70px;">
                    <div class="col-md-12">
                        <img src="{{ asset('images/logo.png') }}" width="150" height="140px" srcset="">
                    </div>
                </div>
                <h3 style="position: relative; top: -10px;">ELABORACIÓN DE PLAN ANUAL DE COMPRAS Y PRESUPUESTO</h3>
            </div>
            <div class="card-body">
                <form class=" validate-form">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input id="usuario" type="text" class="form-control" required placeholder="Usuario">
                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input id="password" type="password" class="form-control" required placeholder="Contraseña">
                    </div>
                    <br><br>
                    <div class="form-group text-center">
                        <input type="button" value="Entrar" id="btnLogin" onclick="login()" class="btn login_btn">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>


<script type="text/javascript">

    // onkey Enter
    var input = document.getElementById("password");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            login();
        }
    });

    function login() {

        var usuario = document.getElementById('usuario').value;
        var password = document.getElementById('password').value;

        if(usuario === ''){
            toastr.error('Error', 'usuario es requerido');
            return;
        }

        if(password === ''){
            toastr.error('Error', 'contraseña es requerida');
            return;
        }

        let formData = new FormData();
        formData.append('usuario', usuario);
        formData.append('password', password);

        axios.post('/admin/acceso', formData, {
        })
            .then((response) => {
                verificar(response);
            })
            .catch((error) => {
                formData.append('usuario', usuario);
            });

    }

    // mensajes para verificar respuesta
    function verificar(response) {

        if (response.data.success == 0) {
            alertify.error("Validacion incorrecta...");
        } else if (response.data.success == 1) {
            window.location = response.data.message
            //console.log(response);
        } else if (response.data.success == 2) {
            alertify.error("Contraseña incorrecta");
        } else if (response.data.success == 3) {
            alertify.error("Usuario no encontrado...");
        } else {
            alertify.error("Error");
        }
    }

    // validaciones frontend
    function validaciones(usuario, password) {
        if (usuario === '') {
            alertify.error("El usuario es requerido...");
            return false;
        }
        else if (password === '') {
            alertify.error("La contraseña es requerida...");
            return false;
        }
        else {
            return true;
        }
    }


</script>
</body>

</html>
