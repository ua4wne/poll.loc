<?php

namespace Modules\Energy\Entities;

use Illuminate\Database\Eloquent\Model;

class OwnLog extends Model
{
    //указываем имя таблицы
    protected $table = 'ownlogs';

    protected $fillable = ['own_ecounter_id','year','month','encount','delta','price'];

    public function own_ecounter()
    {
        return $this->belongsTo('Modules\Admin\Entities\OwnEcounter');
    }
}
