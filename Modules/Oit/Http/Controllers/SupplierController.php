<?php

namespace Modules\Oit\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Supplier;
use Validator;

class SupplierController extends Controller
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
        if(view()->exists('oit::suppliers')){
            $title='Поставщики';
            $rows = Supplier::all();
            $data = [
                'title' => $title,
                'head' => 'Поставщики',
                'rows' => $rows,
            ];
            return view('oit::suppliers',$data);
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
                return redirect()->route('supplierAdd')->withErrors($validator)->withInput();
            }

            $supplier = new Supplier();
            $supplier->fill($input);
            $supplier->created_at = date('Y-m-d H:i:s');
            if($supplier->save()){
                $msg = 'Новый поставщик '. $input['name'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/suppliers')->with('status',$msg);
            }
        }
        if(view()->exists('oit::supplier_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('oit::supplier_add', $data);
        }
        abort(404);
    }
}
