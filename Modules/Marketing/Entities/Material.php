<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    //указываем имя таблицы
    protected $table = 'materials';

    protected $fillable = ['name'];
}
