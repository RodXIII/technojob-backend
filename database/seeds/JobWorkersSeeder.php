<?php

use Illuminate\Database\Seeder;
use App\JobWorker;

class JobWorkersSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('job_workers')->delete();
    $json = File::get("database/data/inscription.json");
    $data = json_decode($json);

    foreach ($data as $obj) {
      JobWorker::create(array(
        'job_id' => $obj->job_id,
        'worker_id' => $obj->worker_id,
      ));
    }
  }
}
