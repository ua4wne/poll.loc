<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    //указываем имя таблицы
    protected $table = 'renters';

    protected $fillable = ['title','name','area','agent','phone1','phone2','encounter','koeff','place_id','status','division_id'];

    public function place()
    {
        return $this->belongsTo('Modules\Admin\Entities\Place');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division');
    }
}
