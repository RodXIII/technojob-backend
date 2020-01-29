<?php

namespace App\Http\Controllers;

use App\Worker;
use App\Company;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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

  public function update(Request $request)
  {
    try {
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

      // $validate = $this->validate($request,[
      //   'name' => 'required|string|max:255',
      //   'email' => 'required|string|email|max:255|unique:users,email,'.$token
      // ]);
      $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $token
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        return \Response::json([
          'created' => false,
          'errors' => $validator->errors()->all(),
        ], 400); // 400 - bad request
      }

      $input = $request->all();
      $user->fill($input)->save();

      return \Response::json([
        'msg' => 'profile modified'
      ], 200); // 200 - request
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'error' => 'catch error',
      ], 500);  // 500 - query error
    }
  }
}
