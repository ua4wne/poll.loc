<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Tvsource extends Model
{
    //указываем имя таблицы
    protected $table = 'tvsources';

    protected $fillable = ['name'];
}
