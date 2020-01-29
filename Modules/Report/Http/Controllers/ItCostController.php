<?php

namespace Modules\Report\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ItCostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin') && !User::hasRole('director')){
            abort(503);
        }
        if(view()->exists('report::it_cost')){
            $title='Затраты ИТ';
            $year = date('Y');
            $typesel = ['expense'=>'По статьям расходов','supplier'=>'По поставщикам'];
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'year' => $year,
                'typesel' => $typesel,
            ];
            return view('report::it_cost',$data);
        }
        abort(404);
    }

    public function graph(Request $request){
        if($request->isMethod('post')){
            $year = $request->input('year');
            $type = $request->input('type');
            return $this::GetGraph($type,$year);
        }
    }

    public function table(Request $request){
        if($request->isMethod('post')){
            $year = $request->input('year');
            $type = $request->input('type');
            return $this::GetTable($type,$year);
        }
    }

    private static function GetGraph($type,$year){
        $data = array();
        switch ($type){
            case 'expense':
                $logs=DB::select("select round(sum(price),2) as price, ex.name as expens from costs c
                                            join unit_groups ug on ug.id = c.unitgroup_id
                                            join expenses ex on ex.id = c.expense_id
                                            where c.created_at like '$year-%' group by c.expense_id
                                            order by c.expense_id, price DESC");
                foreach($logs as $log){
                    $tmp = array();
                    $tmp['y'] = $log->expens;
                    $tmp['a'] = $log->price;
                    array_push($data,$tmp);
                }
                break;
            case 'supplier':
                $logs=DB::select("select round(sum(price),2) as price, s.name as sname from costs c
                                            join unit_groups ug on ug.id = c.unitgroup_id
                                            join suppliers s on s.id = c.supplier_id
                                            where c.created_at like '$year-%' group by c.supplier_id
                                            order by price DESC,c.supplier_id ASC");
                foreach($logs as $log){
                    $tmp = array();
                    $tmp['y'] = $log->sname;
                    $tmp['a'] = $log->price;
                    array_push($data,$tmp);
                }
                break;
        }
        return json_encode($data);
    }

    private static function GetTable($type,$year){
        $k = 1;
        switch ($type){
            case 'expense':
                $content='<br/><table class="table table-hover">
                            <tr><th>№ п\п</th><th>Статья затрат</th><th>Сумма, руб</th></tr>';
                $logs=DB::select("select c.name as cname, round(sum(price),2) as price, ex.name as expn from costs c
                                            join unit_groups ug on ug.id = c.unitgroup_id
                                            join expenses ex on ex.id = c.expense_id
                                            where c.created_at like '$year-%' group by c.expense_id,c.name
                                            order by c.expense_id, price DESC");
                $old = '';
                foreach($logs as $log){
                    if($log->expn != $old){
                        $content.='<tr><td colspan="3" class="info text-center">'.$log->expn.'</td></tr>';
                        $content.='<tr><td>'.$k.'</td><td>'.$log->cname.'</td><td>'.$log->price.'</td></tr>';
                    }
                    else{
                        $content.='<tr><td>'.$k.'</td><td>'.$log->cname.'</td><td>'.$log->price.'</td></tr>';
                    }
                    $old = $log->expn;
                    $k++;
                }
                break;
            case 'supplier':
                $content='<br/><table class="table table-hover table-striped">
                            <tr><th>№ п\п</th><th>Статья затрат</th><th>Сумма, руб</th><th>Поставщик</th></tr>';
                $logs=DB::select("select c.name as cname, round(sum(price),2) as price, s.name as sname from costs c
                                            join unit_groups ug on ug.id = c.unitgroup_id
                                            join suppliers s on s.id = c.supplier_id
                                            where c.created_at like '$year-%' group by c.supplier_id,c.name
                                            order by price DESC,c.supplier_id ASC");
                foreach($logs as $log){
                    $content.='<tr><td>'.$k.'</td><td>'.$log->cname.'</td><td>'.$log->price.'</td><td>'.$log->sname.'</td></tr>';
                    $k++;
                }
                break;
        }
        $content.='</table>';
        return $content;
    }

}
