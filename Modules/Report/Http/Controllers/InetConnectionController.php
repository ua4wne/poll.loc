<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Oit\Entities\Connection;

class InetConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(Role::granted('view_report')){
            $content = '<table class="table table-bordered table-hover">'.PHP_EOL;
            $content .= '<tr>
                        <th>Площадка</th>
                        <th>Арендатор</th>
                        <th>Участок</th>
                        <th>Дата подключения</th>
                        <th>Тип подключения</th>
                        <th>Примечание</th>
                    </tr>';
            //выборка по арендаторам
            $rows=DB::select("select r.title, p.name, r.area, i.date_on, i.type, i.comment from connections i
                join renters r on r.id = i.renter_id
                join places p on p.id = r.place_id
                order by p.name, CAST(r.area AS UNSIGNED)");
            foreach ($rows as $row){
                if($row->type=='static'){
                    $type = 'Выделенный IP';
                    $class = 'class="warning"';
                }
                if($row->type=='dynamic'){
                    $type = 'Динамический IP';
                    $class='';
                }
                $content .= '<tr '.$class.'><td>'. $row->name .'</td>
                                <td>'. $row->title .'</td>
                                <td>'. $row->area .'</td>
                                <td>'. $row->date_on .'</td>
                                <td>'. $type .'</td>
                                <td>'. $row->comment .'</td>
                             </tr>'.PHP_EOL;
            }
            $content .= '</table>'.PHP_EOL;
            $itog = Connection::whereNotNUll('date_on')->count();
            $data = [
                'title' => 'Подключения интернет',
                'head' => 'Подключения к интернет',
                'content' => $content,
                'itog' => $itog,
            ];
            return view('report::inet_conn',$data);
        }
    }
}
