<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        // $this->call(QuickReplySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(NotificationsTableSeeder::class); 

    }
}
