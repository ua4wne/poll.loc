<?php

namespace Modules\Marketing\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;

class QuestionController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $qst = Question::find($input['id']);
            $qst->fill($input);
            if(!User::hasRole('admin') && !User::hasRole('market')){//вызываем event
                $msg = 'Попытка изменения вопроса '.$qst->name. ' в анкете '.$qst->form->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($qst->update()){
                $msg = 'Вопрос '.$qst->name.' в анкете '.$qst->form->name.' был изменен!';
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
            $model = Question::find($id);
            if (!User::hasRole('admin') && !User::hasRole('market')) {//вызываем event
                $msg = 'Попытка удаления вопроса ' . $model->name . ' со всей статистикой из анкеты '.$model->form->name.'!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            //удаляем всю статистику по вопросу анкеты
            Logform::where(['question_id'=>$id])->delete();
            Answer::where(['question_id'=>$id])->delete();
            $fname = $model->form->name;
            $qname = $model->name;
            if ($model->delete()) {
                $msg = 'Удален вопрос ' . $qname . ' со всей статистикой из анкеты '.$fname.'!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
