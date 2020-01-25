<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\OwnEcounter;
use Validator;

class OwnEcounterController extends Controller
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
        if(view()->exists('admin::own_ecounters')){
            $title='Собственные электросчетчики';
            $ecounters = OwnEcounter::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Собственные электросчетчики',
                'ecounters' => $ecounters,
            ];
            return view('admin::own_ecounters',$data);
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
                'text' => 'string|max:100',
                'name' => 'required|max:50|string',
                'koeff' => 'required|numeric',
                'tarif' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('own-ecounterAdd')->withErrors($validator)->withInput();
            }

            $ecounter = new OwnEcounter();
            $ecounter->fill($input);
            $ecounter->created_at = date('Y-m-d H:i:s');
            if($ecounter->save()){
                $msg = 'Собственный счетчик '. $input['name'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/own-ecounters')->with('status',$msg);
            }
        }
        if(view()->exists('admin::own_ecounter_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('admin::own_ecounter_add', $data);
        }
        abort(404);
    }
}
