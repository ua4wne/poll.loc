<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Marketing\Entities\Visitorlog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MegacountController extends Controller
{
    public function index(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            $finish = $input['finish'];
            $group = $input['group'];
            if(isset($input['export'])){
                if(!Role::granted('export')){
                    abort(503);
                }
                return $this->export($group,$start,$finish);
            }
            $data = array();
            if($group==0){
                $rows = DB::select("SELECT SUM(vl.fw) AS forward, SUM(vl.bw) AS backward, m.name AS megacnt, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY vl.counter_id");
                $content ='<table class="table table-hover table-bordered"><tr><th>Территория</th><th>Точка прохода</th><th>Вошло, чел.</th><th>Вышло, чел.</th></tr>';
                foreach ($rows as $row){
                    $content.= '<tr><td>'.$row->plname.'</td><td>'.$row->megacnt.'</td><td>'.$row->forward.'</td><td>'.$row->backward.'</td></tr>';
                }
            }
            if($group==1){
                $rows = DB::select("SELECT SUM(vl.fw) AS forward, SUM(vl.bw) AS backward, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY m.place_id ORDER BY vl.counter_id");
                $content ='<table class="table table-hover table-bordered"><tr><th>Территория</th><th>Вошло, чел.</th><th>Вышло, чел.</th></tr>';
                foreach ($rows as $row){
                    $content.= '<tr><td>'.$row->plname.'</td><td>'.$row->forward.'</td><td>'.$row->backward.'</td></tr>';
                }
            }
            $content .= '</table></div><hr></div>';
            $fw = Visitorlog::whereBetween('data',[$start,$finish])->sum('fw');
            $bw = Visitorlog::whereBetween('data',[$start,$finish])->sum('bw');
            array_push($data,["fw"=>$fw]);
            array_push($data,["bw"=>$bw]);
            array_push($data,["content"=>$content]);
            return json_encode($data);
        }
        if(view()->exists('report::megacount')){
            $title='Счетчики посетителей';
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
            ];
            return view('report::megacount',$data);
        }
        abort(404);
    }

    public function bar_graph(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if ($start == 'start')
                $start = date('Y-m') . '-01';
            $finish = $input['finish'];
            if ($finish == 'finish')
                $finish = date('Y-m-d');
            $rows = DB::select("SELECT `data`, SUM(fw) AS forward, SUM(bw) AS backward FROM visitorlogs
                                WHERE `data` BETWEEN '$start' AND '$finish'
                                GROUP BY `data`");
            $data = array();
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['d'] = $row->data;
                    $tmp['f'] = $row->forward;
                    $tmp['b'] = $row->backward;
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function pie_graph(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if ($start == 'start')
                $start = date('Y-m') . '-01';
            $finish = $input['finish'];
            if ($finish == 'finish')
                $finish = date('Y-m-d');
            $rows = DB::select("SELECT SUM(vl.fw) AS forward, m.name AS megacnt, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY vl.counter_id");
            $data = array();
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['cnt'] = $row->megacnt.' ('.$row->plname.')';
                    $tmp['fw'] = $row->forward;
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function place_pie_graph(Request $request)
    {
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if ($start == 'start')
                $start = date('Y-m') . '-01';
            $finish = $input['finish'];
            if ($finish == 'finish')
                $finish = date('Y-m-d');
            $rows = DB::select("SELECT SUM(vl.fw) AS forward,p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish' AND p.id IN(1,7)");
            $data = array();
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['cnt'] = 'Основная и 225-2';
                    $tmp['fw'] = $row->forward;
                    array_push($data,$tmp);
                }
            }
            $rows = DB::select("SELECT SUM(vl.fw) AS forward,p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish' AND p.id = 6");
            if(!empty($rows)){
                foreach($rows as $row){
                    $tmp = array();
                    $tmp['cnt'] = '225-1';
                    $tmp['fw'] = $row->forward;
                    array_push($data,$tmp);
                }
            }
            return json_encode($data);
        }
    }

    private function export($group,$start,$finish){
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
        $sheet->setCellValue('A'.$k, 'Счетчики посетителей');
        $sheet->mergeCells('A'.$k.':C'.$k);
        $sheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k=2;
        $sheet->setCellValue('A'.$k, 'статистика за период с '.$start.' по '.$finish);
        $sheet->mergeCells('A'.$k.':C'.$k);
        //$sheet->getStyle('A'.$k.':B'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k++;
        $fw = Visitorlog::whereBetween('data',[$start,$finish])->sum('fw');
        $bw = Visitorlog::whereBetween('data',[$start,$finish])->sum('bw');
        $sheet->setCellValue('A'.$k, 'Зашло человек: ');
        $sheet->setCellValue('B'.$k, $fw);
        $sheet->getStyle('B'.$k.':B'.$k)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$k, 'Вышло человек: ');
        $sheet->setCellValue('D'.$k, $bw);
        $sheet->getStyle('D'.$k.':D'.$k)->getFont()->setBold(true);
        //$sheet->getStyle('A'.$k.':B'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k=5;
        //данные в зависимости от условия
        if($group==0){
            $rows = DB::select("SELECT SUM(vl.fw) AS forward, SUM(vl.bw) AS backward, m.name AS megacnt, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY vl.counter_id");
            $sheet->setCellValue('A'.$k, 'Территория');
            $sheet->setCellValue('B'.$k, 'Точка прохода');
            $sheet->setCellValue('C'.$k, 'Зашло, чел.');
            $sheet->setCellValue('D'.$k, 'Вышло, чел.');
            $sheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k++;
            foreach ($rows as $row){
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('A'.$k, $row->plname);
                $sheet->setCellValue('B'.$k, $row->megacnt);
                $sheet->setCellValue('C'.$k, $row->forward);
                $sheet->setCellValue('D'.$k, $row->backward);
                $sheet->getStyle('A'.$k.':D'.$k)->applyFromArray($styleRow);
                $k++;
            }
        }
        if($group==1){
            $rows = DB::select("SELECT SUM(vl.fw) AS forward, SUM(vl.bw) AS backward, p.name AS plname FROM visitorlogs vl
                                JOIN megacounts m ON m.id = vl.counter_id
                                JOIN places p ON p.id = m.place_id
                                WHERE vl.`data` BETWEEN '$start' AND '$finish'
                                GROUP BY m.place_id ORDER BY vl.counter_id");
            $sheet->setCellValue('A'.$k, 'Территория');
            $sheet->setCellValue('B'.$k, 'Зашло, чел.');
            $sheet->setCellValue('C'.$k, 'Вышло, чел.');
            $sheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k++;
            foreach ($rows as $row){
                $sheet->getStyle('A'.$k.':C'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('A'.$k, $row->plname);
                $sheet->setCellValue('B'.$k, $row->forward);
                $sheet->setCellValue('C'.$k, $row->backward);
                $sheet->getStyle('A'.$k.':C'.$k)->applyFromArray($styleRow);
                $k++;
            }
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "megacount";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
