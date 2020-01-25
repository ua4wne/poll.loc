<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class OwnEcounter extends Model
{
    //указываем имя таблицы
    protected $table = 'own_ecounters';

    protected $fillable = ['name','text','koeff','tarif'];
}
