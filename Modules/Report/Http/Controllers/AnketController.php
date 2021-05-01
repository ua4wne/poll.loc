<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Marketing\Entities\Answer;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\FormGroup;
use Modules\Marketing\Entities\FormQty;
use Modules\Marketing\Entities\Logform;
use Modules\Marketing\Entities\Question;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
            $group_id = $input['group_id'];
            //выборка по группе анкет или нет
            $forms = array();
            $in_forms = ''; //строка для условия IN
            if($group_id !=1 && Form::find($form_id)->form_group_id == $group_id){
                $rows = Form::where('form_group_id',$group_id)->get();
                foreach ($rows as $row){
                    array_push($forms,$row->id);
                    $in_forms .= $row->id . ',';
                }
                $in_forms = rtrim($in_forms,','); //запятая в конце не нужна
            }
            if(isset($input['export'])){
                if(!Role::granted('export')){
                    abort(503);
                }
                $version = $input['version'];
                if($version=='old')
                    return $this->exportOld($form_id,$start,$finish);
                else
                    return $this->exportNew($form_id,$start,$finish);
            }
            $data = array();
            if(count($forms)>1){
                $qty = FormQty::whereIn('form_id',$forms)->whereBetween('date', [$start, $finish])->sum('qty');
            }
            else{
                $qty = FormQty::where(['form_id'=>$form_id])->whereBetween('date', [$start, $finish])->sum('qty');
            }
            array_push($data,["qty"=>"$qty"]);
            $pie = array();
            $content = '';
            //определяем вопросы анкеты из логов
            if(count($forms)>1){
                $rows = DB::select("SELECT DISTINCT `name` FROM (`questions` JOIN `logforms`  ON((`questions`.`id` = `logforms`.`question_id`)))
                        WHERE `questions`.`form_id` IN ($in_forms) AND `data` BETWEEN '$start' AND '$finish' ORDER BY `name`+0 ASC");
                $i=1;
                foreach ($rows as $row) {
                    $qst = array();
                    $questions = Question::whereIn('form_id',$forms)->where('name',$row->name)->get();
                    $in_qst = '';
                    $qsts = array();
                    foreach ($questions as $row){
                        array_push($qsts,$row->id);
                        $in_qst .= $row->id . ',';
                    }
                    $in_qst = rtrim($in_qst,',');
                    $sum = Logform::select('answer')->whereIn('question_id' , $qsts)->whereBetween('data',[$start,$finish])->count('answer');
                    $values = DB::select("select answer,count(answer) as kol,count(answer)/$sum as percent from logforms where question_id in ($in_qst)
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
            }
            else{
                $rows = DB::select("SELECT DISTINCT `name` FROM (`questions` JOIN `logforms`  ON((`questions`.`id` = `logforms`.`question_id`)))
                        WHERE `questions`.`form_id`=$form_id AND `data` BETWEEN '$start' AND '$finish' ORDER BY `name`+0 ASC");
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
            }
            array_push($data,["content"=>$content]);
            array_push($data,["pie"=>$pie]);
            return json_encode($data);
        }
        if(view()->exists('report::anket')){
            $title='Анкетирование';
            $forms = Form::where(['is_active'=>1])->get();
            $verselect = ['new'=>'Выгрузка для кросс-анализа','old'=>'Простая выгрузка'];
            foreach($forms as $form) {
                $formselect[$form->id] = $form->name; //массив для заполнения данных в select формы
            }
            $groups = FormGroup::all();
            $grpsel = array();
            foreach ($groups as $row){
                $grpsel[$row->id] = $row->title;
            }
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'formselect' => $formselect,
                'verselect' => $verselect,
                'grpsel' => $grpsel,
            ];
            return view('report::anket',$data);
        }
        abort(404);
    }

    private function exportOld($form_id,$start,$finish){
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

    private function exportNew($form_id,$start,$finish) {
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
        $fill = array('FFFFEFD5','FFFFDAB9','FFFFF8DC','FFFFE4B5','FFFFE4E1','FFBEBEBE','FF6A5ACD','FF4169E1','FF5F9EA0',
            'FF2E8B57','FFADFF2F','FFDAA520','FFD2B48C','FFFFA500','FFDDA0DD','FFFFDAB9','FFFFF8DC','FFFFE4B5','FFFFE4E1','FFBEBEBE');
        //определяем вопросы анкеты
        $rows = DB::select("SELECT DISTINCT `q`.`id` AS `qid`,`q`.`name` AS `q_name` FROM (`questions` as q
                JOIN `logforms` AS l ON((`q`.`id` = `l`.`question_id`))) WHERE `q`.`form_id`=$form_id
                AND `data` BETWEEN '$start' AND '$finish'");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Статистика');
        $sheet->setCellValue('A1','Номер');
        //$sheet->mergeCells('A1:A2');
        $sheet->getStyle('A1:A1')->applyFromArray($styleArray);
        $sheet->getStyle('A1:A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:A1')->getFill()->getStartColor()->setARGB($fill[5]);
        $col=1;
        $row=1;
        $step=1; //смещение по второй строке
        $cell_one = array(); //массив первых ячеек ответов каждого вопроса
        $qstart =$rows[0]->qid; //первый вопрос анкеты
        $f = 0;
        foreach($rows as $arr){
            //$arr_qst[]=$arr[qid];
            //выбираем ответы на вопрос
            $answers = Answer::select('id','name')->where(['question_id'=>$arr->qid])->get();
            // получаем доступ к ячейке по номеру строки
            // (нумерация с единицы) и столбца (нумерация с нуля)
            $cell_start = $sheet->getCellByColumnAndRow($col, $row)->getColumn();
            $cell_one["$arr->qid"]=$cell_start;
            foreach($answers as $ans){ //заполняем заголовки ответов
                $curr_cell=$sheet->getCellByColumnAndRow($step, 2)->getColumn();
                $sheet->setCellValue($curr_cell.'2', $ans->name);
                $sheet->getStyle($curr_cell . '2:'.$curr_cell . '2')->applyFromArray($styleRow);
                $step++;
            }//цикл по ответам
            $col = $col + count($answers)-1;
            $cell_end = $sheet->getCellByColumnAndRow($col, $row)->getColumn();
            $col++;

            $sheet->setCellValue($cell_start.$row, $arr->q_name.'?');
            $sheet->mergeCells($cell_start.$row.':'.$cell_end.$row);
            $sheet->getStyle($cell_start.$row.':'.$cell_end.$row)->applyFromArray($styleArray);
            $sheet->getStyle($cell_start.$row.':'.$cell_end.$row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cell_start.$row.':'.$cell_end.$row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($cell_start.$row.':'.$cell_end.$row)->getFill()->getStartColor()->setARGB($fill[$f]);
            $f++;
        }
        $month_cell=$sheet->getCellByColumnAndRow($step, 2)->getColumn();
        $sheet->setCellValue($month_cell.$row, 'Месяц опроса');
        $sheet->getStyle($month_cell.'1:'.$month_cell.'1')->applyFromArray($styleArray);
        $sheet->getStyle($month_cell.'1:'.$month_cell.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($month_cell.'1:'.$month_cell.'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($month_cell.'1:'.$month_cell.'1')->getFill()->getStartColor()->setARGB($fill[$f]);
        $sheet->getRowDimension('1')->setRowHeight(70);

        //заполняем ответами на вопросы по анкете
        $dat = DB::select("SELECT id,`data`,question_id,answer_id,answer FROM logforms WHERE form_id=$form_id
                AND `data` BETWEEN '$start' AND '$finish'");
        $data_row=4;
        $num = 1; //порядковый номер
        $k=0;
        foreach($dat as $val){
            $idq = $val->question_id;
            $cell_start = $cell_one["$idq"];
            $curr_cell = $cell_start;
            //выбираем ответы на текущий вопрос
            $answ = Answer::select('id','name')->where(['question_id'=>$idq])->get();
            $month = substr($val->data,5,2); //выделяем месяц
            $arr_ans = array(); //массив ответов на вопрос
            for($i=0;$i<count($answ);$i++){
                $tmp = $answ[$i];
                //$arr_ans[$i] = $tmp[tid];
                $arr_ans["$tmp[id]"] = $tmp['name'];
            }
            //dd($answ);
            foreach ($arr_ans as $key=>$value) {
                //$val = $objPHPExcel->getActiveSheet()->getCell($curr_cell . $data_row)->getCalculatedValue();
                if($key==$val->answer_id) {
                    $pos = strpos($value, 'укажите');
                    if ($pos === false)
                        $sheet->setCellValue($curr_cell . $data_row, 1);
                    else
                        $sheet->setCellValue($curr_cell . $data_row, $val->answer);
                    $sheet->setCellValue($month_cell . $data_row, $month);
                    $sheet->getStyle($month_cell . $data_row.':'.$month_cell . $data_row)->applyFromArray($styleRow);
                }
                //if($val!=1)
                //	$objPHPExcel->getActiveSheet()->setCellValue($curr_cell . $data_row, 0);
                $curr_cell++;
            }
            $curr_cell = $cell_start;
            for($i=0;$i<count($arr_ans);$i++){
                $val = $sheet->getCell($curr_cell . $data_row)->getCalculatedValue();
                if(strlen($val)==0)
                    $sheet->setCellValue($curr_cell . $data_row, 0);
                $sheet->getStyle($curr_cell . $data_row.':'.$curr_cell . $data_row)->applyFromArray($styleRow);
                $curr_cell++;
            }

            //$objPHPExcel->getActiveSheet()->setCellValue($curr_cell.$data_row, 1);
            if($idq==$qstart&&$k>3){
                $sheet->setCellValue('A'.$data_row, $num);
                $sheet->getStyle('A' . $data_row.':A' . $data_row)->applyFromArray($styleRow);
                $data_row++;
                $num++;
            }
            $k++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "crosspoll";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function work(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $role_id = Role::where('code','poll')->first()->id;
            $users = DB::select("select user_id from role_user where role_id = $role_id");
            $in_users = '';
            foreach ($users as $user){
                $in_users .= $user->user_id . ',';
            }
            $in_users = mb_substr($in_users,0,strlen($in_users)-1);
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            $finish = $input['finish'];
            $max_qty = $input['max_qty'];
            $type = $input['type'];
            $data = array();
            $content = '<div class="col-md-offset-1 col-md-9">';
            if($type == 'anket'){
                $content.='<table class="table table-hover table-bordered"><tr><th>Дата</th><th>Анкета</th><th>Кол-во анкет</th></tr>';
                $rows = DB::select("SELECT fq.date, f.name AS fname, SUM(fq.qty) AS qty FROM forms_qty AS fq
                                        JOIN forms AS f ON f.id = fq.form_id
                                        WHERE user_id IN ($in_users) AND fq.date BETWEEN '$start' AND '$finish'
                                        group BY fq.date, fname ORDER BY fq.date");
                foreach ($rows as $row){
                    $content.= '<tr><td>'.$row->date.'</td><td>'.$row->fname.'</td><td>'.$row->qty.'</td></tr>';
                }
                $content .= '</table></div>';
            }
            else{
                $content.='<table class="table table-hover table-bordered"><tr><th>Дата</th><th>Кол-во анкет</th><th>% выполнения</th></tr>';
                $rows = DB::select("SELECT `date`, SUM(qty) AS qty, ROUND(100 * sum(qty) / $max_qty, 0) AS percent FROM forms_qty
                                        WHERE user_id IN ($in_users) AND `date` BETWEEN '$start' AND '$finish'
                                        GROUP BY `date` ORDER BY `date`");
                foreach ($rows as $row){
                    $content.= '<tr><td>'.$row->date.'</td><td>'.$row->qty.'</td><td>'.$row->percent.'</td></tr>';
                }
                $content .= '</table></div>';
            }
            array_push($data,["content"=>$content]);
            $qty = FormQty::whereBetween('date',[$start,$finish])->sum('qty');
            array_push($data,["qty"=>$qty]);
            return json_encode($data);
        }
        if (view()->exists('report::anket_work')) {
            $title = 'Работа интервьюера';

            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'max_qty' => '50',
            ];
            return view('report::anket_work', $data);
        }
        abort(404);
    }
}
