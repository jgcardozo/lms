<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 50; $i++) {
            App\Models\User::create([
                'cohort_id' => mt_rand(1,5),
                'name' => $faker->name,
                'email' => $faker->safeEmail,
                'password' => bcrypt($faker->password(8,16)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
