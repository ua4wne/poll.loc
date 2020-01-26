<?php

namespace Modules\Oit\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\UnitGroup;
use Validator;

class UnitGroupController extends Controller
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
        if(view()->exists('oit::unit-groups')){
            $title='Подразделения';
            $rows = UnitGroup::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Структурные подразделения',
                'rows' => $rows,
            ];
            return view('oit::unit-groups',$data);
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
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:100|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('unit-groupAdd')->withErrors($validator)->withInput();
            }

            $group = new UnitGroup();
            $group->fill($input);
            $group->created_at = date('Y-m-d H:i:s');
            if($group->save()){
                $msg = 'Новая группа '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/unit-groups')->with('status',$msg);
            }
        }
        if(view()->exists('oit::unit-group_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('oit::unit-group_add', $data);
        }
        abort(404);
    }
}
