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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <h2 class="text-center">{{ $head }}</h2>
        @if($rows)
            <div class="x_content">
            <button type="button" class="btn btn-danger btn-sm" id="all_delete" title = "Очистить журнал"><i class="fa fa-trash" aria-hidden="true"></i> Очистить лог</button>
            </div>
        <div class="x_panel">
            <table id="my_datatable" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Логин</th>
                    <th>Пользователь</th>
                    <th>IP адрес</th>
                    <th>Тип события</th>
                    <th>Сообщение</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>

                @foreach($rows as $k => $row)

                    <tr>
                        <th>{{ $row->user->login }}</th>
                        <th>{{ $row->user->name }}</th>
                        <td>{{ $row->ip }}</td>
                        <td>{{ $row->type }}</td>
                        <td>{!! $row->text !!}</td>
                        <td>{{ $row->created_at }}</td>

                        <td style="width:70px;">
                                <div class="form-group" role="group">
                                    <button class="btn btn-danger btn-sm row_delete" id="{{ $row->id }}" type="button" title = "Удалить запись"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>
                                </div>
                            </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script src="/js/jquery.dataTables.min.js"></script>
    <script>
        $('#my_datatable').DataTable( {
            "order": [ 4, "desc" ]
        } );

        $('.row_delete').click(function(){
            $('#loader').show();
            let id = $(this).attr("id");
            let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('eventDelete') }}',
                    data: {'id':id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            $('#'+id).parent().parent().parent().hide();
                        if(res=='ERR')
                            alert('Ошибка выполения операции!');
                    }
                });
            }
            else {
                $('#loader').hide();
                return false;
            }
            $('#loader').hide();
        });

        $('#all_delete').click(function(){
            $('#loader').show();
            let x = confirm("Журнал событий будет очищен от всех информационных записей. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('eventDelete') }}',
                    data: {'id':'all'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        //alert(res);
                        if(res=='OK')
                            window.location.reload();
                        if(res=='ERR')
                            alert('Ошибка выполения операции!');
                    }
                });
            }
            else {
                return false;
                $('#loader').hide();
            }
            $('#loader').hide();
        });

    </script>
@endsection
