<?php

use Illuminate\Database\Seeder;
use App\City;

class CitiesSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {

    DB::table('cities')->delete();
    $json = File::get("database/data/city.json");
    $data = json_decode($json);
    foreach ($data as $obj) {
      City::create(array(
        'name' => $obj->name,
      ));
    }
  }
}
