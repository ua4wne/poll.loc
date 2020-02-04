@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('main-counter') }}">{{ $head }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {!! session('status') !!}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="row">
            <div class="alert alert-danger panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {!! session('error') !!}
            </div>
        </div>
    @endif
    <div class="col-md-12">
        <h2 class="text-center">{{ $title }}</h2>
        <div class="x_content">
            <a href="{{route('add_mainVal')}}">
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
            </a>
        </div>
    <div class="x_panel">
        {!! $content !!}
    </div>
    </div>
    </div>
@endsection
