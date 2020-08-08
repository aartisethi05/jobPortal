<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'provider_id', 'company', 'job_title','skills','description','salary_range','location','experience','education','stream','created_date'
    ];
    public $timestamps = false;

}
