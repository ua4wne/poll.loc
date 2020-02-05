@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('rent-calculate') }}">{{ $head }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="x_content">
        <h2 class="text-center">{{ $title }}</h2>
        {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

        <div class="form-group">
            {!! Form::label('year','Год:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('year',$year,['class' => 'form-control','placeholder'=>'ГГГГ','required'=>'required','size'=>'4','id'=>'year'])!!}
                {!! $errors->first('year', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('place_id', 'Территория:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('place_id', $selplace, old('place_id'),['class' => 'form-control','required' => 'required','id'=>'place_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('renter_id', 'Арендатор:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('renter_id', $selrent, old('renter_id'),['class' => 'select2 form-control','required' => 'required','id'=>'renter_id']); !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','id' => 'report','name' => 'report','value' => 'report']) !!}
                {!! Form::button('<span class="fa  fa-table"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','id' => 'table','name' => 'table','value' => 'table']) !!}
            </div>
        </div>

        {!! Form::close() !!}
        <a href="#" onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
        <div class="x_panel" id="result">

        </div>
    </div>
    </div>
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script>
        //select2
        $('.select2').css('width','100%').select2({allowClear:false})

        $("#place_id :first").attr("selected", "selected");
        $("#renter_id").prepend($('<option value="0">Выберите арендатора</option>'));
        $("#renter_id :first").attr("selected", "selected");
        $("#renter_id :first").attr("disabled", "disabled");

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
            $.ajax({
                type: "POST",
                url: "{{ route('rent-calculate') }}",
                data: $('#new_val').serialize(),
                // success - это обработчик удачного выполнения событий
                success: function(res) {
                    //alert("Сервер вернул вот что: " + res);
                    if(res=='OK'){
                        alert('Данные счетчика успешно добавлены!');
                    }
                    if(res=='ERR')
                        alert('Возникла ошибка при попытке записи данных!');
                }
            });
        });
    </script>
@endsection
