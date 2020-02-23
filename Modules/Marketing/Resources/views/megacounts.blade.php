@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('megacounts') }}">{{ $title }}</a></li>
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
        <div class="modal fade" id="editCount" tabindex="-1" role="dialog" aria-labelledby="editCount" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Редактирование записи</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'edit_count','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-10">
                                {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'count_id']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('serial-number','MAC-адрес:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('serial-number',old('serial-number'),['class' => 'form-control','disabled'=>'disabled','id'=>'serial'])!!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('ip-address','IP адрес:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('ip-address',old('ip-address'),['class' => 'form-control','placeholder'=>'Введите адрес IP','required'=>'required','id'=>'ip','size'=>'16'])!!}
                                {!! $errors->first('ip-address', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','id'=>'name','size'=>'100'])!!}
                                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('descr','Описание:',['class' => 'col-xs-3 control-label'])   !!}
                            <div class="col-xs-8">
                                {!! Form::textarea('descr',old('descr'),['class' => 'form-control','placeholder'=>'Введите описание','rows' => 4, 'cols' => 54,'id'=>'descr'])!!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('place_id', 'Территория:',['class'=>'col-xs-3 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::select('place_id', $placesel, old('place_id'),['class' => 'form-control','required' => 'required','id'=>'place_id']); !!}
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
                <div class="x_panel">
                    <table id="my_datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>MAC адрес</th>
                            <th>IP адрес</th>
                            <th>Наименование</th>
                            <th>Описание</th>
                            <th>Территория</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->serial_number }}</td>
                                <td>{{ $row->ip_address }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->descr }}</td>
                                <td>{{ $row->place->name }}</td>
                                @if($row->status)
                                    <td><span role="button" class="label label-success">В норме</span></td>
                                @else
                                    <td><span role="button" class="label label-danger">Не известно</span></td>
                                @endif

                                <td style="width:70px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-edit fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-success btn_edit','type'=>'button','title'=>'Редактироватьть запись','data-toggle'=>'modal','data-target'=>'#editCount','id'=>$row->id]) !!}
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
            "order": [[ 1, "asc" ]]
        } );

        $('#save').click(function(e){
            e.preventDefault();
            let error=0;
            $("#edit_count").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editMegacount') }}',
                    data: $('#edit_count').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            location.reload(true);
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                        if(res=='ERR')
                            alert('Ошибка обновления данных.');
                    }
                });
            }
        });

        $('.btn_edit').click(function(){
            let id = $(this).attr("id");
            let mac = $(this).parent().parent().prevAll().eq(5).text();
            let ip = $(this).parent().parent().prevAll().eq(4).text();
            let name = $(this).parent().parent().prevAll().eq(3).text();
            let descr = $(this).parent().parent().prevAll().eq(2).text();
            let placeid = $(this).parent().parent().prevAll().eq(1).text();

            $('#serial').val(mac);
            $('#ip').val(ip);
            $('#name').val(name);
            $('#descr').val(descr);
            $('#count_id').val(id);
            $("#place_id :contains('"+placeid+"')").attr("selected", "selected");
        });
    </script>
@endsection
