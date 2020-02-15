<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Energy\Entities\MainLog;

class MainController extends Controller
{
    public function index(){
        $year = date('Y');
        $month = date('m');
        $start = date('Y-m').'-01';
        $finish = date('Y-m-d');
        //инфа для виджета энергопотребления
        $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
        $y = $period[0];
        $m = $period[1];
        $main_count = 0;
        $rows = MainLog::where(['year'=>$y,'month'=>$m])->orderBy('ecounter_id', 'asc')->get();
        $energy = '<table class="table table-hover table-striped"><tr  class="tblh"><th>Счетчик</th><th>Потребление, кВт</th></tr>';
        foreach($rows as $row){
            $energy.='<tr><td>'.$row->ecounter->name.'</td><td>'.$row->delta.'</td></tr>';
            if($row->ecounter->name=='Главный')
                $main_count = $row->delta;
        }
        $energy.='</table>';
        $people = Visit::whereBetween('data',[$start,$finish])->sum('ucount');
        $visitors='<table class="table table-hover table-striped"><tr  class="tblh"><th>Показатель</th><th>Кол-во человек</th></tr>';
        $visitors .='<tr><td>Посетителей всего</td><td>'.$people.'</td></tr>';
        $result = DB::select("select sum(ucount) as val from visits where data between '$start' and '$finish' group by `data`");
        if(!empty($result)){
            $max = (max((array)$result));
            if(!isset($max))
                $max = 0;
            $visitors.='<tr><td>Максимально за день</td><td>'.$max->val.'</td></tr>';
            if(!empty($result)>0)
                $min = (min((array)$result));
            if(!isset($min))
                $min = 0;
            $visitors.='<tr><td>Минимально за день</td><td>'.$min->val.'</td></tr>';
        }
        $visitors.='</table>';
        $time_avg = 0;
        $worktime = '<table class="table table-hover table-striped"><tr  class="tblh"><th>Компания</th><th>Часов в день</th></tr>';
        $result = DB::select("SELECT renters.title, renters.area, Sum(period1)+Sum(period2)+Sum(period3)+Sum(period4)+Sum(period5)+Sum(period6)+Sum(period7)+Sum(period8)+Sum(period9)+Sum(period10)+Sum(period11) AS alltime,
                count(rentlogs.`data`) AS alldata FROM rentlogs INNER JOIN renters ON renters.id = rentlogs.renter_id
                WHERE rentlogs.`data` between '$start' AND '$finish'
                GROUP BY renters.title, renters.area ORDER BY alltime desc");
        if(!empty($result)){
            $res = $result[0]; //это максимум
            if(!isset($res))
                $res = 1;
            if($res->alldata>0)
                $hours = $res->alltime/$res->alldata;
            if(isset($hours))
                $hours = round($hours,2);
            else
                $hours = 0;
            if($hours>=9)
                $worktime.='<tr><td>'.$res->title.'</td><td class="success">'.$hours.'</td></tr>';
            else
                $worktime.='<tr><td>'.$res->title.'</td><td class="danger">'.$hours.'</td></tr>';
            $i = count((array)$result)-1; //это минимум
            if(isset($result[$i]))
                $res = $result[$i];
            else
                $res = 1;
            if($res->alldata>0)
                $hours = $res->alltime/$res->alldata;
            if($hours>=9)
                $worktime.='<tr><td>'.$res->title.'</td><td class="success">'.$hours.'</td></tr>';
            else
                $worktime.='<tr><td>'.$res->title.'</td><td class="danger">'.$hours.'</td></tr>';
            //считаем среднее время работы домов
            $time = 0;
            $data = 0;
            foreach ($result as $res){
                $time = $time + $res->alltime;
                $data = $data + $res->alldata;
            }
            if($data>0)
                $hours = $time/$data;
            $time_avg = round($hours,2);
            if($time_avg>=9)
                $worktime.='<tr><td>В среднем</td><td class="success">'.$time_avg.'</td></tr>';
            else
                $worktime.='<tr><td>В среднем</td><td class="danger">'.$time_avg.'</td></tr>';
        }
        $worktime.='</table>';
        $data = [
            'title' => 'Главная панель',
            'people' => $people,
            'visitors' => $visitors,
            'time_avg' => $time_avg,
            'worktime' => $worktime,
            'main_count' => $main_count,
            'energy' => $energy,
        ];

        if(view()->exists('main_index')){
            return view('main_index',$data);
        }
        abort(404);
    }

    public function compare(Request $request){
        if($request->isMethod('post')) {
            $year = date('Y');
            $month = date('m');
            $start = date('Y-m').'-01';
            $finish = date('Y-m-d');
            $input = $request->except('_token'); //параметр _token нам не нужен
            $data = array();
            $date = $year.'-'.$month.'-';
            $logs = DB::select("select sum(ucount) as ucount from visits where `data` like '$date%'"); //текущий месяц
            if($logs){
                foreach($logs as $log){
                    $tmp = array();
                    $tmp['y'] = $year.'-'.$month;
                    if($log->ucount)
                        $tmp['a'] = $log->ucount;
                    else
                        $tmp['a'] = 0;
                    array_push($data,$tmp);
                }
            }

            $period = explode('-', date('Y-m-d', strtotime("$finish -1 month"))); // предыдущий месяц
            $y = $period[0];
            $m = $period[1];
            //$d = $period[2];
            $date = $y.'-'.$m.'-';
            $logs = DB::select("select sum(ucount) as ucount from visits where `data` like '$date%'");
            foreach($logs as $log){
                $tmp = array();
                $tmp['y'] = $y.'-'.$m;
                $tmp['a'] = $log->ucount;
                array_push($data,$tmp);
            }
            $period = explode('-', date('Y-m-d', strtotime("$finish -2 month"))); // препредыдущий месяц
            $y = $period[0];
            $m = $period[1];
            //$d = $period[2];
            $date = $y.'-'.$m.'-';
            $logs = DB::select("select sum(ucount) as ucount from visits where `data` like '$date%'");
            foreach($logs as $log){
                $tmp = array();
                $tmp['y'] = $y.'-'.$m;
                $tmp['a'] = $log->ucount;
                array_push($data,$tmp);
            }
            $period = explode('-', date('Y-m-d', strtotime("$finish -1 year"))); //текущий месяц предыдущего года
            $y = $period[0];
            $m = $period[1];
            //$d = $period[2];
            $date = $y.'-'.$m.'-';
            $logs = DB::select("select sum(ucount) as ucount from visits where `data` like '$date%'");
            foreach($logs as $log){
                $tmp = array();
                $tmp['y'] = $y.'-'.$m;
                $tmp['a'] = $log->ucount;
                array_push($data,$tmp);
            }
            return json_encode($data);
        }
    }
}
