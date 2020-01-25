<?php

namespace Modules\Admin\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Place;

class PlaceController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $place = Place::find($input['id']);
            $place->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения данных территории '.$place->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($place->update()){
                $msg = 'Данные территории '.$place->name.' были изменены!';
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
            $model = Place::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления территории '.$model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }

            if($model->delete()) {
                $msg = 'Территория '.$model->name.' была удалена!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else{
                return 'ERR';
            }
        }
    }

}
