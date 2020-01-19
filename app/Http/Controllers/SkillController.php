<?php

namespace App\Http\Controllers;
use App\Skill;

class SkillController extends Controller {

  /**
   * responds with all Skills.
   * -----------------------------------------------*/
  public function getSkills() {

    return Skill::all();
  }

  /**
   * responds with one city filter by id.
   * -----------------------------------------------*/
  public function getSkillById($id) {

    return Skill::find($id);
  }

  /**
   * responds with list of skills that contain name value
   * ------------------------------------------------------*/
  public function getSkillByName($name) {

    return Skill::where('skill', 'like', "%$name%")->get();
  }

}