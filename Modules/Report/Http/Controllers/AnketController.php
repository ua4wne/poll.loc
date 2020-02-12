<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\FormQty;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;

class AnketController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            $finish = $input['finish'];
            $form_id = $input['form_id'];
            $data = array();
            $pie = array();
            $qty = FormQty::where(['form_id'=>$form_id])->whereBetween('date', [$start, $finish])->sum('qty');
            array_push($data,["qty"=>"$qty"]);
            $content = '';
            //определяем вопросы анкеты из логов
            $rows = DB::select("SELECT DISTINCT `name` FROM (`questions` JOIN `logforms`  ON((`questions`.`id` = `logforms`.`question_id`))) WHERE `questions`.`form_id`=$form_id AND `data` BETWEEN '$start' AND '$finish' ");
            $i=1;
            foreach ($rows as $row) {
                $question = Question::where(['form_id'=>$form_id,'name'=>$row->name])->first();
                $sum = Logform::select('answer')->where(['question_id' => $question->id])->whereBetween('data',[$start,$finish])->count('answer');
                $values = DB::select("select answer,count(answer) as kol,count(answer)/$sum as percent from logforms where question_id=$question->id 
                                     and (`data` between '$start' and '$finish') group by answer order by kol DESC");
                $content.= '<div class="col-md-offset-2 col-md-8"><div class="pie" id="pie-'.$i.'">График</div>';
                $content.= '<button type="button" class="btn btn-default btn-sm"><i class="fa fa-expand fa-lg show" aria-hidden="true"></i></button><div class="other">';
                $content.='<table class="table table-hover table-bordered"><tr><th>Ответ</th><th>Кол-во ответов</th><th>% ответов</th></tr>';
                foreach ($values as $val){
                    $tmp = array();
                    $content.= '<tr><td>'.$val->answer.'</td><td>'.$val->kol.'</td><td>'.round($val->percent*100,2).'</td></tr>';
                    $tmp['answer'] = $val->answer;
                    $tmp['kol'] = $val->kol;
                    array_push($pie,$tmp);
                }
                $content .= '</table></div></div>';
                $i++;
            }
            array_push($data,["content"=>$content]);
            array_push($data,["pie"=>$pie]);
            return json_encode($data);
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
