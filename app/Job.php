<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
  protected $fillable = [
    'job_name', 'salary', 'requeriments', 'description', 'company_id',
  ];


  /**
   * Relations
   */
  public function companies()
  {
    return $this->hasOne('App\Company');  // take 'id' of companies table
  }
  // public function workers()
  // {
  //   return $this->hasMany('App\Job_Worker');  //foreign key needed 'job_id' 
  // }
  public function workers()
  {
    return $this->belongsToMany('App\Worker', 'job_worker')
    ->withPivot('worker_id', 'status');
  }
}
