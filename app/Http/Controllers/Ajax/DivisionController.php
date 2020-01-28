<?php

namespace App\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\Models\Division;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $division = Division::find($input['id']);
            $division->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения записи юрлица '.$division->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($division->update()){
                $msg = 'Запись юрлица '.$division->name.' была изменена!';
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
            $model = Division::find($id);
            if (!User::hasRole('admin')) {//вызываем event
                $msg = 'Попытка удаления записи юрлица ' . $model->name;
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
