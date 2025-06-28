<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import your User model
use App\Notifications\SimpleTestNotification; // Import your Notification class

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a user to notify (e.g., the first user in the database)
        $user = User::first(); // Or User::find(1);

        if ($user) {
            // Send multiple notifications
            $user->notify(new SimpleTestNotification('مرحباً، إشعار 1 من المصنع!'));
            $user->notify(new SimpleTestNotification('تنبيه: منتج جديد متاح الآن!'));
            $user->notify(new SimpleTestNotification('تم تحديث حالة طلبك.'));

            // Example of a notification that is already read
            $notification = new SimpleTestNotification('إشعار قديم تم قراءته.');
            $user->notify($notification);
            $user->notifications()->latest()->first()->markAsRead(); // Mark the last one as read
        } else {
            echo "No users found. Please run 'php artisan db:seed --class=UserSeeder' first (if you have one).\n";
        }
    }
}