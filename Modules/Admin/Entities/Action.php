<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    //указываем имя таблицы
    protected $table = 'actions';

    protected $fillable = ['code','name'];

    /**
     * Роли, принадлежащие действию.
     */
    public function roles()
    {
        return $this->belongsToMany('Role');
    }
}
