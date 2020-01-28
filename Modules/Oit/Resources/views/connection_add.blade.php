@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('connections') }}">Подключения к интернет</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая запись</h2>
        {!! Form::open(['url' => route('connectionAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'new_connect']) !!}

        <div class="form-group">
            {!! Form::label('renter_id', 'Юрлицо:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('renter_id', $rentsel, old('renter_id'),['class' => 'select2 form-control','required' => 'required','id'=>'renter_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('date_on', 'Дата подключения:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {{ Form::date('date_on', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('type', 'Тип подключения:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('type', $typesel, old('type'),['class' => 'form-control','required' => 'required','id'=>'type']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('comment','Комментарий:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('comment',old('comment'),['class' => 'form-control','placeholder'=>'Введите комментарий','id'=>'comment'])!!}
                {!! $errors->first('comment', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-8">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script>
        //select2
        $('.select2').css('width','100%').select2({allowClear:false})

        $("#renter_id").prepend($('<option value="0">Выберите юрлицо</option>'));
        $("#renter_id :first").attr("selected", "selected");
        $("#renter_id :first").attr("disabled", "disabled");

        $("#type").prepend($('<option value="0">Выберите тип подключения</option>'));
        $("#type :first").attr("selected", "selected");
        $("#type :first").attr("disabled", "disabled");

        $('#new_connect').submit(function(){
            //e.preventDefault();
            let error=0;
            $("#new_connect").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
