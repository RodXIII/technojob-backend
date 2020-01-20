<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name', 'cif', 'email', 'password', 'token', 'sector', 'description', 'url_img', 'city_id',
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
  public function jobs()
  {
    return $this->hasMany('App\Job');  //foreign key needed 'company_id' in jobs table
  }}
