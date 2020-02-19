<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\GetData;

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
        if ($request->isMethod('post')) {
            $log = new GetData();
            $log->created_at = date('Y-m-d H:i:s');
            $log->type = 'POST';
            $log->request = $request->getContent();
            $log->save();
        }
        else {
            $json_str = file_get_contents('php://input');
            $log = new GetData();
            $log->created_at = date('Y-m-d H:i:s');
            $log->type = 'JSON';
            $log->request = $json_str;
            $log->save();
        }
        return 'OK';
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
