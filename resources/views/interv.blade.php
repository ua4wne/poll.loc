@extends('layouts.poll')

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li>{{ $name }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div class="row">
                <a href="{{ route('singleForm') }}" class="btn btn-app btn-group-justified border-blue">
                    <i class="fa fa-file-text-o blue"></i> Отдельные анкеты
                </a>

                <a href="{{ route('groupForm') }}" class="btn btn-app btn-group-justified border-blue">
                    <i class="fa fa-book blue"></i> Группа анкет
                </a>

            </div>
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

