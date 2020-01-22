<?php

namespace Modules\Admin\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    //указываем имя таблицы
    protected $table = 'roles';

    protected $fillable = ['code','name'];

    /**
     * Пользователи, принадлежащие роли.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Действия, принадлежащие роли.
     */
    public function actions()
    {
        return $this->belongsToMany('Action');
    }

    public static function granted($code){
        // получить id текущего залогиненного юзера
        $user_id = Auth::id();
        $roles = User::find($user_id)->roles;
        foreach ($roles as $role){
            $actions = $role->actions;
            foreach ($actions as $action){
                if($action->code=='admin' || $action->code==$code)
                    return TRUE;
            }
        }
        return FALSE;
    }
}
