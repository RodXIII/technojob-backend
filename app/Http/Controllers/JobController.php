<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Support\Facades\DB;

class JobController extends Controller {
  /**
   * responds with all jobs order DESc.
   *
   * accept limit filter
   * -----------------------------------------------*/
  public function getJobs($limit = 500) {

    // return Job::all();
    return Job::
      orderBy('created_at', 'DESC')
      ->limit($limit)
      ->get();
  }

  /**
   * responds with jobs order DESc.
   *
   * accept limit, city and type filter
   * -----------------------------------------------*/
  public function getFilteredJobs($limit, $city, $type) {
    echo "$limit  $city  $type";

    $filter = "";

    if ($city != "0") {
      $filter = "AND companies.city_id = $city ";
    }

    if ($type != "0") {
      $filter = $filter . "AND jobs.job_name LIKE ('%$type%') ";
    }

    if ($limit != "0") {
      $filter = $filter . "LIMIT $limit";
    }

    echo "CONSULTA:  $filter";

    //SELECT * FROM Orders WHERE OrderID = ?, OpenDate = ?, Status = ?
    $jobs = DB::select("SELECT jobs.*, companies.id as company_id, companies.city_id
                          FROM jobs, companies
                         WHERE jobs.company_id = companies.id
                            $filter", [1]);

    return $jobs;
  }
}

// removeMe
//AND  companies.city_id = 13", [1]);
// $jobs = DB::select("SELECT *, companies-city_id FROM jobs, companies WHERE $filter", [1]);
// $jobs = DB::select('SELECT * FROM workers WHERE id = ? AND name = ?', [1, 'camey']);
// $jobs = DB::select('SELECT * FROM workers WHERE id = :id AND name = :name', ['id' => 1, 'name' => 'camey']);
//select *, companies.city_id from jobs, companies where jobs.company_id = companies.id and companies.city_id = 13