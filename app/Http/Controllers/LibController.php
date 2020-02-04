<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\OwnEcounter;
use Modules\Energy\Entities\MainLog;
use Modules\Energy\Entities\OwnLog;

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
    //показания главных счетчиков в таблицу
    public static function GetMainEnergyTable($year,$losses=false){
        $data = array(1=>0,0,0,0,0,0,0,0,0,0,0,0); //показания общих счетчиков, нумерация с 1
        $main = array(1=>0,0,0,0,0,0,0,0,0,0,0,0); //показания главного счетчика, нумерация с 1
        $content='<table class="table table-hover table-striped">
            <tr><th>Счетчик</th><th>Январь</th><th>Февраль</th><th>Март</th><th>Апрель</th><th>Май</th><th>Июнь</th><th>Июль</th><th>Август</th><th>Сентябрь</th>
                <th>Октябрь</th><th>Ноябрь</th><th>Декабрь</th>
            </tr>';
        $models = Ecounter::all();
        foreach ($models as $model){
            $content.='<tr><td>'.$model->name.'</td>';
            $logs = MainLog::select(['month','delta'])->where(['ecounter_id'=>$model->id,'year'=>$year])->orderBy('month','asc')->get();
            //return print_r($logs);
            $k=1;
            foreach($logs as $log){
                if((int)$log->month == $k){
                    $content .='<td>'.$log->delta.'</td>';
                }
                else
                    $content .='<td>0</td>';
                $k++;
            }
            while($k<13){
                $content .='<td>0</td>';
                $k++;
            }
            $content .='</tr>';
            //считаем потери
            $k=1;
            if($model->name == 'Главный') {
                foreach ($logs as $log) {
                    if ((int)$log->month == $k)
                        $main[$k] = $log->delta;
                    $k++;
                }
            }
            else{
                foreach ($logs as $log) {
                    if ((int)$log->month == $k)
                        $data[$k] = $data[$k] + $log->delta;
                    else
                        $data[$k] = $data[$k] + 0;
                    $k++;
                }
            }
        }
        if($losses){
            //выводим данные по потерям
            $content .= '<tr><td>Потери</td>';
            for($i=1; $i<13; $i++){
                $val = $main[$i] - $data[$i];
                if($val>0)
                    $content .= '<td class="danger">' . $val . '</td>';
                else
                    $content .= '<td class="success">' . $val . '</td>';
            }
            $content.='</tr>';
        }
        $content.='</table>';
        return $content;
    }

    //показания собственных счетчиков в таблицу
    public static function GetOwnEnergyTable($year){
        $data = array(1=>0,0,0,0,0,0,0,0,0,0,0,0); //показания собственных счетчиков, нумерация с 1
        $content='<table class="table table-hover table-striped">
            <tr><th>Счетчик</th><th>Январь</th><th>Февраль</th><th>Март</th><th>Апрель</th><th>Май</th><th>Июнь</th><th>Июль</th><th>Август</th><th>Сентябрь</th>
                <th>Октябрь</th><th>Ноябрь</th><th>Декабрь</th>
            </tr>';
        $models = OwnEcounter::all();
        foreach ($models as $model){
            $content.='<tr><td>'.$model->name.'</td>';
            $logs = OwnLog::select(['month','delta'])->where(['own_ecounter_id'=>$model->id,'year'=>$year])->orderBy('month','asc')->get();
            //return print_r($logs);
            $k=1;
            foreach($logs as $log){
                if((int)$log->month == $k){
                    $content .='<td>'.$log->delta.'</td>';
                }
                else
                    $content .='<td>0</td>';
                $k++;
            }
            while($k<13){
                $content .='<td>0</td>';
                $k++;
            }
            $content .='</tr>';
            //считаем потери
            $k=1;
            foreach ($logs as $log) {
                    if ((int)$log->month == $k)
                        $data[$k] = $data[$k] + $log->delta;
                    else
                        $data[$k] = $data[$k] + 0;
                    $k++;
                }
        }
        $content.='</table>';
        return $content;
    }
}
