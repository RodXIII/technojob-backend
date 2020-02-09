<?php

use Illuminate\Database\Seeder;
use App\Company;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('companies')->delete();
      $json = File::get("database/data/company.json");
      $data = json_decode($json);
  
      foreach ($data as $obj) {
        Company::create(array(
          'name' => $obj->name,
          'cif' => $obj->cif,
          'email' => $obj->email,
          'password' => $obj->password,
          'city_id' => $obj->city_id,
          'sector' => $obj->sector,
          'description' => $obj->description,
        ));
      }
    }
}
