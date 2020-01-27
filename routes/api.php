<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['cors']], function () {

  /*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

  // Route::middleware('auth:api')->get('/user', function (Request $request) {
  //     return $request->user();
  // });

  // Cities
  Route::get('/cities', 'CityController@getCities');
  Route::get('/cities/{id}', 'CityController@getCityById')->where(['id' => '[0-9]+']);
  Route::get('/cities/{name}', 'CityController@getCityByName');

  // Skills
  Route::get('/skills', 'SkillController@getSkills');
  Route::get('/skills/{id}', 'SkillController@getSkillById')->where(['id' => '[0-9]+']);
  Route::get('/skills/{name}', 'SkillController@getSkillByName');

  // Acces
  Route::post('/access/register/{usertype}', 'AccessController@register');
  Route::post('/access/login/{usertype}', 'AccessController@login');


  
});
