<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    //указываем имя таблицы
    protected $table = 'visits';

    protected $fillable = ['data','hours','ucount'];

}
