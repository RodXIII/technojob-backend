<?php

use Illuminate\Database\Seeder;
use App\Job;

class JobsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('jobs')->delete();
    $json = File::get("database/data/job.json");
    $data = json_decode($json);

    foreach ($data as $obj) {
      Job::create(array(
        'job_name' => $obj->job_name,
        'job_description' => $obj->job_description,
        'salary' => $obj->salary,
        'company_id' => $obj->company_id,
      ));
    }
  }
}
