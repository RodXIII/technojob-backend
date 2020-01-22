<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobWorker extends Model
{
  protected $fillable = [
    'job_id', 'worker_id', 'status',
  ];
}
