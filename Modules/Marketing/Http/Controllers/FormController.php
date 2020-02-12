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
            $data = [
                'title' => $title,
                'head' => 'Список анкет',
                'rows' => $rows,
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
            $data = [
                'title' => 'Новая запись',
            ];
            return view('marketing::form_add', $data);
        }
        abort(404);
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
            if(isset($input[$idx])){
                //return '$_POST[$idx]='.$_POST[$idx];
                if(is_array($input[$idx]))
                {
                    foreach($input[$idx] as $value)
                    {
                        $answer = Answer::find($value);
                        if(strstr($answer->name,'укажите')||strstr($answer->name,'указать'))
                        {
                            $alt = "other".$answer->id;
                            if(strlen($input[$alt])!=0)
                                $val = $input[$alt];
                            else
                                $val = 'Другое (не указано)';
                        }
                        else
                            $val = $answer->name;
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
                else{
                    $answer = Answer::find($input[$idx]);
                    if(strstr($answer->name,'укажите')||strstr($answer->name,'указать'))
                    {
                        $alt = "other".$answer->id;
                        if(strlen($input[$alt])!=0)
                            $val = $input[$alt];
                        else
                            $val = 'Другое (не указано)';
                    }
                    else
                        $val = $answer->name; //заполняем ассоциативный массив ответов
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
        //после сохранения анкеты увеличиваем счетчик опрошенных в таблице form_qty
        $table = FormQty::where(['form_id'=>$idform, 'date'=>$input['date']])->first();
        if(empty($table)){
            $new = new FormQty();
            $new->form_id = $idform;
            $new->date = $input['date'];
            $new->qty = 1;
            $new->created_at = date('Y-m-d H:i:s');
            //$new->updated_at = $new->created_at;
            $new->save();
        }
        else{
            $qty = $table->qty;
            $qty++;
            $table->qty = $qty;
            $table->update();
        }
    }

}