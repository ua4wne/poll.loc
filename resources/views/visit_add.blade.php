@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('visits') }}">Посещение выставки</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая запись</h2>
        {!! Form::open(['url' => route('visitAdd'),'class'=>'form-horizontal','method'=>'POST']) !!}

        <div class="form-group">
            {!! Form::label('data', 'Дата:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {{ Form::date('data', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('hours[]', 'Период времени:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('hours[]', $hoursel, old('hours[]'),['class' => 'form-control','required' => 'required','id'=>'hours','size'=>'11','multiple'=>'true']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('ucount','Кол-во посетителей:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('ucount',old('ucount'),['class' => 'form-control','placeholder'=>'Введите число','id'=>'ucount'])!!}
                {!! $errors->first('ucount', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-3 col-xs-8">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection
