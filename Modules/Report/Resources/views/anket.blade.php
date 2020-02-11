@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('anket-report') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
            <p class="text-header text-center"></p>
            <div class="x_content">
                {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

                <div class="form-group">
                    {!! Form::label('start', 'Начало периода:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('start', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-').'01'),['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('finish', 'Конец периода:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('finish', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('form_id', 'Выбор анкет:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('form_id', $formselect, old('form_id'),['class' => 'select2 form-control','required' => 'required','id'=>'form_id']); !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('version', 'Выбор анкет:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('version', $verselect, old('version'),['class' => 'form-control','required' => 'required','id'=>'version']); !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-offset-2 col-xs-8">
                        {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','id'=>'report']) !!}
                        {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','id' => 'export']) !!}
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
            <a href="#"
               onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i
                    class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
            <div class="x_panel" id="result">
                <div class="pull-center" style="width: 50%; height: 400px;" id="graph_pie"></div>
                <div id="table-data"></div>
            </div>

        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script src="/js/gstatic_charts_loader.js"></script>
    <script>
        $('.select2').css('width', '100%').select2({allowClear: false})
        $('#result').hide();
        $('.fa-plus-square-o').hide();
        $('#report').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('anket-report') }}',
                type: 'POST',
                data: $('#new_val').serialize(),
                success: function (res) {
                    $("p.text-header").text('за период с '+$('#start').val()+' по '+$('#finish').val()+' опрошено '+res+' человек');
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    $("#graph_pie").empty();
                    //alert("Сервер вернул вот что: " + res);
                    var obj = jQuery.parseJSON(res);
                    google.charts.load('current', {'packages': ['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'counter');
                        data.addColumn('number', 'cost');
                        $.each(obj, function (key, value) {
                            data.addRow([
                                value.name,
                                parseFloat(value.delta),
                            ]);
                        });
                        var options = {
                            is3D: true,
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('graph_pie'));
                        chart.draw(data, options);
                    }
                    $.ajax({
                        url: '{{ route('rent-table') }}',
                        type: 'POST',
                        data: {'year': year},
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
                    $("h2.text-header").text($('#form_id').text());
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
