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
        <li class="active">Новый ответ</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новый ответ</h2>
        {!! Form::open(['url' => route('answerAdd',[$question->id]),'class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

        <div class="form-group">
            <div class="col-xs-10">
                {!! Form::hidden('question_id',$question->id,['class' => 'form-control','required'=>'required','id'=>'question_id']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','Наименование:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите ответ на вопрос анкеты','required'=>'required','maxlength'=>'100'])!!}
                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('visibility', 'Видимость:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('visibility', ['1'=>'Да','0'=>'Нет'], old('visibility'),['class' => 'form-control','required' => 'required','id'=>'visibility']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('htmlcode', 'Тип ответа:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('htmlcode', $htmlsel, old('htmlcode'),['class' => 'form-control','id'=>'htmlcode']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('jump','Переход к вопросу №:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('jump',old('jump'),['class' => 'form-control','placeholder'=>'Введите номер вопроса анкеты','maxlength'=>'2'])!!}
                {!! $errors->first('jump', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('refbook', 'Справочник:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('refbook', $refsel, old('refbook'),['class' => 'form-control','id'=>'refbook']); !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-3 col-xs-8">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection
@section('user_script')
    <script>
        $("#refbook").hide();
        $("#refbook").parent().prev().hide();
        $("#htmlcode").change(function() {
            if($("#htmlcode").val()=='tonesel'||$("#htmlcode").val()=='tmulsel') {
                $("#refbook").show();
                $("#refbook").parent().prev().show();
            }
            else {
                $("#refbook").hide();
                $("#refbook").parent().prev().hide();
            }
        });
        $('#save').click(function(){
            //e.preventDefault();
            let error=0;
            $("#new_val").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                return true;
            }
        });
    </script>
@endsection
