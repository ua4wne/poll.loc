<?php

namespace Modules\Oit\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Cost;

class CostController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $cost = Cost::find($input['id']);
            $cost->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения данных расхода ИТ '.$cost->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($cost->update()){
                $msg = 'Данные записи расхода ИТ '.$cost->name.' были изменены!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function delete(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $model = Cost::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления записи расхода ИТ '.$model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }

            if($model->delete()) {
                $msg = 'Запись расхода ИТ '.$model->name.' была удалена!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else{
                return 'ERR';
            }
        }
    }
}
