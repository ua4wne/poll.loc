<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rentlog extends Model
{
    //указываем имя таблицы
    protected $table = 'rentlogs';

    protected $fillable = ['renter_id','data','period1','period2','period3','period4','period5','period6','period7','period8','period9','period10','period11'];

    public function renter()
    {
        return $this->belongsTo('App\Models\Renter');
    }

    //выборка всех действующих арендаторов выставки
    public static function GetActiveRenters($letter){
        $renters=DB::select("select id,title,area from renters where status=1 and
                                place_id in (select id from places where name like '%$letter%')
                                order by cast(area as unsigned) ASC ");
        return $renters;
    }

    //сохраняем данные по присутствию арендаторов на выставке в базу
    public function SaveData($alltime,$notime,$periods)
    {
        $fields = '';
        $val = '';
        if ($alltime) {
            $fields = ',period1,period2,period3,period4,period5,period6,period7,period8,period9,period10,period11';
            $val = ',1,1,1,1,1,1,1,1,1,1,1';
        } elseif ($notime) {
            $fields = ',period1,period2,period3,period4,period5,period6,period7,period8,period9,period10,period11';
            $val = ',0,0,0,0,0,0,0,0,0,0,0';
        } else {
            foreach ($periods as $period) {
                switch ($period) //определяем период
                {
                    case '10':
                        $fields .= ',period1'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '11':
                        $fields .= ',period2'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '12':
                        $fields .= ',period3'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '13':
                        $fields .= ',period4'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '14':
                        $fields .= ',period5'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '15':
                        $fields .= ',period6'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '16':
                        $fields .= ',period7'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '17':
                        $fields .= ',period8'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '18':
                        $fields .= ',period9'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '19':
                        $fields .= ',period10'; //период
                        $val .= ',1'; //значение периода
                        break;
                    case '20':
                        $fields .= ',period11'; //период
                        $val .= ',1'; //значение периода
                        break;
                }
            }
        }
        $created_at = date('Y-m-d H:i:s');
        //удаляем данные, если они есть
        $dbl = Rentlog::where(['renter_id' => $this->renter_id, 'data' => $this->data])->first();
        if (!empty($dbl))
            $dbl->delete();
        //добавляем новые данные
        DB::insert("insert into rentlogs(renter_id,`data` $fields,created_at) values($this->renter_id,'$this->data'$val,'$created_at')");
        $insert_id = $this->id;
        if (isset($insert_id))
            return true;
        else
            return false;
    }

    //выборка в отчет для одного арендатора
    public static function OneRenterReport($id,$s,$f){
        //цикл по датам и периодам
        $logs = Rentlog::select(['data', 'period1', 'period2', 'period3', 'period4', 'period5', 'period6', 'period7', 'period8', 'period9', 'period10', 'period11'])
            ->where(['renter_id'=>$id])->whereBetween('data', [$s, $f])->orderBy('data', 'asc')->get();
        //return print_r($logs);
        $content='<table class="table table-hover table-striped">
            <tr><th>Дата\Период</th><th>10-11</th><th>11-12</th><th>12-13</th><th>13-14</th><th>14-15</th><th>15-16</th><th>16-17</th><th>17-18</th><th>18-19</th>
                <th>19-20</th><th>20-21</th>
            </tr>';
        foreach($logs as $log){
            $content .= '<tr><td>' . $log->data . '</td>';
            for ($j = 1; $j < 12; $j++) {
                $period = 'period' . $j;
                if ($log->$period == 1)
                    $content .= '<td class="success"><span class="fa fa-check"></span></td>';
                else
                    $content .= '<td class="danger"><span class="fa fa-times"></span></td>';
            }
            $content .= '</tr>';
        }
        $content.='</table>';
        return $content;
    }

    //выборка в отчет для нескольких арендаторов
    public static function RentersReport($renters,$s,$f){
        $content='<table class="table table-hover table-striped"><tbody><tr>';
        $content.='<th>№ участка</th><th>Название компании</th><th>Кол-во часов</th><th>Кол-во дней</th><th>В среднем часов в день</th></tr>';
        foreach($renters as $renter){
            //группировка по датам и периодам
            $query="SELECT renters.name, renters.area, Sum(period1)+Sum(period2)+Sum(period3)+Sum(period4)+Sum(period5)+Sum(period6)+Sum(period7)+Sum(period8)+Sum(period9)+Sum(period10)+Sum(period11) AS alltime,";
            $query.="count(rentlogs.data) AS alldata FROM rentlogs INNER JOIN renters ON renters.id = rentlogs.renter_id";
            $query.=" WHERE renter_id=". $renter ." AND rentlogs.`data` BETWEEN '".$s."' AND '".$f."'";
            $query.=" GROUP BY renters.name, renters.area ORDER BY renters.area+0";
            $result = DB::select($query);
            if(count($result)==0)
                continue;
            $content.='<tr><td>'.$result[0]->area.'</td><td>'.$result[0]->name.'</td>';
            $content.='<td>'.$result[0]->alltime.'</td><td>'.$result[0]->alldata.'</td>';
            if($result[0]->alldata > 0)
                $avg=round($result[0]->alltime/$result[0]->alldata,2);
            else
                $avg = 0;
            if($avg<9)
                $content.='<td class="danger">'.$avg.'</td></tr>';
            else
                $content.='<td class="success">'.$avg.'</td></tr>';
        }
        $content.='</table>';
        return $content;
    }
}
