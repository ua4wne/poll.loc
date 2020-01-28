<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    //указываем имя таблицы
    protected $table = 'divisions';

    protected $fillable = ['name','status'];

    /**
     * Арендаторы, принадлежащие дивизиону.
     */
    public function renters()
    {
        return $this->belongsToMany('App\Models\Renter');
    }
}
