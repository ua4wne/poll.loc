<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;

class OwnCounterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if(view()->exists('report::own-counter')){
            $title='Собственное потребление';
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
            ];
            return view('report::own-counter',$data);
        }
        abort(404);
    }

    public function graph(Request $request){
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $year = $input['year'];
            $counts = DB::select("select l.month, round(sum(l.delta),2) as delta, round(sum(l.price),2) as price from mainlogs l
                                        join ecounters e on e.id = l.ecounter_id where e.name !='Главный' and year='$year' group by l.month order by l.month"); //выбираем показания общих счетчиков
            $data = array();
            $logs = DB::select("select l.month, round(sum(l.delta),2) as delta, round(sum(l.price),2) as price from energylogs l
                                                    join renters r on r.id=l.renter_id
                                                    join places p on p.id=r.place_id
                                                    where year = '$year' group by l.month order by l.month"); //выбираем показания счетчиков арендаторов
            //return print_r($logs);
            $tmp = array();
            for($i=0;$i<count($counts);$i++){
                $count = $counts[$i];
                $log = $logs[$i];
                if($count->month == $log->month){
                    $tmp['m'] = $year.'-'.$log->month;
                    $tmp['d'] = round($count->delta - $log->delta,2);
                    $tmp['p'] = round($count->price - $log->price,2);
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function pie_graph(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $year = $input['year'];
            $data = array();
            $counts = DB::select("select e.name, e.id, sum(l.delta) as delta, sum(l.price) as price from mainlogs l
                                              join ecounters e on e.id = l.ecounter_id where e.name !='Главный' and year='$year' group by e.id order by e.id"); //выбираем показания общих счетчиков

            $logs = DB::select("select p.ecounter_id, round(sum(l.delta),2) as delta, round(sum(l.price),2) as price from energylogs l
                                            join renters r on r.id=l.renter_id
                                            join places p on p.id=r.place_id
                                            where year = '$year' group by p.ecounter_id order by p.ecounter_id");

            $owns = DB::select("select e.name, round(sum(l.delta),2) as delta from ownlogs l
                                            join own_ecounters e on e.id = l.own_ecounter_id
                                            where year='$year' group by l.own_ecounter_id"); //выбираем сумму показаний своих счетчиков

            //return print_r($owns);
            $tmp = array();
            $own_sum = 0;
            $k=0;
            for($i=0;$i<count($counts);$i++){
                $count = $counts[$i];
                $log = $logs[$i];
                if(strpos($count->name,'МС')){
                    $own = $owns[$k];
                    $own_sum+=$own->delta;
                    $k++;
                }
                if($count->id == $log->ecounter_id){
                    $tmp['name'] = $count->name;
                    if(strpos($count->name,'МС')){
                        $tmp['delta'] = round($count->delta - $log->delta - $own_sum, 2); //вычитаем показания собственных счетчиков из главного счетчика АЗ
                    }
                    else
                        $tmp['delta'] = round($count->delta - $log->delta,2);
                    array_push($data,$tmp);
                }
            }

            foreach ($owns as $own){
                //$tmp = array();
                $tmp['name'] = $own->name;
                $tmp['delta'] = $own->delta;
                array_push($data,$tmp);
            }
            return json_encode($data);
        }
    }

    public function table(Request $request){
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $year = $input['year'];
            $content='<table class="table table-hover table-striped">
            <tr><th>Счетчик</th><th>Январь</th><th>Февраль</th><th>Март</th><th>Апрель</th><th>Май</th><th>Июнь</th><th>Июль</th><th>Август</th><th>Сентябрь</th>
                <th>Октябрь</th><th>Ноябрь</th><th>Декабрь</th>
            </tr>';
            $cols = [0,0,0,0,0,0,0,0,0,0,0,0];
            $counts = DB::select("select e.name, e.id, l.month, l.delta, l.price from mainlogs l
                                              join ecounters e on e.id = l.ecounter_id where e.name !='Главный' and year='$year' order by e.id, l.month"); //выбираем показания общих счетчиков

            $logs = DB::select("select p.ecounter_id, l.month, round(sum(l.delta),2) as delta, round(sum(l.price),2) as price from energylogs l
                                            join renters r on r.id=l.renter_id
                                            join places p on p.id=r.place_id
                                            where year = '$year' group by p.ecounter_id, l.month order by p.ecounter_id, l.month");

            $owns = DB::select("select e.name, e.id, l.month, l.delta, l.price from ownlogs l
                                join own_ecounters e on e.id = l.own_ecounter_id where year='$year' order by e.id, l.month"); //выбираем показания общих счетчиков
            //return print_r($logs);
            $old = 'new';
            $k=1;
            $own_sum = 0;
            $o = 0;
            for($i=0;$i<count($counts);$i++){
                $count = $counts[$i];
                $log = $logs[$i];
                if(strpos($count->name,'МС')){
                    $own = $owns[$o];
                    $own_sum+=$own->delta;
                    $o++;
                }
                if($old != $count->name){
                    if($k > count($counts)/2){
                        while($k<13){
                            $content .='<td>0</td>';
                            $k++;
                        }
                    }
                    $content .= '<tr><td>'.$count->name.'</td>';
                    $k=1;
                }
                if($count->id == $log->ecounter_id){
                    if((int)$log->month == $k){
                        if(strpos($count->name,'МС')){
                            $delta = $count->delta - $log->delta - $own_sum;
                        }
                        else
                            $delta = $count->delta - $log->delta;
                        $content .='<td>'.$delta.'</td>';
                    }
                }
                $old = $count->name;
                $k++;
            }
            while($k<13){
                $content .='<td>0</td>';
                $k++;
            }
            $content .='</tr>';
            $k = 0;
            foreach ($owns as $own){
                $cols[$k] = $own->delta;
                $k++;
            }
            for($i=0; $i<count($cols); $i++){
                if(!empty($owns[$i])){
                    $own = $owns[$i];
                    if($i==0){
                        $content.='<tr><td>'.$own->name.'</td><td>'.$cols[$i].'</td>';
                    }
                    else{
                        $content.='<td>'.$cols[$i].'</td>';
                    }
                }
                else{
                    $content.='<td>'.$cols[$i].'</td>';
                }
            }
            $content.='</tr></table>';
            return $content;
        }
    }

}
