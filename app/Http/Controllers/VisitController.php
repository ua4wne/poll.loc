<?php

namespace App\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class VisitController extends Controller
{
    public function index()
    {
        if(!Role::granted('work_guard')){
            abort(503);
        }
        if(view()->exists('visits')){
            $head='Посещение выставки';
            $period = date('Y-m'); //берем за текущий месяц
            $rows = Visit::where("data", "like", $period . "-%")->get();
            $title = 'Учет посетителей выставки';
            $data = [
                'title' => $title,
                'head' => $head,
                'rows' => $rows,
            ];
            return view('visits',$data);
        }
        abort(404);
    }

    public function create(Request $request)
    {
        if(!Role::granted('work_guard')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'array' => 'Значение поля должно быть массивом!',
                'date' => 'Значение поля должно быть датой!',
                'numeric' => 'Значение поля должно быть числом!',
            ];
            $validator = Validator::make($input,[
                'data' => 'required|date',
                'hours' => 'required|array',
                'ucount' => 'required|numeric'
            ],$messages);
            if($validator->fails()){
                return redirect()->route('visitAdd')->withErrors($validator)->withInput();
            }
            $hours = $input['hours'];
            foreach ($hours as $hour){
                //$visit = new Visit();
                $date = date('Y-m-d H:i:s');
                $visit = Visit::updateOrCreate(['data'=>$input['data'],'hours'=>$hour],['ucount'=>$input['ucount'],'created_at'=>$date]);
            }

            //$visit->fill($input);
            //$visit->created_at = date('Y-m-d H:i:s');
            if($visit){
                $msg = 'Записи успешно добавлены!';
                //$ip = $request->getClientIp();
                //вызываем event
                //event(new AddEventLogs('info',Auth::id(),$msg,$ip));
                return redirect('/visits')->with('status',$msg);
            }
        }
        if(view()->exists('visit_add')){
            $hoursel = ['10'=>'С 10:00 до 11:00','11'=>'С 11:00 до 12:00','12'=>'С 12:00 до 13:00','13'=>'С 13:00 до 14:00',
                '14'=>'С 14:00 до 15:00','15'=>'С 15:00 до 16:00','16'=>'С 16:00 до 17:00','17'=>'С 17:00 до 18:00',
                '18'=>'С 18:00 до 19:00','19'=>'С 19:00 до 20:00','20'=>'С 20:00 до 21:00'];
            $data = [
                'title' => 'Новая запись',
                'hoursel' => $hoursel,
            ];
            return view('visit_add', $data);
        }
        abort(404);
    }

    public function upload(){
        if(!Role::granted('work_guard')){
            abort(503);
        }
        $max = date('t');
        $currd = date('Y-m');
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
        $sheet->getStyle('A'.$k.':L'.$k)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
        for($i=1;$i<=$max;$i++)
        {
            $d = $i;
            if(strlen($d)==1)
                $d = '0'.$d;
            $sheet->setCellValue('A'.$k, $currd.'-'.$d);
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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "template";
        header('Content-Disposition: attachment;filename='.$filename .' ');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
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
                for($i=1;$i<$rows;$i++){
                    $row = $table[$i];
                    $date = $row[0];
                    for($k=1;$k<12;$k++){
                        $hour=$k+9;
                        if($row[$k]>0)
                            $visit = Visit::updateOrCreate(['data'=>$date,'hours'=>$hour],['ucount'=>$row[$k],'created_at'=>$date]);
                        if(!empty($visit)) $num++;
                        $visit = null;
                    }
                }
            }
            $msg = 'Выполнение импорта данных по посетителям выставки из файла Excel!';
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/visits')->with('status','Обработано записей: '.$num);
        }
        $msg = 'Ну и где? Где я спрашиваю тот файл, который я должен загрузить из шаблона?';
        return redirect('/visits')->with('error', $msg);
    }
}
