<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\City;
use Validator;

class CityController extends Controller
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
        if(view()->exists('marketing::cities')){
            $title='Справочник городов';
            $rows = City::all();
            $data = [
                'title' => $title,
                'head' => 'Справочник городов',
                'rows' => $rows,
            ];
            return view('marketing::cities',$data);
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
                'name' => 'required|max:50|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('cityAdd')->withErrors($validator)->withInput();
            }

            $city = new City();
            $city->fill($input);
            $city->created_at = date('Y-m-d H:i:s');
            if($city->save()){
                $msg = 'Новый город '. $input['name'] .' был успешно добавлен в справочник!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/cities')->with('status',$msg);
            }
        }
        if(view()->exists('marketing::city_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('marketing::city_add', $data);
        }
        abort(404);
    }
}
