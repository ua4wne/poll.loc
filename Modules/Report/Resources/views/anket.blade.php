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
            <div class="x_panel">
                <h2 class="text-header text-center">{{ $head }}</h2>
                <p class="text-header text-center text-info"></p>
            </div>
            <div class="x_content">
                {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

                <div class="form-group">
                    {!! Form::label('start', 'Начало периода:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('start', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-').'01'),['class' => 'form-control','required' => 'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('finish', 'Конец периода:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('finish', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control','required' => 'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('form_id', 'Выбор анкет:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('form_id', $formselect, old('form_id'),['class' => 'select2 form-control','required' => 'required','id'=>'form_id']); !!}
                    </div>
                </div>

                {{--<div class="form-group">
                    {!! Form::label('version', 'Выбор анкет:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('version', $verselect, old('version'),['class' => 'form-control','required' => 'required','id'=>'version']); !!}
                    </div>
                </div>--}}

                <div class="form-group">
                    <div class="col-xs-offset-2 col-xs-8">
                        {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-success','type'=>'submit','id'=>'report']) !!}
                        {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'export','value' => 'export','id'=>'export']) !!}
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

        $('#export').click(function () {
            //e.preventDefault();
            let error = 0;
            $("#new_val").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
            $("#loader").show();
            return true;
        });

        $('#report').click(function (e) {
            e.preventDefault();
            $("#loader").show();
            $.ajax({
                url: '{{ route('anket-report') }}',
                type: 'POST',
                data: $('#new_val').serialize(),
                success: function (res) {
                    let obj = jQuery.parseJSON(res);
                    $("p.text-header").text('за период с ' + $('#start').val() + ' по ' + $('#finish').val() + ' кол-во опрошенных ' + obj[0].qty);
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    //заполняем графики
                    let qst = obj[2].pie;
                    //цикл по вопросам
                    $.each(qst, function (key, objpie) {
                        let idx = key + 1;
                        google.charts.load('current', {'packages': ['corechart']});
                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {
                            let data = new google.visualization.DataTable();
                            data.addColumn('string', 'Answer');
                            data.addColumn('number', 'Kol-vo');
                            $.each(objpie, function (index, val) {
                                data.addRow([
                                    val.answer,
                                    parseFloat(val.kol),
                                ]);
                            });
                            let options = {
                                is3D: true,
                            };
                            let chart = new google.visualization.PieChart(document.getElementById('pie-' + idx));
                            chart.draw(data, options);
                        }
                    });
                    $('#result').html(obj[1].content);
                    $("h2.text-header").text($('#form_id option:selected').text());
                    $('.x_content').hide();
                    $('.other').hide(); //скрыли значения в таблице вывода анкеты
                    $('.fa-plus-square-o').show();
                    $("#loader").hide();
                },
                error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
            });
        });

    </script>
@endsection
