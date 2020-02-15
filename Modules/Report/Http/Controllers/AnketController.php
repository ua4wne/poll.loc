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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            if(isset($input['export'])){
                if(!Role::granted('export')){
                    abort(503);
                }
                return $this->export($form_id,$start,$finish);
            }
            $data = array();
            $qty = FormQty::where(['form_id'=>$form_id])->whereBetween('date', [$start, $finish])->sum('qty');
            array_push($data,["qty"=>"$qty"]);
            $pie = array();
            $content = '';
            //определяем вопросы анкеты из логов
            $rows = DB::select("SELECT DISTINCT `name` FROM (`questions` JOIN `logforms`  ON((`questions`.`id` = `logforms`.`question_id`))) WHERE `questions`.`form_id`=$form_id AND `data` BETWEEN '$start' AND '$finish' ");
            $i=1;
            foreach ($rows as $row) {
                $qst = array();
                $question = Question::where(['form_id'=>$form_id,'name'=>$row->name])->first();
                $sum = Logform::select('answer')->where(['question_id' => $question->id])->whereBetween('data',[$start,$finish])->count('answer');
                $values = DB::select("select answer,count(answer) as kol,count(answer)/$sum as percent from logforms where question_id=$question->id 
                                     and (`data` between '$start' and '$finish') group by answer order by kol DESC");
                $content.= '<div class="col-md-offset-2 col-md-8"><h2 class="text-info text-center">'.$row->name.'</h2><div class="pie" id="pie-'.$i.'">График</div>';
                $content.= '<button type="button" class="btn btn-default btn-sm"><i class="fa fa-expand fa-lg show" aria-hidden="true"></i></button><div class="other">';
                $content.='<table class="table table-hover table-bordered"><tr><th>Ответ</th><th>Кол-во ответов</th><th>% ответов</th></tr>';
                foreach ($values as $val){
                    $tmp = array();
                    $content.= '<tr><td>'.$val->answer.'</td><td>'.$val->kol.'</td><td>'.round($val->percent*100,2).'</td></tr>';
                    $tmp['answer'] = $val->answer;
                    $tmp['kol'] = $val->kol;
                    array_push($qst,$tmp);
                }
                $content .= '</table></div><hr></div>';
                $i++;
                array_push($pie,$qst);
            }
            array_push($data,["content"=>$content]);
            array_push($data,["pie"=>$pie]);
            return json_encode($data);
        }
        if(view()->exists('report::anket')){
            $title='Анкетирование';
            $forms = Form::where(['is_active'=>1])->get();
            //$verselect = ['new'=>'Новый вариант (версия 2)','old'=>'Старый вариант (версия 1)'];
            foreach($forms as $form) {
                $formselect[$form->id] = $form->name; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'formselect' => $formselect,
                //'verselect' => $verselect,
            ];
            return view('report::anket',$data);
        }
        abort(404);
    }

    private function export($form_id,$start,$finish){
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'left' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => Border::BORDER_THIN,
                ),
            )
        );
        $styleRow = array(
            'font' => array(
                'bold' => false,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'left' => array(
                    'style' => Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => Border::BORDER_THIN,
                ),
            )
        );
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Статистика');
        $k=1;
        $sheet->setCellValue('A'.$k, Form::find($form_id)->name);
        $sheet->mergeCells('A'.$k.':C'.$k);
        $sheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k=2;
        $sheet->setCellValue('A'.$k, 'статистика за период с '.$start.' по '.$finish);
        $sheet->mergeCells('A'.$k.':C'.$k);
        //$sheet->getStyle('A'.$k.':B'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k++;
        $qty = FormQty::where(['form_id'=>$form_id])->whereBetween('date', [$start, $finish])->sum('qty');
        $sheet->setCellValue('A'.$k, 'Опрошено человек: ');
        $sheet->setCellValue('B'.$k, $qty);
        $sheet->getStyle('B'.$k.':B'.$k)->getFont()->setBold(true);
        //$sheet->getStyle('A'.$k.':B'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k=5;
        //определяем все вопросы анкеты
        $questions = Question::where(['form_id'=>$form_id])->get();
        foreach ($questions as $question){
            if($question->name != 'Ваши контакты'){
                $rows = DB::select("select answer,count(answer) as qty, count(answer)/$qty as percent from logforms where question_id=$question->id and `data` between '$start' and '$finish' group by answer order by qty DESC");
                if(!empty($rows)){
                    $sheet->setCellValue('A'.$k, $question->name);
                    $sheet->mergeCells('A'.$k.':C'.$k);
                    $sheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $k++;
                    $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('A'.$k, 'Ответ');
                    $sheet->setCellValue('B'.$k, 'Общее кол-во');
                    $sheet->setCellValue('C'.$k, '% ответов');
                    $sheet->getStyle('A'.$k.':C'.$k)->applyFromArray($styleArray);
                    $k++;
                    foreach ($rows as $row){
                        $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue('A'.$k, $row->answer);
                        $sheet->setCellValue('B'.$k, $row->qty);
                        $sheet->setCellValue('C'.$k, round($row->percent*100,2));
                        $sheet->getStyle('A'.$k.':C'.$k)->applyFromArray($styleRow);
                        $k++;
                    }
                    $k++;
                }
            }
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "statpoll";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }


}
