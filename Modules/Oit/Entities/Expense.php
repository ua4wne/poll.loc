<?php

namespace Modules\Oit\Entities;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //указываем имя таблицы
    protected $table = 'expenses';

    protected $fillable = ['name'];
}
