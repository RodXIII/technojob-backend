<?php

namespace App\Http\Controllers;

use App\Worker;
use App\Company;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{

  public function register(Request $request, $usertype)
  {

    try {

      // Creamos las reglas de validación
      $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        return \Response::json([
          'created' => false,
          'errors' => $validator->errors()->all(),
        ], 400); // 400 - bad request
      }
      $password = $request->input('password');
      $hashed = Hash::make($password, ['rounds' => 10]);

      $hashedReq = $request->all();
      $hashedReq['password'] = $hashed;

      if ($usertype === 'worker') {
        $user = Worker::create($hashedReq);
      } else if ($usertype === 'company') {
        $user = Company::create($hashedReq);
      }

      return ($user) ? $user : \Response::json([
        'created' => false,
        'error' => 'usertype invalid',
      ], 400);  // 400 - bad request

    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'error' => 'email duplicated',
      ], 500);  // 500 - query error
    }
  }

  public function login(Request $request, $usertype)
  {
    try {

      // Creamos las reglas de validación
      $rules = [
        'email' => 'required|email',
        'password' => 'required',
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        return \Response::json([
          'logged' => false,
          'errors' => $validator->errors()->all(),
        ], 400); // 400 - bad request
      }

      $email = $request->input('email');
      $password = $request->input('password');


      if ($usertype === 'worker') {
        $user = Worker::where('email', '=', $email)->first();
      } else if ($usertype == 'company') {
        $user = Company::where('email', '=', $email)->first();
      }

      if (Hash::check($password, $user['password'])) {
        echo 'checked';
        
        return ($user) ? $user : \Response::json([
          'logged' => false,
          'error' => 'error',
        ], 400);  // 400 - bad request
      }

    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'error' => 'catch error',
      ], 500);  // 500 - query error
    }
  }
}
