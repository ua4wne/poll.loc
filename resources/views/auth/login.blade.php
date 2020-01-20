@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('status') }}
            </div>
        </div>
    @endif

    {!! Form::open(['url' => route('login'),'class'=>'form-horizontal','method'=>'POST']) !!}

    <h2 class="text-center">Авторизация</h2>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            {!! Form::text('login',old('login'),['class' => 'form-control','placeholder'=>'Введите логин','required'=>''])!!}
        </div>
        {!! $errors->first('login', '<p class="text-danger">:message</p>') !!}
    </div>

    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
            {{ Form::password('password', array('id' => 'password', "class" => "form-control",'placeholder'=>'Введите пароль','required'=>'')) }}
            {!! $errors->first('password', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::button('<i class="fa fa-sign-in fa-lg" aria-hidden="true"></i> Войти в систему', ['class' => 'btn btn-default','type'=>'submit']) !!}
        {!! Form::button('<i class="fa fa-repeat fa-lg" aria-hidden="true"></i> Забыл пароль', ['class' => 'btn pull-right','id'=>'lost']) !!}
    </div>

    {!! Form::close() !!}

@endsection
