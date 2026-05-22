<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
        'id' => 1,
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
    ]);

        $this->call([
        CategorySeeder::class,
        ItemsTableSeeder::class,
    ]);
    }
}
