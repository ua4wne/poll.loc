<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\GetData;
use Modules\Marketing\Entities\Megacount;
use Modules\Marketing\Entities\Visitorlog;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('admin::requests')) {
            //какой режим установлен?
            //$debug = SysConst::where(['param'=>'USE_GET_DEBUG'])->first()->val;
            //выбираем данные из таблицы
            $rows = GetData::all();
            return view('admin::requests', [
                'title' => 'Отладчик',
                'head' => 'Запросы к системе',
                'rows' => $rows,
            ]);
        }
        abort(404);
    }

    public function control(Request $request)
    {
        $debug = false;
        if ($request->isMethod('post')) {
            if($debug){
                $log = new GetData();
                $log->created_at = date('Y-m-d H:i:s');
                $log->type = 'POST';
                $log->request = $request->getContent();
                $log->save();
            }
            $json = $request->getContent();
            $data = json_decode($json,true);
            //разгребаем поступившие данные
            //определяем есть ли такой счетчик, если нет - то создаем запись в БД
            $serial = $data['sensor-info']['serial-number'];
            $ip = $data['sensor-info']['ip-address'];
            $name = $data['sensor-info']['name'];
            $mega = Megacount::firstOrCreate(['ip_address'=>$ip],['serial_number'=>$serial,'name'=>$name,'place_id'=>1]);
            $counter_id = $mega->id;
            $rows = $data['content']['element'][0]['measurement'];
            foreach ($rows as $row){
                $dt = explode('T', $row['from']);
                $date = $dt[0];
                $hour = substr($dt[1],0,2);
                foreach ($row['value'] as $val){
                    if($val['label']=='fw') $fw = $val['value'];
                    if($val['label']=='bw') $bw = $val['value'];
                }
                if((int)$hour > 9 && (int)$hour < 21){//рабочее время счетчика
                    Visitorlog::updateOrCreate(['data'=>$date,'hours'=>$hour,'counter_id'=>$counter_id],['fw'=>$fw,'bw'=>$bw,'created_at'=>date('Y-m-d H:i:s')]);
                }
            }
        }
        return view('admin::empty');
    }

    public function delete(Request $request){
        if($request->isMethod('post')){
            $id = $request->input('id');
            if($id=='delete')
                DB::table('requests')->delete();
            return 'OK';
        }
    }
}
