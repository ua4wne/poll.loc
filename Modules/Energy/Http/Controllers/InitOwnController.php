<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\OwnLog;
use Validator;

class InitOwnController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Role::granted('work_energy')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input, [
                'own_ecounter_id' => 'required|numeric',
                'year' => 'required|max:4|string',
                'month' => 'required|max:2|string',
                'encount' => 'required|numeric',
                'delta' => 'required|numeric',
            ], $messages);
            if ($validator->fails()) {
                return redirect('/energy/initown')->withErrors($validator)->withInput();
            }
            $model = new OwnLog();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $model->encount = $model->encount * $model->own_ecounter->koeff;
            $model->delta = $model->delta * $model->own_ecounter->koeff;
            $model->price = $model->delta * $model->own_ecounter->tarif;
            $result = OwnLog::updateOrCreate(['own_ecounter_id' => $model->own_ecounter_id, 'year' => $model->year,'month'=>$model->month],
                ['encount' => $model->encount,'delta'=>$model->delta,'price'=>$model->price,'created_at'=>$model->created_at]);
            if(!empty($result)){
                $msg = 'Начальные данные счетчика <strong>'. $model->own_ecounter->name .'</strong> за '. LibController::SetMonth($model->month) . ' месяц ' . $model->year . ' года были обновлены.';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
            }
            return redirect('/energy/initown')->with('status',$msg);

        }

        if(view()->exists('energy::initown')){
            $head='Новая запись';
            $title = 'Ввод начальных показаний счетчиков';
            $counters = LibController::GetOwnCounters();
            $selmain = array();
            $month = LibController::GetMonths();
            $smonth = date("m");
            $year = date('Y');
            if(strlen($smonth)==1)
                $smonth.='0';
            foreach($counters as $count) {
                $selmain[$count->id] = $count->name.' ('.$count->text.')'; //массив для заполнения данных в select формы
            }
            $tbl = LibController::GetOwnEnergyTable($year);
            $data = [
                'title' => $title,
                'head' => $head,
                'selmain' => $selmain,
                'year' => $year,
                'month' => $month,
                'tbl' => $tbl,
            ];
            return view('energy::initown',$data);
        }
        abort(404);
    }
}
