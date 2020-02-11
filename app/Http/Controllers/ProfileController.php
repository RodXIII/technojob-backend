<?php

namespace App\Http\Controllers;

use App\Company;
use App\Worker;
use Firebase\JWT\JWT;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        'message' => '.. no token ..',
      ], 400); // 400 - bad request
    }
    $decode = JWT::decode($token, "misecretito", array('HS256'));

    $usertype = $decode->data->usertype;

    if ($usertype === 'worker') {
      $user = Worker::with('jobs.company')->where('token', '=', $token)->first();
    } else if ($usertype === 'company') {
      $user = Company::with('jobs.workers')->where('token', '=', $token)->first();
    }

    return ($user) ? $user : \Response::json([
      'logged' => false,
      'message' => '.. error ..',
    ], 400); // 400 - bad request

  }

  public function getProfile($usertype, $id)
  {

    if ($usertype === 'worker') {
      $user = Worker::with('jobs')->where('id', '=', $id)->first();
    } else if ($usertype === 'company') {
      $user = Company::with('jobs')->where('id', '=', $id)->first();
    }

    return ($user) ? $user : \Response::json([
      'logged' => false,
      'message' => '.. error ..',
    ], 400); // 400 - bad request

  }

  public function update(Request $request)
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

      if ($usertype === 'worker') {
        $user = Worker::where('token', '=', $token)->first();
      } else if ($usertype === 'company') {
        $user = Company::where('token', '=', $token)->first();
      }

      $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $token,
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        return \Response::json([
          'created' => false,
          'message' => $validator->errors()->all(),
        ], 400); // 400 - bad request
      }

      $input = $request->all();
      $user->fill($input)->save();

      $image = $request->file('url_img');
      $imgId = $user['id'];
      $image_name = "$usertype-$imgId";
      if ($image) {
        //Guardamos en la carpeta storage(storage/app/users)
        Storage::disk('users')->put($image_name, File::get($image));
        //seteo el nombre de la imagen en el objeto
        $user['url_img'] = $image_name;
        $user->update();
      }

      return \Response::json([
        'user' => $user,
        'message' => '.. profile modified ..',
      ], 200); // 200 - request
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }

  public function setImage(Request $request)
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

      if ($usertype === 'worker') {
        $user = Worker::where('token', '=', $token)->first();
      } else if ($usertype === 'company') {
        $user = Company::where('token', '=', $token)->first();
      }

      $image = $request->file('image');
      $imgId = $user['id'];
      $image_name = "$usertype-$imgId";
      if ($image) {
        //Guardamos en la carpeta storage(storage/app/users)
        Storage::disk('users')->put($image_name, File::get($image));
        //seteo el nombre de la imagen en el objeto
        $user['url_img'] = $image_name;
        $user->update();
      }

      return \Response::json([
        'user' => $user,
        'message' => '.. image modified ..',
      ], 200); // 200 - request
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }

  public function getImage()
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

      if ($usertype === 'worker') {
        $user = Worker::where('token', '=', $token)->first();
      } else if ($usertype === 'company') {
        $user = Company::where('token', '=', $token)->first();
      }

      $image_name = $user['url_img'];
      $file = Storage::disk('users')->get($image_name);

      return new Response($file, 200);
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }

  public function pass(Request $request)
  {
    try {
      $token = $_SERVER['HTTP_AUTHORIZATION'];

      if (empty($token)) {
        return \Response::json([
          'msg' => '.. no token ..',
        ], 400); // 400 - bad request
      }
      $decode = JWT::decode($token, "misecretito", array('HS256'));

      $usertype = $decode->data->usertype;

      if ($usertype === 'worker') {
        $user = Worker::where('token', '=', $token)->first();
      } else if ($usertype === 'company') {
        $user = Company::where('token', '=', $token)->first();
      }

      $rules = [
        'password' => 'required|string|max:255',
        'newPassword' => 'required|string|max:255',
      ];

      // Ejecutamos el validador, en caso de que falle devolvemos la respuesta
      $validator = \Validator::make($request->all(), $rules);
      if ($validator->fails()) {

        return \Response::json([
          'message' => $validator->errors()->all()
        ], 400); // 400 - bad request
      }

      $password = $request->input('password');
      $newPassword = $request->input('newPassword');

      if (Hash::check($password, $user['password'])) {

        $hashed = Hash::make($newPassword, ['rounds' => 10]);
        $user['password'] = $hashed;
        $user->save();

        return \Response::json([
          'user' => $user,
          'message' => '.. change successful ..'
        ], 200); // 200 - change successful
      }

      return \Response::json([
        'message' => '.. change fail ..',
      ], 400); // 400 - bad request

    } catch (QueryException $e) {

      return \Response::json([
        'message' => '.. DB error ..',
      ], 500); // 500 - query error
    }
  }

  public function searchWorker(Request $request)
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

      $type = $request->input('input');
      $city = $request->input('city');

      $workers = Worker::when($city, function ($query, $city) {
        $query->where('city_id', $city);
      })
        ->when($type, function ($query, $type) {     //"->when()" is used to allow null values on search
          $query->where(function ($q) use ($type) {
            // Nested OR condition
            $q->where('about', 'LIKE', '%' . $type . '%')
              ->orWhere('education', 'LIKE', '%' . $type . '%')
              ->orWhere('skills', 'LIKE', '%' . $type . '%')
              ->orWhere('experience', 'LIKE', '%' . $type . '%');
          });
        })
        ->orderBy('created_at', 'DESC')
        ->get();

      return $workers;
    } catch (QueryException $e) {

      return \Response::json([
        'created' => false,
        'message' => '.. no workers ..' . $e,
      ], 500); // 500 - query error
    }
  }
}
