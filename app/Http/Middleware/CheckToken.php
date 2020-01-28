<?php

namespace App\Http\Middleware;

use Closure;
use App\Worker;
use App\Company;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class CheckToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
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

      if ($user['email'] !== $decode->data->email) {
        return \Response::json([
          'msg' => 'token no valido'
        ], 400); // 400 - bad request
      }

      return $next($request);
      
    } catch (\Firebase\JWT\ExpiredException $e) {
      echo 'Exception message '.$e ;
      return \Response::json([
        'msg' => 'token no valido'
      ], 500);  // 500 - query error
    }
  }
}
