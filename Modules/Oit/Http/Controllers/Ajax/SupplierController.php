<?php

namespace Modules\Oit\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Supplier;

class SupplierController extends Controller
{
    public function edit(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $supplier = Supplier::find($input['id']);
            $supplier->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения данных поставщика '.$supplier->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($supplier->update()){
                $msg = 'Данные поставщика '.$supplier->name.' были изменены!';
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
            $model = Supplier::find($id);
            if (!User::hasRole('admin')) {//вызываем event
                $msg = 'Попытка удаления поставщика ' . $model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            if ($model->delete()) {
                $msg = 'Поставщик ' . $model->name . ' был удален!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info', Auth::id(), $msg, $ip));
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
