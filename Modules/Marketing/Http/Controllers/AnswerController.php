<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Catalog;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;
use Validator;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if(view()->exists('marketing::answers')){
            $question = Question::find($id);
            $title='Ответы на вопрос "'.$question->name.'"';
            $rows = Answer::where(['question_id'=>$id])->get();
            $data = [
                'title' => $title,
                'rows' => $rows,
                'question' => $question,
            ];
            return view('marketing::answers',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request, $id)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть текстовой строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:255|string',
                'question_id' => 'required|numeric',
                'htmlcode' => 'required|string',
                'refbook' => 'nullable|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('/answerAdd',[$id])->withErrors($validator)->withInput();
            }
            $dbl = Answer::where(['name'=>$input['name'],'question_id'=>$input['question_id']])->first();
            if(empty($dbl)){
                $answer = new Answer();
                $answer->fill($input);
                $answer->created_at = date('Y-m-d H:i:s');
                if($answer->save()){
                    switch ($answer->htmlcode) {
                        case 'tradio':
                            $html='<input type="radio" name="q'.$id.'" id="a'.$answer->id.'" value="'.$answer->id.'" >'.$answer->name;
                            break;
                        case 'tcheck':
                            $html='<input type="checkbox" name="q'.$id.'[]" id="a'.$answer->id.'" value="'.$answer->id.'">'.$answer->name;
                            break;
                        case 'tmail':
                            $html='<label for="mail">'.$answer->name.'</label><input type="email" name="mail" id="mail" value="" placeholder="login@domain" maxlength="30">';
                            break;
                        case 'tphone':
                            $html='<label for="phone">'.$answer->name.'</label><input type="text" name="phone" id="phone" value="" placeholder="4951234567" maxlength="20">';
                            break;
                        case 'taddr':
                            $html='<label for="addr">'.$answer->name.'</label><input type="text" name="addr" id="addr" value="" maxlength="100">';
                            break;
                        case 'tonetext':
                            $html='<input type="radio" name="q'.$id.'" id="a'.$answer->id.'" value="'.$answer->id.'" >'.$answer->name.'<input type="text" name="other'.$answer->id.'" value="">';
                            break;
                        case 'tmultext':
                            $html='<input type="checkbox" name="q'.$id.'[]" id="a'.$answer->id.'" value="'.$answer->id.'" >'.$answer->name.'<input type="text" name="other'.$answer->id.'" value="">';
                            break;
                        case 'tonesel':
                            //if($model->refbook=='city')
                            //    $html='<input type="radio" name="q'.$id.'" id="a'.$last_id.'" value="'.$last_id.'" >'.$model->name.'<input type="text" name="other'.$last_id.'" value="" placeholder="начинайте вводить текст" class="'.$model->refbook.'">';
                            //else
                            $html='<input type="radio" name="q'.$id.'" id="a'.$answer->id.'" value="'.$answer->id.'" >'.$answer->name.'<select size="1" name="other'.$answer->id.'" id="s'.$answer->id.'">';
                            break;
                        case 'tmulsel':
                            //if($model->refbook=='city')
                            //    $html='<input type="checkbox" name="q'.$id.'[]" id="a'.$last_id.'" value="'.$last_id.'" >'.$model->name.'<input type="text" name="other'.$last_id.'" value="" placeholder="начинайте вводить текст" class="'.$model->refbook.'">';
                            //else
                            $html='<input type="checkbox" name="q'.$id.'[]" id="a'.$answer->id.'" value="'.$answer->id.'" >'.$answer->name.'<select size="1" name="other'.$answer->id.'" id="s'.$answer->id.'">';
                            break;
                    }
                    if($answer->htmlcode=='tonesel' || $answer->htmlcode=='tmulsel'){

                        $answer->source = $input['refbook'];
                    }
                    $answer->htmlcode = $html;
                    $answer->update();
                    $msg = 'Новый ответ '. $answer->name .' добавлен к вопросу '.$answer->question->name.'!';
                    $ip = $request->getClientIp();
                    //вызываем event
                    event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                }
            }
        }
        if(view()->exists('marketing::answer_add')){
            $question = Question::find($id);
            $htmlsel = array ('tmail' => 'Контактный email','tphone' => 'Контактный телефон','taddr' => 'Контактный адрес',
                'tradio' => 'Единичный выбор','tcheck' => 'Множественный выбор', 'tonetext' => 'Свой вариант (единичный выбор)',
                'tmultext' => 'Свой вариант (множественный выбор)', 'tonesel' => 'Выбор из списка (единичный выбор)',
                'tmulsel' => 'Выбор из списка (множественный выбор)');
            //выбираем доступные справочники
            $books = Catalog::all();
            $refsel = array();
            foreach ($books as $book){
                $refsel[$book->nameEN] = $book->nameRU;
            }
            $data = [
                'title' => 'Новая запись',
                'question' => $question,
                'htmlsel' => $htmlsel,
                'refsel' => $refsel,
            ];
            return view('marketing::answer_add', $data);
        }
        abort(404);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            $model = Answer::find($id);
            if (!User::hasRole('admin') && !User::hasRole('market')) {//вызываем event
                $msg = 'Попытка удаления ответа ' . $model->name . ' на вопрос '.$model->question->name.' со всей статистикой из анкеты '.$model->question->form->name.'!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            //удаляем всю статистику по ответу анкеты
            Logform::where(['answer_id'=>$id])->delete();
            $fname = $model->question->form->name;
            $qname = $model->question->name;
            $answer = $model->name;
            if ($model->delete()) {
                $msg = 'Удален ответ ' . $answer . 'из вопроса '.$qname.' со всей статистикой из анкеты '.$fname.'!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
