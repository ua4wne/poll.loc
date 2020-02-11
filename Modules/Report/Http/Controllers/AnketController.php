<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Role;
use Modules\Marketing\Entities\Form;

class AnketController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if(view()->exists('report::anket')){
            $title='Анкетирование';
            $forms = Form::where(['is_active'=>1])->get();
            $verselect = ['new'=>'Новый вариант (версия 2)','old'=>'Старый вариант (версия 1)'];
            foreach($forms as $form) {
                $formselect[$form->id] = $form->name; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'formselect' => $formselect,
                'verselect' => $verselect,
            ];
            return view('report::anket',$data);
        }
        abort(404);
    }


}
