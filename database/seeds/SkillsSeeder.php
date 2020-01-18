<?php

use Illuminate\Database\Seeder;
use App\Skill;

class SkillsSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {

    DB::table('skills')->delete();
    $json = File::get("database/data/skill.json");
    $data = json_decode($json);

    foreach ($data as $obj) {
      Skill::create(array(
        'skill' => $obj->skill,
      ));
    }
  }
}
