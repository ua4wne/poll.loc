<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\OwnEcounter;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\OwnLog;
use Validator;

class OwnLogController extends Controller
{
    const NOT_VAL = 0; //нет значений
    const MORE_VAL = 1; //предыдущее значение больше текущего
    const LESS_VAL = 2; //предыдущее значение меньше текущего
    private $previous; //предыдущее показание счетчика
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('work_energy')){
            abort(503);
        }
        if(view()->exists('energy::ownlogs')){
            $head='Собственные счетчики';
            $year = date('Y');
            $title = 'Таблица показаний собственных счетчиков за '.$year.' год';
            $content = LibController::GetOwnEnergyTable($year);
            $data = [
                'title' => $title,
                'head' => $head,
                'content' => $content
            ];
            return view('energy::ownlogs',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
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
            $validator = Validator::make($input,[
                'own_ecounter_id' => 'required|numeric',
                'year' => 'required|max:4|string',
                'month' => 'required|max:2|string',
                'encount' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect('/energy/ownlog/add')->withErrors($validator)->withInput();
            }

            //$name = OwnEcounter::find($input['own_ecounter_id'])->name;
            $model = new OwnLog();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');
            $model->encount = $model->encount * $model->own_ecounter->koeff;

            $result = $this->CheckCountVal($model->own_ecounter_id,$model->encount,$model->year,$model->month);
            if($result===self::NOT_VAL){
                $msg = 'Отсутствует показание счетчика <strong>'. $model->own_ecounter->name .'</strong> за предыдущий месяц!';
                //$err = 'Отсутствует показание счетчика за предыдущий месяц!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                return redirect('/energy/own-counter')->with('error',$msg);
            }
            elseif($result===self::MORE_VAL){
                //$err = 'Предыдущее показание счетчика больше, чем текущее!';
                $msg = 'Предыдущее показание счетчика <strong>'. $model->own_ecounter->name .'</strong> больше, чем текущее!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                return redirect('/energy/own-counter')->with('error',$msg);
            }
            else{
                //удаляем, если имеется запись за текущий месяц, чтобы не было дублей
                OwnLog::where(['own_ecounter_id'=>$model->own_ecounter_id,'year'=>$model->year,'month'=>$model->month])->delete();
                $model->delta = $model->encount - $this->previous;
                $model->price = $model->delta * $model->own_ecounter->tarif;
                if($model->save()){
                    $msg = 'Данные счетчика <strong>'. $model->own_ecounter->name .'</strong> успешно добавлены.';
                    $ip = $request->getClientIp();
                    //вызываем event
                    event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                    return redirect('/energy/own-counter')->with('status',$msg);
                }
            }
        }
        if(view()->exists('energy::ownlog_add')){
            $head='Новая запись';
            $title = 'Ввод показаний собственных счетчиков';
            $counters = LibController::GetOwnCounters();
            $selown = array();
            $month = LibController::GetMonths();
            $smonth = date("m");
            $year = date('Y');
            if(strlen($smonth)==1)
                $smonth.='0';
            foreach($counters as $count) {
                $selown[$count->id] = $count->name.' ('.$count->text.')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => $head,
                'selown' => $selown,
                'year' => $year,
                'month' => $month,
            ];
            return view('energy::ownlog_add',$data);
        }
        abort(404);
    }

    //проверка корректности данных счетчика
    public function CheckCountVal($id,$val,$year,$month){
        $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
        $y = $period[0];
        $m = $period[1];
        //выбираем данные за предыдущий период
        $numrow = OwnLog::where(['own_ecounter_id'=>$id,'year'=>$y,'month'=>$m])->count();
        if($numrow) {
            $row = OwnLog::where(['own_ecounter_id'=>$id,'year'=>$y,'month'=>$m])->first();
            $this->previous = $row->encount;
            if($this->previous > $val)
                return self::MORE_VAL;
            else
                return self::LESS_VAL;
        }
        else return self::NOT_VAL;
    }

}
