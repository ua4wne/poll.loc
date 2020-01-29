@extends('layouts.main')

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
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">{{ $head }}</h2>
            @if($typesel)
                <div class="x_content">
                    {!! Form::open(['url' => route('it_costView'),'class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('year','Укажите год:',['class' => 'col-xs-2 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('year',$year,['class' => 'form-control','placeholder'=>'ГГГГ','required'=>'required','id'=>'year'])!!}
                            {!! $errors->first('year', '<p class="text-danger">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('type', 'Тип отчета:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('type', $typesel, old('type'),['class' => 'form-control','required' => 'required','id'=>'type']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-8">
                            {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','id'=>'main-report']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="x_panel" id="result">
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')

    <script>

        $('#result').hide();

    </script>
@endsection
