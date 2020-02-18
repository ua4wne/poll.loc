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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="modal fade" id="editAnket" tabindex="-1" role="dialog" aria-labelledby="editAnket" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_anket','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'form_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','id'=>'name'])!!}
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('is_active', 'Статус активности:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('is_active', ['0'=>'Не активная','1'=>'Активная'], old('is_active'),['class' => 'form-control','required' => 'required','id'=>'is_active']); !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('is_work', 'Видимость анкеты:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('is_work', ['0'=>'Отключена','1'=>'В работе'], old('is_work'),['class' => 'form-control','required' => 'required','id'=>'is_work']); !!}
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
        <div class="col-md-12">
            <h2 class="text-center">{{ $head }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('formAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая анкета</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="my_datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Статус активности</th>
                            <th>Видимость анкеты</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->name }}</td>
                                @if($row->is_active)
                                    <td><span role="button" class="label label-success">Активная</span></td>
                                @else
                                    <td><span role="button" class="label label-danger">Не активная</span></td>
                                @endif
                                @if($row->is_work)
                                    <td><span role="button" class="label label-success">В работе</span></td>
                                @else
                                    <td><span role="button" class="label label-danger">Отключена</span></td>
                                @endif

                                <td style="width:220px;">
                                    <div class="form-group" role="group">
                                        <a href="{{ route('questions',[$row->id]) }}"><button class="btn btn-info btn_qst" type="button" title="Вопросы анкеты"><i class="fa fa-list-ol fa-lg>" aria-hidden="true"></i></button></a>
                                        <a href="{{ route('form_view',[$row->id]) }}"><button class="btn btn-warning btn_view" type="button" title="Просмотр анкеты"><i class="fa fa-eye fa-lg>" aria-hidden="true"></i></button></a>
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn_edit','type'=>'button','title'=>'Редактироватьть запись','data-toggle'=>'modal','data-target'=>'#editAnket','id'=>$row->id]) !!}
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
            "order": [[ 0, "asc" ]]
        } );

        $('#save').click(function(e){
            e.preventDefault();
            let error=0;
            $("#edit_anket").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editForm') }}',
                    data: $('#edit_anket').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            location.reload(true);
                        if(res=='ERR')
                            alert('Ошибка обновления данных.');
                    }
                });
            }
        });

        $('.btn_edit').click(function(){
            let id = $(this).attr("id");
            let name = $(this).parent().parent().prevAll().eq(2).text();
            let isactive = $(this).parent().parent().prevAll().eq(1).text();
            let iswork = $(this).parent().parent().prevAll().eq(0).text();

            $('#name').val(name);
            $('#form_id').val(id);
            $("#is_active :contains('"+isactive+"')").attr("selected", "selected");
            $("#is_work :contains('"+iswork+"')").attr("selected", "selected");
        });

        $('.btn_del').click(function(){
            let id = $(this).prev().attr("id");
            let x = confirm("Выбранная анкета будет удалена безвозвратно со всей имеющейся статистикой. Продолжить (Да/Нет)?");
            $("#loader").show();
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteForm') }}',
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
                    },
                    error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
                });
            }
            else {
                return false;
            }
            $("#loader").hide();
        });

    </script>
@endsection
