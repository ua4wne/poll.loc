<?php

namespace Modules\Admin\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Describer;

class DescriberController extends Controller
{
    public function switchStatus(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $status = $request->input('status');
            if($id==1)
                return 'NOT';
            $describer = Describer::find($id);
            $describer->status = $status;
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения статуса подписчика '.$describer->email;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($describer->update()){
                if($status)
                    $msg = 'Подписчик с e-mail '.$describer->email.' был активирован';
                else
                    $msg = 'Подписчик с e-mail '.$describer->email.' была деактивирован';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function delete(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $model = Describer::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления подписчика '.$model->email;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }

            if($model->delete()) {
                $msg = 'Подписчик '.$model->email.' был удален!';
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
