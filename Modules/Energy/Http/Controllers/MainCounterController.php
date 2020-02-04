<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\MainLog;
use Validator;

class MainCounterController extends Controller
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
        if(view()->exists('energy::mainlogs')){
            $head='Общие счетчики';
            $year = date('Y');
            $title = 'Таблица показаний общих счетчиков за '.$year.' год';
            $content = LibController::GetMainEnergyTable($year,false);
            $data = [
                'title' => $title,
                'head' => $head,
                'content' => $content
            ];
            return view('energy::mainlogs',$data);
        }
        abort(404);
    }

    public function create(Request $request){
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
                'ecounter_id' => 'required|numeric',
                'year' => 'required|max:4|string',
                'month' => 'required|max:2|string',
                'encount' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect('/energy/mainlog/add')->withErrors($validator)->withInput();
            }

            $name = Ecounter::find($input['ecounter_id'])->name;
            $model = new MainLog();
            $model->fill($input);
            $model->created_at = date('Y-m-d H:i:s');

            if($name == 'Главный'){// это главный счетчик, по нему представляют только потребление, показания нужно вычислять самим
                $model->delta = $model->encount;
                $period = explode('-', date('Y-m', strtotime("$model->year-$model->month-01 -1 month"))); //определяем предыдущий период
                $y = $period[0];
                $m = $period[1];
                //выбираем данные за предыдущий период
                $row = MainLog::where(['ecounter_id'=>$model->ecounter_id,'year'=>$y,'month'=>$m])->first();
                if(!empty($row)) {
                    $model->encount += $row[0]->encount;
                }
            }
            else
                $model->encount = $model->encount * $model->ecounter->koeff;
            $result = $this->CheckCountVal($model->ecounter_id,$model->encount,$model->year,$model->month);
            if($result===self::NOT_VAL){
                $msg = 'Отсутствует показание счетчика <strong>'. $model->ecounter->name .'</strong> за предыдущий месяц!';
                //$err = 'Отсутствует показание счетчика за предыдущий месяц!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                return redirect('/energy/main-counter')->with('error',$msg);
            }
            elseif($result===self::MORE_VAL){
                //$err = 'Предыдущее показание счетчика больше, чем текущее!';
                $msg = 'Предыдущее показание счетчика <strong>'. $model->ecounter->name .'</strong> больше, чем текущее!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('error',Auth::id(),$msg,$ip));
                return redirect('/energy/main-counter')->with('error',$msg);
            }
            else{
                //удаляем, если имеется запись за текущий месяц, чтобы не было дублей
                MainLog::where(['ecounter_id'=>$model->ecounter_id,'year'=>$model->year,'month'=>$model->month])->delete();
                if($name == 'Главный'){} //$model->delta уже определили ранее
                else
                    $model->delta = $model->encount - $this->previous;
                $model->price = $model->delta * $model->ecounter->tarif;
                if($model->save()){
                    $msg = 'Данные счетчика <strong>'. $model->ecounter->name .'</strong> успешно добавлены.';
                    $ip = $request->getClientIp();
                    //вызываем event
                    event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                    return redirect('/energy/main-counter')->with('status',$msg);
                }
            }
        }
        if(view()->exists('energy::mainlog_add')){
            $head='Новая запись';
            $title = 'Ввод показаний общих счетчиков';
            $counters = LibController::GetCounters();
            $selmain = array();
            $month = LibController::GetMonths();
            $smonth = date("m");
            $year = date('Y');
            if(strlen($smonth)==1)
                $smonth.='0';
            foreach($counters as $count) {
                $selmain[$count->id] = $count->name.' ('.$count->text.')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => $head,
                'selmain' => $selmain,
                'year' => $year,
                'month' => $month,
            ];
            return view('energy::mainlog_add',$data);
        }
        abort(404);
    }

    //проверка корректности данных счетчика
    public function CheckCountVal($id,$val,$year,$month){
        $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
        $y = $period[0];
        $m = $period[1];
        //выбираем данные за предыдущий период
        $numrow = MainLog::where(['ecounter_id'=>$id,'year'=>$y,'month'=>$m])->count();
        if($numrow) {
            $row = MainLog::where(['ecounter_id'=>$id,'year'=>$y,'month'=>$m])->first();
            $this->previous = $row->encount;
            if($this->previous > $val)
                return self::MORE_VAL;
            else
                return self::LESS_VAL;
        }
        else return self::NOT_VAL;
    }
}
