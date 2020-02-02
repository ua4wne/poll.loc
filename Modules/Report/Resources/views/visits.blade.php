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
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
                <div class="x_content">
                    {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('start', 'Начало периода:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {{ Form::date('start', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-').'01'),['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('finish', 'Конец периода:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {{ Form::date('finish', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('group', 'Тип отчета:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('group', ['not'=>'Без группировки','byday'=>'По дням недели','byweek'=>'По неделям','bymonth'=>'По месяцам'], old('group'),['class' => 'form-control','required' => 'required','id'=>'group']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-8">
                            {!! Form::button('<span class="fa  fa-bar-chart-o"></span> График', ['class' => 'btn btn-primary','type'=>'submit','id' => 'visit-report','name' => 'report','value' => 'report']) !!}
                            {!! Form::button('<span class="fa  fa-table"></span> Таблица', ['class' => 'btn btn-primary','type'=>'submit','id' => 'vtable','name' => 'vtable','value' => 'vtable']) !!}
                            {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'export','value' => 'export']) !!}
                            <!-- Раздельная кнопка -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary"><span class="fa  fa-bar-chart-o"></span> Аналитика</button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li id="view_year"><a href="#">Текущий год</a></li>
                                        <li id="view_all"><a href="#">За все время</a></li>
                                    </ul>
                                </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            <a href="#" onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
            <div class="x_panel" id="result">
                    <div id="chart_visit"></div>
                </div>
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/raphael.min.js"></script>
    <script src="/js/morris.min.js"></script>
    <script>

        $('#result').hide();
        $('.fa-plus-square-o').hide();

        $('#visit-report').click(function(e){
            e.preventDefault();
            $("#loader").show();
            var start = $('#start').val();
            var finish = $('#finish').val();
            var group = $('#group').val();
            var msg = '<h4>Динамика посещений выставки за период с '+start+' по '+finish+'</h4>';
            $.ajax({
                url: '{{ route('visit-report') }}',
                type: 'POST',
                data: {'start':start,'finish':finish,'group':group},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $('#result').show();
                    $("#chart_visit").empty();
                    if(group=='not'){
                        Morris.Line({
                            element: 'chart_visit',
                            data: JSON.parse(res),
                            xkey: 'y',
                            ykeys: ['a'],
                            labels: ['Кол-во']
                        });
                    }
                    else{
                        Morris.Bar({
                            element: 'chart_visit',
                            data: JSON.parse(res),
                            xkey: 'y',
                            ykeys: ['a'],
                            labels: ['Кол-во']
                        });
                    }

                    $(".text-center").html(msg);
                    $("#loader").hide();
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function(xhr, response){
                    alert('Error! '+ xhr.responseText);
                }
            });
            $('#loader').css('display:none');
        });

        $('#vtable').click(function(e){
            e.preventDefault();
            $("#loader").show();
            var start = $('#start').val();
            var finish = $('#finish').val();
            var msg = '<h4>Таблица посещений выставки за период с '+start+' по '+finish+'</h4>';
            $.ajax({
                url: '{{ route('visitTable') }}',
                type: 'POST',
                data: {'start':start,'finish':finish},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $('#result').show();
                    $("#chart_visit").html(res);
                    $(".text-center").html(msg);
                    $("#loader").hide();
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function(xhr, response){
                    alert('Error! '+ xhr.responseText);
                }
            });
            $('#loader').css('display:none');
        });

        $('#view_year').click(function(e){
            e.preventDefault();
            $("#loader").show();
            $.ajax({
                url: '{{ route('analise') }}',
                type: 'POST',
                data: {'action':'year'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $("#chart_visit").empty();
                    $('#result').show();
                    Morris.Bar({
                        element: 'chart_visit',
                        data: JSON.parse(res),
                        xkey: 'y',
                        ykeys: ['a'],
                        labels: ['Кол-во']
                    });
                    $(".text-center").html('<h4>Динамика посещений выставки в течении текущего года</h4>');
                    $("#loader").hide();
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function(){
                    alert('Error!');
                }
            });
            $('#loader').css('display:none');
        });

        $('#view_all').click(function(e){
            e.preventDefault();
            $("#loader").show();
            $.ajax({
                url: '{{ route('analise') }}',
                type: 'POST',
                data: {'action':'all'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    $("#chart_visit").empty();
                    $('#result').show();
                    Morris.Bar({
                        element: 'chart_visit',
                        data: JSON.parse(res),
                        xkey: 'y',
                        ykeys: ['a'],
                        labels: ['Кол-во']
                    });
                    $(".text-center").html('<h4>Динамика посещений выставки за все время</h4>');
                    $("#loader").hide();
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function(){
                    alert('Error!');
                }
            });
            $('#loader').css('display:none');
        });

    </script>
@endsection
