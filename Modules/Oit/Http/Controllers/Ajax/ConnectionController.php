<?php

namespace Modules\Oit\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Connection;

class ConnectionController extends Controller
{
    public function switchType(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $type = $request->input('type');
            $conn = Connection::find($id);
            $conn->type = $type;
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения типа подключения к интернет на '.$type. ' для подключения с ID '.$id;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($conn->update()){
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function delete(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $model = Connection::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления подключения к интернет. ID записи '.$model->id;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }

            if($model->delete()) {
                return 'OK';
            }
            else{
                return 'ERR';
            }
        }
    }
}
