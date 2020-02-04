<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use App\Models\Renter;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\EnergyLog;
use Validator;

class RentersCounterController extends Controller
{
    const NOT_VAL = 0; //нет значений
    const MORE_VAL = 1; //предыдущее значение больше текущего
    const LESS_VAL = 2; //предыдущее значение меньше текущего
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('work_energy')) {
            abort(503);
        }
        if (view()->exists('energy::rentlog_add')) {
            $head = 'Новая запись';
            $title = 'Ввод показаний счетчиков арендаторов';
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
            return view('energy::rentlog_add', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
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
            ], $messages);
            if ($validator->fails()) {
                return redirect('/energy/renters-counter')->withErrors($validator)->withInput();
            }
            $model = new EnergyLog();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $result = $this->CheckCountVal($model->renter_id,$model->encount,$model->year,$model->month);
            if($result===self::NOT_VAL){
                $msg = 'Отсутствует показание счетчика арендатора <strong>'. $model->renter($model->renter_id)->title .'</strong> за предыдущий месяц!';
                //$err = 'Отсутствует показание счетчика за предыдущий месяц!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                //return redirect('/energy/renters-counter')->with('error',$msg);
            }
            elseif($result===self::MORE_VAL){
                $msg = 'Предыдущее показание счетчика арендатора <strong>'. $model->renter($model->renter_id)->title .'</strong> больше, чем текущее!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                //return redirect('/energy/renters-counter')->with('error',$msg);
            }
            else{
                //удаляем, если имеется запись за текущий месяц, чтобы не было дублей
                EnergyLog::where(['renter_id'=>$model->renter_id,'year'=>$model->year,'month'=>$model->month])->delete();
                $model->delta = $model->encount - $this->previous;
                $model->price = $model->delta * $model->renter($model->renter_id)->koeff;
                if($model->save()){
                    $msg = 'Данные счетчика арендатора <strong>'. $model->renter($model->renter_id)->title .'</strong> успешно добавлены.';
                    $ip = $request->getClientIp();
                    //вызываем event
                    event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                    //return redirect('/energy/renters-counter')->with('status',$msg);
                    $result = 'OK';
                }
                else{
                    $msg = 'При попытке добавления данных счетчика арендатора <strong>'. $model->renter($model->renter_id)->title .'</strong> возникла ошибка!';
                    $ip = $request->getClientIp();
                    //вызываем event
                    event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                    //return redirect('/energy/renters-counter')->with('error',$msg);
                    $result = 'ERR';
                }
            }
            return $result;
        }
    }

    public function select(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $place_id = $input['selrent'];
            $renters = Rentlog::GetActiveRentersByPlace($place_id);
            $content = '';
            foreach ($renters as $renter) {
                $content .= '<option value="' . $renter->id . '">' . $renter->title . ' (' . $renter->area . ')</option>'; //массив для заполнения данных в select формы
            }
            return $content;
        }
    }

    public function table(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->GetRenterTable($input['renter_id']);
        }
    }

    private function GetRenterTable($renter_id)
    {
        //показания счетчика арендатора в таблицу
        $year = date('Y');
        $data = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); //показания собственных счетчиков, нумерация с 1
        $content = '<table class="table table-hover table-striped">
            <tr><th>Счетчик</th><th>Январь</th><th>Февраль</th><th>Март</th><th>Апрель</th><th>Май</th><th>Июнь</th><th>Июль</th><th>Август</th><th>Сентябрь</th>
                <th>Октябрь</th><th>Ноябрь</th><th>Декабрь</th>
            </tr>';
        $renter = Renter::find($renter_id);
        $content .= '<tr><td>' . $renter->name .' (' . $renter->area . ')</td>';
        $logs = EnergyLog::select(['month', 'delta'])->where(['renter_id' => $renter_id, 'year' => $year])->orderBy('month', 'asc')->get();
        //return print_r($logs);
        $k = 1;
        foreach ($logs as $log) {
            if ((int)$log->month == $k) {
                $content .= '<td>' . $log->delta . '</td>';
            } else
                $content .= '<td>0</td>';
            $k++;
        }
        while ($k < 13) {
            $content .= '<td>0</td>';
            $k++;
        }
        $content .= '</tr>';
        $content .= '</table>';
        return $content;
    }

    //проверка корректности данных счетчика
    public function CheckCountVal($id,$val,$year,$month){
        $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
        $y = $period[0];
        $m = $period[1];
        //выбираем данные за предыдущий период
        $numrow = EnergyLog::where(['renter_id'=>$id,'year'=>$y,'month'=>$m])->count();
        if($numrow) {
            $row = EnergyLog::select('encount')->where(['renter_id'=>$id,'year'=>$y,'month'=>$m])->first();
            $this->previous = $row->encount;
            if($this->previous > $val)
                return self::MORE_VAL;
            else
                return self::LESS_VAL;
        }
        else return self::NOT_VAL;
    }

}
