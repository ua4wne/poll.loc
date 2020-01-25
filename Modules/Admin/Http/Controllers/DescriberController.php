<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Describer;
use Validator;

class DescriberController extends Controller
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
        if(view()->exists('admin::describers')){
            $title='Подписчики';
            $rows = Describer::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Подписчики',
                'rows' => $rows,
            ];
            return view('admin::describers',$data);
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
                'email' => 'Значение поля должно быть email-адресом!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'email' => 'required|max:50|email',
                'status' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('describerAdd')->withErrors($validator)->withInput();
            }

            $describer = new Describer();
            $describer->fill($input);
            $describer->created_at = date('Y-m-d H:i:s');
            if($describer->save()){
                $msg = 'Новый подписчик '. $input['email'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/describers')->with('status',$msg);
            }
        }
        if(view()->exists('admin::describer_add')){
            $statsel = ['0'=>'Не активный', '1'=>'Активный'];
            $data = [
                'title' => 'Новая запись',
                'statsel' => $statsel,
            ];
            return view('admin::describer_add', $data);
        }
        abort(404);
    }
}
