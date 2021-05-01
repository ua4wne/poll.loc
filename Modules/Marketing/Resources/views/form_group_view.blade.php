@extends('layouts.poll')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('groupForm') }}">Группа анкет </a></li>
        <li class="active">{{ $title }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <p class="text-uppercase text-info">Собрано анкет {{ $ankets ?? '' }}</p>
            {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'form-poll']) !!}
            <h2 class="text-center">{{ $title }}</h2>
            {!! $content !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script>
        $('.panel-info').hide();
        $('select option:selected').each(function () {
            this.selected = false;
        });
        $('#qpanel1').show();
        $('#save_btn').hide();
        $('#presave_btn').hide();
        $('#prev_btn').hide();

        let step = 1;
        let prev_step = 0;
        let next_step = 0;
        let qcnt = $('#qst_count').val();

        //$('#qpanel1').find(':radio').first().prop('checked', true);
        $('td').css('cursor', 'pointer');

        $('td').click(function(){
            $(this).find(':radio').prop('checked', true);
            $(this).find(':radio').change();
        });

        $('input[type="radio"]').on('change', function (e) {
            let stp = $(this).data('step');
            let finish = $(this).data('finish');
            if (typeof (stp) !== "undefined") {
                step = stp - 1;
            }
            if (typeof (finish) !== "undefined") {
                $('.progress-bar').css('width', '100%');
                $('.progress-bar').text('100%');
                $('#save_btn').show();
                $('#next_btn').hide();
            }
            $('#next_btn').attr('disabled', false);
        });

        $('input[type="checkbox"]').on('change', function (e) {
            let stp = $(this).data('step');
            let finish = $(this).data('finish');
            if (typeof (stp) !== "undefined") {
                step = stp - 1;
            }
            if (typeof (finish) !== "undefined") {
                $('.progress-bar').css('width', '100%');
                $('.progress-bar').text('100%');
                $('#save_btn').show();
                $('#next_btn').hide();
            }
            $('#next_btn').attr('disabled', false);
        });

        $('#next_btn').click(function (e) {
            e.preventDefault();
            let err = 0;
            $(".table").find(":input[name*='other']").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).prev().is(':checked')) { //если выбран чекбокс
                    if (!$(this).val()) {// если поле пустое
                        alert('Необходимо заполнить поле или выбрать значение из списка!');
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        $(this).focus(); //установка фокуса на поле с ошибкой
                        err = 1;
                    } else {
                        $(this).css('border', '');// устанавливаем рамку зеленого цвета
                    }
                }
            })

            if (!err) {
                prev_step = step;
                if (step < qcnt) {
                    for (i = 1; i <= step; i++) {
                        $('#qpanel' + i).hide();
                    }
                    step++;
                    $('#qpanel' + step).show();
                    let per = set_percent(step, qcnt);
                    $('.progress-bar').css('width', per + '%');
                    $('.progress-bar').text(per + '%');
                }
                if (step == qcnt) {
                    $('#save_btn').show();
                    $('#presave_btn').hide();
                    $('#next_btn').hide();
                    $('.progress-bar').css('width', '100%');
                    $('.progress-bar').text('100%');
                }
                if (step > 1){
                    $('#prev_btn').show();
                    $('#presave_btn').show();
                }
                $('#next_btn').attr('disabled', true);
            }
        });

        $('#prev_btn').click(function (e) {
            e.preventDefault();
            next_step = step;
            let obj = $('#qpanel' + step);
            // Снять все
            obj.find(':radio').prop('checked', false);
            obj.find(':checkbox').prop('checked', false);
            if (next_step > 1) {
                $('#qpanel' + next_step).hide();
                step--;
                $('#qpanel' + step).show();
                let per = set_percent(step, qcnt);
                $('.progress-bar').css('width', per + '%');
                $('.progress-bar').text(per + '%');
            }
            if (step < qcnt) {
                $('#save_btn').hide();
                $('#next_btn').show();
            }
            if (step == 1) {
                $('#prev_btn').hide();
                $('#presave_btn').hide();
                $('.progress-bar').css('width', '2%');
                $('.progress-bar').text('0%');
            }
            $('#next_btn').attr('disabled', false);
        });

        $('#save_btn').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('save_poll') }}',
                beforeSend: function() {
                    $('#loader').show();
                },
                data: $('#form-poll').serialize(),
                success: function(res){
                    //alert(res);
                    if(res=='OK')
                        window.location = '{{ route('set_form') }}'
                },
                error: function(xhr, response){
                    alert('Error! '+ xhr.responseText);
                }
            });
            clear_set();
            $('#loader').hide();
        });

        $('#presave_btn').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('save_poll') }}',
                beforeSend: function() {
                    $('#loader').show();
                },
                data: $('#form-poll').serialize(),
                success: function(res){
                    //alert(res);
                    if(res=='OK')
                        window.location = '{{ route('set_form') }}'
                },
                error: function(xhr, response){
                    alert('Error! '+ xhr.responseText);
                }
            });
            clear_set();
            $('#loader').hide();
        });

        function set_percent(a, b) {
            return Math.round((a / b) * 100);
        }

        function clear_set() {
            //сбрасываем все отмеченные чекбоксы
            $('input:checkbox:checked').each(function() {
                $(this).prop('checked', false);
            });
            $('input:radio:checked').each(function() {
                $(this).prop('checked', false);
            });
        }

    </script>
@endsection
