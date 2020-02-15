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
    @if (session('error'))
        <div class="row">
            <div class="alert alert-error panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('error') }}
            </div>
        </div>
    @endif
    <div class="row">
        <div class="modal fade" id="importVisitor" tabindex="-1" role="dialog" aria-labelledby="importVisitor"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Загрузка данных из Excel</h4>
                    </div>
                    {!! Form::open(array('route' => 'importVisitor','method'=>'POST','files'=>'true')) !!}
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
            <h2 class="text-center">{{ $head }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('visitAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green"
                                                                                aria-hidden="true"></i> Новая запись
                        </button>
                    </a>
                    <a href="{{ route('uploadVisit') }}" id="upload">
                        <button type="button" class="btn btn-info btn-sm"><i class="fa fa-upload"
                                                                             aria-hidden="true"></i> Выгрузить в шаблон
                        </button>
                    </a>
                    <a href="#" id="download">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#importVisitor"><i class="fa fa-download" aria-hidden="true"></i> Загрузить
                            из шаблона
                        </button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Период времени</th>
                            <th>Кол-во посетителей</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->data }}</td>
                                <td>{{ $row->hours }}</td>
                                <td>{{ $row->ucount }}</td>

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
                    url: '{{ route('deleteVisit') }}',
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
