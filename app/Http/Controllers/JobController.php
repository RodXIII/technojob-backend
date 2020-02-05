<?php

namespace App\Http\Controllers;

use App\Job;
use App\JobWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;

class JobController extends Controller
{
  /**
   * responds with all jobs order DESc.
   *
   * accept limit filter
   * -----------------------------------------------*/
  public function getJobs($limit = 500)
  {

    // return Job::all();
    return Job::orderBy('created_at', 'DESC')
      ->limit($limit)
      ->get();
  }

  /**
   * responds with jobs order DESc.
   *
   * accept limit, city and type filter
   * -----------------------------------------------*/
  public function getFilteredJobs($limit, $city, $type)
  {
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

  /**
   * add and responds job created
   *
   * -----------------------------------------------*/
  public function createJob(Request $request)
  {

    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];

      if (empty($token)) {
        return \Response::json([
          'message' => '.. no token ..',
        ], 400); // 400 - bad request
      }
      $decode = JWT::decode($token, "misecretito", array('HS256'));

      $usertype = $decode->data->usertype;

      if ($usertype != 'company') {
        return \Response::json([
          'message' => '.. usertype invalid ..',
        ], 400); // 400 - bad request
      }

      // Creamos las reglas de validación
      $rules = [
        'job_name' => 'required',
        'salary' => 'required',
        'description' => 'required'
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        $error = $validator->errors()->all();
        return \Response::json([
          'created' => false,
          'message' => $error[0],
        ], 400); // 400 - bad request
      }

      return Job::create($request->all());
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. no job ..' . $e,
      ], 500); // 500 - query error
    }
  }

  public function finalizeJob($jobId)
  {

    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];

      if (empty($token)) {
        return \Response::json([
          'message' => '.. no token ..',
        ], 400); // 400 - bad request
      }
      $decode = JWT::decode($token, "misecretito", array('HS256'));

      $usertype = $decode->data->usertype;
      $id = $decode->data->id;

      if ($usertype != 'company') {
        return \Response::json([
          'message' => '.. usertype invalid ..',
        ], 400); // 400 - bad request
      }

      $job = Job::find($jobId);

      if ($id != $job['company_id']) {
        return \Response::json([
          'message' => '.. unauthorized ..',
        ], 400); // 400 - bad request
      }

      $job['active'] = false;
      $job->update();

      $jobworkers = JobWorker::where('job_id', '=', $jobId)->get();
      foreach ($jobworkers as &$jobworker) {
        if ($jobworker['status'] != 2) {
          $jobworker['status'] = 0;
          $jobworker->update();
        }
      }


      return \Response::json([
        'finalized' => true,
        'message' => '.. job offer finalized ..',
      ], 200);
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. job no found..' . $e,
      ], 500); // 500 - query error
    }
  }
}

// removeMe
//AND  companies.city_id = 13", [1]);
// $jobs = DB::select("SELECT *, companies-city_id FROM jobs, companies WHERE $filter", [1]);
// $jobs = DB::select('SELECT * FROM workers WHERE id = ? AND name = ?', [1, 'camey']);
// $jobs = DB::select('SELECT * FROM workers WHERE id = :id AND name = :name', ['id' => 1, 'name' => 'camey']);
//select *, companies.city_id from jobs, companies where jobs.company_id = companies.id and companies.city_id = 13
