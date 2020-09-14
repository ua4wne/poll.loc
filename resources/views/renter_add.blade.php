@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('renters') }}">Арендаторы</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_content">
        <h2 class="text-center">Новая запись</h2>
        {!! Form::open(['url' => route('renterAdd'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_renter']) !!}

        <div class="form-group">
            {!! Form::label('title','Юрлицо:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Укажите юрлицо','required'=>'required','size'=>'100'])!!}
                {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','Наименование:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование','required'=>'required','size'=>'100'])!!}
                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('area','Участок:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('area',old('area'),['class' => 'form-control','placeholder'=>'Введите № участка','required'=>'required','size'=>'20'])!!}
                {!! $errors->first('area', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('agent','Представитель:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('agent',old('agent'),['class' => 'form-control','placeholder'=>'Укажите ФИО представителя','required'=>'required','size'=>'50'])!!}
                {!! $errors->first('agent', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('phone1','Телефон рабочий:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::tel('phone1',old('phone1'),['class' => 'form-control','placeholder'=>'Укажите телефон','size'=>'20','data-inputmask'=>'\'mask\' : \'(999) 999-9999\''])!!}
                {!! $errors->first('phone1', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('phone2','Телефон сотовый:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::tel('phone2',old('phone2'),['class' => 'form-control','placeholder'=>'Укажите телефон','size'=>'20','data-inputmask'=>'\'mask\' : \'(999) 999-9999\''])!!}
                {!! $errors->first('phone2', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('encounter','Счетчик:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('encounter',old('encounter'),['class' => 'form-control','placeholder'=>'Укажите счетчик','required'=>'required','size'=>'20'])!!}
                {!! $errors->first('encounter', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('koeff','Коэффициент:',['class' => 'col-xs-2 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('koeff',old('koeff'),['class' => 'form-control','placeholder'=>'Укажите коэффициент','required'=>'required','id'=>'koeff'])!!}
                {!! $errors->first('koeff', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('place_id', 'Территория:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('place_id', $placesel, old('place_id'),['class' => 'form-control','required' => 'required','id'=>'place_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('status', 'Статус:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('status', $statsel, old('status'),['class' => 'form-control','required' => 'required','id'=>'status']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('division_id', 'Закреплен за:',['class'=>'col-xs-2 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::select('division_id', $divsel, old('division_id'),['class' => 'form-control','required' => 'required','id'=>'division_id']); !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
    </div>
@endsection

@section('user_script')
    <script src="/js/jquery.inputmask.bundle.min.js"></script>
    <script>
         $("#place_id").prepend($('<option value="0">Выберите территорию</option>'));
            $("#place_id :first").attr("selected", "selected");
            $("#place_id :first").attr("disabled", "disabled");
            $("#division_id").prepend($('<option value="0">Выберите за кем закреплен</option>'));
            $("#division_id :first").attr("selected", "selected");
            $("#division_id :first").attr("disabled", "disabled");
            $('#koeff').val('6.2');

         $('#new_renter').submit(function(){
             //e.preventDefault();
             let error=0;
             $("#new_renter").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
             return true;
         });
    </script>
@endsection
