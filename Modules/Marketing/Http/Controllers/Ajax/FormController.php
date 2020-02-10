<?php

namespace Modules\Marketing\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;

class FormController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $anket = Form::find($input['id']);
            $anket->fill($input);
            if(!User::hasRole('admin') && !User::hasRole('market')){//вызываем event
                $msg = 'Попытка изменения анкеты '.$anket->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($anket->update()){
                $msg = 'Анкета '.$anket->name.' была изменена!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            $model = Form::find($id);
            if (!User::hasRole('admin') && !User::hasRole('market')) {//вызываем event
                $msg = 'Попытка удаления анкеты ' . $model->name . ' со всей статистикой по ней!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            //удаляем всю статистику по анкете
            Logform::where(['form_id'=>$id])->delete();
            //выбираем все вопросы анкеты
            $questions = Question::select('id')->where(['form_id'=>$id])->get();
            //удаляем все ответы на вопросы в цикле
            foreach ($questions as $qst){
                Answer::where(['question_id'=>$qst->id])->delete();
            }
            //удаляем все вопросы анкеты
            Question::where(['form_id'=>$id])->delete();

            if ($model->delete()) {
                $msg = 'Удалена анкета ' . $model->name . ' со всей статистикой по ней!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
