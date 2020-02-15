@extends('layouts.main')

@section('content')
    <!-- page content -->
    <div class="modal fade" id="viewVisit" tabindex="-1" role="dialog" aria-labelledby="viewVisit" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Посещение выставки за текущий месяц</h4>
                </div>
                <div class="modal-body">
                    {!! $visitors !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewTimeWork" tabindex="-1" role="dialog" aria-labelledby="viewTimeWork"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Работа домов за текущий месяц</h4>
                </div>
                <div class="modal-body">
                    {!! $worktime !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewCount" tabindex="-1" role="dialog" aria-labelledby="viewCount" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Данные со счетчиков посетителей Megacount</h4>
                </div>
                <div class="modal-body">
                    <span
                        class="label label-primary">Функционал в разработке! Скоро здесь появятся реальные данные.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewEnergy" tabindex="-1" role="dialog" aria-labelledby="viewEnergy" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Энергопотребление за текущий месяц</h4>
                </div>
                <div class="modal-body">
                    {!! $energy !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default x_panel">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> <span>Графики посещений выставки</span>
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            Действия
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li id="view_table"><a href="#">Показать таблицу</a>
                            </li>
                            <li id="view_graph"><a href="#">Показать графики</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="/">Обновить</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.panel-heading -->
        <div class="col-md-7  col-sm-7 col-xs-12 h-panel">
            <div class="x_panel">
                <div class="x_title">
                    <h2>График посещений выставки за текущий месяц</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="visitor-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5  col-sm-5 col-xs-12 h-panel">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Динамика посещений выставки в сравнении с прошлым годом</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="visitor-bar"></div>
                </div>
            </div>
        </div>
        <div class="panel-body col-md-12 x_content">
            <div id="vtbl"></div>
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/raphael.min.js"></script>
    <script src="/js/morris.min.js"></script>

    <script>

        $.ajax({
            type: 'POST',
            url: '{{ route('visit-report') }}',
            data: {'start': 'start', 'finish': 'finish', 'group': 'not'},
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                //alert(res);
                $("#visitor-chart").empty();
                Morris.Bar({
                    element: 'visitor-chart',
                    data: JSON.parse(res),
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Кол-во'],
                    barColors: ["#26b99a"],
                });
                $.ajax({
                    type: 'POST',
                    url: '{{ route('compare-bar') }}',
                    data: {'start': 'start', 'finish': 'finish'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#visitor-bar").empty();
                        Morris.Bar({
                            element: 'visitor-bar',
                            data: JSON.parse(res),
                            xkey: 'y',
                            ykeys: ['a'],
                            labels: ['Кол-во'],
                            barColors: ["#34495e"],
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });

        $('#view_table').click(function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('visitTable') }}',
                data: {'start': 'start', 'finish': 'finish'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $("#visitor-chart").hide();
                    $(".h-panel").hide();
                    $("#visitor-bar").hide();
                    $('.fa-fw').removeClass('fa-bar-chart-o');
                    $('.fa-fw').addClass('fa-table');
                    $('.fa-fw').next().text('Таблица посещений выставки за текущий месяц');
                    $("#vtbl").show();
                    $("#vtbl").addClass('x_panel');
                    $("#vtbl").html(res);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });

        $('#view_graph').click(function (e) {
            e.preventDefault();
            $("#vtbl").removeClass('x_panel');
            $("#vtbl").hide();
            $('.fa-fw').removeClass('fa-table');
            $('.fa-fw').addClass('fa-bar-chart-o');
            $('.fa-fw').next().text('Графики посещений выставки');
            $(".h-panel").show();
            $("#visitor-chart").show();
            $("#visitor-bar").show();
        });
    </script>

@endsection
