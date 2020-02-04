<?php

namespace Modules\Energy\Http\Controllers;

use App\Http\Controllers\LibController;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;

class RentersCounterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('work_energy')){
            abort(503);
        }
        if(view()->exists('energy::rentlog_add')){
            $head='Новая запись';
            $title = 'Ввод показаний счетчиков арендаторов';
            $places = Place::select(['id', 'name'])->get();
            $place_id =  $places[0]->id;
            $selplace = array();
            $renters = Rentlog::GetActiveRentersByPlace($place_id);
            $selrent = array();
            foreach($renters as $renter) {
                $selrent[$renter->id] = $renter->title.' ('.$renter->area.')'; //массив для заполнения данных в select формы
            }
            foreach ($places as $place) {
                $selplace[$place->id] = $place->name; //массив для заполнения данных в select формы
            }
            $month = LibController::GetMonths();
            $smonth = date("m");
            $year = date('Y');
            if(strlen($smonth)==1)
                $smonth.='0';

            $data = [
                'title' => $title,
                'head' => $head,
                'selplace' => $selplace,
                'selrent' => $selrent,
                'year' => $year,
                'month' => $month,
            ];
            return view('energy::rentlog_add',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Role::granted('work_energy')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
        }
    }

    public function select(Request $request) {
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $place_id = $input['selrent'];
            $renters = Rentlog::GetActiveRentersByPlace($place_id);
            $content = '';
            foreach($renters as $renter) {
                $content.= '<option value="'.$renter->id . '">' . $renter->title.' ('.$renter->area.')</option>'; //массив для заполнения данных в select формы
            }
            return $content;
        }
    }

}
