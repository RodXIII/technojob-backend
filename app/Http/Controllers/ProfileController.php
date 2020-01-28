<?php

namespace App\Http\Controllers;

use App\Worker;
use App\Company;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Firebase\JWT\JWT;

class ProfileController extends Controller
{
  public function getAll($usertype)
  {
    if ($usertype === 'worker') {
      $users = Worker::all();
    } else if ($usertype === 'company') {
      $users = Company::all();
    }

    return $users;
  }

  public function getMyProfile()
  {

    $token = $_SERVER['HTTP_AUTHORIZATION'];

    if (empty($token)) {
      return \Response::json([
        'msg' => 'no hay token'
      ], 400); // 400 - bad request
    }
    $decode = JWT::decode($token, "misecretito", array('HS256'));

    $usertype = $decode->data->usertype;

    if ($usertype === 'worker') {
      $user = Worker::where('token', '=', $token)->first();
    } else if ($usertype === 'company') {
      $user = Company::where('token', '=', $token)->first();
    }

    return ($user) ? $user : \Response::json([
      'logged' => false,
      'error' => 'error',
    ], 400);  // 400 - bad request

  }

  public function getProfile($usertype, $id)
  {
    if ($usertype === 'worker') {
      $user = Worker::where('id', '=', $id)->first();
    } else if ($usertype === 'company') {
      $user = Company::where('id', '=', $id)->first();
    }

    return ($user) ? $user : \Response::json([
      'logged' => false,
      'error' => 'error',
    ], 400);  // 400 - bad request

  }
}
