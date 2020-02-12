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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
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
               onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); $('p.text-header').text(''); $('h2.text-header').text('Задайте условия отбора'); return false;"><i
                    class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
            <div class="x_panel" id="result">

            </div>

        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script src="/js/raphael.min.js"></script>
    <script src="/js/morris.min.js"></script>
    <script src="/js/gstatic_charts_loader.js"></script>
    <script>
        $('.select2').css('width', '100%').select2({allowClear: false})
        $('#result').hide();
        $('.fa-plus-square-o').hide();
        let c = 0;
        $('.other').hide(); //скрыли значения в таблице вывода анкеты

        $(document).on({
            click: function () {
                $(this).removeClass('fa-expand');
                $(this).addClass('fa-compress');
                $(this).parent().next().show();

            }
        }, ".fa-expand");

        $(document).on({
            click: function () {
                $(this).removeClass('fa-compress');
                $(this).addClass('fa-expand');
                $(this).parent().next().hide();
            }
        }, ".fa-compress");

        $('#report').click(function (e) {
            e.preventDefault();
            $("#loader").show();
            $.ajax({
                url: '{{ route('anket-report') }}',
                type: 'POST',
                data: $('#new_val').serialize(),
                success: function (res) {
                    let obj = jQuery.parseJSON(res);
                    $("p.text-header").text('за период с ' + $('#start').val() + ' по ' + $('#finish').val() + ' опрошено ' + obj[0].qty + ' человек');
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    //заполняем графики
                    let objpie = obj[2].pie;
                    $.each(objpie,function(index,val) {
                        google.charts.load('current', {'packages': ['corechart']});
                        google.charts.setOnLoadCallback(drawChart);
                        function drawChart() {
                            var data = new google.visualization.DataTable();
                            data.addColumn('string', 'name');
                            data.addColumn('number', 'kol');
                            $.each(val, function(key,value) {
                                alert(value.answer+' | '+value.kol);
                                data.addRow([
                                    value.answer,
                                    parseInt(value.kol),
                                ]);
                            });
                            var options = {
                                is3D: true,
                            };
                            let idx = index;
                            idx++;
                            var chart = new google.visualization.PieChart(document.getElementById('pie-1'));
                            chart.draw(data, options);
                        }
                        //console.log('Индекс: ' + index + '; Значение: ' + val);
                    });
                    $('#result').html(obj[1].content);
                    $("h2.text-header").text($('#form_id').text());
                    $('.x_content').hide();
                    $('.other').hide(); //скрыли значения в таблице вывода анкеты
                    $('.fa-plus-square-o').show();
                },
                error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
            });
            $("#loader").hide();
        });

    </script>
@endsection
