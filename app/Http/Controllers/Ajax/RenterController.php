<?php

namespace App\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\Models\Renter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;

class RenterController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $renter = Renter::find($input['id']);
            $renter->fill($input);
            if(!Role::granted('edit_renter')){//вызываем event
                $msg = 'Попытка изменения записи арендатора '.$renter->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($renter->update()){
                $msg = 'Запись арендатора '.$renter->name.' была изменена!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function switchRenter(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $active = $request->input('active');
            $renter = Renter::find($id);
            $renter->status = $active;
            if(!Role::granted('edit_renter')){//вызываем event
                $msg = 'Попытка изменения статуса арендатора '.$renter->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($renter->update()){
                if($active)
                    $msg = 'Арендатор '.$renter->name.' был активирован';
                else
                    $msg = 'Арендатор '.$renter->name.' был деактивирован';
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
            $model = Renter::find($id);
            if (!Role::granted('del_renter')) {//вызываем event
                $msg = 'Попытка удаления записи арендатора ' . $model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            if ($model->delete()) {
                $msg = 'Удалена запись арендатора ' . $model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
