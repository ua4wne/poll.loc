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
        <div class="modal fade" id="editCounter" tabindex="-1" role="dialog" aria-labelledby="editCounter" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_counter','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'counter_id']) !!}
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
                            {!! Form::label('text','Описание:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('text',old('text'),['class' => 'form-control','placeholder'=>'Описание счетчика','id'=>'text'])!!}
                                {!! $errors->first('text', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('koeff','Коэффициент:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('koeff',old('koeff'),['class' => 'form-control','placeholder'=>'Укажите коэффициент','required'=>'required','id'=>'koeff'])!!}
                                {!! $errors->first('koeff', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('tarif','Тариф:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('tarif',old('tarif'),['class' => 'form-control','placeholder'=>'Укажите тариф','required'=>'required','id'=>'tarif'])!!}
                                {!! $errors->first('tarif', '<p class="text-danger">:message</p>') !!}
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
            @if($ecounters)
                <div class="x_content">
                    <a href="{{route('own-ecounterAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Описание</th>
                            <th>Коэффициент</th>
                            <th>Тариф</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($ecounters as $k => $ecounter)

                            <tr>
                                <td>{{ $ecounter->name }}</td>
                                <td>{{ $ecounter->text }}</td>
                                <td>{{ $ecounter->koeff }}</td>
                                <td>{{ $ecounter->tarif }}</td>

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn-sm btn_edit','type'=>'button','title'=>'Редактироватьть запись','data-toggle'=>'modal','data-target'=>'#editCounter','id'=>$ecounter->id]) !!}
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись']) !!}
                                    </div>
                                    {!! Form::close() !!}
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                    {{ $ecounters->links() }}
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
        $(document).ready(function(){
            var options = {
                'backdrop' : 'true',
                'keyboard' : 'true'
            }
            $('#basicModal').modal(options);
        });

        $('#save').click(function(e){
            e.preventDefault();
            var error=0;
            $("#edit_counter").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editOwnEcounter') }}',
                    data: $('#edit_counter').serialize(),
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
            var id = $(this).attr("id");
            var tarif = $(this).parent().parent().prevAll().eq(0).text();
            var koeff = $(this).parent().parent().prevAll().eq(1).text();
            var text = $(this).parent().parent().prevAll().eq(2).text();
            var name = $(this).parent().parent().prevAll().eq(3).text();

            $('#tarif').val(tarif);
            $('#koeff').val(koeff);
            $('#text').val(text);
            $('#name').val(name);
            $('#counter_id').val(id);
        });

        $('.btn_del').click(function(){
            var id = $(this).prev().attr("id");
            var x = confirm("Выбраный счетчик будет удален. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteOwnEcounter') }}',
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
