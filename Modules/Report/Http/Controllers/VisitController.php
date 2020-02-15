<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VisitController extends Controller
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
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            if($start=='start')
                $start = date('Y-m').'-01';
            $finish = $input['finish'];
            if($finish=='finish')
                $finish = date('Y-m-d');
            if(isset($input['export'])){
                if(!Role::granted('export')){
                    abort(503);
                }
                return $this->export($start,$finish);
            }
            $group = $input['group'];
            $data = array();
            if($group=='byday')
                $logs=DB::select("select WEEKDAY(`data`) as data, sum(ucount) as ucount from visits where `data` between '$start' and '$finish' group by WEEKDAY(`data`)");
            else if($group=='byweek')
                $logs=DB::select("select date_format(`data`, \"%Y-%v\") as data, sum(ucount) as ucount from visits where `data` between '$start' and '$finish' group by date_format(`data`, \"%Y-%v\")");
            else if($group=='bymonth')
                $logs=DB::select("select date_format(`data`, \"%Y-%m\") as data, sum(ucount) as ucount from visits where `data` between '$start' and '$finish' group by date_format(`data`, \"%Y-%m\")");
            else
                $logs=DB::select("select `data`, sum(ucount) as ucount from visits where `data` between '$start' and '$finish' group by `data`");
            if($logs) {
                foreach($logs as $log){
                    $tmp = array();
                    switch ($log->data) {
                        case 0:
                            $log->data='ПН';
                            break;
                        case 1:
                            $log->data='ВТ';
                            break;
                        case 2:
                            $log->data='СР';
                            break;
                        case 3:
                            $log->data='ЧТ';
                            break;
                        case 4:
                            $log->data='ПТ';
                            break;
                        case 5:
                            $log->data='СБ';
                            break;
                        case 6:
                            $log->data='ВС';
                            break;
                        default:
                            break;
                    }
                    $tmp['y'] = $log->data;
                    $tmp['a'] = $log->ucount;
                    array_push($data,$tmp);
                }
            }
            else{
                $tmp = array();
                $tmp['y'] = $start;
                $tmp['a'] = 0;
                $tmp['y'] = $finish;
                $tmp['a'] = 0;
                array_push($data,$tmp);
            }
            return json_encode($data);
        }
        if(view()->exists('report::visits')){
            $title='Посещение выставки';
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
            ];
            return view('report::visits',$data);
        }
        abort(404);
    }

    public function table(Request $request){
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $start = $input['start'];
            $finish = $input['finish'];
            if($start=='start')
                $start = date('Y-m').'-01';
            if($finish=='finish')
                $finish = date('Y-m-d');
            return Visit::VisitTable($start,$finish);
        }
    }

    private function export($start,$finish){
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Посетители выставки');
        $k=1;
        $sheet->setCellValue('A'.$k, 'Учет количества посетителей выставки за период с '.$start.' по '.$finish.'');
        $sheet->mergeCells('A'.$k.':L'.$k);
        $sheet->getStyle('A'.$k.':L'.$k)->getFont()->setBold(true);
        $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $k=5;
        $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        //    $k++;
        $sheet->setCellValue('A'.$k, 'Дата\Период');
        $sheet->setCellValue('B'.$k, '10-11');
        $sheet->setCellValue('C'.$k, '11-12');
        $sheet->setCellValue('D'.$k, '12-13');
        $sheet->setCellValue('E'.$k, '13-14');
        $sheet->setCellValue('F'.$k, '14-15');
        $sheet->setCellValue('G'.$k, '15-16');
        $sheet->setCellValue('H'.$k, '16-17');
        $sheet->setCellValue('I'.$k, '17-18');
        $sheet->setCellValue('J'.$k, '18-19');
        $sheet->setCellValue('K'.$k, '19-20');
        $sheet->setCellValue('L'.$k, '20-21');
        $sheet->getStyle('A'.$k.':L'.$k)->applyFromArray($styleArray);
        $k++;
        $itog = 0; //общее кол-во посетителей
        $dates = Visit::select(['data'])->distinct('data')->whereBetween('data', [$start, $finish])->orderBy('data','asc')->get();
        $cell = ['10'=>'B','11'=>'C','12'=>'D','13'=>'E','14'=>'F','15'=>'G','16'=>'H','17'=>'I','18'=>'J','19'=>'K','20'=>'L'];
        foreach($dates as $date){
            $logs=Visit::select(['hours','ucount'])->where(['data'=>$date->data])->orderBy('hours','asc')->get();
            $sheet->setCellValue('A'.$k, $date->data);
            //сначала заполняем все нулями
            $sheet->setCellValue('B'.$k, 0);
            $sheet->setCellValue('C'.$k, 0);
            $sheet->setCellValue('D'.$k, 0);
            $sheet->setCellValue('E'.$k, 0);
            $sheet->setCellValue('F'.$k, 0);
            $sheet->setCellValue('G'.$k, 0);
            $sheet->setCellValue('H'.$k, 0);
            $sheet->setCellValue('I'.$k, 0);
            $sheet->setCellValue('J'.$k, 0);
            $sheet->setCellValue('K'.$k, 0);
            $sheet->setCellValue('L'.$k, 0);
            foreach($logs as $log){
                $itog = $itog+$log->ucount;
                $sheet->setCellValue($cell[$log->hours].$k, $log->ucount);
            }
            $k++;
        }
        $sheet->setCellValue('A3', 'Всего посетителей выставки - '.$itog);
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "attendance";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function analise(Request $request){
        if(!Role::granted('view_report')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $data = array();
            $action = $input['action'];
            if($action=='year'){
                $year = date('Y');
                $logs=DB::select("select SUBSTRING(`data`, 6, 2) as `month`, sum(ucount) as ucount from visits where `data` like '$year-%' group by `month`");
                foreach($logs as $log){
                    $tmp = array();
                    $tmp['y'] = $year.'-'.$log->month;
                    $tmp['a'] = $log->ucount;
                    array_push($data,$tmp);
                }
                return json_encode($data);
            }
            elseif($action=='all'){
                $logs=DB::select("select SUBSTRING(`data`, 1, 4) as `year`, sum(ucount) as ucount from visits group by `year`");
                foreach($logs as $log){
                    $tmp = array();
                    $tmp['y'] = $log->year;
                    $tmp['a'] = $log->ucount;
                    array_push($data,$tmp);
                }
                return json_encode($data);
            }
            else
                return 'Не известный запрос!';
        }
    }
}
