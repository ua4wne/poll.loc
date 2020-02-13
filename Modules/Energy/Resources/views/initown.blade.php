@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('initown') }}">Собственные счетчики</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove col-xs-offset-2 col-xs-8">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {!! session('status') !!}
            </div>
        </div>
    @endif
    <div class="x_content">
        <h2 class="text-center">{{ $title }}</h2>
        <div class="row">
            <div class="alert alert-warning col-xs-offset-2 col-xs-8"><p class="text-center">Форма заполняется в случае установки нового или замены старого счетчика!</p></div>
        </div>
        {!! Form::open(['url' => route('initown'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

        <div class="form-group">
            {!! Form::label('own_ecounter_id', 'Счетчик:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('own_ecounter_id', $selmain, old('own_ecounter_id'),['class' => 'form-control','required' => 'required','id'=>'own_ecounter_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('year','Год:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('year',$year,['class' => 'form-control','placeholder'=>'ГГГГ','required'=>'required','size'=>'4','id'=>'year'])!!}
                {!! $errors->first('year', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('month', 'Месяц:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('month', $month, date('m'),['class' => 'form-control','required' => 'required','id'=>'month']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('encount','Текущие показания, кВт:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('encount',old('encount'),['class' => 'form-control','required'=>'required','id'=>'encount'])!!}
                {!! $errors->first('encount', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('delta','Потребление, кВт:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('delta',0,['class' => 'form-control','required'=>'required','id'=>'delta'])!!}
                {!! $errors->first('delta', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'submit']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>
    <div class="x_title">{!! $tbl !!}</div>
    </div>
@endsection

@section('user_script')
    <script>
        $('#submit').click(function () {
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
    </script>
@endsection
