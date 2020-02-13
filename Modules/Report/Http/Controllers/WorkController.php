<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Renter;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WorkController extends Controller
{
    private $firm='';
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
            $renter_id = $input['renter_id'];
            if(isset($input['report'])){
                $title='Учет времени присутствия';
                $content = $this->GetReport($start,$finish,$renter_id);
                $data = [
                    'title' => $title,
                    'head' => 'Учет времени присутствия на выставке',
                    'start' => $start,
                    'finish' => $finish,
                    'firm' => $this->firm,
                    'content' => $content,
                ];
                return view('report::work-report',$data);
            }
            if(isset($input['export'])) {
                if(!Role::granted('export')){
                    abort(503);
                }
                return $this->RenterReport($renter_id,$start,$finish);
            }
        }
        if(view()->exists('report::works')){
            $title='Присутствие арендаторов на выставке';
            $renters = Rentlog::GetActiveRenters('МС Выставка');
            $rentsel = array();
            foreach($renters as $renter) {
                $rentsel[$renter->id] = $renter->title.' ('.$renter->area.')'; //массив для заполнения данных в select формы
            }
            $places = Place::select(['id','name'])->where('name', 'like', '%МС%')->orderBy('name', 'asc')->get();
            $select2 = array();
            foreach($places as $place) {
                $select2[$place->id] = $place->name; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => $title,
                'head' => 'Задайте условия отбора',
                'rentsel' => $rentsel,
                'places' => $select2,
            ];
            return view('report::works',$data);
        }
        abort(404);
    }

    public function select(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $places = $input['place'];
            $res = '';
            foreach ($places as $place){
                $res.=$place.',';
            }
            $res = mb_substr($res,0,strlen($res)-1);
            //выбираем всех арендаторов согласно выбранным площадкам
            //$renters =  Renter::select(['id','name','area'])->where(['place_id'=>$places,'status'=>1])->orderBy('name', 'asc')->get();
            $renters = DB::select("select id, name,area from renters where place_id in ($res) and status=1 order by name ASC");
            $html = '';
            foreach ($renters as $renter){
                $html.='<option value="'.$renter->id.'">'.$renter->name.' ('.$renter->area.')</option>';
            }
            return $html;
        }
        return 'ERR';
    }

    private function GetReport($start,$finish,$renters){
        if(count($renters)==1){
            foreach($renters as $renter){
                $model_renter = Renter::find($renter);
            }
            $this->firm = "<p>Компания <b>".$model_renter->title."</b> участок №<b>".$model_renter->area."</b></p>";
            return Rentlog::OneRenterReport($renter,$start,$finish);
        }
        if(count($renters)>1){
            return Rentlog::RentersReport($renters,$start,$finish);
        }
    }

    public function RenterReport($renters,$start,$finish){
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
        $sheet->setTitle('Время работы домов');
        $k=1;
        if(count($renters)==1){ //выбран один арендатор
            foreach($renters as $renter){
                $model_renter = Renter::find($renter);
            }
            $sheet->setCellValue('A'.$k, 'Учет времени присутствия на выставке за период с '.$start.' по '.$finish.'');
            $sheet->mergeCells('A'.$k.':L'.$k);
            $sheet->getStyle('A'.$k.':L'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k++;
            $sheet->setCellValue('A'.$k, 'Компания "'.$model_renter->title.'"  участок '.$model_renter->area);
            $sheet->mergeCells('A'.$k.':L'.$k);
            //$sheet->getStyle('A'.$k.':L'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k++;
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
            //цикл по датам и периодам
            $logs = Rentlog::select(['data', 'period1', 'period2', 'period3', 'period4', 'period5', 'period6', 'period7', 'period8', 'period9', 'period10', 'period11'])
                ->where(['renter_id'=>$renter])->whereBetween('data', [$start, $finish])->orderBy('data', 'asc')->get();
            foreach($logs as $log){
                $sheet->setCellValue('A'.$k, $log->data);
                for($j=1; $j<12; $j++)
                {
                    $period = 'period'.$j;
                    if($log->$period==1)
                        $sheet->setCellValueByColumnAndRow($j, $k, 'Да');
                    else
                    {
                        $objRichText = new RichText();
                        $objRichText->createText('');
                        $objNo = $objRichText->createTextRun('Нет');
                        $objNo->getFont()->setColor( new Color(  Color::COLOR_RED ) );
                        $sheet->setCellValueByColumnAndRow($j, $k, $objRichText);
                    }
                }//for
                $k++;
            }
        }
        if(count($renters) > 1){ //выбрано более одного арендатора
            $sheet->setCellValue('A'.$k, 'Учет времени присутствия на выставке за период с '.$start.' по '.$finish.'');
            $sheet->mergeCells('A'.$k.':E'.$k);
            $sheet->getStyle('A'.$k.':E'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $k++;
            $sheet->setCellValue('A'.$k, '№ участка');
            $sheet->setCellValue('B'.$k, 'Название компании');
            $sheet->setCellValue('C'.$k, 'Кол-во часов');
            $sheet->setCellValue('D'.$k, 'Кол-во дней');
            $sheet->setCellValue('E'.$k, 'В среднем часов в день');
            $sheet->getStyle('A'.$k.':E'.$k)->applyFromArray($styleArray);
            $k++;
            foreach($renters as $renter) {
                //группировка по датам и периодам
                $query = "SELECT renters.title, renters.area, Sum(period1)+Sum(period2)+Sum(period3)+Sum(period4)+Sum(period5)+Sum(period6)+Sum(period7)+Sum(period8)+Sum(period9)+Sum(period10)+Sum(period11) AS alltime,";
                $query .= "count(rentlogs.data) AS alldata FROM rentlogs INNER JOIN renters ON renters.id = rentlogs.renter_id";
                $query .= " WHERE renter_id=" . $renter . " AND rentlogs.`data` BETWEEN '" . $start . "' AND '" . $finish . "'";
                $query .= " GROUP BY renters.title, renters.area ORDER BY renters.area+0";
                $result = DB::select($query);
                if(count($result)==0)
                    continue;
                $sheet->setCellValue('A'.$k, $result[0]->area);
                $sheet->setCellValue('B'.$k, $result[0]->title);
                $sheet->setCellValue('C'.$k, $result[0]->alltime);
                $sheet->setCellValue('D'.$k, $result[0]->alldata);
                if($result[0]->alldata > 0)
                    $avg=round($result[0]->alltime/$result[0]->alldata,2);
                else
                    $avg = 0;
                $sheet->getStyle('E'.$k)->getNumberFormat()
                    ->setFormatCode('[Black][>=9]#,##0.00;[Red][<9]#,##0.00');
                $sheet->getCell('E'.$k)->setValue($avg);
                $sheet->getStyle('E'.$k)->getNumberFormat();
                $k++;
            }
        }
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
        $filename = "presence";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

}
