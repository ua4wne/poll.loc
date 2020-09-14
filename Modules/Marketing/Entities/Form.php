<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    //указываем имя таблицы
    protected $table = 'forms';

    protected $fillable = ['name','is_active','is_work','form_group_id'];

    public function questions()
    {
        return $this->hasMany('Modules\Marketing\Entities\Question');
    }

    public function form_group(){
        return $this->belongsTo('Modules\Marketing\Entities\FormGroup');
    }
}
