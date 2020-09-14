<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class FormGroup extends Model
{
    //указываем имя таблицы
    protected $table = 'form_groups';

    protected $fillable = ['title'];

    public function forms()
    {
        return $this->hasMany('Modules\Marketing\Entities\Form');
    }
}
