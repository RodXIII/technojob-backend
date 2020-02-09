<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
  protected $fillable = [
    'job_name', 'salary', 'requirements', 'job_description', 'company_id',
  ];


  /**
   * Relations
   */
  public function company()
  {
    return $this->belongsTo('App\Company','company_id','id');  // take 'id' of companies table
  }
  // public function workers()
  // {
  //   return $this->hasMany('App\Job_Worker');  //foreign key needed 'job_id' 
  // }
  public function workers()
  {
    return $this->belongsToMany('App\Worker', 'job_workers')
    ->withPivot('worker_id', 'status');
  }
}
