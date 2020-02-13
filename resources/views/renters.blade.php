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
        <div class="modal fade" id="editRenter" tabindex="-1" role="dialog" aria-labelledby="editRenter" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_renter','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'renter_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('title','Юрлицо:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Укажите юрлицо','required'=>'required','size'=>'100','id'=>'title'])!!}
                                {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','size'=>'100','id'=>'name'])!!}
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('area','Участок:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('area',old('area'),['class' => 'form-control','placeholder'=>'Введите № участка','required'=>'required','size'=>'20','id'=>'area'])!!}
                                {!! $errors->first('area', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('agent','Представитель:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('agent',old('agent'),['class' => 'form-control','placeholder'=>'Укажите ФИО представителя','required'=>'required','size'=>'50','id'=>'agent'])!!}
                                {!! $errors->first('agent', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('encounter','Счетчик:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('encounter',old('encounter'),['class' => 'form-control','placeholder'=>'Укажите счетчик','required'=>'required','size'=>'20','id'=>'encounter'])!!}
                                {!! $errors->first('encounter', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('koeff','Коэффициент:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('koeff',old('koeff'),['class' => 'form-control','placeholder'=>'Укажите коэффициент','required'=>'required','value'=>'5.8','id'=>'koeff'])!!}
                                {!! $errors->first('koeff', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('place_id', 'Территория:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('place_id', $placesel, old('place_id'),['class' => 'form-control','required' => 'required','id'=>'place_id']); !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('division_id', 'Закреплен за:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('division_id', $divsel, old('division_id'),['class' => 'form-control','required' => 'required','id'=>'division_id']); !!}
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
                    <a href="{{route('renterAdd')}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                    </a>
                    <a class="btn btn-sm btn-success" href="{{route('view_renters',['status'=>1])}}">
                        <i class="fa fa-check"></i> Действующие арендаторы</a>
                    <a class="btn btn-sm btn-warning" href="{{route('view_renters',['status'=>0])}}">
                        <i class="fa fa-remove"></i> Не действующие арендаторы</a>
                </div>
                <div class="x_panel">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Юрлицо</th>
                            <th>Наименование</th>
                            <th>Представитель</th>
                            <th>Участок</th>
                            <th>Счетчик</th>
                            <th>Коэффициент</th>
                            <th>Территория</th>
                            <th>Статус</th>
                            <th>Закреплен за</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->title }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->agent }}</td>
                                <td>{{ $row->area }}</td>
                                <td>{{ $row->encounter }}</td>
                                <td>{{ $row->koeff }}</td>
                                <td>{{ $row->place->name }}</td>
                                <td>
                                    @if($row->status)
                                        <span role="button" class="label label-success">Действующий</span>
                                    @else
                                        <span role="button" class="label label-danger">Не действующий</span>
                                    @endif
                                </td>
                                <td>{{ $row->division->name }}</td>

                                <td style="width:110px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn-sm btn_edit','type'=>'button','title'=>'Редактироватьть запись','data-toggle'=>'modal','data-target'=>'#editRenter','id'=>$row->id]) !!}
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
    @include('confirm')
    <script src="/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function(){
            let options = {
                'backdrop' : 'true',
                'keyboard' : 'true'
            }
            $('#basicModal').modal(options);
        });

        $('#save').click(function(e){
            e.preventDefault();
            let error=0;
            $("#edit_renter").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editRenter') }}',
                    data: $('#edit_renter').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            location.reload(true);
                        if(res=='ERR')
                            alert('Ошибка обновления данных.');
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            }
        });

        $('.label-success').click(function(){
            let id = $(this).parent().next().next().find('.btn_edit').attr("id");
            $.ajax({
                type: 'POST',
                url: '{{ route('switchRenter') }}',
                data: {'id':id,'active':0},
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
            let id = $(this).parent().next().next().find('.btn_edit').attr("id");
            $.ajax({
                type: 'POST',
                url: '{{ route('switchRenter') }}',
                data: {'id':id,'active':1},
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

        $('.btn_edit').click(function(){
            let id = $(this).attr("id");
            let division = $(this).parent().parent().prevAll().eq(0).text();
            let place = $(this).parent().parent().prevAll().eq(2).text();
            let koeff = $(this).parent().parent().prevAll().eq(3).text();
            let encount = $(this).parent().parent().prevAll().eq(4).text();
            let area = $(this).parent().parent().prevAll().eq(5).text();
            let agent = $(this).parent().parent().prevAll().eq(6).text();
            let name = $(this).parent().parent().prevAll().eq(7).text();
            let title = $(this).parent().parent().prevAll().eq(8).text();

            $("division_id:selected").removeAttr("selected");
            $("place_id:selected").removeAttr("selected");

            $('#koeff').val(koeff);
            $('#encounter').val(encount);
            $('#area').val(area);
            $('#agent').val(agent);
            $('#title').val(title);
            $('#name').val(name);
            $('#renter_id').val(id);

            $("#division_id :contains('"+division+"')").attr("selected", "selected");
            $("#place_id :contains('"+place+"')").attr("selected", "selected");
        });

        $('.btn_del').click(function(){
            let id = $(this).prev().attr("id");
            let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('deleteRenter') }}',
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
