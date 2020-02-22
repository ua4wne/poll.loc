<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Place;
use Modules\Marketing\Entities\Megacount;

class MegacountController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (view()->exists('marketing::megacounts')) {
            $rows = Megacount::all();
            $places = Place::select(['id','name'])->get();
            $placesel = array();
            foreach ($places as $val){
                $placesel[$val->id] = $val->name;
            }
            return view('marketing::megacounts', [
                'title' => 'Счетчики Megacount',
                'head' => 'Счетчики посетителей',
                'rows' => $rows,
                'placesel' => $placesel,
            ]);
        }
        abort(404);
        return view('marketing::megacounts');
    }

    public function edit(Request $request)
    {
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $model = Megacount::find($input['id']);
            $model->fill($input);
            if(!User::hasRole('admin')){//вызываем event
                $msg = 'Попытка изменения записи счетчика посетителей '.$model->name;
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'NO';
            }
            if($model->update()){
                $msg = 'Данные счетчика посетителей '.$model->name.' были изменены!';
                $ip = $request->getClientIp();
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return 'OK';
            }
            else
                return 'ERR';
        }
    }

    public function delete(){
        return 'OK';
    }

}
