<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Ecounter;
use Modules\Admin\Entities\Place;
use Validator;

class PlaceController extends Controller
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
        if(view()->exists('admin::places')){
            $title='Территории';
            $cnts = Ecounter::select(['id','name'])->get();
            $cntsel = array();
            foreach ($cnts as $cnt){
                $cntsel[$cnt->id] = $cnt->name;
            }
            $rows = Place::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Территории',
                'rows' => $rows,
                'cntsel' => $cntsel,
            ];
            return view('admin::places',$data);
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
                'name' => 'required|max:50|string',
                'ecounter_id' => 'required|numeric',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('placeAdd')->withErrors($validator)->withInput();
            }

            $place = new Place();
            $place->fill($input);
            $place->created_at = date('Y-m-d H:i:s');
            if($place->save()){
                $msg = 'Территория '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/places')->with('status',$msg);
            }
        }
        if(view()->exists('admin::place_add')){
            $cnts = Ecounter::select(['id','name'])->get();
            $cntsel = array();
            foreach ($cnts as $cnt){
                $cntsel[$cnt->id] = $cnt->name;
            }
            $data = [
                'title' => 'Новая запись',
                'cntsel' => $cntsel,
            ];
            return view('admin::place_add', $data);
        }
        abort(404);
    }
}
