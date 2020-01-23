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
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('status') }}
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">{{ $head }}</h2>
            @if($actions)
                <div class="x_content">
                <a href="{{route('actionAdd')}}">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                </a>
                </div>
            <div class="x_panel">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Системный код</th>
                        <th>Наименование</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($actions as $k => $action)

                        <tr>
                            <td>{{ $action->code }}</td>
                            <td>{{ $action->name }}</td>

                            <td style="width:110px;">
                                {!! Form::open(['url'=>route('actionEdit',['id'=>$action->id]), 'class'=>'form-horizontal','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                {{ method_field('DELETE') }}
                                <div class="form-group" role="group">
                                    {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger','type'=>'submit','title'=>'Удалить запись']) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                {{ $actions->links() }}
            </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
@endsection
