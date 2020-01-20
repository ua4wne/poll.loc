@extends('layouts.main')

@section('content')
    <!-- page content -->

        <div class="row">
            {!! $content !!}
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    {{--<script src="/js/raphael.min.js"></script>
    <script src="/js/morris.min.js"></script>

    <script>
        $.ajax({
            type: 'POST',
            url: '{{ route('balance_graph') }}',
            data: {'id':'graph'},
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                //alert(res);
                var obj = jQuery.parseJSON(res);
                $.each(obj,function(key,data) {
                    Morris.Bar({
                        element: data.org.toString(),
                        data: data.val,
                        xkey: 'x',
                        ykeys: ['y', 'z'],
                        labels: ['Приход', 'Расход']
                    });
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    </script>--}}

@endsection
