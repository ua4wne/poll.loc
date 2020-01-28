<?php

namespace Modules\Oit\Entities;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    //указываем имя таблицы
    protected $table = 'connections';

    protected $fillable = ['renter_id','date_on','type','comment'];

    public function renter()
    {
        return $this->belongsTo('App\Models\Renter');
    }
}
