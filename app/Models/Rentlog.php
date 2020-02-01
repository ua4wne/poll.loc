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
}
