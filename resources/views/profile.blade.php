@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('profiles') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    @if (session('error'))
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="alert alert-error panel-remove">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif
    <!-- page content -->
    <div class="row">
        <div class="modal fade" id="editAvatar" tabindex="-1" role="dialog" aria-labelledby="editAvatar" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Выберите графический файл для аватара</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(array('route' => 'editAvatar','method'=>'POST','files'=>'true')) !!}
                        <div class="form-group">
                            {!! Form::hidden('old_image', $user->image) !!}
                            {!! Form::label('avatar', 'Файл:',['class'=>'col-xs-2 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::file('avatar', ['class' => 'filestyle','data-buttonText'=>'Выберите файл','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран"]) !!}
                                {!! $errors->first('avatar', '<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        {!! Form::submit('Загрузить',['class'=>'btn btn-primary','id'=>'upload']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="modal fade" id="editUser" tabindex="-1" role="dialog" aria-labelledby="editUser" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование данных профиля</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_user','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id',$user->id,['class' => 'form-control','required'=>'required','id'=>'user_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','ФИО:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',$user->name,['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','id'=>'name'])!!}
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('email','E-mail:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('email',$user->email,['class' => 'form-control','placeholder'=>'Введите e-mail','required'=>'required','id'=>'email'])!!}
                                {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('sex', 'Пол:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('sex', ['male'=>'Мужской','female'=>'Женский'], $user->sex,['class' => 'form-control','required' => 'required','id'=>'sex']); !!}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        {!! Form::submit('Обновить',['class'=>'btn btn-primary','id'=>'edit']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="modal fade" id="editPass" tabindex="-1" role="dialog" aria-labelledby="editPass" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Смена пароля</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="alert alert-warning col-xs-12"><p class="text-center">Пароль должен быть не менее 8 символов и содержать цифры и буквы большого и малого регистра!</p></div>
                        </div>
                        {!! Form::open(['url' => '#','id'=>'edit_pass','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id',$user->id,['class' => 'form-control','required'=>'required','id'=>'user_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('oldpass','Старый пароль:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::password('oldpass',['class' => 'form-control','required'=>'required','id'=>'oldpass'])!!}
                                {!! $errors->first('oldpass', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('newpass','Новый пароль:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::password('newpass',['class' => 'form-control','required'=>'required','id'=>'newpass'])!!}
                                {!! $errors->first('newpass', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('confpass','Подтверждение:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::password('confpass',['class' => 'form-control','required'=>'required','id'=>'confpass'])!!}
                                {!! $errors->first('confpass', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        {!! Form::submit('Обновить',['class'=>'btn btn-primary','id'=>'new-pass']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-offset-2 col-md-8">
            <h2 class="text-center">{{ $head }}</h2>
            @if($user)
                <div class="x_panel">
                    <div class="col-md-4">
                        @if(Auth::user()->image)
                            <img src="{{ $user->image }}" alt="...">
                        @else
                            <img src="/images/male.png" alt="...">
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-striped table-bordered">
                            <tr><th>Логин</th><td>{{ $user->login }}</td></tr>
                            <tr><th>ФИО</th><td>{{ $user->name }}</td></tr>
                            <tr><th>E-mail</th><td>{{ $user->email }}</td></tr>
                            @if($user->sex=='male')
                            <tr><th>Пол</th><td>Мужской</td></tr>
                            @else
                                <th>Пол</th><td>Женский</td>
                            @endif
                        </table>
                        <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editAvatar"><i class="fa fa-download blue fa-lg" aria-hidden="true"></i> Сменить аватар</button>
                        <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editUser"><i class="fa fa-edit blue fa-lg" aria-hidden="true"></i> Редактировать данные</button>
                        <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editPass"><i class="fa fa-key blue fa-lg" aria-hidden="true"></i> Сменить пароль</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')

    <script>
        $(document).ready(function () {
            var options = {
                'backdrop': 'true',
                'keyboard': 'true'
            }
            $('#basicModal').modal(options);
        });

        $('#edit').click( function(e){
            e.preventDefault();
            let error=0;
            $("#edit_user").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editProfile') }}',
                    data: $('#edit_user').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            alert('Данные профиля обновлены!');
                            location.reload();
                        }
                        if(res=='ERR')
                            alert('Ошибка обновления данных!');
                        if(res=='NO')
                            alert('Не корректные данные!');
                        if(res=='DBL')
                            alert('Указанный Вами e-mail занят! Введите другой e-mail.');
                    }
                });
            }
        });

        $('#new-pass').click( function(e){
            e.preventDefault();
            let error=0;
            $("#edit_pass").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('newPass') }}',
                    data: $('#edit_pass').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            alert('Новый пароль установлен!');
                            window.location.href = "/logout"
                        }
                        if(res=='ERR')
                            alert('Ошибка обновления данных!');
                        if(res=='NO')
                            alert('Новый пароль и его повтор не совпадают!');
                        if(res=='NOT')
                            alert('Старый пароль указан не верно!');
                        if(res=='BAD')
                            alert('Слишком слабый пароль!');
                    }
                });
            }
        });

    </script>
@endsection
