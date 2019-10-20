<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create();

        $ids = [];
        $ids['01'] = '01';
        $ids['99'] = '99';
        do {
            $id = $faker->numerify("##");
            if(!isset($ids[$id])){
                $ids[$id] = $id;
            }
        } while (count($ids) < 25);

        foreach($ids AS $id) {
            $image = factory(App\Image::class)->create();

            \App\User::create([
                'name' => "User".$id,
                'email' => "user".$id."@sportal-app.com",
                'phone_number' => "9627900000".$id,
                'password' => '123123',
                'birth_date' => $faker->date("Y-m-d", "-14 years"),
                'image_id' => $image->id,
                'status' => \App\User::USERSTATUS_VERIFIED
            ]);
        }
    }
}
