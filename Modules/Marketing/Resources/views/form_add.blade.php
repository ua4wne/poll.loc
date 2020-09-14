@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('forms') }}">Анкеты</a></li>
        <li class="active">Новая анкета</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая анкета</h2>
        {!! Form::open(['url' => route('formAdd'),'class'=>'form-horizontal','method'=>'POST']) !!}

        <div class="form-group">
            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование анкеты','required'=>'required','size'=>'80'])!!}
                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('form_group_id', 'Группа анкет:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('form_group_id', $selgroup, old('form_group_id'),['class' => 'form-control','required' => 'required','id'=>'form_group_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('is_active', 'Статус активности:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('is_active', ['0'=>'Не активная','1'=>'Активная'], old('is_active'),['class' => 'form-control','required' => 'required','id'=>'is_active']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('is_work', 'Видимость анкеты:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('is_work', ['0'=>'Отключена','1'=>'В работе'], old('is_work'),['class' => 'form-control','required' => 'required','id'=>'is_work']); !!}
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
