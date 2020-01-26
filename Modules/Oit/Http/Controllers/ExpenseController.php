<?php

namespace Modules\Oit\Http\Controllers;

use App\Events\AddEventLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Oit\Entities\Expense;
use Validator;

class ExpenseController extends Controller
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
        if(view()->exists('oit::expenses')){
            $title='Статьи расходов';
            $rows = Expense::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => $title,
                'head' => 'Статьи расходов',
                'rows' => $rows,
            ];
            return view('oit::expenses',$data);
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
                return redirect()->route('expenseAdd')->withErrors($validator)->withInput();
            }

            $expense = new Expense();
            $expense->fill($input);
            $expense->created_at = date('Y-m-d H:i:s');
            if($expense->save()){
                $msg = 'Новая статья расходов '. $input['name'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/expenses')->with('status',$msg);
            }
        }
        if(view()->exists('oit::expense_add')){
            $data = [
                'title' => 'Новая запись',
            ];
            return view('oit::expense_add', $data);
        }
        abort(404);
    }
}
