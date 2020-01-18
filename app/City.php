<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model {

  public $timestamps = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'name',
  ];

  /**
   * Relations
   */
  public function workers()
  {
      return $this->hasMany('App\Worker');  // we need 'city_id' as fk in 'Worker' model
  }

  public function companies()
  {
      return $this->hasMany('App\Company');  // we need 'company_id' as fk in 'Company' model
  }
}
