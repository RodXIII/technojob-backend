<?php

namespace App\Http\Controllers;

use App\Worker;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AccessController extends Controller {

  public function register(Request $request, $usertype) {

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

      if ($usertype === 'worker') {
        $user = Worker::create($request->all());
      } else if ($usertype === 'company') {
        $user = Company::create($request->all());
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
}
