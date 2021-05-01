@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('anket-work') }}">{{ $title }}</a></li>
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
                    {!! Form::label('type', 'Тип отчета:',['class'=>'col-xs-2 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('type', ['percent'=>'Процент выполнения','anket'=>'По анкетам'], old('type'),['class' => 'form-control','required' => 'required','id'=>'type']); !!}
                    </div>
                </div>

                <div class="form-group plan">
                    {!! Form::label('max_qty','План на день:',['class' => 'col-xs-2 control-label'])   !!}
                    <div class="col-xs-8">
                        {!! Form::text('max_qty',$max_qty,['class' => 'form-control','placeholder'=>'Задайте план на день','required'=>'required','id'=>'max_qty'])!!}
                        {!! $errors->first('max_qty', '<p class="text-danger">:message</p>') !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-offset-2 col-xs-8">
                        {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-success','type'=>'submit','id'=>'report']) !!}
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
{{--    <script src="/js/gstatic_charts_loader.js"></script>--}}
    <script>
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

        $('#type').change(function() {
            let t = $("#type option:selected").text();
            if(t == "По анкетам")
                $('.plan').hide()
            else
                $('.plan').show();
        });

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
            return true;
        });

        $('#report').click(function (e) {
            e.preventDefault();
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
            $.ajax({
                url: '{{ route('anket-work') }}',
                type: 'POST',
                data: $('#new_val').serialize(),
                success: function (res) {
                    let obj = jQuery.parseJSON(res);
                    $("p.text-header").text('за период с ' + $('#start').val() + ' по ' + $('#finish').val() + ' кол-во анкет ' + obj[1].qty);
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    $('#result').html(obj[0].content);
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
