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
        <div class="modal fade" id="editSupplier" tabindex="-1" role="dialog" aria-labelledby="editSupplier" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_supplier','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'supplier_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','id'=>'name'])!!}
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
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
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">{{ $head }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('supplierAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="my_datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->name }}</td>

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn-sm btn_edit','type'=>'button','title'=>'Редактироватьть запись','data-toggle'=>'modal','data-target'=>'#editSupplier','id'=>$row->id]) !!}
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись']) !!}
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
        $(document).ready(function(){
            let options = {
                'backdrop' : 'true',
                'keyboard' : 'true'
            }
            $('#basicModal').modal(options);
        });

        $('#my_datatable').DataTable( {
            "order": [[ 0, "desc" ]]
        } );

        $('#save').click(function(e){
            e.preventDefault();
            let error=0;
            $("#edit_supplier").find(":input").each(function() {// проверяем каждое поле ввода в форме
                if($(this).attr("required")=='required'){ //обязательное для заполнения поле формы?
                    if(!$(this).val()){// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error=1;// определяем индекс ошибки
                    }
                    else{
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if(error){
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '{{ route('editSupplier') }}',
                    data: $('#edit_supplier').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            location.reload(true);
                        if(res=='ERR')
                            alert('Ошибка обновления данных.');
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                        else{
                            alert('Ошибка валидации данных');
                        }
                    }
                });
            }
        });

        $('.btn_edit').click(function(){
            let id = $(this).attr("id");
            let name = $(this).parent().parent().prevAll().eq(0).text();

            $('#name').val(name);
            $('#supplier_id').val(id);
        });

        $('.btn_del').click(function(){
            let id = $(this).prev().attr("id");
            let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteSupplier') }}',
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
        });

    </script>
@endsection
