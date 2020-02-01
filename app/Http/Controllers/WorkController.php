<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Rentlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class WorkController extends Controller
{
    public function index()
    {
        if (!Role::granted('work_guard')) {
            abort(503);
        }
        if (view()->exists('works')) {
            $head = 'Присутствие на выставке';
            $period = date('Y-m'); //берем за текущий месяц
            $rows = Rentlog::where("data", "like", $period . "-%")->get();
            $title = 'Журнал присутствия на выставке';
            $data = [
                'title' => $title,
                'head' => $head,
                'rows' => $rows,
            ];
            return view('works', $data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if (!Role::granted('work_guard')) {
            abort(503);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'array' => 'Значение поля должно быть массивом!',
                'date' => 'Значение поля должно быть датой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input, [
                'data' => 'required|date',
                'renter_id' => 'required|array',
                'period' => 'nullable|array'
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('workAdd')->withErrors($validator)->withInput();
            }
            $renters = $input['renter_id'];
            if (isset($input['period']))
                $period = $input['period'];
            else
                $period = null;
            if (isset($input['alltime']))
                $alltime = $input['alltime'];
            else
                $alltime = null;
            if (isset($input['notime']))
                $notime = $input['notime'];
            else
                $notime = null;

            foreach ($renters as $renter_id) {
                $model = new Rentlog();
                $model->renter_id = $renter_id;
                $model->data = $input['data'];
                $model->SaveData($alltime, $notime, $period);
            }
            $msg = 'Записи успешно добавлены!';
            //$ip = $request->getClientIp();
            //вызываем event
            //event(new AddEventLogs('info',Auth::id(),$msg,$ip));
            return redirect('/works')->with('status', $msg);
        }
        if (view()->exists('work_add')) {
            $renters = Rentlog::GetActiveRenters('МС');
            $rentsel = array();
            foreach ($renters as $renter) {
                $rentsel[$renter->id] = $renter->title . ' (' . $renter->area . ')'; //массив для заполнения данных в select формы
            }
            $hoursel = ['10' => 'С 10:00 до 11:00', '11' => 'С 11:00 до 12:00', '12' => 'С 12:00 до 13:00', '13' => 'С 13:00 до 14:00',
                '14' => 'С 14:00 до 15:00', '15' => 'С 15:00 до 16:00', '16' => 'С 16:00 до 17:00', '17' => 'С 17:00 до 18:00',
                '18' => 'С 18:00 до 19:00', '19' => 'С 19:00 до 20:00', '20' => 'С 20:00 до 21:00'];
            $data = [
                'title' => 'Новая запись',
                'rentsel' => $rentsel,
                'hoursel' => $hoursel,
            ];
            return view('work_add', $data);
        }
        abort(404);
    }

    public function upload(Request $request)
    {
        if (!Role::granted('work_guard')) {
            abort(503);
        }
        //$date = date('Y-m-d');
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'date' => 'Значение поля должно быть датой!',
            ];
            $validator = Validator::make($input, [
                'data' => 'required|date',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('workAdd')->withErrors($validator)->withInput();
            }
            $date = $input['data'];
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
            $sheet->setTitle('Присутствие на выставке');
            $sheet->setCellValue('A1', $date);
            $k = 2;
            $sheet->getStyle('A' . $k . ':M' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $k, 'ID');
            $sheet->setCellValue('B' . $k, 'Арендатор\Период');
            $sheet->setCellValue('C' . $k, '10-11');
            $sheet->setCellValue('D' . $k, '11-12');
            $sheet->setCellValue('E' . $k, '12-13');
            $sheet->setCellValue('F' . $k, '13-14');
            $sheet->setCellValue('G' . $k, '14-15');
            $sheet->setCellValue('H' . $k, '15-16');
            $sheet->setCellValue('I' . $k, '16-17');
            $sheet->setCellValue('J' . $k, '17-18');
            $sheet->setCellValue('K' . $k, '18-19');
            $sheet->setCellValue('L' . $k, '19-20');
            $sheet->setCellValue('M' . $k, '20-21');
            $sheet->getStyle('A' . $k . ':M' . $k)->applyFromArray($styleArray);
            $k++;
            $rents = Rentlog::GetActiveRenters('МС');
            foreach ($rents as $rent) {
                $sheet->setCellValue('A' . $k, $rent->id);
                $sheet->setCellValue('B' . $k, $rent->title . '    участок № ' . $rent->area);
                $sheet->getStyle('A' . $k . ':A' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('C' . $k, 0);
                $sheet->setCellValue('D' . $k, 0);
                $sheet->setCellValue('E' . $k, 0);
                $sheet->setCellValue('F' . $k, 0);
                $sheet->setCellValue('G' . $k, 0);
                $sheet->setCellValue('H' . $k, 0);
                $sheet->setCellValue('I' . $k, 0);
                $sheet->setCellValue('J' . $k, 0);
                $sheet->setCellValue('K' . $k, 0);
                $sheet->setCellValue('L' . $k, 0);
                $sheet->setCellValue('M' . $k, 0);
                $sheet->getStyle('C' . $k . ':M' . $k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $k++;
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
            $sheet->getColumnDimension('M')->setAutoSize(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = "template";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

        }
    }

    public function download(Request $request){
        if(!Role::granted('work_guard')){
            abort(503);
        }
        if($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $excel = IOFactory::load($path);
            // Цикл по листам Excel-файла
            foreach ($excel->getWorksheetIterator() as $worksheet) {
                // выгружаем данные из объекта в массив
                $tables[] = $worksheet->toArray();
            }
            $num = 0;
            // Цикл по листам Excel-файла
            foreach( $tables as $table ) {
                $rows = count($table);
                $date = $table[0][0];
                for($i=2;$i<$rows;$i++){
                    $row = $table[$i];
                    $model = new RentLog();
                    $model->renter_id = $row[0];
                    $model->data = $date;
                    $model->period1 = $row[2];
                    $model->period2 = $row[3];
                    $model->period3 = $row[4];
                    $model->period4 = $row[5];
                    $model->period5 = $row[6];
                    $model->period6 = $row[7];
                    $model->period7 = $row[8];
                    $model->period8 = $row[9];
                    $model->period9 = $row[10];
                    $model->period10 = $row[11];
                    $model->period11 = $row[12];
                    $dbl = RentLog::where(['renter_id'=>$row[0],'data'=>$date])->first();
                    if(!empty($dbl)) $dbl->delete(); //удаляем дубли, если есть
                    if($model->save())
                    $num++;
                }
            }
            $msg = 'Выполнен импорт данных по присутствию на выставке из файла Excel!';
            //вызываем event
            //event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/works')->with('status',$msg.' Обработано записей: '.$num);
        }
    }
}
