<?php

namespace App\Http\Middleware;

use App\Company;
use App\Worker;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;

class CheckToken {
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next) {
    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];

      if (empty($token)) {
        return \Response::json([
          'message' => '.. empty token ..',
        ], 400); // 400 - bad request
      }
      $decode = JWT::decode($token, "misecretito", array('HS256'));

      $usertypeToken = $decode->data->usertype;

      if ($usertypeToken === 'worker') {
        $user = Worker::where('token', '=', $token)->first();
      } else if ($usertypeToken === 'company') {
        $user = Company::where('token', '=', $token)->first();
      }

      if ($user['email'] != $decode->data->email) {
        return \Response::json([
          'message' => '.. invalid token ..',
        ], 400); // 400 - bad request
      }

      return $next($request);

    } catch (\Firebase\JWT\Exception $e) {
      echo 'Exception message ' . $e;
      return \Response::json([
        'msg' => 'JWT error',
      ], 500); // 500 - query error
    }
  }
}
