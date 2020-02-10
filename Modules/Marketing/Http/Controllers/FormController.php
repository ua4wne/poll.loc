<?php

namespace Modules\Marketing\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\Entities\Form;
use Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if(view()->exists('marketing::forms')){
            $title='Анкеты';
            $rows = Form::all();
            $data = [
                'title' => $title,
                'head' => 'Список анкет',
                'rows' => $rows,
            ];
            return view('marketing::forms',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!User::hasRole('admin') && !User::hasRole('market')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть текстовой строкой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'name' => 'required|max:80|string',
                'is_active' => 'required|numeric',
                'is_work' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('/formAdd')->withErrors($validator)->withInput();
            }

            $form = new Form();
            $form->fill($input);
            $form->created_at = date('Y-m-d H:i:s');
            if($form->save()){
                $msg = 'Новая анкета '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/forms')->with('status',$msg);
            }
        }
        if(view()->exists('marketing::form_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('marketing::form_add', $data);
        }
        abort(404);
    }

}
