<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Category::factory(10)->create();
        \App\Models\Quiz::factory(10)->create();
        \App\Models\User::factory(10)->create();
        \App\Models\Question::factory(30)->create();
        $this->call(UserSeeder::class);
    }
}
