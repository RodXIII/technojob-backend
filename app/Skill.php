<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
  public $timestamps = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'skill', 'img',
  ];
}
