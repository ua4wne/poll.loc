<?php

namespace Modules\Energy\Http\Controllers;

use App\Http\Controllers\LibController;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\EnergyLog;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function report(){
        if (!Role::granted('view_report')) {
            abort(503);
        }
        $this->EnergyReport(false);
    }

    public function viamail(Request $request){
        if (!Role::granted('view_report')) {
            abort(503);
        }
        $this->EnergyReport(true);
    }

    private function EnergyReport($save=false){
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'bottom' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'left' => array(
                    'style' => Border::BORDER_THICK,
                ),
                'right' => array(
                    'style' => Border::BORDER_THICK,
                ),
            )
        );
        $styleCell = array(
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
        $year = date('Y');
        $month = date('m');
        $period = explode('-', date('Y-m', strtotime("$year-$month-01 -1 month"))); //определяем предыдущий период
        $y = $period[0];
        $m = LibController::SetMonth($period[1]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //готовим файл для отправки отчета бухгалтеру
        //формируем лист по МС
        $sheet->setCellValue('A1', 'Ведомость по оплате за электроэнергию')
            ->setCellValue('A2', 'Год')
            ->setCellValue('B2', $y)
            ->setCellValue('A3', 'Месяц')
            ->setCellValue('B3', $m)
            ->setCellValue('A5', '№ дома')
            ->setCellValue('B5', 'Название организации')
            ->setCellValue('C5', 'Сумма, руб')
            ->setCellValue('D5', 'Территория')
            ->setCellValue('E5', 'За кем закреплен');

        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        //Название листа
        $sheet->setTitle("Расчет по МС");
        $rows = DB::select("select r.area as `area`, p.name as pname, d.name as `owner`, r.title as rname, round(sum(l.price),2) as price from energylogs as l
                join renters as r on r.id = l.renter_id
                join divisions as d on d.id = r.division_id
                join places as p on p.id = r.place_id
                WHERE p.name LIKE ('%МС%') and l.year='$y' and l.month='$period[1]' GROUP BY rname ORDER BY  pname, `area` + 0 , `owner`, rname");
        $k = 6;
        //$num = 1;
        $pay = 0;
        foreach($rows as $row){
            $sheet->setCellValue('A'.$k, $row->area)
                ->setCellValue('B'.$k, $row->rname)
                ->setCellValue('C'.$k, $row->price)
                ->setCellValue('D'.$k, $row->pname)
                ->setCellValue('E'.$k, $row->owner);
            $sheet->getStyle('A'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E'.$k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('A'.$k)->applyFromArray($styleCell);
            $sheet->getStyle('B'.$k)->applyFromArray($styleCell);
            $sheet->getStyle('C'.$k)->applyFromArray($styleCell);
            $sheet->getStyle('D'.$k)->applyFromArray($styleCell);
            $sheet->getStyle('E'.$k)->applyFromArray($styleCell);
            $k++;
            //$num++;
            $pay+=$row->price;
        }
        $sheet->setCellValue('D'.$k, 'ИТОГО:')
            ->setCellValue('E'.$k, $pay);
        $sheet->getStyle('D'.$k.':E'.$k)->getFont()->setBold(true);
        $sheet->getStyle('D'.$k.':E'.$k)->applyFromArray($styleCell);
        $sheet->getStyle('E'.$k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getStyle('A5:E5')->applyFromArray($styleArray);

        //формируем теперь отдельный лист по СК
        $sheet1 = $spreadsheet->createSheet();
        //готовим файл для отправки отчета бухгалтеру
        $sheet1->setCellValue('A1', 'Ведомость по оплате за электроэнергию')
            ->setCellValue('A2', 'Год')
            ->setCellValue('B2', $y)
            ->setCellValue('A3', 'Месяц')
            ->setCellValue('B3', $m)
            ->setCellValue('A5', '№ п\п')
            ->setCellValue('B5', 'Территория')
            ->setCellValue('C5', 'За кем закреплен')
            ->setCellValue('D5', 'Арендатор')
            ->setCellValue('E5', 'К оплате, руб');

        $sheet1->mergeCells('A1:E1');
        $sheet1->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet1->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        //Название листа
        $sheet1->setTitle("Расчет по СК");
        $rows = DB::select("select r.area as `area`, p.name as pname, d.name as `owner`, r.title as rname, round(sum(l.price),2) as price from energylogs as l
                join renters as r on r.id = l.renter_id
                join divisions as d on d.id = r.division_id
                join places as p on p.id = r.place_id
                WHERE p.name NOT LIKE ('%МС%') and l.year='$y' and l.month='$period[1]' GROUP BY rname ORDER BY  pname, `owner`, rname");
        $k = 6;
        $num = 1;
        $pay = 0;
        foreach($rows as $row){
            $sheet1->setCellValue('A'.$k, $num)
                ->setCellValue('B'.$k, $row->pname)
                ->setCellValue('C'.$k, $row->owner)
                ->setCellValue('D'.$k, $row->rname)
                ->setCellValue('E'.$k, $row->price);
            $sheet1->getStyle('A'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('E'.$k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet1->getStyle('A'.$k)->applyFromArray($styleCell);
            $sheet1->getStyle('B'.$k)->applyFromArray($styleCell);
            $sheet1->getStyle('C'.$k)->applyFromArray($styleCell);
            $sheet1->getStyle('D'.$k)->applyFromArray($styleCell);
            $sheet1->getStyle('E'.$k)->applyFromArray($styleCell);
            $k++;
            $num++;
            $pay+=$row->price;
        }
        $sheet1->setCellValue('D'.$k, 'ИТОГО:')
            ->setCellValue('E'.$k, $pay);
        $sheet1->getStyle('D'.$k.':E'.$k)->getFont()->setBold(true);
        $sheet1->getStyle('D'.$k.':E'.$k)->applyFromArray($styleCell);
        $sheet1->getStyle('E'.$k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet1->getColumnDimension('B')->setAutoSize(true);
        $sheet1->getColumnDimension('C')->setAutoSize(true);
        $sheet1->getColumnDimension('D')->setAutoSize(true);
        $sheet1->getColumnDimension('E')->setAutoSize(true);
        $sheet1->getStyle('A5:E5')->applyFromArray($styleArray);

        if($save) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("./download/billing.xlsx");
        }
        else{
            //сохраняем или выгружаем файл без сохранения
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "template";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

}
