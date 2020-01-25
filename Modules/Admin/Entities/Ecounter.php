<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Ecounter extends Model
{
    //указываем имя таблицы
    protected $table = 'ecounters';

    protected $fillable = ['name','text','koeff','tarif'];

    public function places()
    {
        //return $this->belongsTo('Modules\Admin\Entities\Place');
        return $this->belongsToMany('Modules\Admin\Entities\Place');
    }
}
