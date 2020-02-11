@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('media_form') }}">Источники медиарекламы</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        {!! Form::open(['url' => route('media_form'),'class'=>'form-horizontal','method'=>'POST','id'=>'fMedia']) !!}

        {!! $content !!}

        <div class="col-xs-offset-2 col-xs-8">
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('date', 'Начало периода:',['class'=>'col-xs-4 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('date', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control', 'id'=>'date']) }}
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('kolvo','Кол-во опрошенных:',['class' => 'col-xs-4 control-label'])   !!}
                    <div class="col-xs-8">
                        {!! Form::text('kolvo',old('kolvo'),['class' => 'form-control digits','placeholder'=>'Введите число','required'=>'required','id'=>'kolvo'])!!}
                        {!! $errors->first('kolvo', '<p class="text-danger">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection
@section('user_script')
    <script>
        $('#save').click(function(e){
            e.preventDefault();
            let kolvo = $('#kolvo').val();
            if(!$.isNumeric(kolvo)){
                alert('Количество опрошенных должно быть числом!');
                $('#kolvo').css('border', '1px solid red');// устанавливаем рамку красного цвета
                $('#kolvo').focus(); //установка фокуса на поле с ошибкой
                $('#kolvo').val('');
                return false;
            }
            let err = 0;
            $(".table").find(":input[name*='other']").each(function() {// проверяем каждое поле ввода в форме
                if($(this).prev().is(':checked')){ //если выбран чекбокс
                    if(!$(this).val()){// если поле пустое
                        alert('Необходимо заполнить поле или выбрать значение из списка!');
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        $(this).focus(); //установка фокуса на поле с ошибкой
                        err=1;
                        return false;
                    }
                    else{
                        $(this).css('border', '');// устанавливаем рамку зеленого цвета
                    }
                }
            })
            $('.panel-info').each(function() { //проверяем наличие вопросов без ответов
                let obj = $(this);
                let qname = obj.find('.panel-heading').text();
                let qst = obj.find('input:checked').length;
                //var rqst = obj.find('input[type=radio]:checked').length;
                //alert('val='+qst+' qname='+qname);
                if(qst==0) {
                    if(qname!='Ваши контакты?') {
                        err++;
                        alert('Не выбран вариант ответа на вопрос "'+qname+'"');
                        $(':first-child',this).focus();
                        return false;
                    }
                }
            })
            if(!err){
                $.ajax({
                    url: '{{ route('media_form') }}',
                    type: 'POST',
                    data: $('#fMedia').serialize(),
                    success: function(res){
                        //alert("Сервер вернул вот что: " + res);
                        if(res=='OK'){
                            alert("Данные успешно добавлены!");
                            $('#kolvo').val('');
                            //сбрасываем предыдущий выбор
                            $('body input:checkbox').prop('checked', false);
                        }
                    },
                    error: function(xhr, response){
                        alert('Error! '+ xhr.responseText);
                    }
                });
            }
            return false;
        });
    </script>
@endsection
