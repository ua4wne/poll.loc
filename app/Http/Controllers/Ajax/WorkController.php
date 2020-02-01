<?php

namespace App\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;

class WorkController extends Controller
{
    public function delete(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            $model = Rentlog::find($id);
            if (!Role::granted('work_guard')) {//вызываем event
                $msg = 'Попытка удаления записи из таблицы rentlogs не уполноченным пользователем!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('access', Auth::id(), $msg, $ip));
                return 'NO';
            }

            if ($model->delete()) {
                return 'OK';
            } else {
                return 'ERR';
            }
        }
    }
}
