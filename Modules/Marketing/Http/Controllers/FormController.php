<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\FormGroup;
use Modules\Marketing\Entities\FormQty;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;
use Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if(view()->exists('marketing::forms')){
            $title='Анкеты';
            $rows = Form::all();
            $groups = FormGroup::select(['id', 'title'])->get();
            $selgroup = array();
            foreach ($groups as $val) {
                $selgroup[$val->id] = $val->title; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => 'Список анкет',
                'rows' => $rows,
                'selgroup' => $selgroup,
            ];
            return view('marketing::forms',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть текстовой строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:80|string',
                'is_active' => 'required|numeric',
                'is_work' => 'required|numeric',
                'form_group_id' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('/formAdd')->withErrors($validator)->withInput();
            }

            $form = new Form();
            $form->fill($input);
            $form->created_at = date('Y-m-d H:i:s');
            if($form->save()){
                $msg = 'Новая анкета '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/forms')->with('status',$msg);
            }
        }
        if(view()->exists('marketing::form_add')){
            $groups = FormGroup::select(['id', 'title'])->get();
            $selgroup = array();
            foreach ($groups as $val) {
                $selgroup[$val->id] = $val->title; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Новая запись',
                'selgroup' => $selgroup,
            ];
            return view('marketing::form_add', $data);
        }
        abort(404);
    }

    public function view($id){
        if(User::hasRole('poll')){ //для интервьюеров свой отдельный вид
            if(view()->exists('marketing::form_single')) {
                $ankets = Form::select('id','name')->where(['is_active'=>1,'is_work'=>1])->get();
                $menu = '';
                foreach ($ankets as $row){
                    $menu .= '<li><h4><a href="/forms/view/'. $row->id .'">'.$row->name.'</a></h4></li>';
                }
                $content = $this->ViewForm($id);
                $name = Form::find($id)->name;
                while(strlen($name)<132){
                    $name = ' - '.$name.' - ';
                }
                $data = [
                    'title' => Form::find($id)->name,
                    'name' => $name,
                    'content' => $content,
                    'menu' => $menu,
                ];
                return view('marketing::form_single',$data);
            }
            abort(404);
        }
        if(view()->exists('marketing::form_view')){
            $content = $this->ViewForm($id);
            $data = [
                'title' => Form::find($id)->name,
                'content' => $content,
            ];
            return view('marketing::form_view', $data);
        }
        abort(404);
    }

    public function singleForm(){
        if(User::hasRole('poll')){ //для интервьюеров свой отдельный вид
            if(view()->exists('marketing::form_single')) {
                $ankets = Form::select('id','name')->where(['is_active'=>1,'is_work'=>1])->get();
                $menu = '';
                foreach ($ankets as $row){
                    $menu .= '<li><a href="/forms/view/'. $row->id .'">'.$row->name.'</a></li>';
                }
                $name = 'Выбор анекты';
                $content = '';
                while(strlen($name)<132){
                    $name = ' - '.$name.' - ';
                }
                $data = [
                    'title' => 'Нет выбранных анкет',
                    'name' => $name,
                    'menu' => $menu,
                    'content' => $content,
                ];
                return view('marketing::form_single',$data);
            }
            abort(404);
        }
    }

    public function groupForm(Request $request){
        if(User::hasRole('poll')) { //для интервьюеров свой отдельный вид
            $date = date('Y-m-d');
            $qty = FormQty::where('date',$date)->sum('qty');
            if($request->isMethod('post')){
                $input = $request->except('_token'); //параметр _token нам не нужен
                //выбираем все активные анкеты группы
                $forms = Form::select('id')->where(['form_group_id'=>$input['group_id'],'is_active'=>1,'is_work'=>1])->get();
                $result = array();
                foreach ($forms as $row){
                    array_push($result,$row->id);
                }
                $key = $result[0]; //первый элемент массива
                $keys = implode('|',$result);
                //запоминаем выбранные значения в сессии
                session(['key' => $key]);
                session(['keys' => $keys]);
                if(view()->exists('marketing::form_group_view')) {
                    $name = Form::find($key)->name;
                    $content = $this->groupView($key);
                    $data = [
                        'title' => Form::find($key)->name,
                        'name' => $name,
                        'content' => $content,
                        'ankets' => $qty,
                    ];
                    return view('marketing::form_group_view',$data);
                }
                abort(404);
            }
            if (view()->exists('marketing::form_group')) {
                $groups = FormGroup::select('id', 'title')->where('title', '!=', 'Без группы')->get();
                $content = '';
                foreach ($groups as $row) {
                    $content .= '<option value="'.$row->id.'">'.$row->title.'</option>';
                }
                $data = [
                    'title' => 'Выбор группы анкет',
                    'content' => $content,
                    'ankets' => $qty,
                ];
                return view('marketing::form_group', $data);
            }
            abort(404);
        }
    }

    public function setForm(){
        //получаем значения из сессии
        $old = session('key');
        $keys = session('keys');
        if(!empty($old) && !empty($keys)){
            $tmp = explode('|',$keys);
            $max_index = count($tmp) - 1;
            $idx = array_search($old,$tmp);
            $idx < $max_index ? $idx++ : $idx = 0;
            $key = $tmp[$idx];
            session(['key' => $key]); //установили новое значение
            if(view()->exists('marketing::form_group_view')) {
                $date = date('Y-m-d');
                $qty = FormQty::where('date',$date)->sum('qty');
                $name = Form::find($key)->name;
                $content = $this->groupView($key);
                $data = [
                    'title' => Form::find($key)->name,
                    'name' => $name,
                    'content' => $content,
                    'ankets' => $qty,
                ];
                return view('marketing::form_group_view',$data);
            }
            abort(404);
        }
    }

    public function storePoll(Request $request) {
        if(!User::hasRole('admin') && !User::hasRole('poll') && !User::hasRole('market')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $id = $input['form_id'];
            $input['date'] = date('Y-m-d'); //текущая дата
            self::SavePoll($id, $input);
            return 'OK';
        }
    }

    public function media(Request $request){
        if(!User::hasRole('admin') && !User::hasRole('guard')){
            abort(503);
        }
        $id = 7; //Опрос посетителей выставки домов Малоэтажная Страна
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //$date = $input['date'];
            $kol = $input['kolvo'];
            if ($kol > 1) {
                while ($kol) {
                    self::SavePoll($id, $input);
                    $kol--;
                }
            }
            elseif ($kol==1)
                self::SavePoll($id, $input);
            return 'OK';
        }
        if(view()->exists('marketing::mediaform')){
            $data = [
                'content' => $this->ViewMedia($id),
                'title' => 'Источники медиарекламы',
            ];
            return view('marketing::mediaform', $data);
        }
        abort(404);
    }

    private function ViewMedia($id){
        $content='<div class="col-xs-offset-2 col-xs-8">';
        $content.='<input type="hidden" name="form_id" id="form_id" value="'.$id.'">';
        //выбираем только вопрос Откуда Вы узнали о выставке домов "Малоэтажная страна" анкеты
        $questions = Question::where(['form_id'=>$id, 'name'=>'Откуда Вы узнали о выставке домов "Малоэтажная страна"'])->get();
        foreach($questions as $question){
            $content.='<div class="row"><div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">'.
                $question->name . '?'.
                '</div>
                        <div class="panel-body">';
            //выбираем все ответы на вопрос
            $answers = Answer::where(['question_id'=>$question->id])->get();
            $k=0;
            $content.='<table class="table">';
            foreach ($answers as $answer){
                if($k==0)
                    $content.='<tr>';
                if(strpos($answer->htmlcode,"select size=",0)!=false)
                {
                    $html='<option value="" selected disabled>Выберите из списка</option>';
                    if($answer->source =='renter'){
                        $rows = DB::select("SELECT title FROM ".$answer->source." WHERE status=1 AND place_id IN(1,6)");
                        foreach($rows as $row){
                            if($row->title != 'Другое (свой вариант)')
                                $html.='<option value="'.$row->title.'">'.$row->title.'</option>';
                        }
                        $html.='</select>';
                    }
                    else{
                        $rows = DB::select("SELECT name FROM ".$answer->source);
                        foreach($rows as $row){
                            if($row->name != 'Другое (свой вариант)')
                                $html.='<option value="'.$row->name.'">'.$row->name.'</option>';
                        }
                        $html.='</select>';
                    }

                    $content.= '<td>'.$answer->htmlcode.$html.'</td>';
                }
                else
                    $content.= '<td>'.$answer->htmlcode.'</td>';

                $k++;
                if($k==2){
                    $content.='</tr>';
                    $k=0;
                }
            }
            if($k==1){
                $content.='<td></td></tr>';
            }
            $content.='</table></div>
                    </div></div>
                </div>';
        }
        $content.='</div>';
        return $content;
    }

    public static function SavePoll($idform,$input){
        $iduser = Auth::user()->id; //id пользователя
        $questions = Question::select('id')->where(['form_id'=>$idform])->get(); //выбрали все вопросы анкеты
        foreach ($questions as $question){
            $idx="q".$question->id; //value = id ответов
            if(!empty($input[$idx])){
                //return '$_POST[$idx]='.$_POST[$idx];
                if(is_array($input[$idx]))
                {
                    foreach($input[$idx] as $value)
                    {
                        $answer = Answer::find($value);
                        if(strstr($answer->name,'укажите')||strstr($answer->name,'указать'))
                        {
                            $alt = "other".$answer->id;
                            $val = $input[$alt];
                        }
                        else
                            $val = $answer->name;
                        if(is_array($val)){
                            foreach ($val as $v){
                                $model = new Logform();
                                $model->data = $input['date'];
                                $model->form_id = $idform;
                                $model->question_id = $question->id;
                                $model->answer_id = $answer->id;
                                $model->answer = $v;
                                $model->user_id = $iduser;
                                $model->save();
                            }
                        }
                        else{
                            $model = new Logform();
                            $model->data = $input['date'];
                            $model->form_id = $idform;
                            $model->question_id = $question->id;
                            $model->answer_id = $answer->id;
                            $model->answer = $val;
                            $model->user_id = $iduser;
                            $model->save();
                        }
                    }
                }
                else{
                    $answer = Answer::find($input[$idx]);
                    if(strstr($answer->name,'укажите')||strstr($answer->name,'указать'))
                    {
                        $alt = "other".$answer->id;
                        $val = $input[$alt];
                    }
                    else
                        $val = $answer->name; //заполняем ассоциативный массив ответов
                    if(is_array($val)){
                        foreach ($val as $v){
                            $model = new Logform();
                            $model->data = $input['date'];
                            $model->form_id = $idform;
                            $model->question_id = $question->id;
                            $model->answer_id = $answer->id;
                            $model->answer = $v;
                            $model->user_id = $iduser;
                            $model->save();
                        }
                    }
                    else{
                        $model = new Logform();
                        $model->data = $input['date'];
                        $model->form_id = $idform;
                        $model->question_id = $question->id;
                        $model->answer_id = $answer->id;
                        $model->answer = $val;
                        $model->user_id = $iduser;
                        $model->save();
                    }
                }
            }
        }
        //после сохранения анкеты увеличиваем счетчик опрошенных в таблице form_qty
        $table = FormQty::where(['form_id'=>$idform, 'date'=>$input['date'], 'user_id'=>$iduser])->first();
        if(empty($table)){
            $new = new FormQty();
            $new->form_id = $idform;
            $new->date = $input['date'];
            $new->qty = 1;
            $new->user_id = $iduser;
            $new->created_at = date('Y-m-d H:i:s');
            $new->save();
        }
        else{
            $qty = $table->qty;
            $qty++;
            $table->qty = $qty;
            $table->update();
        }
    }

    private function ViewForm($id){
        $content='<div class="x_panel">';
        $content.='<div class="row"><div class="col-xs-12">
                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 2%;">0%</div></div></div><hr>';
        $content.='<input type="hidden" name="form_id" id="form_id" value="'.$id.'">';
        //выбираем все активные вопросы анкеты
        $questions = Question::where(['form_id'=>$id,'visibility'=>1])->get();
        $content.='<input type="hidden" name="qst_count" id="qst_count" value="'.$questions->count().'">';
        $qst = 1;
        foreach($questions as $question){
            $content.='<div class="row"><div class="col-md-12">
                    <div class="panel panel-info" id="qpanel'.$qst.'">
                        <div class="panel-heading">'. $qst . '. ' .
                $question->name . '?'.
                '</div>
                        <div class="panel-body">';
            //выбираем все активные ответы на вопрос
            $answers = Answer::where(['question_id'=>$question->id,'visibility'=>1])->get();
            $num=1;
            $content.='<table class="table table-bordered">';
            foreach ($answers as $answer){
                //if($k==0)
                    $content.='<tr>';
                if(strpos($answer->htmlcode,"select size=",0)!=false)
                {
                    $html='<option value="" selected disabled>Выберите из списка</option>';
                    $query="SELECT name FROM ".$answer->source;
                    if($answer->source=='renters'){
                        $query = "SELECT `name`,`area` FROM renters where STATUS=1 and place_id IN (1,6,7) ORDER BY `area`+0 ASC";
                    }
                    // подключение к базе данных
                    $rows = DB::select($query);
                    foreach($rows as $row){
                        if($row->name != 'Другое (свой вариант)')
                            if($answer->source=='renters')
                                $html.='<option value="'.$row->name.'"> Участок №'.$row->area.' '.$row->name.'</option>';
                            else
                                $html.='<option value="'.$row->name.'">'.$row->name.'</option>';
                    }
                    $html.='</select>';
                    $content.= '<td>'.$num.'</td><td>'.$answer->htmlcode.$html.'</td>';
                }
                else
                    $content.= '<td>'.$num.'</td><td>'.$answer->htmlcode.'</td>';

                $num++;
                //if($k==2){
                    $content.='</tr>';
                //    $k=0;
                //}
            }
//            if($k==1){
//                $content.='<td></td></tr>';
//            }
            $content.='</table></div>
                    </div></div>
                </div>';
            $qst++;
        }
        $content.='<button class="btn btn-primary" id="prev_btn"><i class="fa fa-chevron-left" aria-hidden="true"></i> Назад</button>
                    <button class="btn btn-primary" id="next_btn">Вперед <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    <button class="btn btn-success" id="save_btn">Сохранить анкету</button>
                    <button class="btn btn-danger pull-right" id="presave_btn">Завершить опрос</button>';
        $content.='</div>';
        return $content;
    }

    private function groupView($id){
        $content='<div class="x_panel">';
        $content.='<div class="row"><div class="col-xs-12">
                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 2%;">0%</div></div></div><hr>';
        $content.='<input type="hidden" name="form_id" id="form_id" value="'.$id.'">';
        //выбираем все активные вопросы анкеты
        $questions = Question::where(['form_id'=>$id,'visibility'=>1])->get();
        $content.='<input type="hidden" name="qst_count" id="qst_count" value="'.$questions->count().'">';
        $qst = 1;
        foreach($questions as $question){
            $content.='<div class="row"><div class="col-md-12">
                    <div class="panel panel-info" id="qpanel'.$qst.'">
                        <div class="panel-heading">'.
                $question->name . '?'.
                '</div>
                        <div class="panel-body">';
            //выбираем все активные ответы на вопрос
            $answers = Answer::where(['question_id'=>$question->id,'visibility'=>1])->get();
            $k=0;
            $content.='<table class="table table-bordered">';
            foreach ($answers as $answer){
                if($k==0)
                    $content.='<tr>';
                if(strpos($answer->htmlcode,"select size=",0)!=false)
                {
                    $html='<option value="" selected disabled>Выберите из списка</option>';
                    $query="SELECT name FROM ".$answer->source;
                    if($answer->source=='renters'){
                        $query = "SELECT `name`,`area` FROM renters where STATUS=1 and place_id IN (1,6,7) ORDER BY `area`+0 ASC";
                    }
                    // подключение к базе данных
                    $rows = DB::select($query);
                    foreach($rows as $row){
                        if($row->name != 'Другое (свой вариант)')
                            if($answer->source=='renters')
                                $html.='<option value="'.$row->name.'"> Участок №'.$row->area.' '.$row->name.'</option>';
                            else
                                $html.='<option value="'.$row->name.'">'.$row->name.'</option>';
                    }
                    $html.='</select>';
                    $content.= '<td>'.$answer->htmlcode.$html.'</td>';
                }
                else
                    $content.= '<td>'.$answer->htmlcode.'</td>';

                $k++;
                if($k==2){
                    $content.='</tr>';
                    $k=0;
                }
            }
            if($k==1){
                $content.='<td></td></tr>';
            }
            $content.='</table></div>
                    </div></div>
                </div>';
            $qst++;
        }
        $content.='<button class="btn btn-primary" id="prev_btn"><i class="fa fa-chevron-left" aria-hidden="true"></i> Назад</button>
                    <button class="btn btn-primary" id="next_btn">Вперед <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    <button class="btn btn-success" id="save_btn">Сохранить анкету</button>
                    <button class="btn btn-danger pull-right" id="presave_btn">Завершить опрос</button>';
        $content.='</div>';
        return $content;
    }

}
