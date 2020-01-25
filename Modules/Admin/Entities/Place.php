<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //указываем имя таблицы
    protected $table = 'places';

    protected $fillable = ['name','ecounter_id'];

    public function ecounter()
    {
        return $this->belongsTo('Modules\Admin\Entities\Ecounter');
    }
}
