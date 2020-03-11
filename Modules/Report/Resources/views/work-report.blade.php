@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('work-report') }}">Присутствие на выставке</a></li>
        <li class="active">{{ $title }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
            @if($firm)
                <p class="text-header text-center">За период с {{ $start }} по {{ $finish }}</p>
            @else
                <p class="text-header text-center">За прошедший месяц</p>
            @endif
                <div class="x_panel">
                    {!! $firm !!}
                    {!! $content !!}
                </div>
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

