<?php

namespace Modules\Oit\Entities;

use Illuminate\Database\Eloquent\Model;

class UnitGroup extends Model
{
    //указываем имя таблицы
    protected $table = 'unit_groups';

    protected $fillable = ['name'];
}
