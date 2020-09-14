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
        <div class="col-md-12">
            <h2 class="text-center">{{ $head }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('connectionAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="my_datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Территория</th>
                            <th>№ участка</th>
                            <th>Юрлицо</th>
                            <th>Дата подключения</th>
                            <th>Тип подключения</th>
                            <th>Комментарий</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->renter->place->name }}</td>
                                <td>{{ $row->renter->area }}</td>
                                <td>{{ $row->renter->title }}</td>
                                <td>{{ $row->date_on }}</td>
                                <td>
                                    @if($row->type == 'dynamic')
                                        <span role="button" class="label label-success">Динамический IP</span>
                                    @else
                                        <span role="button" class="label label-info">Статический IP</span>
                                    @endif
                                </td>
                                <td>{{ $row->comment }}</td>

                                <td style="width:100px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись', 'id'=>$row->id]) !!}
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
    <script src="/js/jquery.dataTables.min.js"></script>
    @include('confirm')
    <script>

        $('#my_datatable').DataTable( {
            "order": [ 3, "desc" ]
        } );

        $(document).on ({
            click: function() {
                let id = $(this).parent().next().next().find('.btn_del').attr("id");
                let obj = $(this).parent();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('switchType') }}',
                    data: {'id':id,'type':'static'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            obj.html('<span role="button" class="label label-info">Статический IP</span>');
                        }
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            }
        }, ".label-success" );

        $(document).on ({
            click: function() {
                let id = $(this).parent().next().next().find('.btn_del').attr("id");
                let obj = $(this).parent();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('switchType') }}',
                    data: {'id':id,'type':'dynamic'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            //location.reload(true);
                            obj.html('<span role="button" class="label label-success">Динамический IP</span>');
                        }
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            }
        }, ".label-info" );

        $(document).on ({
            click: function() {
                let id = $(this).attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('deleteConnection') }}',
                        data: {'id':id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res){
                            //alert(res);
                            if(res=='OK')
                                $('#'+id).parent().parent().parent().hide();
                            if(res=='NO')
                                alert('Выполнение операции запрещено!');
                        }
                    });
                }
                else {
                    return false;
                }
            }
        }, ".btn_del" );

    </script>
@endsection
