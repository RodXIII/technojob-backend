<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
  protected $fillable = [
    'name', 'surname', 'email', 'password', 'token', 'about', 'education', 'skills', 'experience', 'url_img', 'city_id',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
  ];

  /**
   * Relations
   */
  public function cities()
  {
    return $this->hasOne('App\City');  // take 'id' of cities table
  }
  // public function jobs()
  // {
  //   return $this->hasMany('App\Job_Worker');  //foreign key needed 'worker_id' 
  // }
  public function jobs()
  {
    return $this->belongsToMany('App\Job', 'job_worker')
    ->withPivot('job_id', 'status');
  }
}

