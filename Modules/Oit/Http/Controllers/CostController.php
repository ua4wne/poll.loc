<?php

namespace Modules\Oit\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Cost;
use Modules\Oit\Entities\Expense;
use Modules\Oit\Entities\Supplier;
use Modules\Oit\Entities\UnitGroup;
use Validator;

class CostController extends Controller
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
        if(view()->exists('oit::costs')){
            $title='Расходы';
            $year = date('Y');
            $sups = Supplier::select(['id','name'])->get();
            $supsel = array();
            foreach ($sups as $val){
                $supsel[$val->id] = $val->name;
            }
            $groups = UnitGroup::select(['id','name'])->get();
            $groupsel = array();
            foreach ($groups as $val){
                $groupsel[$val->id] = $val->name;
            }
            $exps = Expense::select(['id','name'])->get();
            $expsel = array();
            foreach ($exps as $val){
                $expsel[$val->id] = $val->name;
            }
            $rows = Cost::where('created_at','like',$year.'%')->get();
            $data = [
                'title' => $title,
                'head' => 'Расходы ИТ',
                'rows' => $rows,
                'supsel' => $supsel,
                'groupsel' => $groupsel,
                'expsel' => $expsel,
            ];
            return view('oit::costs',$data);
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
                'string' => 'Значение поля должно быть строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:150|string',
                'price' => 'required|numeric',
                'supplier_id' => 'required|numeric',
                'unitgroup_id' => 'required|numeric',
                'expense_id' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('costAdd')->withErrors($validator)->withInput();
            }

            $cost = new Cost();
            $cost->fill($input);
            $cost->created_at = date('Y-m-d H:i:s');
            if($cost->save()){
                $msg = 'Новая запись расхода ИТ '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/costs')->with('status',$msg);
            }
        }
        if(view()->exists('oit::cost_add')){
            $sups = Supplier::select(['id','name'])->orderBy('name','asc')->get();
            $supsel = array();
            foreach ($sups as $val){
                $supsel[$val->id] = $val->name;
            }
            $groups = UnitGroup::select(['id','name'])->get();
            $groupsel = array();
            foreach ($groups as $val){
                $groupsel[$val->id] = $val->name;
            }
            $exps = Expense::select(['id','name'])->get();
            $expsel = array();
            foreach ($exps as $val){
                $expsel[$val->id] = $val->name;
            }
            $data = [
                'title' => 'Новая запись',
                'supsel' => $supsel,
                'groupsel' => $groupsel,
                'expsel' => $expsel,
            ];
            return view('oit::cost_add', $data);
        }
        abort(404);
    }
}
