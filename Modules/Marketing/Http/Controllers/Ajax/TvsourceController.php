<?php

namespace Modules\Marketing\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Tvsource;

class TvsourceController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $tvsrc = Tvsource::find($input['id']);
            $tvsrc->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения записи '.$tvsrc->name. ' справочника медиа-источников.';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($tvsrc->update()){
                $msg = 'Запись '.$tvsrc->name.' справочника медиа-источников была изменена!';
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
            $model = Tvsource::find($id);
            if (!User::hasRole('admin')) {//вызываем event
                $msg = 'Попытка удаления записи ' . $model->name . ' из справочника медиа-источников.';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            if ($model->delete()) {
                $msg = 'Удалена запись ' . $model->name . ' из справочника медиа-источников!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
