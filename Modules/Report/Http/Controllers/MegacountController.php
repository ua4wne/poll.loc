<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;

class MegacountController extends Controller
{

    public function bar_graph(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if ($start == 'start')
                $start = date('Y-m') . '-01';
            $finish = $input['finish'];
            if ($finish == 'finish')
                $finish = date('Y-m-d');
            $rows = DB::select("SELECT `data`, SUM(fw) AS forward, SUM(bw) AS backward FROM visitorlogs
                                WHERE `data` BETWEEN '$start' AND '$finish'
                                GROUP BY `data`");
            $data = array();
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['d'] = $row->data;
                    $tmp['f'] = $row->forward;
                    $tmp['b'] = $row->backward;
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function pie_graph(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if ($start == 'start')
                $start = date('Y-m') . '-01';
            $finish = $input['finish'];
            if ($finish == 'finish')
                $finish = date('Y-m-d');
            $rows = DB::select("SELECT SUM(vl.fw) AS forward, m.name AS megacnt, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY vl.counter_id");
            $data = array();
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['cnt'] = $row->megacnt.' ('.$row->plname.')';
                    $tmp['fw'] = $row->forward;
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }
}
