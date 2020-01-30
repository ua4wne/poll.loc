<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class VisitController extends Controller
{
    public function index()
    {
        if(!Role::granted('work_guard')){
            abort(503);
        }
        if(view()->exists('visits')){
            $head='Посещение выставки';
            $period = date('Y-m'); //берем за текущий месяц
            $rows = Visit::where("data", "like", $period . "-%")->get();
            $title = 'Учет посетителей выставки';
            $data = [
                'title' => $title,
                'head' => $head,
                'rows' => $rows,
            ];
            return view('visits',$data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if(!Role::granted('work_guard')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'array' => 'Значение поля должно быть массивом!',
                'date' => 'Значение поля должно быть датой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'data' => 'required|date',
                'hours' => 'required|array',
                'ucount' => 'required|numeric'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('visitAdd')->withErrors($validator)->withInput();
            }
            $hours = $input['hours'];
            foreach ($hours as $hour){
                $visit = new Visit();
                $visit->data = $input['data'];
                $visit->hours = $hour;
                $visit->ucount = $input['ucount'];
                $visit->created_at = date('Y-m-d H:i:s');
                $visit->updateOrCreate();
            }

            //$visit->fill($input);
            //$visit->created_at = date('Y-m-d H:i:s');
            //if($visit->save()){
                $msg = 'Запись успешно добавлена!';
                //$ip = $request->getClientIp();
                //вызываем event
                //event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/visits')->with('status',$msg);
            //}
        }
        if(view()->exists('visit_add')){
            $hoursel = ['10'=>'С 10:00 до 11:00','11'=>'С 11:00 до 12:00','12'=>'С 12:00 до 13:00','13'=>'С 13:00 до 14:00',
                '14'=>'С 14:00 до 15:00','15'=>'С 15:00 до 16:00','16'=>'С 16:00 до 17:00','17'=>'С 17:00 до 18:00',
                '18'=>'С 18:00 до 19:00','19'=>'С 19:00 до 20:00','20'=>'С 20:00 до 21:00'];
            $data = [
                'title' => 'Новая запись',
                'hoursel' => $hoursel,
            ];
            return view('visit_add', $data);
        }
        abort(404);
    }
}
