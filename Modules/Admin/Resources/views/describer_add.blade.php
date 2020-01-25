@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="{{ route('main') }}">Рабочий стол</a></li>
    <li><a href="{{ route('describers') }}">Подписчики</a></li>
    <li class="active">Новая запись</li>
</ul>
<!-- END BREADCRUMB -->
<!-- page content -->

<div class="x_content">
    <h2 class="text-center">Новый подписчик</h2>
    {!! Form::open(['url' => route('describerAdd'),'class'=>'form-horizontal','method'=>'POST']) !!}

    <div class="form-group">
        {!! Form::label('email','E-mail:',['class' => 'col-xs-2 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('email',old('email'),['class' => 'form-control','placeholder'=>'Введите email','required'=>'required'])!!}
            {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Статус:',['class'=>'col-xs-2 control-label']) !!}
        <div class="col-xs-8">
            {!! Form::select('status', $statsel, old('status'),['class' => 'form-control','required' => 'required']); !!}
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
