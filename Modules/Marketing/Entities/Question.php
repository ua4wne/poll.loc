<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //указываем имя таблицы
    protected $table = 'questions';

    protected $fillable = ['name','form_id','visibility'];

    public function answers()
    {
        return $this->hasMany('Modules\Marketing\Entities\Answer');
    }

    public function form(){
        return $this->belongsTo('Modules\Marketing\Entities\Form');
    }

    public function getOnAnswerAttribute()
    {
        $on = Answer::where(['question_id'=>$this->id,'visibility'=>1])->get()->count();
        return $on;
    }

    public function getOffAnswerAttribute()
    {
        $off = Answer::where(['question_id'=>$this->id,'visibility'=>0])->count('id');
        return $off;
    }
}
