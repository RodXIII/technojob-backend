<?php

namespace App\Http\Controllers;

use App\Worker;
use App\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class AccessController extends Controller
{
  public function register(Request $request, $usertype)
  {
    $request->validate([
      'name' => 'required',
      'surname' => 'required',
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $user = Worker::create($request->all());
    return $user;
  }
}
