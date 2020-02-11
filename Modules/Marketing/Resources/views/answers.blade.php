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
        <div class="col-md-12">
            <h2 class="text-center">{{ $title }}</h2>
            @if($rows)
                <div class="x_content">
                    <a href="{{route('answerAdd',[$question->id])}}">
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новый ответ</button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="my_datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Вопрос</th>
                            <th>Ответ</th>
                            <th>Вид HTML</th>
                            <th>Справочник</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $k => $row)

                            <tr>
                                <td>{{ $row->question->name }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{!! $row->htmlcode !!}</td>
                                <td>{{ $row->source }}</td>

                                <td style="width:70px;">
                                    <div class="form-group" role="group">
                                        {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn_del','type'=>'button','title'=>'Удалить запись','id'=>$row->id]) !!}
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
            "order": [[ 0, "asc" ]]
        } );

        $('.btn_del').click(function(){
            let id = $(this).attr("id");
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
                $("#loader").hide();
                return false;
            }
            $("#loader").hide();
        });

    </script>
@endsection
