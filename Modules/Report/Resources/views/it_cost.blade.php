@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active">{{ $title }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
            @if($typesel)
                <div class="x_content">
                    {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('year','Укажите год:',['class' => 'col-xs-2 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('year',$year,['class' => 'form-control','placeholder'=>'ГГГГ','required'=>'required','id'=>'year'])!!}
                            {!! $errors->first('year', '<p class="text-danger">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('type', 'Тип отчета:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('type', $typesel, old('type'),['class' => 'form-control','required' => 'required','id'=>'type']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-8">
                            {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','id'=>'main-report']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <a href="#" onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
                <div class="x_panel" id="result">
                    <div id="bar-chart"></div>
                    <div id="table-data"></div>
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/raphael.min.js"></script>
    <script src="/js/morris.min.js"></script>
    <script>

        $('#result').hide();
        $('.fa-plus-square-o').hide();
        $('#main-report').click(function(e) {
            e.preventDefault();
            var year = $('#year').val();
            var type = $('#type').val();
            $.ajax({
                url: '{{ route('it_costGraph') }}',
                type: 'POST',
                data: {'year': year, 'type': type},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    $("#bar-chart").empty();
                    Morris.Bar({
                        element: 'bar-chart',
                        data: JSON.parse(res),
                        xkey: 'y',
                        ykeys: ['a'],
                        labels: ['Сумма, руб']
                    });
                    $.ajax({
                        url: '{{ route('it_costTable') }}',
                        type: 'POST',
                        data: {'year': year, 'type': type},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert("Сервер вернул вот что: " + res);
                            $("#table-data").html(res);
                        },
                        error: function (xhr, response) {
                            alert('Error! ' + xhr.responseText);
                        }
                    });
                    $(".text-header").html('<h4>Динамика затрат по ИТ за ' + year + ' год</h4>');
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
            });
        });

    </script>
@endsection
