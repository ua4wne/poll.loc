<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\Question;
use Validator;

class QuestionController extends Controller
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
        if(view()->exists('marketing::questions')){
            $title='Вопросы для анкеты';
            $anket = Form::find($id);
            $rows = Question::where(['form_id'=>$id])->get();
            $data = [
                'title' => $title,
                'head' => 'Вопросы для анкеты "'. $anket->name.'"',
                'rows' => $rows,
                'form_id' => $id,
            ];
            return view('marketing::questions',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request,$id)
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
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:255|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('/questionAdd',[$id])->withErrors($validator)->withInput();
            }

            $qst = new Question();
            $qst->fill($input);
            $qst->created_at = date('Y-m-d H:i:s');
            if($qst->save()){
                $msg = 'Новый вопрос '. $qst['name'] .' добавлен в анкету '.$qst->form->name.'!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                //return redirect('/questionAdd',[$id])->with('status',$msg);
            }
        }
        if(view()->exists('marketing::question_add')){
            $form = Form::find($id)->name;
            $data = [
                'title' => 'Новая запись',
                'form_id' => $id,
                'header' => $form,
            ];
            return view('marketing::question_add', $data);
        }
        abort(404);
    }
}
