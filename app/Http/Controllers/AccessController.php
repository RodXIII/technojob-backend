<?php

namespace App\Http\Controllers;

use App\Company;
use App\Worker;
use Firebase\JWT\JWT;
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

        $error = $validator->errors()->all();
        return \Response::json([
          'created' => false,
          'message' => $error[0],
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
        'message' => '.. usertype invalid ..',
      ], 400); // 400 - bad request

    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. email duplicated .. ',
      ], 500); // 500 - query error
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
          'message' => $validator->errors()->all(),
        ], 400); // 400 - bad request
      }

      $email = $request->input('email');
      $password = $request->input('password');

      if ($usertype === 'worker') {
        $user = Worker::where('email', '=', $email)->first();
      } else if ($usertype === 'company') {
        $user = Company::where('email', '=', $email)->first();
      }

      if (Hash::check($password, $user['password'])) {

        $time = time();
        $key = 'misecretito';
        $token = array(
          'iat' => $time, // Tiempo que inició el token
          'exp' => $time + (60 * 60 * 72), // Tiempo que expirará el token
          'data' => [ // información del usuario
            'id' => $user['id'],
            'email' => $user['email'],
            'usertype' => $usertype,
          ],
        );
        $user['token'] = JWT::encode($token, $key);
        $user->save();

        return \Response::json([
          'logged' => true,
          'message' => '.. login successful ..',
          'user' => $user
        ], 200);
      } else {

        return \Response::json([
          'logged' => false,
          'message' => '.. login failed ..',
        ], 400); // 400 - bad request
      }
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }

  public function logout(Request $request, $usertype)
  {
    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];
      $decode = JWT::decode($token, "misecretito", array('HS256'));
      $email = $decode->data->email;

      if ($usertype === 'worker') {
        $user = Worker::where('email', '=', $email)->get();
      } else if ($usertype === 'company') {
        $user = Company::where('email', '=', $email)->get();
      }

      $userEmail = $request->input('email');

      if ($email == $userEmail) {
        $user[0]->token = null;
        $user[0]->save();

        return \Response::json([
          'message' => '.. logout successful ..'
        ], 200);
      }

      return \Response::json([
        'logged' => false,
        'message' => '.. logout failed ..',
      ], 400); // 400 - bad request

    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }
}
