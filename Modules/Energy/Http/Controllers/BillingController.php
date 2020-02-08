<?php

namespace Modules\Energy\Http\Controllers;

use App\Events\AddEventLogs;
use App\Http\Controllers\LibController;
use App\Models\Renter;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Entities\Describer;
use Modules\Admin\Entities\Place;
use Modules\Admin\Entities\Role;
use Modules\Energy\Entities\EnergyLog;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

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

    public function calculate(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->view_calculate($input['year'],$input['renter_id']);
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

    public function summary(Request $request){
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->SummaryReport($input['start'],$input['finish'],$input['renter_id']);
        }
        if (view()->exists('energy::summary')) {
            $head = 'Расчет потребления за период';
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
            ];
            return view('energy::summary', $data);
        }
        abort(404);
    }

    public function calcExcel(Request $request)
    {
        if (!Role::granted('export')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->CalculateToExcel($input['year'],$input['renter_id']);
        }
    }

    public function summaryExcel(Request $request){
        if (!Role::granted('export')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            return $this->SummaryToExcel($input['start'],$input['finish'],$input['renter_id'],false);
        }
    }

    public function report()
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        $this->EnergyReport(false);
    }

    public function viamail(Request $request)
    {
        if (!Role::granted('view_report')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if ($input['type'] == 'mail')
                $this->EnergyReport(true);
            if (file_exists('./download/billing.xlsx')) {
                //определяем наличие активных подписчиков
                $describers = Describer::select('email')->where(['status' => 1])->get();
                $subj = 'Расчеты по оплате электроэнергии';
                //отправляем сообщение пользователю
                $msg = '<html><head><title>Расчеты по оплате электроэнергии</title></head>
                    <body><h3>Расчеты по оплате электроэнергии</h3>
                    <p>Здравствуйте!<br>Во вложении находится файл, содержащий расчеты по оплате электроэнергии за предыдущий месяц.</p>
                    <em style="color:red;">Письмо отправлено автоматически. Отвечать на него не нужно.</em><br>
                    <p style="color:darkblue;">С уважением,<br> Ваш почтовый робот.</p>
                    </body></html>';
                $file = './download/billing.xlsx';
                foreach ($describers as $describer) {
                    Mail::send('emails.energy', array('msg' => $msg), function ($message) use ($describer, $file) {
                        $message->to($describer->email)->subject('Расчеты по оплате электроэнергии');
                        $message->attach($file);
                    });
                    $msg = 'На email <strong>' . $describer->email . '</strong> отправлен расчет по электроэнергии.';
                    //вызываем event
                    event(new AddEventLogs('info', Auth::id(), $msg));
                }
                return 'OK';
            }
            return 'NO';
        }
    }

    public function summaryMail(Request $request){
        if (!Role::granted('export')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'email' => 'Значение поля должно быть не более :max символов!',
            ];
            $validator = Validator::make($input, [
                'email' => 'nullable|email',
            ], $messages);
            if ($validator->fails()) {
                return 'ERR';
            }
            $file = $this->SummaryToExcel($input['start'],$input['finish'],$input['renter_id'],true);
            if (file_exists($file)) {
                $resp = $input['email'];
                $msg = '<html><head><title>Расчеты по электроэнергии</title></head>
                    <body><h3>Расчеты по электроэнергии</h3>
                    <p>Здравствуйте!<br>Во вложении находится файл, содержащий расчеты по электроэнергии.</p>
                    <em style="color:red;">Письмо отправлено автоматически. Отвечать на него не нужно.</em><br>
                    <p style="color:darkblue;">С уважением,<br> Почтовый робот МС.</p>
                    </body></html>';
                //отправляем сообщение пользователю
                Mail::send('emails.energy', array('msg' => $msg), function ($message) use ($resp, $file) {
                    $message->to($resp)->subject('Расчеты по оплате электроэнергии');
                    $message->attach($file);
                });
                $msg = 'На email <strong>' . $resp . '</strong> отправлен расчет по электроэнергии.';
                //вызываем event
                event(new AddEventLogs('info', Auth::id(), $msg));
                return 'OK';
            }
            return 'NO';
        }
        return 'ERR';
    }

    private function EnergyReport($save = false)
    {
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
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $k, $row->area)
                ->setCellValue('B' . $k, $row->rname)
                ->setCellValue('C' . $k, $row->price)
                ->setCellValue('D' . $k, $row->pname)
                ->setCellValue('E' . $k, $row->owner);
            $sheet->getStyle('A' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('A' . $k)->applyFromArray($styleCell);
            $sheet->getStyle('B' . $k)->applyFromArray($styleCell);
            $sheet->getStyle('C' . $k)->applyFromArray($styleCell);
            $sheet->getStyle('D' . $k)->applyFromArray($styleCell);
            $sheet->getStyle('E' . $k)->applyFromArray($styleCell);
            $k++;
            //$num++;
            $pay += $row->price;
        }
        $sheet->setCellValue('D' . $k, 'ИТОГО:')
            ->setCellValue('E' . $k, $pay);
        $sheet->getStyle('D' . $k . ':E' . $k)->getFont()->setBold(true);
        $sheet->getStyle('D' . $k . ':E' . $k)->applyFromArray($styleCell);
        $sheet->getStyle('E' . $k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

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
        foreach ($rows as $row) {
            $sheet1->setCellValue('A' . $k, $num)
                ->setCellValue('B' . $k, $row->pname)
                ->setCellValue('C' . $k, $row->owner)
                ->setCellValue('D' . $k, $row->rname)
                ->setCellValue('E' . $k, $row->price);
            $sheet1->getStyle('A' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('E' . $k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet1->getStyle('A' . $k)->applyFromArray($styleCell);
            $sheet1->getStyle('B' . $k)->applyFromArray($styleCell);
            $sheet1->getStyle('C' . $k)->applyFromArray($styleCell);
            $sheet1->getStyle('D' . $k)->applyFromArray($styleCell);
            $sheet1->getStyle('E' . $k)->applyFromArray($styleCell);
            $k++;
            $num++;
            $pay += $row->price;
        }
        $sheet1->setCellValue('D' . $k, 'ИТОГО:')
            ->setCellValue('E' . $k, $pay);
        $sheet1->getStyle('D' . $k . ':E' . $k)->getFont()->setBold(true);
        $sheet1->getStyle('D' . $k . ':E' . $k)->applyFromArray($styleCell);
        $sheet1->getStyle('E' . $k)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet1->getColumnDimension('B')->setAutoSize(true);
        $sheet1->getColumnDimension('C')->setAutoSize(true);
        $sheet1->getColumnDimension('D')->setAutoSize(true);
        $sheet1->getColumnDimension('E')->setAutoSize(true);
        $sheet1->getStyle('A5:E5')->applyFromArray($styleArray);

        //сохраняем или выгружаем файл без сохранения
        if ($save) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("./download/billing.xlsx");
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "template";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    private function view_calculate($year,$renters){
        $content='';
        if(is_array($renters)){
            foreach($renters as $renter){
                $logs = EnergyLog::select(['month','encount','delta','price'])->where(['renter_id'=>$renter,'year'=>$year])->orderBy('month', 'asc')->get();
                $rent = Renter::find($renter);
                $content.='<div class="agileinfo-grap">';
                $content.='<p class="text-info">Данные расчета для '.$rent->title.' ('.$rent->area.')</p>';
                $content.='<table class="table table-hover table-striped">
            <tr><th>Месяц</th><th>Показания счетчика, кВт</th><th>Потребление, кВт</th><th>Сумма, руб</th></tr>';
                foreach ($logs as $log){
                    $month = LibController::SetMonth($log->month);
                    $content.='<tr><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
                }
                $content.='</table></div>';
            }
        }
        else{
            $logs = EnergyLog::select(['month','encount','delta','price'])->where(['renter_id'=>$renters,'year'=>$year])->orderBy('month', 'asc')->get();
            $rent = Renter::find($renters);
            $content.='<div class="agileinfo-grap">';
            $content.='<p class="text-info">Данные расчета для '.$rent->title.' ('.$rent->area.')</p>';
            $content.='<table class="table table-hover table-striped">
            <tr><th>Месяц</th><th>Показания счетчика, кВт</th><th>Потребление, кВт</th><th>Сумма, руб</th></tr>';
            foreach ($logs as $log){
                $month = LibController::SetMonth($log->month);
                $content.='<tr><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
            }
            $content.='</table></div>';
        }

        return $content;
    }

    private function CalculateToExcel($year,$renters){

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
        //готовим файл excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $p=1;
        foreach($renters as $renter){
            $logs = EnergyLog::select(['month','encount','delta','price'])->where(['renter_id'=>$renter,'year'=>$year])->orderBy('month', 'asc')->get();
            $rent = Renter::find($renter);
            //$objPHPExcel->setActiveSheetIndex($p);
            $sheet->setTitle('Лист №'.$p);
            $k=1;
            $sheet->setCellValue('A'.$k, 'Расчет энергопотребления за '.$year.' год.');
            $sheet->mergeCells('A'.$k.':D'.$k);
            $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k=3;
            $sheet->setCellValue('A'.$k, $rent->title.' ('.$rent->area.')');
            $sheet->mergeCells('A'.$k.':D'.$k);
            $sheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
            $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $k=5;
            $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A'.$k, 'Месяц');
            $sheet->setCellValue('B'.$k, 'Показания счетчика, кВт');
            $sheet->setCellValue('C'.$k, 'Потребление, кВт');
            $sheet->setCellValue('D'.$k, 'Сумма, руб');
            $sheet->getStyle('A'.$k.':D'.$k)->applyFromArray($styleArray);
            $k++;
            foreach ($logs as $log){
                $month = LibController::SetMonth($log->month);
                $sheet->setCellValue('A'.$k, $month);
                //сначала заполняем все нулями
                $sheet->setCellValue('B'.$k, $log->encount);
                $sheet->setCellValue('C'.$k, $log->delta);
                $sheet->setCellValue('D'.$k, $log->price);
                $k++;
            }
            $p++;
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet = $spreadsheet->createSheet();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "calculate";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    private function SummaryReport($from,$to,$renters){
        //выделяем год и месяц периода
        $start = explode('-',$from);
        $from_year = $start[0];
        $from_month = $start[1];
        $finish = explode('-',$to);
        $to_year = $finish[0];
        $to_month = $finish[1];
        if($from_year == $to_year && $from_month == $to_month){ //данные за месяц
            return $this->view_calculate($from_year,$renters);
        }
        else if($from_year == $to_year && $from_month != $to_month){ //один год, но разные месяцы
            $content = '';
            foreach($renters as $renter){
                $logs = EnergyLog::select(['month','encount','delta','price'])->where(['renter_id'=>$renter,'year'=>$from_year])->whereBetween('month',[$from_month,$to_month])->orderBy('month', 'asc')->get();
                $rent = Renter::find($renter);
                //$content.='<div class="agileinfo-grap">';
                $content.='<p class="text-info">Данные расчета для '.$rent->title.' ('.$rent->area.')</p>';
                $content.='<table class="table table-hover table-striped">
            <tr><th>Месяц</th><th>Показания счетчика, кВт</th><th>Потребление, кВт</th><th>Сумма, руб</th></tr>';
                foreach ($logs as $log){
                    $month = LibController::SetMonth($log->month);
                    $content.='<tr><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
                }
                $content.='</table>';
            }
            return $content;
        }
        else{ //данные за период по арендаторам за разные года
            $content = '';
            foreach($renters as $renter){
                //сначала выбираем все записи за период
                $logs = EnergyLog::select(['year','month','encount','delta','price'])->where(['renter_id'=>$renter])->whereBetween('year',[$from_year,$to_year])->orderBy('year','asc')->orderBy('month','asc')->get();
                $rent = Renter::find($renter);
                //$content.='<div class="agileinfo-grap">';
                $content.='<p class="text-info">Данные расчета для '.$rent->title.' ('.$rent->area.')</p>';
                $content.='<table class="table table-hover table-striped">
                            <tr><th>Год</th><th>Месяц</th><th>Показания счетчика, кВт</th><th>Потребление, кВт</th><th>Сумма, руб</th></tr>';
                foreach ($logs as $log){
                    if($log->year == $from_year && (int)$log->month >= (int)$from_month){
                        $month = LibController::SetMonth($log->month);
                        $content.='<tr><td>'.$log->year.'</td><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
                    }
                    else if($log->year == $to_year && (int)$log->month <= (int)$to_month){
                        $month = LibController::SetMonth($log->month);
                        $content.='<tr><td>'.$log->year.'</td><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
                    }
                    else if((int)$log->year>(int)$from_year && (int)$log->year<(int)$to_year){
                        $month = LibController::SetMonth($log->month);
                        $content.='<tr><td>'.$log->year.'</td><td>'.$month.'</td><td>'.$log->encount.'</td><td>'.$log->delta.'</td><td>'.$log->price.'</td></tr>';
                    }

                }
                $content.='</table>';
            }
            return $content;
        }
    }

    private function SummaryToExcel($from,$to,$renters, $save=FALSE){

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
        //выделяем год и месяц периода
        $start = explode('-',$from);
        $from_year = $start[0];
        $from_month = $start[1];
        $finish = explode('-',$to);
        $to_year = $finish[0];
        $to_month = $finish[1];
        if($from_year == $to_year && $from_month == $to_month){ //данные за месяц
            //готовим файл excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $p=0;
            foreach($renters as $renter){
                $logs = EnergyLog::select(['encount','delta','price'])->where(['renter_id'=>$renter,'year'=>$from_year,'month'=>$from_month])->get();
                $rent = Renter::find($renter);
                if(count($logs)<1) continue; //нет данных, идем на следующую итерацию
                $sheet->setTitle('Лист №'.$p);
                $k=1;
                $sheet->setCellValue('A'.$k, 'Расчет энергопотребления за период с '.$from_year.'-'.$from_month.' по '.$to_year.'-'.$to_month);
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=3;
                $sheet->setCellValue('A'.$k, $rent->title.' ('.$rent->area.')');
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=5;
                $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('A'.$k, 'Показания счетчика, кВт');
                $sheet->setCellValue('B'.$k, 'Потребление, кВт');
                $sheet->setCellValue('C'.$k, 'Сумма, руб');
                $sheet->getStyle('A'.$k.':C'.$k)->applyFromArray($styleArray);
                $k++;
                foreach ($logs as $log){
                    //$month = HelpController::SetMonth($log->month);
                    $sheet->setCellValue('A'.$k, $log->encount);
                    $sheet->setCellValue('B'.$k, $log->delta);
                    $sheet->setCellValue('C'.$k, $log->price);
                    $k++;
                }
                $p++;
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet = $spreadsheet->createSheet();
            }
        }
        else if($from_year == $to_year && $from_month != $to_month) { //один год, но разные месяцы
            //готовим файл excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $p=0;
            foreach($renters as $renter){
                $logs = EnergyLog::select(['month','encount','delta','price'])->where(['renter_id'=>$renter,'year'=>$from_year])->whereBetween('month',[$from_month,$to_month])->orderBy('month', 'asc')->get();
                $rent = Renter::find($renter);
                if(count($logs)<1) continue; //нет данных, идем на следующую итерацию
                $sheet->setTitle('Лист №'.$p);
                $k=1;
                $sheet->setCellValue('A'.$k, 'Расчет энергопотребления за период с '.$from_year.'-'.$from_month.' по '.$to_year.'-'.$to_month);
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=3;
                $sheet->setCellValue('A'.$k, $rent->title.' ('.$rent->area.')');
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=5;
                $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('A'.$k, 'Месяц');
                $sheet->setCellValue('B'.$k, 'Показания счетчика, кВт');
                $sheet->setCellValue('C'.$k, 'Потребление, кВт');
                $sheet->setCellValue('D'.$k, 'Сумма, руб');
                $sheet->getStyle('A'.$k.':D'.$k)->applyFromArray($styleArray);
                $k++;
                foreach ($logs as $log){
                    $month = LibController::SetMonth($log->month);
                    $sheet->setCellValue('A'.$k, $month);
                    $sheet->setCellValue('B'.$k, $log->encount);
                    $sheet->setCellValue('C'.$k, $log->delta);
                    $sheet->setCellValue('D'.$k, $log->price);
                    $k++;
                }
                $p++;
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet = $spreadsheet->createSheet();
            }
        }
        else{
            //готовим файл excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $p=0;
            foreach($renters as $renter){
                $logs = EnergyLog::select(['year','month','encount','delta','price'])->where(['renter_id'=>$renter])->whereBetween('year',[$from_year,$to_year])->orderBy('year', 'asc')->orderBy('month', 'asc')->get();
                $rent = Renter::find($renter);
                if(count($logs)<1) continue; //нет данных, идем на следующую итерацию
                $sheet->setTitle('Лист №'.$p);
                $k=1;
                $sheet->setCellValue('A'.$k, 'Расчет энергопотребления за период с '.$from_year.'-'.$from_month.' по '.$to_year.'-'.$to_month);
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=3;
                $sheet->setCellValue('A'.$k, $rent->title.' ('.$rent->area.')');
                $sheet->mergeCells('A'.$k.':D'.$k);
                $sheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
                $sheet->getStyle('A'.$k.':D'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k=5;
                $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('A'.$k, 'Месяц');
                $sheet->setCellValue('B'.$k, 'Показания счетчика, кВт');
                $sheet->setCellValue('C'.$k, 'Потребление, кВт');
                $sheet->setCellValue('D'.$k, 'Сумма, руб');
                $sheet->getStyle('A'.$k.':D'.$k)->applyFromArray($styleArray);
                $k++;
                foreach ($logs as $log){
                    if($log->year == $from_year && (int)$log->month >= (int)$from_month){
                        $month = LibController::SetMonth($log->month);
                        $sheet->setCellValue('A'.$k, $month.'-'.$log->year);
                        $sheet->setCellValue('B'.$k, $log->encount);
                        $sheet->setCellValue('C'.$k, $log->delta);
                        $sheet->setCellValue('D'.$k, $log->price);
                        $k++;
                    }
                    else if($log->year == $to_year && (int)$log->month <= (int)$to_month){
                        $month = LibController::SetMonth($log->month);
                        $sheet->setCellValue('A'.$k, $month.'-'.$log->year);
                        $sheet->setCellValue('B'.$k, $log->encount);
                        $sheet->setCellValue('C'.$k, $log->delta);
                        $sheet->setCellValue('D'.$k, $log->price);
                        $k++;
                    }
                    else if((int)$log->year>(int)$from_year && (int)$log->year<(int)$to_year){
                        $month = LibController::SetMonth($log->month);
                        $sheet->setCellValue('A'.$k, $month.'-'.$log->year);
                        $sheet->setCellValue('B'.$k, $log->encount);
                        $sheet->setCellValue('C'.$k, $log->delta);
                        $sheet->setCellValue('D'.$k, $log->price);
                        $k++;
                    }
                }
                $p++;
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet = $spreadsheet->createSheet();
            }
        }

        if($save){
            $writer = new Xlsx($spreadsheet);
            $writer->save("./download/summary-$from_year-$from_month-$to_year-$to_month.xlsx");
            return "./download/summary-$from_year-$from_month-$to_year-$to_month.xlsx";
        }
        else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "summary";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

}
