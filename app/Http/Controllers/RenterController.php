<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Division;
use App\Models\Renter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Validator;

class RenterController extends Controller
{
    public function index()
    {
        if(!Role::granted('view_renter')){
            abort(503);
        }
        if(view()->exists('renters')){
            $head='Арендаторы';
            $places = Place::select(['id','name'])->get();
            $placesel = array();
            foreach ($places as $val){
                $placesel[$val->id] = $val->name;
            }
            $statsel = ['1'=>'Действующий','0'=>'Не действующий'];
            $divs = Division::select(['id','name'])->where(['status'=>'1'])->get();
            $divsel = array();
            foreach ($divs as $val){
                $divsel[$val->id] = $val->name;
            }
            $rows = Renter::where(['status'=>1])->get();
            $title = 'Действующие арендаторы';
            $data = [
                'title' => $title,
                'head' => $head,
                'rows' => $rows,
                'placesel' => $placesel,
                'statsel' => $statsel,
                'divsel' => $divsel,
            ];
            return view('renters',$data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if(!Role::granted('edit_renter')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|max:100|string',
                'name' => 'required|max:100|string',
                'area' => 'required|max:20|string',
                'agent' => 'required|max:50|string',
                'phone1' => 'nullable|max:20|string',
                'phone2' => 'nullable|max:20|string',
                'encounter' => 'required|max:20|string',
                'koeff' => 'required|numeric',
                'place_id' => 'required|numeric',
                'status' => 'required|numeric',
                'division_id' => 'required|numeric',
            ],$messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $renter = new Renter();
            $renter->fill($input);
            $renter->created_at = date('Y-m-d H:i:s');
            if($renter->save()){
                $msg = 'Новый арендатор '. $input['name'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/renters')->with('status',$msg);
            }
        }
        if(view()->exists('renter_add')){
            $places = Place::select(['id','name'])->get();
            $placesel = array();
            foreach ($places as $val){
                $placesel[$val->id] = $val->name;
            }
            $statsel = ['1'=>'Действующий','0'=>'Не действующий'];
            $divs = Division::select(['id','name'])->where(['status'=>'1'])->get();
            $divsel = array();
            foreach ($divs as $val){
                $divsel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Новая запись',
                'placesel' => $placesel,
                'statsel' => $statsel,
                'divsel' => $divsel,
            ];
            return view('renter_add', $data);
        }
        abort(404);
    }

    public function view($status)
    {
        if(!Role::granted('view_renter')){
            abort(503);
        }
        if(view()->exists('renters')){
            $head='Арендаторы';
            $places = Place::select(['id','name'])->get();
            $placesel = array();
            foreach ($places as $val){
                $placesel[$val->id] = $val->name;
            }
            $statsel = ['1'=>'Действующий','0'=>'Не действующий'];
            $divs = Division::select(['id','name'])->where(['status'=>'1'])->get();
            $divsel = array();
            foreach ($divs as $val){
                $divsel[$val->id] = $val->name;
            }
            $rows = Renter::where(['status'=>$status])->get();
            if($status)
                $title = 'Действующие арендаторы';
            else
                $title = 'Не действующие арендаторы';
            $data = [
                'title' => $title,
                'head' => $head,
                'rows' => $rows,
                'placesel' => $placesel,
                'statsel' => $statsel,
                'divsel' => $divsel,
            ];
            return view('renters',$data);
        }
        abort(404);
    }
}
