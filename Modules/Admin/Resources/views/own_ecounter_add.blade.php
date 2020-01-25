@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="{{ route('main') }}">Рабочий стол</a></li>
    <li><a href="{{ route('own-ecounters') }}">Счетчики собственные</a></li>
    <li class="active">Новая запись</li>
</ul>
<!-- END BREADCRUMB -->
<!-- page content -->

<div class="x_content">
    <h2 class="text-center">Новый счетчик</h2>
    {!! Form::open(['url' => route('own-ecounterAdd'),'class'=>'form-horizontal','method'=>'POST']) !!}

    <div class="form-group">
        {!! Form::label('name','Наименование:',['class' => 'col-xs-2 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required'])!!}
            {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('text','Описание:',['class' => 'col-xs-2 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('text',old('text'),['class' => 'form-control','placeholder'=>'Описание счетчика','required'=>'required'])!!}
            {!! $errors->first('text', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('koeff','Коэффициент:',['class' => 'col-xs-2 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('koeff',old('koeff'),['class' => 'form-control','placeholder'=>'Укажите коэффициент','required'=>'required'])!!}
            {!! $errors->first('koeff', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('tarif','Тариф:',['class' => 'col-xs-2 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('tarif',old('tarif'),['class' => 'form-control','placeholder'=>'Укажите тариф','required'=>'required'])!!}
            {!! $errors->first('tarif', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-xs-offset-2 col-xs-10">
            {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
        </div>
    </div>

    {!! Form::close() !!}

</div>
</div>
@endsection
