<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\LibController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\MainLog;

class MainCounterController extends Controller
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
        if(view()->exists('report::main-counter')){
            $title='Потребление общих счетчиков';
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
            ];
            return view('report::main-counter',$data);
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
            $id = Ecounter::where(['name'=>'Главный'])->first()->id; //выбираем главный счетчик
            $logs = MainLog::select(['month','delta','price'])->where(['ecounter_id'=>$id,'year'=>$year])->orderBy('month', 'asc')->get();
            $data = array();
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

    public function pie_graph(Request $request){
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $year = $input['year'];
            $data = array();
            $main=0;
            $encount=0;
            $logs=DB::select("select e.name,sum(l.delta) as delta from mainlogs l
                                            join ecounters e on e.id = l.ecounter_id
                                            where year='$year' group by ecounter_id");
            foreach($logs as $log){
                $tmp = array();
                if($log->name=='Главный'){
                    $main = $log->delta;
                }
                else{
                    $tmp['name'] = $log->name;
                    $tmp['delta'] = $log->delta;
                    array_push($data,$tmp);
                    $encount = $encount + $log->delta;
                }
            }
            $err = $main - $encount;
            if($err>0){
                $tmp['name'] = 'Потери';
                $tmp['delta'] = $err;
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
            return LibController::GetMainEnergyTable($year,true);
        }
    }

}
