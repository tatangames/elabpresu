<html>
<head>
    <title>Alcaldía Metapán | Panel</title>
    <style>
        body{
            font-family: Arial;
        }
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

        .content img {
            margin-right: 15px;
            float: right;
        }

        .content h3{
            font-size: 20px;

        }
        .content p{
            margin-left: 15px;
            display: block;
            margin: 2px 0 0 0;
        }

        hr{
            page-break-after: always;
            border: none;
            margin: 0;
            padding: 0;
        }

        #tabla {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 35px;
            text-align: center;
        }

        #tabla td{
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 15px;
        }

        #tabla th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #tabla th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #f2f2f2;
            color: #1E1E1E;
            text-align: center;
            font-size: 16px;
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
            <img src="{{ asset('images/logo.png') }}" style="float: right" alt="" height="88px" width="72px">
            <h3>ALCALDIA MUNICIPAL DE METAPAN</h3>
            <h3>REPORTE</h3>
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

    <p class="fecha"><strong>Fecha: </strong></p>


    <table id="tabla" style="width: 95%">
        <thead>
        <tr>
            <th style="text-align: center; font-size:13px; width: 11%">COD.</th>
            <th style="text-align: center; font-size:13px; width: 40%">ESPECIFICO</th>
            <th style="text-align: center; font-size:13px; width: 12%">OBJ.ESPECIFICO</th>
            <th style="text-align: center; font-size:13px; width: 10%">CUENTA</th>
            <th style="text-align: center; font-size:13px; width: 10%">RUBRO</th>
        </tr>
        </thead>
        @foreach($rubro as $item)
            <tr>
                <td style="font-size:11px; text-align: left">{{ $item->numero }}</td>
                <td style="font-size:11px; text-align: left">{{ $item->nombre }}</td>
                <td></td>
                <td></td>
                <td style="font-size:11px; text-align: right">${{ $item->sumarubro }}</td>
            </tr>

            @foreach($item->cuenta as $cc)

                <tr>
                    <td style="font-size:11px; text-align: left">{{ $cc->numero }}</td>
                    <td style="font-size:11px; text-align: left">{{ $cc->nombre }}</td>
                    <td></td>
                    <td style="font-size:11px; text-align: right">${{ $cc->sumaobjetototal }}</td>
                    <td></td>
                </tr>

                @foreach($cc->objeto as $obj)

                    <tr>
                        <td style="font-size:11px; text-align: left">{{ $obj->numero }}</td>
                        <td style="font-size:11px; text-align: left">{{ $obj->nombre }}</td>
                        <td style="font-size:11px; text-align: right">${{ $obj->sumaobjeto }}</td>
                        <td></td>
                        <td></td>
                    </tr>

                @endforeach
            @endforeach
        @endforeach

        <tr>
            <td style="border: none"></td>
            <td style="font-size:13px; text-align: center; border: none">TOTAL</td>
            <td style="font-size:13px; text-align: right">${{ $totalobj }}</td>
            <td style="font-size:13px; text-align: right">${{ $totalcuenta }}</td>
            <td style="font-size:13px; text-align: right">${{ $totalrubro }}</td>
        </tr>

    </table>

</div>




</body>
</html>
