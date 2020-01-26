<?php

namespace Modules\Admin\Http\Controllers\Ajax;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Validator;
use App\Events\AddEventLogs;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function switchLogin(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            $active = $request->input('active');
            if($id==1)
                return 'NOT';
            $user = User::find($id);
            $user->active = $active;
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения учетной записи '.$user->login;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($user->update()){
                if($active)
                    $msg = 'Учетная запись '.$user->login.' была включена';
                else
                    $msg = 'Учетная запись '.$user->login.' была выключена';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function editLogin(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $user = User::find($input['id']);
            $user->fill($input);
            if($input['id']==1)
                $user->active = 1; //первый админ всегда активен!!!
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения учетной записи '.$user->login;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($user->update()){
                $msg = 'Учетная запись '.$user->login.' была изменена!';
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
            $model = User::find($id);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка удаления учетной записи '.$model->login;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            //удаляем роли
            DB::table('role_user')->where('user_id', '=', $id)->delete();

            if($model->delete()) {
                $msg = 'Учетная запись '.$model->login.' была удалена!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else{
                return 'ERR';
            }
        }
    }

    public function addRole(Request $request){
        if($request->isMethod('post')){
            $user_id = $request->input('id');
            $login = User::find($user_id)->login;
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения ролей учетной записи '.$login;
                $ip = $request->getClientIp();
                event(new AddEventLogs('access',Auth::id(),$msg,$ip));
                return 'NO';
            }
            $roles = $request->input('roles');
            if(empty($roles))
                return 'EMPTY';
            DB::table('role_user')->where('user_id', '=', $user_id)->delete(); //удаляем предыдущие роли пользователя
            $values = array();
            foreach ($roles as $role){
                $role_id = Role::where('code',$role)->first()->id; //получили ID role
                $date = date('Y-m-d H:i:s');
                $val = array('role_id'=>$role_id,'user_id'=>$user_id,'created_at'=>$date,'updated_at'=>$date);
                array_push($values, $val);
            }
            if(DB::table('role_user')->insert($values)){
                $msg = 'Изменены роли для учетной записи '.$login;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function getRole(Request $request){
        if($request->isMethod('post')) {
            $id = $request->input('id');
            $roles = User::find($id)->roles;
            $code = array();
            foreach ($roles as $role){
                array_push($code,$role->code);
            }
            return json_encode($code);
        }
    }
}
