@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    @if (session('error'))
        <div class="row">
            <div class="alert alert-error panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('error') }}
            </div>
        </div>
    @endif
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
        <div class="modal fade" id="exportWork" tabindex="-1" role="dialog" aria-labelledby="exportWork"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Выберите дату</h4>
                    </div>
                    {!! Form::open(array('route' => 'uploadWork','method'=>'POST')) !!}
                    <div class="modal-body">

                        <div class="form-group">
                            {!! Form::label('data', 'Дата:',['class'=>'col-xs-2 control-label']) !!}
                            <div class="col-xs-8">
                                {{ Form::date('data', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control']) }}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        {!! Form::submit('Создать',['class'=>'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="modal fade" id="importWork" tabindex="-1" role="dialog" aria-labelledby="importWork"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Загрузка данных из Excel</h4>
                    </div>
                    {!! Form::open(array('route' => 'importWork','method'=>'POST','files'=>'true')) !!}
                    <div class="modal-body">

                        <div class="form-group">
                            {!! Form::label('file', 'Файл:',['class'=>'col-xs-2 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::file('file', ['class' => 'filestyle','data-buttonText'=>'Выберите файл Excel','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран"]) !!}
                                {!! $errors->first('file', '<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        {!! Form::submit('Загрузить',['class'=>'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h2 class="text-center">{{ $title }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('workAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green"
                                                                                aria-hidden="true"></i> Новая запись
                        </button>
                    </a>
                    <a href="#" id="upload">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exportWork">
                            <i class="fa fa-upload" aria-hidden="true"></i> Выгрузить в шаблон
                        </button>
                    </a>
                    <a href="#" id="download">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#importWork"><i class="fa fa-download" aria-hidden="true"></i> Загрузить из
                            шаблона
                        </button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Юрлицо</th>
                            <th>Дата</th>
                            <th>10:00-11:00</th>
                            <th>11:00-12:00</th>
                            <th>12:00-13:00</th>
                            <th>13:00-14:00</th>
                            <th>14:00-15:00</th>
                            <th>15:00-16:00</th>
                            <th>16:00-17:00</th>
                            <th>17:00-18:00</th>
                            <th>18:00-19:00</th>
                            <th>19:00-20:00</th>
                            <th>20:00-21:00</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->renter->name }}</td>
                                <td>{{ $row->data }}</td>
                                <td>{{ $row->period1 }}</td>
                                <td>{{ $row->period2 }}</td>
                                <td>{{ $row->period3 }}</td>
                                <td>{{ $row->period4 }}</td>
                                <td>{{ $row->period5 }}</td>
                                <td>{{ $row->period6 }}</td>
                                <td>{{ $row->period7 }}</td>
                                <td>{{ $row->period8 }}</td>
                                <td>{{ $row->period9 }}</td>
                                <td>{{ $row->period10 }}</td>
                                <td>{{ $row->period11 }}</td>

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись','id'=>$row->id]) !!}
                                    </div>
                                    {!! Form::close() !!}
                                </td>
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
        $(document).ready(function () {
            var options = {
                'backdrop': 'true',
                'keyboard': 'true'
            }
            $('#basicModal').modal(options);
        });

        $('.btn_del').click(function () {
            let id = $(this).attr("id");
            let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteWork') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'OK')
                            $('#' + id).parent().parent().parent().hide();
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            } else {
                return false;
            }
        });

    </script>
@endsection
