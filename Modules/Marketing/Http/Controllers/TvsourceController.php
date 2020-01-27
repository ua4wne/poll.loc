<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Tvsource;
use Validator;

class TvsourceController extends Controller
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
        if(view()->exists('marketing::tvsources')){
            $title='Справочник медиа-источников';
            $rows = Tvsource::all();
            $data = [
                'title' => $title,
                'head' => 'Справочник медиа-источников',
                'rows' => $rows,
            ];
            return view('marketing::tvsources',$data);
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
                'name' => 'required|max:80|string',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('tvsourceAdd')->withErrors($validator)->withInput();
            }

            $tvsrc = new Tvsource();
            $tvsrc->fill($input);
            $tvsrc->created_at = date('Y-m-d H:i:s');
            if($tvsrc->save()){
                $msg = 'Новый медиа-источник '. $input['name'] .' был успешно добавлен в справочник!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/tvsources')->with('status',$msg);
            }
        }
        if(view()->exists('marketing::tvsource_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('marketing::tvsource_add', $data);
        }
        abort(404);
    }
}
