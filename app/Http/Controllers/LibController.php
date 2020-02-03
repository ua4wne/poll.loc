<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\OwnEcounter;

class LibController extends Controller
{
    //выборка всех счетчиков
    public static function GetCounters(){
        return Ecounter::select(['id','name','text'])->orderBy('name', 'asc')->get();
    }

    //выборка всех собственных счетчиков
    public static function GetOwnCounters(){
        return OwnEcounter::select(['id','name','text'])->orderBy('name', 'asc')->get();
    }

    //выборка всех месяцев
    public static function GetMonths(){
        return array('01'=>'Январь','02'=>'Февраль','03'=>'Март','04'=>'Апрель','05'=>'Май','06'=>'Июнь','07'=>'Июль',
            '08'=>'Август','09'=>'Сентябрь','10'=>'Октябрь','11'=>'Ноябрь','12'=>'Декабрь',);
    }

    //возвращаем название месяца по номеру
    public static function SetMonth($id){
        $months = self::GetMonths();
        foreach ($months as $key=>$month){
            if($key == $id)
                return mb_strtolower($month,'UTF-8');
        }
    }
}
