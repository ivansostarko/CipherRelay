<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            DemoDataSeeder::class,
            // WordSeeder::class, // optional: run if you want to overwrite words list into config/pass_words.php
        ]);
    }
}
