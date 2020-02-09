<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //указываем имя таблицы
    protected $table = 'answers';

    protected $fillable = ['name','question_id','htmlcode','source'];

    public function question(){
        return $this->belongsTo('Modules\Marketing\Entities\Question');
    }
}
