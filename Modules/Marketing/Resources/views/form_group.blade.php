@extends('layouts.poll')

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
        <div class="col-md-offset-2 col-md-8">
            <p class="text-uppercase text-info">Собрано анкет {{ $ankets ?? '' }}</p>
            {!! Form::open(['url' => route('groupForm'),'class'=>'form-horizontal','method'=>'POST']) !!}
            <h2 class="text-center">{{ $title }}</h2>
            <div class="form-group">
                <label for="fgroup">Выберите группу анкет:</label>
                <select id="fgroup" class="form-control" required="" name="group_id">
                    {!! $content !!}
                </select>
            </div>
            <div class="form-group">
                {!! Form::button('Выбрать', ['class' => 'btn btn-primary','type'=>'submit']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection
