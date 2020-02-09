<?php

namespace App\Http\Controllers;

use App\Job;
use App\JobWorker;
use App\Worker;
use Firebase\JWT\JWT;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller {
  /**
   * responds with all jobs order DESc.
   *
   * accept limit filter
   * -----------------------------------------------*/
  public function getJobs($limit = 500) {

    // return Job::all();
    return Job::with('workers')->orderBy('created_at', 'DESC')
      ->limit($limit)
      ->get();
  }

  /**
   * responds with all jobs order TOP (number of workers).
   *
   * accept limit filter
   * -----------------------------------------------*/
  public function getTopJobs($limit = 500) {
    // return Job::all();
    $jobs = Job::with('workers', 'company')
      ->withCount('workers')
      ->orderBy('workers_count', 'desc')
      ->limit($limit)
      ->get();

    return $jobs;
  }

  /**
   * responds with jobs order DESc.
   *
   * accept limit, city and type filter
   * -----------------------------------------------*/
  public function getFilteredJobs($limit, $city, $type) //TODO
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

  public function searchJob(Request $request) {

    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];

      if (empty($token)) {
        return \Response::json([
          'message' => '.. no token ..',
        ], 400); // 400 - bad request
      }
      $decode = JWT::decode($token, "misecretito", array('HS256'));

      $usertype = $decode->data->usertype;

      if ($usertype != 'worker') {
        return \Response::json([
          'message' => '.. usertype invalid ..',
        ], 400); // 400 - bad request
      }

      $type = $request->input('input');
      $city = $request->input('city');

      $jobs = Job::with('company')
        ->when($city, function ($query, $city) {
          $query->whereHas('company', function ($query) use ($city) {
            $query->where('city_id', $city);
          });
        })
        ->where(function ($q) use ($type) {
          // Nested OR condition
          $q->where('job_name', 'LIKE', '%' . $type . '%')
            ->orWhere('requirements', 'LIKE', '%' . $type . '%')
            ->orWhere('job_description', 'LIKE', '%' . $type . '%')
            ->orWhereHas('company', function ($query) use ($type) {
              $query->where('name', 'LIKE', '%' . $type . '%');
            });
        })
        ->withCount('workers')
        ->orderBy('created_at', 'DESC')
        ->get();

      return $jobs;

    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. no jobs ..' . $e,
      ], 500); // 500 - query error
    }
  }

  /**
   * add and responds job created
   *
   * -----------------------------------------------*/
  public function createJob(Request $request) {

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
        'job_description' => 'required',
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

  /**
   * set job.active value to false
   *
   * set workers status subscribed (not accepted) to 0
   * -----------------------------------------------*/
  public function finalizeJob($jobId) {

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

  /**
   * worker subscribes to a job
   *
   * -----------------------------------------------*/
  public function subscribe($jobId) {

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

      if ($usertype != 'worker') {
        return \Response::json([
          'message' => '.. usertype invalid ..',
        ], 400); // 400 - bad request
      }

      $checkWorker = JobWorker::where(['job_id' => $jobId, 'worker_id' => $id])->first();

      if ($checkWorker) {
        return \Response::json([
          'message' => ".. you can't subscribe two times ..",
        ], 400); // 400 - bad request
      }
      $subscription = JobWorker::create(['job_id' => $jobId, 'worker_id' => $id]);
      $subscription->save();

      return \Response::json([
        'finalized' => true,
        'message' => '.. subscribed ..',
      ], 200);
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. subscription not done..' . $e,
      ], 500); // 500 - query error
    }
  }

  /**
   * delete job and its subscriptors
   *
   * -----------------------------------------------*/
  public function deleteJob($jobId) {

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

      $jobworkers = JobWorker::where('job_id', '=', $jobId)->get();
      foreach ($jobworkers as &$jobworker) {

        $jobworker->delete();
      }

      $job->delete();

      return \Response::json([
        'finalized' => true,
        'message' => '.. job offer deleted ..',
      ], 200);
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. job no found..' . $e,
      ], 500); // 500 - query error
    }
  }

  /**
   * modify job-worker status
   *
   * set workers status subscribed (not accepted) to 0
   * -----------------------------------------------*/
  public function editStatus(Request $request,$jobId) {

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

      $status = $request->input('status');
      $workerId = $request->input('workerId');

      $job->workers()->updateExistingPivot($workerId,['status' => $status]);

      return \Response::json([
        'finalized' => true,
        'message' => '.. job offer modified ..',
      ], 200);
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. job no found..' . $e,
      ], 500); // 500 - query error
    }
  }
}

