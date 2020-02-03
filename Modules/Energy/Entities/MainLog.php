<?php

namespace Modules\Energy\Entities;

use Illuminate\Database\Eloquent\Model;

class MainLog extends Model
{
    //указываем имя таблицы
    protected $table = 'mainlogs';

    protected $fillable = ['ecounter_id','year','month','encount','delta','price'];

    public function ecounter()
    {
        return $this->belongsTo('Modules\Admin\Entities\Ecounter');
    }
}
