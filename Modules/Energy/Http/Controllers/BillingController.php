<?php

namespace Modules\Energy\Http\Controllers;

use App\Http\Controllers\LibController;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\EnergyLog;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!Role::granted('work_energy')) {
            abort(503);
        }
        if (view()->exists('energy::billing')) {
            $head = 'Расчет потребления электроэнергии';
            $title = 'Расчет потребления электроэнергии арендаторами';
            $year = date('Y');
            $month = date('m');
            $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
            $y = $period[0];
            $m = LibController::SetMonth($period[1]);
            $rows = EnergyLog::where(['year' => $y, 'month' => $period[1]])->get();
            $delta = EnergyLog::where(['year' => $y, 'month' => $period[1]])->sum('delta');
            $delta = round($delta, 2);
            $price = EnergyLog::where(['year' => $y, 'month' => $period[1]])->sum('price');
            $price = round($price, 2);
            $data = [
                'rows' => $rows,
                'year' => $y,
                'month' => $m,
                'delta' => $delta,
                'price' => $price,
                'head' => $head,
                'title' => $title,
            ];
            return view('energy::billing', $data);
        }
    }

    public function calculate(Request $request){
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
        }
        if (view()->exists('energy::rent_calculate')) {
            $head = 'Расчет потребления';
            $title = 'Расчет потребления электроэнергии арендаторами';
            $places = Place::select(['id', 'name'])->get();
            $place_id = $places[0]->id;
            $selplace = array();
            $renters = Rentlog::GetActiveRentersByPlace($place_id);
            $selrent = array();
            foreach ($renters as $renter) {
                $selrent[$renter->id] = $renter->title . ' (' . $renter->area . ')'; //массив для заполнения данных в select формы
            }
            foreach ($places as $place) {
                $selplace[$place->id] = $place->name; //массив для заполнения данных в select формы
            }
            $year = date('Y');

            $data = [
                'title' => $title,
                'head' => $head,
                'selplace' => $selplace,
                'selrent' => $selrent,
                'year' => $year,
            ];
            return view('energy::rent_calculate', $data);
        }
        abort(404);
    }

    public function period(Request $request){
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
        }
    }

}
