@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active">Доступ запрещен</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="row">
        @if(empty($exception->getMessage()))
            <h1 class="text-center">Доступ к странице запрещен!</h1>
        @else
            <h1 class="text-center">{{ $exception->getMessage() }}</h1>
        @endif
        <img src="/images/ops.jpg" class="img-responsive center-block">
    </div>
    </div>

@endsection

@section('user_script')

@endsection