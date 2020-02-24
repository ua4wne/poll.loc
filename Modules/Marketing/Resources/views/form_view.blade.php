@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('forms') }}">Анкеты</a></li>
        <li class="active">{{ $title }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <h2 class="text-center">{{ $title }}</h2>
            {!! $content !!}
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
<script>
    $('.panel-info').hide();
    $('select option:selected').each(function(){
        this.selected=false;
    });
    $("select :first").attr("disabled", "disabled");
    $('#qpanel1').show();
    $('#save_btn').hide();
    $('#prev_btn').hide();

    let step = 1;
    let prev_step = 0;
    let next_step = 0;
    let qcnt = $('#qst_count').val();

    $('#next_btn').click(function(e){
        e.preventDefault();
        prev_step = step;
        if(step<qcnt){
            $('#qpanel'+prev_step).hide();
            step++;
            $('#qpanel'+step).show();
            let per = set_percent(step,qcnt);
            $('.progress-bar').css('width',per+'%');
            $('.progress-bar').text(per+'%');
        }
        if(step==qcnt){
            $('#save_btn').show();
            $('#next_btn').hide();
            $('.progress-bar').css('width','100%');
            $('.progress-bar').text('100%');
        }
        if(step == 2)
            $('#prev_btn').show();
    });

    $('#prev_btn').click(function(e){
        e.preventDefault();
        next_step = step;
        if(next_step > 1){
            $('#qpanel'+next_step).hide();
            step--;
            $('#qpanel'+step).show();
            let per = set_percent(step,qcnt);
            $('.progress-bar').css('width',per+'%');
            $('.progress-bar').text(per+'%');
        }
        if(step<qcnt){
            $('#save_btn').hide();
            $('#next_btn').show();
        }
        if(step == 1){
            $('#prev_btn').hide();
            $('.progress-bar').css('width','2%');
            $('.progress-bar').text('0%');
        }
    });

    $('#save_btn').click(function(e){
        e.preventDefault();
        alert('Сохраняем анкету');
    });

    function set_percent(a,b){
        return Math.round((a/b)*100);
    }

</script>
@endsection
