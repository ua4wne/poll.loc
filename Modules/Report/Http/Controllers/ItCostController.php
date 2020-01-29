<?php

namespace Modules\Report\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ItCostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin') && !User::hasRole('director')){
            abort(503);
        }
        if(view()->exists('report::it_cost')){
            $title='Затраты ИТ';
            $year = date('Y');
            $typesel = ['expense'=>'По статьям расходов','supplier'=>'По поставщикам'];
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'year' => $year,
                'typesel' => $typesel,
            ];
            return view('report::it_cost',$data);
        }
        abort(404);
    }

}
