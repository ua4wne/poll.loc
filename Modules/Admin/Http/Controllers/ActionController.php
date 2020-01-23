<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Action;
use Modules\Admin\Entities\Role;
use Validator;
use App\Events\AddEventLogs;
use Illuminate\Support\Facades\Auth;
use App\User;

class ActionController extends Controller
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
        if(view()->exists('admin::actions')){
            $title='Список разрешений';
            $actions = Action::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Список разрешений в системе',
                'actions' => $actions,
            ];
            return view('admin::actions',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request){
        if(!User::hasRole('admin')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'unique' => 'Значение поля должно быть уникальным!',
            ];
            $validator = Validator::make($input,[
                'code' => 'required|max:70|unique:actions',
                'name' => 'required|max:100',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('actionAdd')->withErrors($validator)->withInput();
            }

            $action = new Action();
            $action->fill($input);
            if($action->save()){
                $msg = 'Разрешение '. $input['name'] .' было успешно добавлено!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/actions')->with('status',$msg);
            }
        }
        if(view()->exists('admin::action_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('admin::action_add', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id,Request $request){
        if(!User::hasRole('admin')){
            abort(503);
        }
        $model = Action::find($id);
        if($request->isMethod('delete')){
            $model->delete();
            $msg = 'Разрешение '. $model->name .' было удалено!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg,$ip));
        }
        return redirect('/actions')->with('status',$msg);
    }

}
