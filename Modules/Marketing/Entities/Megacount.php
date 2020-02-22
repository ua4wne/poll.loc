<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Megacount extends Model
{
    //указываем имя таблицы
    protected $table = 'megacounts';

    protected $fillable = ['serial_number','name','ip_address','descr','place_id','status'];

    public function place()
    {
        return $this->belongsTo('Modules\Admin\Entities\Place');
    }
}
