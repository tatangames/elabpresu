<section class="content">
    <div class="container-fluid">

        <div class="container-fluid">
            <button type="button" onclick="verPdf()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                PDF
            </button>
        </div>

        <div class="row">
            <div class="col-12">

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
                                                                        <th style="width: 10%; text-align: center">Total</th>

                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    <!-- foreach para material -->

                                                                    @foreach($obj->material as $mm)

                                                                        <tr>
                                                                            <td><input value="{{ $mm->descripcion }}" disabled class="form-control"  type="text"></td>
                                                                            <td><input value="${{ $mm->multiunidad }}" disabled class="form-control" type="text" style="max-width: 180px"></td>
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

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

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


    function verPdf(){
        var idanio = {{ $anio }};
        window.open("{{ URL::to('admin/generador/pdf/presupuesto') }}/" + idanio);
    }

</script>

