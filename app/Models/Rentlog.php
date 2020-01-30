<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rentlog extends Model
{
    //указываем имя таблицы
    protected $table = 'rentlogs';

    protected $fillable = ['renter_id','data','period1','period2','period3','period4','period5','period6','period7','period8','period9','period10','period11'];

    public function renter()
    {
        return $this->belongsTo('app\Models\Renter');
    }
}
