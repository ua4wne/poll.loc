<?php

namespace Modules\Oit\Entities;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    //указываем имя таблицы
    protected $table = 'costs';

    protected $fillable = ['name','supplier_id','price','unitgroup_id','expense_id'];

    public function supplier()
    {
        return $this->belongsTo('Modules\Oit\Entities\Supplier');
    }

    public function unitgroup()
    {
        return $this->belongsTo('Modules\Oit\Entities\UnitGroup');
    }

    public function expense()
    {
        return $this->belongsTo('Modules\Oit\Entities\Expense');
    }
}
