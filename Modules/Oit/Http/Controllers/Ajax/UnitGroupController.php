<?php

namespace Modules\Oit\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\UnitGroup;

class UnitGroupController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $group = UnitGroup::find($input['id']);
            $group->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения данных подразделения '.$group->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($group->update()){
                $msg = 'Данные подразделения '.$group->name.' были изменены!';
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
            $model = UnitGroup::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления подразделения '.$model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }

            if($model->delete()) {
                $msg = 'Подразделение '.$model->name.' было удалено!';
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
