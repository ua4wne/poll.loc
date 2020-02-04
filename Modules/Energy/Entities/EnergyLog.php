<?php

namespace Modules\Energy\Entities;

use App\Models\Renter;
use Illuminate\Database\Eloquent\Model;

class EnergyLog extends Model
{
    //указываем имя таблицы
    protected $table = 'energylogs';

    protected $fillable = ['renter_id','year','month','encount','delta','price'];

    public function renter($id)
    {
        return Renter::find($id);
    }
}
