<?php

namespace Modules\Oit\Entities;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //указываем имя таблицы
    protected $table = 'suppliers';

    protected $fillable = ['name'];
}
