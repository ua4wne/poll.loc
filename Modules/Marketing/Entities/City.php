<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //указываем имя таблицы
    protected $table = 'cities';

    protected $fillable = ['name'];
}
