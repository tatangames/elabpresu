<html>
<head>
    <title>Alcaldía Metapán | Panel</title>
    <style>


        @page {
            margin: 145px 25px;
            /* margin-bottom: 10%;*/
        }
        header { position: fixed;
            left: 0px;
            top: -160px;
            right: 0px;
            height: 100px;
            text-align: center;
            font-size: 12px;
        }
        header h1{
            margin: 10px 0;
        }
        header h2{
            margin: 0 0 10px 0;
        }
        footer {
            position: fixed;
            left: 0px;
            bottom: -10px;
            right: 0px;
            height: 10px;
            /* border-bottom: 2px solid #ddd;*/
        }

        footer table {
            width: 100%;
        }
        footer p {
            text-align: right;
        }
        footer .izq {
            margin-top: 20px; !important;
            margin-left: 20px;
            text-align: left;
        }

        .content {
            padding: 20px;
            margin-left: auto;
            margin-right: auto;
        }


        .fecha{
            font-size: 16px;
            margin-left: 17px;
            text-align: justify;
        }


    </style>
<body>

<header style="margin-top: 25px">
    <div class="row">

        <div class="content">
            <img src="{{ asset('images/logo.png') }}" style="float: right" alt="" height="88px" width="74px">
            <h3>ALCALDIA MUNICIPAL DE METAPAN</h3>
            <h3>REPORTE CONSOLIDADO TOTALES</h3>
        </div>

    </div>
</header>

<footer>
    <table>
        <tr>
            <td>
                <p class="izq">
                    <br>

                </p>
            </td>
            <td>
                <p class="page">

                </p>
            </td>
        </tr>
    </table>
</footer>

<div id="content">
    <p class="fecha"><strong>Año: {{ $fechaanio }}</strong></p>
    <table id="tabla" style="width: 95%">
        <thead>
        <tr>
            <th style="text-align: center; font-size:13px; width: 9%">CORR.</th>
            <th style="text-align: center; font-size:13px; width: 12%">COD. ESPEC.</th>
            <th style="text-align: center; font-size:13px; width: 20%">NOMBRE</th>
            <th style="text-align: center; font-size:13px; width: 9%">CANTIDAD</th>
            <th style="text-align: center; font-size:13px; width: 10%">PREC. UNI.</th>
            <th style="text-align: center; font-size:13px; width: 9%">TOTAL</th>
        </tr>
        </thead>
        @foreach($materiales as $dato)

            <tr>
                <td style="font-size:11px; text-align: center">{{ $dato->correlativo }}</td>
                <td style="font-size:11px; text-align: center">{{ $dato->codigo }}</td>
                <td style="font-size:11px; text-align: center">{{ $dato->descripcion }}</td>
                <td style="font-size:11px; text-align: center">{{ $dato->sumacantidad }}</td>
                <td style="font-size:11px; text-align: center">${{ $dato->costo }}</td>
                <td style="font-size:11px; text-align: center">${{ $dato->total }}</td>
            </tr>

        @endforeach

    </table>

</div>




</body>
</html>
