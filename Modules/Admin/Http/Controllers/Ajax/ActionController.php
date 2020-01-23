<?php

namespace Modules\Admin\Http\Controllers\Ajax;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Action;
use Modules\Admin\Entities\Role;

class ActionController extends Controller
{
    public function getAction(Request $request){
        if($request->isMethod('post')) {
            $id = $request->input('id');
            $actions = Role::find($id)->actions;
            $code = array();
            foreach ($actions as $action){
                array_push($code,$action->code);
            }
            return json_encode($code);
        }
    }

    public function addAction(Request $request){
        $role = Role::find($request->input('id'))->name;
        if(!User::hasRole('admin')){//вызываем event
            $msg = 'Попытка создания нового разрешения для роли '.$role;
            $ip = $request->getClientIp();
            event(new AddEventLogs('access',Auth::id(),$msg,$ip));
            return 'NO';
        }
        if($request->isMethod('post')){
            $role_id = $request->input('id');
            $actions = $request->input('actions');
            if(empty($actions))
                return 'EMPTY';
            DB::table('action_role')->where('role_id', '=', $role_id)->delete(); //удаляем предыдущие разрешения для роли
            $values = array();
            foreach ($actions as $action){
                $action_id = Action::where('code',$action)->first()->id; //получили ID action
                $date = date('Y-m-d H:i:s');
                $val = array('role_id'=>$role_id,'action_id'=>$action_id,'created_at'=>$date,'updated_at'=>$date);
                array_push($values, $val);
            }
            if(DB::table('action_role')->insert($values)){
                $msg = 'Изменены разрешения для роли '.$role;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }
}
