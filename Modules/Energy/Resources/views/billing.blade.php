@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('main') }}">{{ $head }}</a></li>
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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">{{ $title }}</h2>
            <p class="text-center text-info">За {{ $month }} месяц {{ $year }} года</p>
            @if($rows)
                <p class="text-info"><b>ИТОГО: {{ $delta }} кВт. На сумму в размере {{ $price }} рублей</b></p>
                <div class="x_content">
                    <a class="btn btn-primary" href="#" id="send-mail">
                        <i class="fa fa-envelope-o"></i> Отправить</a>
                    <a class="btn btn-primary" href="{{ route('energy_report') }}" id="report">
                        <i class="fa fa-file-excel-o"></i> Скачать</a>
                </div>
                <div class="x_panel">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Юрлицо</th>
                            <th>Текущие показания, кВт.</th>
                            <th>Потребление, кВт.</th>
                            <th>Цена, руб.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rows as $k => $row)
                            <tr>
                                <td>{{ $row->renter($row->renter_id)->title }} ({{ $row->renter($row->renter_id)->area }})</td>
                                <td>{{ $row->encount }}</td>
                                <td>{{ $row->delta }}</td>
                                <td>{{ $row->price }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script src="/js/jquery.dataTables.min.js"></script>

    <script>
        $('#send-mail').click(function(e){
            e.preventDefault();
            $("#loader").show();
            alert('Send mail');
            $("#loader").hide();
        });

    </script>

@endsection
