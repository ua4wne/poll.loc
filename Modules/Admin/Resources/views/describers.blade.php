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
            @if($rows)
                <div class="x_content">
                    <a href="{{route('describerAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>E-mail</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->email }}</td>
                                @if($row->status)
                                    <td><span role="button" class="label label-success" id="{{ $row->id }}">Активен</span></td>
                                @else
                                    <td><span role="button" class="label label-danger" id="{{ $row->id }}">Не активен</span></td>
                                @endif

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись']) !!}
                                    </div>
                                    {!! Form::close() !!}
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script>

        $('.label-success').click(function(){
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '{{ route('switchStatus') }}',
                data: {'id':id,'status':0},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert(res);
                    if(res=='OK'){
                        location.reload(true);
                    }
                    if(res=='NO')
                        alert('Выполнение операции запрещено!');
                    else
                        alert('Ошибка операции.');
                }
            });
        });

        $('.label-danger').click(function(){
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '{{ route('switchStatus') }}',
                data: {'id':id,'status':1},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert(res);
                    if(res=='OK'){
                        location.reload(true);
                    }
                    if(res=='NO')
                        alert('Выполнение операции запрещено!');
                    else
                        alert('Ошибка операции.');
                }
            });
        });

        $('.btn_del').click(function(){
            let id = $(this).parent().parent().prevAll().eq(0).find('span').attr("id");
            let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteDescriber') }}',
                    data: {'id':id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            $('#'+id).parent().parent().hide();
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            }
            else {
                return false;
            }
        });

    </script>
@endsection
