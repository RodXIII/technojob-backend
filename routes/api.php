<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['cors']], function () {

  // Cities
  Route::get('/cities', 'CityController@getCities');
  Route::get('/cities/{id}', 'CityController@getCityById')->where(['id' => '[0-9]+']);
  Route::get('/cities/{name}', 'CityController@getCityByName');

  // Skills
  Route::get('/skills', 'SkillController@getSkills');
  Route::get('/skills/{id}', 'SkillController@getSkillById')->where(['id' => '[0-9]+']);
  Route::get('/skills/{name}', 'SkillController@getSkillByName');

  // Access
  Route::post('/access/register/{usertype}', 'AccessController@register');
  Route::post('/access/login/{usertype}', 'AccessController@login');
  Route::patch('/access/logout/{usertype}', 'AccessController@logout')->middleware('token');

  // Profiles
  Route::get('/profiles/{usertype}', 'ProfileController@getAll')->middleware('token');
  Route::get('/myprofile', 'ProfileController@getMyProfile')->middleware('token');
  Route::get('/profile/{usertype}/{id}', 'ProfileController@getProfile')->middleware('token');
  Route::patch('/myprofile/update', 'ProfileController@update')->middleware('token');
  Route::patch('/myprofile/pass', 'ProfileController@pass')->middleware('token');

  // Jobs
  Route::get('/jobs/{limit?}', 'JobController@getJobs');
  Route::get('/jobs/{limit}/{city}/{type}', 'JobController@getFilteredJobs');
  Route::post('/jobs/add', 'JobController@createJob')->middleware('token');
  Route::patch('/jobs/final/{jobId}', 'JobController@finalizeJob')->middleware('token');
  Route::delete('/jobs/remove/{jobId}', 'JobController@deleteJob')->middleware('token');

});
