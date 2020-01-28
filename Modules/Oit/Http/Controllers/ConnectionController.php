<?php

namespace Modules\Oit\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Renter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Connection;
use Validator;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('oit::connections')){
            $title='Подключения к интернет';
            $renters = Renter::select(['id','title'])->where(['status'=>1])->get();
            $rentsel = array();
            foreach ($renters as $val){
                $rentsel[$val->id] = $val->title;
            }
            $typesel = ['dynamic'=>'Динамический IP','static'=>'Статический IP'];
            $rows = Connection::all();
            $data = [
                'title' => $title,
                'head' => 'Подключения к интернет',
                'rows' => $rows,
                'rentsel' => $rentsel,
                'typesel' => $typesel,
            ];
            return view('oit::connections',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть текстовой строкой!',
                'date' => 'Значение поля должно быть датой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'renter_id' => 'required|numeric',
                'date_on' => 'nullable|date',
                'date_off' => 'nullable|date',
                'type' => 'required|max:7|string',
                'сomment' => 'nullable|max:200|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('connectionAdd')->withErrors($validator)->withInput();
            }

            $conn = new Connection();
            $conn->fill($input);
            $conn->created_at = date('Y-m-d H:i:s');
            if($conn->save()){
                $msg = 'Подключение к интернет для юрлица '. Renter::find($input['renter_id'])->name .' было успешно добавлено!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/connections')->with('status',$msg);
            }
        }
        if(view()->exists('oit::connection_add')){
            $renters = Renter::select(['id','title'])->where(['status'=>1])->get();
            $rentsel = array();
            foreach ($renters as $val){
                $rentsel[$val->id] = $val->title;
            }
            $typesel = ['dynamic'=>'Динамический IP','static'=>'Статический IP'];
            $data = [
                'title' => 'Новая запись',
                'rentsel' => $rentsel,
                'typesel' => $typesel,
            ];
            return view('oit::connection_add', $data);
        }
        abort(404);
    }
}
