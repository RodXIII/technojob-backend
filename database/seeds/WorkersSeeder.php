<?php

use Illuminate\Database\Seeder;
use App\Worker;

class WorkersSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('workers')->delete();
    $json = File::get("database/data/worker.json");
    $data = json_decode($json);

    foreach ($data as $obj) {
      Worker::create(array(
        'name' => $obj->name,
        'surname' => $obj->surname,
        'email' => $obj->email,
        'password' => $obj->password,
        'city_id' => $obj->city_id,

      ));
    }
  }
}
