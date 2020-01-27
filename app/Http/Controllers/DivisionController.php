<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Division;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class DivisionController extends Controller
{
    public function index()
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('divisions')){
            $title='Наши юрлица';
            $rows = Division::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Наши юрлица',
                'rows' => $rows,
            ];
            return view('divisions',$data);
        }
        abort(404);
    }

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
                'name' => 'required|max:50|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('divisionAdd')->withErrors($validator)->withInput();
            }

            $division = new Division();
            $division->fill($input);
            $division->created_at = date('Y-m-d H:i:s');
            if($division->save()){
                $msg = 'Новое юрлицо '. $input['name'] .' было успешно добавлено!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/divisions')->with('status',$msg);
            }
        }
        if(view()->exists('division_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('division_add', $data);
        }
        abort(404);
    }
}
