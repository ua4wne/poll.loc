<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\EnergyLog;
use Validator;

class InitCounterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if (!Role::granted('work_energy')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input, [
                'renter_id' => 'required|numeric',
                'year' => 'required|max:4|string',
                'month' => 'required|max:2|string',
                'encount' => 'required|numeric',
                'delta' => 'required|numeric',
            ], $messages);
            if ($validator->fails()) {
                return 'ERR';
            }
            $model = new EnergyLog();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $model->price = $model->delta * $model->renter($model->renter_id)->koeff;
            $result = EnergyLog::updateOrCreate(['renter_id' => $model->renter_id, 'year' => $model->year,'month'=>$model->month],
                ['encount' => $model->encount,'delta'=>$model->delta,'price'=>$model->price,'created_at'=>$model->created_at]);
            if(!empty($result)){
                $msg = 'Начальные данные счетчика <strong>'. $model->renter($model->renter_id)->name .'</strong> за '. LibController::SetMonth($model->month) . ' месяц ' . $model->year . ' года были обновлены.';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            return 'ERR';
        }

        if (view()->exists('energy::init_rent_counter')) {
            $head = 'Новая запись';
            $title = 'Ввод начальных показаний счетчиков арендаторов';
            $places = Place::select(['id', 'name'])->get();
            $place_id = $places[0]->id;
            $selplace = array();
            $renters = Rentlog::GetActiveRentersByPlace($place_id);
            $selrent = array();
            foreach ($renters as $renter) {
                $selrent[$renter->id] = $renter->title . ' (' . $renter->area . ')'; //массив для заполнения данных в select формы
            }
            foreach ($places as $place) {
                $selplace[$place->id] = $place->name; //массив для заполнения данных в select формы
            }
            $month = LibController::GetMonths();
            $smonth = date("m");
            $year = date('Y');
            if (strlen($smonth) == 1)
                $smonth .= '0';

            $data = [
                'title' => $title,
                'head' => $head,
                'selplace' => $selplace,
                'selrent' => $selrent,
                'year' => $year,
                'month' => $month,
            ];
            return view('energy::init_rent_counter', $data);
        }
        abort(404);
    }
}
