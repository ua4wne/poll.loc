<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\Role;

class RenterCounterController extends Controller
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
        if(view()->exists('report::rent-counter')){
            $title='Счетчики арендаторов';
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
            ];
            return view('report::rent-counter',$data);
        }
        abort(404);
    }

    public function graph(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $year = $input['year'];
            $data = array();
            $id = Ecounter::where(['name'=>'Главный'])->first()->id; //выбираем главный счетчик
            $logs = DB::select("select month, round(sum(delta),2) as delta, round(sum(price),2) as price from mainlogs where ecounter_id !=$id and `year`='$year' group by `month` order by `month`");
            //return print_r($logs);
            foreach($logs as $log){
                $tmp = array();
                $tmp['m'] = $year.'-'.$log->month;
                $tmp['d'] = $log->delta;
                $tmp['p'] = $log->price;
                array_push($data,$tmp);
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
            $logs = DB::select("select p.name, round(sum(l.delta),2) as delta from energylogs l
                                        join renters r on r.id = l.renter_id
                                        join places p on p.id = r.place_id
                                        where year='$year' group by p.name order by p.name");
            //return print_r($logs);
            foreach($logs as $log){
                $tmp = array();
                if($log->name=='Главный'){
                    $main = $log->delta;
                }
                else{
                    $tmp['name'] = $log->name;
                    $tmp['delta'] = $log->delta;
                    array_push($data,$tmp);
                }
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
            $logs = DB::select("select p.name, l.month, round(sum(l.delta),2) as delta from energylogs l
                                                join renters r on r.id = l.renter_id
                                                join places p on p.id = r.place_id
                                                where year='$year' group by p.name, l.month order by p.name, l.month");
            //return print_r($logs);
            $old = 'new';
            $k=0;
            foreach($logs as $log){
                if($old != $log->name){
                    if($k>1){
                        while($k<13){
                            $content .='<td>0</td>';
                            $k++;
                        }
                        $content .='</tr>';
                    }
                    $k=1;
                    $content .= '<tr><td>'.$log->name.'</td>';
                }
                if((int)$log->month == $k){
                    $content .='<td>'.$log->delta.'</td>';
                }
                elseif((int)$log->month > $k){
                    while($k<(int)$log->month){
                        $content .='<td>0</td>';
                        $k++;
                    }
                    $content .='<td>'. $log->delta. '</td>';
                }
                else
                    $content .='<td>0</td>';
                $k++;
                $old = $log->name;
            }
            while($k<13){
                $content .='<td>0</td>';
                $k++;
            }
            $content .='</tr>';
            $content.='</table>';
            return $content;
        }
    }

}
