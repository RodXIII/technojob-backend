<?php

namespace App\Http\Controllers;
use App\City;

class CityController extends Controller {

  /**
   * responds with all cities.
   * -----------------------------------------------*/
  public function getCities() {

    return City::all();
  }

  /**
   * responds with one city filter by id.
   * -----------------------------------------------*/
  public function getCityById($id) {

    return City::find($id);
  }

  /**
   * responds with one city filter by name.
   * -----------------------------------------------*/
  public function getCityByName($name) {

    return City::where('name', 'like', $name)->get();
  }

}