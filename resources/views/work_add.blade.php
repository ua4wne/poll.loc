@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('works') }}">Присутствие на выставке</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая запись</h2>
        {!! Form::open(['url' => route('workAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'new_work']) !!}

        <div class="form-group">
            {!! Form::label('data', 'Дата:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {{ Form::date('data', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control','required' => 'required','id'=>'data']) }}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('renter_id[]', 'Арендатор:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('renter_id[]', $rentsel, old('renter_id[]'),['class' => 'form-control','required' => 'required','id'=>'renter_id','size'=>'6','multiple'=>'true']); !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-3 control-label"></div>
            <div class="col-xs-8">
                <label>
                    <input name="allrent" type="checkbox" value="1" id="allrent"> Все арендаторы
                </label>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('period[]', 'Период времени:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('period[]', $hoursel, old('period[]'),['class' => 'form-control','required' => 'required','id'=>'period','size'=>'11','multiple'=>'true']); !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-3 control-label"></div>
            <div class="col-xs-8">
                <label>
                    <input name="alltime" type="checkbox" value="1" id="alltime"> Все периоды
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-3 control-label"></div>
            <div class="col-xs-8">
                <label>
                    <input name="notime" type="checkbox" value="1" id="notime"> Отсутствовал весь день
                </label>
            </div>
        </div>


        <div class="form-group">
            <div class="col-xs-offset-3 col-xs-8">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection
@section('user_script')
    <script>
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

        $('#alltime').on('change', function () {
            if ($('#alltime').prop('checked')) {
                $('#notime').prop('checked',false);
                $('#period option').each(function () {
                    $(this).prop("selected", true);
                });
            } else {
                $('#period option').each(function () {
                    $(this).prop("selected", false);
                });
            }
        });

        $('#notime').on('change', function () {
            if ($('#notime').prop('checked')) {
                $('#alltime').prop('checked',false);
                $('#period option').each(function () {
                    $(this).prop("selected", true);
                });
                $('#period').prop('disabled',true);
            } else {
                $('#period option').each(function () {
                    $(this).prop("selected", false);
                    $('#period').prop('disabled',false);
                });
            }
        });

        $('#save').click(function(){
            //e.preventDefault();
            let error=0;
            $("#new_work").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
