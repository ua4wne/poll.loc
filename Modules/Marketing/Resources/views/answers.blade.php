@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('forms') }}">Анкеты</a></li>
        <li><a href="{{ route('questions',[$question->form->id]) }}">{{ $question->form->name }}</a></li>
        <li><a href="{{ route('answers',[$question->id]) }}">{{ $question->name }}</a></li>
        <li class="active">Список ответов</li>
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
        <div class="modal fade" id="editAnswer" tabindex="-1" role="dialog" aria-labelledby="editAnswer" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_row','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'answer_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите ответ на вопрос анкеты',
                                'required'=>'required','maxlength'=>'100','id'=>'name'])!!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('visibility', 'Видимость:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('visibility', ['1'=>'Да','0'=>'Нет'], old('visibility'),['class' => 'form-control','required' => 'required','id'=>'visibility']); !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('htmlcode', 'Тип ответа:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('htmlcode', $htmlsel, old('htmlcode'),['class' => 'form-control','id'=>'htmlcode']); !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('jump','Переход к вопросу №:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('jump',old('jump'),['class' => 'form-control','placeholder'=>'Введите номер вопроса анкеты','maxlength'=>'2','id'=>'jump'])!!}
                                {!! $errors->first('jump', '<p class="text-danger">:message</p>') !!}
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
            <h2 class="text-center">{{ $title }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('answerAdd',[$question->id])}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новый ответ</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Ответ</th>
                            <th>Вид HTML</th>
                            <th>Видимость</th>
                            <th>Переход к вопросу №</th>
                            <th>Справочник</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr id="{{ $row->id }}">
                                <td>{{ $row->name }}</td>
                                <td>{!! $row->htmlcode !!}</td>
                                <td>
                                    @if($row->visibility)
                                        <span role="button" class="label label-success">Включена</span>
                                    @else
                                        <span role="button" class="label label-danger">Отключена</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->jump)
                                    {{ $row->jump }}
                                    @else
                                        По порядку
                                    @endif
                                </td>
                                <td>{{ $row->source }}</td>

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn_edit','type'=>'button','title'=>'Редактировать запись','data-toggle'=>'modal','data-target'=>'#editAnswer']) !!}
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
{{--    <script src="/js/jquery.dataTables.min.js"></script>--}}
    @include('confirm')
    <script>
        // $('#my_datatable').DataTable( {
        //     "order": [[ 0, "asc" ]]
        // } );

        $('.label-success').click(function(){
            let id = $(this).parent().parent().attr('id');
            $.ajax({
                type: 'POST',
                url: '{{ route('switchAnswer') }}',
                data: {'id':id,'visibility':0},
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
                }
            });
        });

        $('.label-danger').click(function(){
            let id = $(this).parent().parent().attr('id');
            $.ajax({
                type: 'POST',
                url: '{{ route('switchAnswer') }}',
                data: {'id':id,'visibility':1},
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
                }
            });
        });

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr('id');
                let x = confirm("Ответ будет удален безвозвратно со всей имеющейся статистикой. Продолжить (Да/Нет)?");
                $("#loader").show();
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('deleteAnswer') }}',
                        data: {'id':id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res){
                            //alert(res);
                            if(res=='OK')
                                $('#'+id).hide();
                            if(res=='NO')
                                alert('Выполнение операции запрещено!');
                        },
                        error: function (xhr, response) {
                            alert('Error! ' + xhr.responseText);
                        }
                    });
                }
                else {
                    $("#loader").hide();
                    return false;
                }
                $("#loader").hide();
            }
        }, ".btn_del");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr('id');
                let name = $(this).parent().parent().prevAll().eq(4).text().trim();
                let jump = $(this).parent().parent().prevAll().eq(1).text().trim();
                $('#answer_id').val(id);
                $('#name').val(name);
                $('#jump').val('');
                if($.isNumeric(jump))
                    $('#jump').val(jump);
            }
        }, ".btn_edit");

        $('#save').click(function(e){
            e.preventDefault();
            let error=0;
            $("#edit_row").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editAnswer') }}',
                    data: $('#edit_row').serialize(),
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

    </script>
@endsection
