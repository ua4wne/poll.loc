<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    protected $table = 'eventlogs';

    protected $fillable = ['type','user_id','text','ip'];

    public function users()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
