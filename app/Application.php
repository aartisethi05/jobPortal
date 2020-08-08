<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_id', 'provider_id', 'seeker_id','resume','contact','message','status'
    ];
    public $timestamps = false;

}
