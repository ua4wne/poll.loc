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
            <h2 class="text-header text-center">{{ $head }}</h2>
            @if($content)
                <div class="x_panel">
                    <p>Всего подключено участков: {{ $itog }}</p>
                    {!! $content !!}
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection
