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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
                <div class="x_content">
                    {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'filter']) !!}

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
                        {!! Form::label('place_id[]', 'Площадки:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('place_id[]', $places, old('place_id[]'),['class' => 'form-control','id'=>'place','size'=>'5','multiple'=>'true']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('renter_id[]', 'Арендатор:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('renter_id[]', $rentsel, old('renter_id[]'),['class' => 'form-control','required' => 'required','id'=>'renter_id','size'=>'6','multiple'=>'true']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-2 control-label"></div>
                        <div class="col-xs-8">
                            <label>
                                <input name="allrent" type="checkbox" value="1" id="allrent"> Все арендаторы
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-8">
                            {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'report','value' => 'report','id'=>'report']) !!}
                            {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'export','value' => 'export','id'=>'export']) !!}
                            {!! Form::button('<span class="fa  fa-bug"></span> Контроль заполнения', ['class' => 'btn btn-danger','type'=>'submit','name' => 'control','value' => 'control','id'=>'control']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            <a href="#" onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
            <div class="x_panel" id="result">
                    <div id="chart_visit"></div>
                </div>
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script>

        $('#result').hide();
        $('.fa-plus-square-o').hide();
        $("#place :first").attr("selected", "selected");
        $("#place").change(function(){
            var place = $("#place").val();
            $.ajax({
                url: '{{ route('rentsel') }}',
                type: 'POST',
                data: {'place':place},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $("#renter_id").empty();
                    $("#renter_id").append($(res));
                },
                error: function(xhr, response){
                    alert('Error! '+ xhr.responseText);
                }
            });
        });

        $('#allrent').on('change', function () {
            if ($('#allrent').prop('checked')) {
                $('#renter_id option').each(function () {
                    $(this).prop("selected", true);
                });
            } else {
                $('#renter_id option').each(function () {
                    $(this).prop("selected", false);
                });
            }
        });

        $('#report').click(function(){
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

    </script>
@endsection
