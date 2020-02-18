@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('view-requests') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="row">
        <div class="alert alert-danger print-error-msg panel-remove" style="display:none">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <ul></ul>
        </div>
    </div>

    <h2 class="text-center">{{ $head }}</h2>
    @if($rows)
        <div class="x_content">
            <div class="btn-group">
                <a href="#">
                    <button type="button" class="btn btn-default btn-sm" id="del_btn"><i class="fa fa-trash red" aria-hidden="true"></i> Очистить лог</button>
                </a>
            </div>
        </div>

        <div class="x_panel">
            <table id="my_datatable" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Тип запроса</th>
                    <th>Тело запроса</th>
                    <th>Создан</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $k => $row)
                    <tr>
                        <td>{{ $row->type }}</td>
                        <td>{{ $row->request }}</td>
                        <td>{{ $row->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @endif
        </div>
        </div>
        <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.dataTables.min.js"></script>

    <script>
        var myDatatable = $('#my_datatable').DataTable({
            //"order": [[ 0, "desc" ]]
        });

        $('#del_btn').click( function(e){
            e.preventDefault();
            var x = confirm("Все записи лога будут удалены. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('requestDel') }}',
                    data: {'id': 'delete'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'OK') {
                            location.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            } else {
                return false;
            }
        });

    </script>
@endsection
