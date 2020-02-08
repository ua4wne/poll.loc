@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('summary') }}">{{ $head }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <!-- page content -->
    <h2 class="text-center">{{ $title }}</h2>
    <p class="text-center text-info" id="title"></p>
    <div class="x_content" id="filter">
        {!! Form::open(['url' => route('summary_excel'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

        <div class="form-group">
            {!! Form::label('start', 'Начало периода:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {{ Form::date('start', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-'.'01')),['class' => 'form-control','required' => 'required','id'=>'start']) }}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('finish', 'Конец периода:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {{ Form::date('finish', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control','required' => 'required','id'=>'finish']) }}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('place_id', 'Территория:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('place_id', $selplace, old('place_id'),['class' => 'form-control','required' => 'required','id'=>'place_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('renter_id[]', 'Арендатор:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('renter_id[]', $selrent, old('renter_id[]'),['class' => 'select2 form-control','required' => 'required','id'=>'renter_id','multiple'=>'true']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('email','E-mail получателя:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::email('email',old('email'),['class' => 'form-control','placeholder'=>'Введите e-mail','id'=>'email'])!!}
                {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','id' => 'report','name' => 'report','value' => 'report']) !!}
                {!! Form::button('<span class="fa  fa-table"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','id' => 'export','name' => 'export','value' => 'export']) !!}
                {!! Form::button('<span class="fa  fa-envelope-o"></span> Отправить', ['class' => 'btn btn-primary','type'=>'submit','id' => 'viamail','name' => 'viamail','value' => 'viamail']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
    <a href="#" onclick="refresh();"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
    <div class="x_panel" id="result">

    </div>
    </div>
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script>
        //select2
        $('.select2').css('width','100%').select2({allowClear:true,placeholder:'Выберите арендатора'})

        $("#place_id :first").attr("selected", "selected");


        $('#result').hide();
        $('.fa-plus-square-o').hide();

        $('#place_id').change(function() {
            // отправляем AJAX запрос
            let selrent=$("#place_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('sel_renters') }}",
                dataType: "html",
                data: {selrent:selrent},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                // success - это обработчик удачного выполнения событий
                success: function(res) {
                    //alert("Сервер вернул вот что: " + response);
                    $('#renter_id').html(res);
                }
            });
        });

        $('#report').click(function (e) {
            e.preventDefault();
            $('#loader').show();
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
                $('#loader').hide();
                return false;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('summary') }}",
                data: $('#new_val').serialize(),
                // success - это обработчик удачного выполнения событий
                success: function(res) {
                    //alert("Сервер вернул вот что: " + res);
                    $('#filter').hide();
                    $('#result').show();
                    $('#result').empty();
                    $('.fa-plus-square-o').show();
                    $('#result').html(res);
                    $('#title').text('За период с '+$('#start').val()+' по '+$('#finish').val());
                }
            });
            $('#loader').hide();
        });

        $('#viamail').click(function (e) {
            e.preventDefault();
            $('#loader').show();
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
                $('#loader').hide();
                return false;
            }
            if ($('#email').val()=='') {
                alert("Не указан e-mail!");
                $('#loader').hide();
                return false;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('summary_mail') }}",
                data: $('#new_val').serialize(),
                // success - это обработчик удачного выполнения событий
                success: function(res) {
                    //alert("Сервер вернул вот что: " + res);
                    if(res=='OK'){
                        alert('Почта успешно отправлена!');
                    }
                    if(res=='ERR')
                        alert('Возникла ошибка при отправке почты!');
                    if(res=='NO')
                        alert('Файл отчета не обнаружен на сервере!');
                }
            });
            $('#loader').hide();
        });

        $('#export').click(function(){
            let error=0;
            $("#filter").find(":input").each(function() {// проверяем каждое поле ввода в форме
                if($(this).attr("required")=='required'){ //обязательное для заполнения поле формы?
                    if(!$(this).val()){// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error=1;// определяем индекс ошибки
                    }
                    else{
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if(error){
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
            return true;
        });

        function refresh(){
            $('.x_content').show();
            $('#result').hide();
            $('.fa-plus-square-o').hide();
            $('#title').text('Расчет потребления электроэнергии арендаторами');
            return false;
        }

    </script>
@endsection
