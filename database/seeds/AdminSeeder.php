<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'jair alejandro',
            'age' => 19,
            'email' => 'jairalejandro32@outlook.com',
            'password' => Hash::make('12345678'),
            'image' => "",
            'confirmed' => 1
        ]);
    }
}
