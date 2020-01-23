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
            <div class="modal fade" id="actions" tabindex="-1" role="dialog" aria-labelledby="roleAction" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title" id="role-title">Разрешения для роли</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'addActions','class'=>'form-horizontal','method'=>'POST']) !!}

                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'role_id']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 col-sm-4 col-xs-12 control-label">Выберите разрешения</label>

                                <div class="col-md-8 col-sm-8 col-xs-12" id="sel_roles">
                                    @foreach($actions as $action)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="actions[]" value="{{ $action->code }}" id="{{ $action->code }}"> {{ $action->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="save">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="text-center">{{ $head }}</h2>
            @if($roles)
                <div class="x_content">
                <a href="{{route('roleAdd')}}">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                </a>
                </div>
            <div class="x_panel">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Символьный код</th>
                        <th>Наименование</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($roles as $k => $role)

                        <tr id="{{ $role->id }}">
                            <td>{{ $role->code }}</td>
                            <td>{{ $role->name }}</td>

                            <td style="width:110px;">
                                {!! Form::open(['url'=>route('roleEdit',['id'=>$role->id]), 'class'=>'form-horizontal','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                {{ method_field('DELETE') }}
                                <div class="form-group" role="group">
                                    <button class="btn btn-info btn-sm role" type="button" data-toggle="modal" data-target="#actions" title="Настройка разрешений"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i></button>
                                    {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger','type'=>'submit','title'=>'Удалить запись']) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                {{ $roles->links() }}
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
        $('#save').click(function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('addAction') }}',
                data: $('#addActions').serialize(),
                success: function(res){
                    //alert(res);
                    if(res=='OK')
                        location.reload(true);
                    if(res=='ERR')
                        alert('Ошибка обновления данных.');
                    if(res=='NO')
                        alert('Выполнение операции запрещено!');
                    else{
                        alert('Не выбрано ни одной роли!');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });

        $('.role').click(function(){
            var id = $(this).parent().parent().parent().parent().attr("id");
            var name = $(this).parent().parent().parent().prevAll().eq(0).text();
            $('#name').val(name);
            $('#role_id').val(id);
            $('#role-title').text('Разрешения для роли '+name);
            //снимем ранее взведенные чекбоксы
            $('input:checkbox:checked').each(function(){
                $(this).prop('checked', false);
            });
            $.ajax({
                async: false,
                type: 'POST',
                url: '{{ route('getAction') }}',
                data: {'id':id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert(res);
                    var arr = jQuery.parseJSON(res);
                    // переберём массив arr
                    $.each(arr,function(key,value){
                        $('#'+value.toString()).prop('checked', true);
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    </script>
@endsection
