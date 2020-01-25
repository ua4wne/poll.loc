<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Describer extends Model
{
    //указываем имя таблицы
    protected $table = 'describers';

    protected $fillable = ['email','status'];
}
