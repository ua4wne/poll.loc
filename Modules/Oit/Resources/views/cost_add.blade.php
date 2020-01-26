@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('costs') }}">Расходы</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая запись</h2>
        {!! Form::open(['url' => route('costAdd'),'class'=>'form-horizontal','method'=>'POST']) !!}

        <div class="form-group">
            {!! Form::label('supplier_id', 'Поставщик:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('supplier_id', $supsel, old('supplier_id'),['class' => 'form-control','required' => 'required','id'=>'supplier_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','id'=>'name'])!!}
                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('price','Цена, руб:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('price',old('price'),['class' => 'form-control','placeholder'=>'Введите цену','required'=>'required','id'=>'price'])!!}
                {!! $errors->first('price', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('unitgroup_id', 'Подразделение:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('unitgroup_id', $groupsel, old('unitgroup_id'),['class' => 'form-control','required' => 'required','id'=>'unitgroup_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('expense_id', 'Статья расхода:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('expense_id', $expsel, old('expense_id'),['class' => 'form-control','required' => 'required','id'=>'expense_id']); !!}
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
    <script>
        $("#supplier_id").prepend($('<option value="0">Выберите поставщика</option>'));
        $("#supplier_id :first").attr("selected", "selected");
        $("#supplier_id :first").attr("disabled", "disabled");

        $("#unitgroup_id").prepend($('<option value="0">Выберите подразделение</option>'));
        $("#unitgroup_id :first").attr("selected", "selected");
        $("#unitgroup_id :first").attr("disabled", "disabled");

        $("#expense_id").prepend($('<option value="0">Выберите статью расхода</option>'));
        $("#expense_id :first").attr("selected", "selected");
        $("#expense_id :first").attr("disabled", "disabled");

    </script>
@endsection
