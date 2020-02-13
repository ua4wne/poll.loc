<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\EventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EventLogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('admin::events')){
            $title='Системный лог';
            $rows = EventLog::all();
            $data = [
                'title' => $title,
                'head' => 'Журнал событий системы',
                'rows' => $rows,
            ];
            return view('admin::events',$data);
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function delete(Request $request)
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            if($input['id']=='all') {
                //удаляем все информационные записи
                EventLog::whereIn('type',['info','logon','logoff'])->delete();
                $msg = 'Журнал событий был очищен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else {
                $model = EventLog::find($input['id']);
                $model->delete();
                return 'OK';
            }
            return 'ERR';
        }
    }
}
