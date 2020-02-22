<?php

namespace Modules\Marketing\Entities;

use Illuminate\Database\Eloquent\Model;

class Visitorlog extends Model
{
    //указываем имя таблицы
    protected $table = 'visitorlogs';

    protected $fillable = ['data','hours','fw','bw','counter_id'];

    public static function VisitorTable($start,$finish){
        $content = '<table class="table table-hover table-striped"><tr>
                        <th>Дата\Период</th><th>10-11</th><th>11-12</th><th>12-13</th><th>13-14</th><th>14-15</th>
                        <th>15-16</th><th>16-17</th><th>17-18</th><th>18-19</th><th>19-20</th><th>20-21</th><th>Итого за день</th></tr>';
        $dates = self::select(['data'])->distinct('data')->whereBetween('data', [$start, $finish])->orderBy('data','asc')->get();
        $sum = 0;
        foreach($dates as $date){
            $itog = 0;
            $data = ['10'=>'0','11'=>'0','12'=>'0','13'=>'0','14'=>'0','15'=>'0','16'=>'0','17'=>'0','18'=>'0','19'=>'0','20'=>'0'];
            $logs=Visit::select(['data','hours','ucount'])->where(['data'=>$date->data])->orderBy('hours','asc')->get();
            foreach($logs as $log){
                $data[$log->hours] = $log->ucount;
                $itog+=$log->ucount;
            }
            $date=explode("-", $log->data);
            $numday = date("w", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
            if($numday==0 || $numday==6)
                $content.='<tr class="warning"><td>'.$log->data.'</td>'; //это выходные
            else
                $content.='<tr><td>'.$log->data.'</td>';
            foreach($data as $val){
                $content.='<td>'.$val.'</td>';
            }
            $content.='<td>'.$itog.'</td></tr>';
            $sum+=$itog;
        }

        $content .= '</table>';
        $header = "<p>Всего посетителей: <strong>$sum</strong></p>";
        return $header.$content;
    }
}
