<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Illuminate\Support\Str;        // Import Str for UUID generation
use App\Models\User;             // Import User model if you're linking to it

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID for notification IDs
            $table->string('type');
            $table->morphs('notifiable'); // This creates notifiable_type and notifiable_id
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // ====================================================================
        // !!! WARNING: This is generally NOT RECOMMENDED for data insertion.
        // !!! Use Seeders (php artisan make:seeder NotificationSeeder) instead.
        // ====================================================================

        // Example: Insert a sample notification for a user with ID 1
        // Make sure a User with ID 1 exists, or adjust this ID.
        // And ensure 'App\Models\User' is the correct class for your User model.
        // Also, create a dummy notification type if you don't have one, e.g., 'App\Notifications\DummyNotification'

        // First, check if a user with ID 1 exists, otherwise this will fail
        $firstUser = User::first(); // Or User::find(1);

        if ($firstUser) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(), // Generate a UUID
                'type' => 'App\Notifications\SimpleTestNotification', // Replace with your actual Notification class name
                'notifiable_type' => User::class, // Class name of the model being notified
                'notifiable_id' => $firstUser->id, // ID of the user being notified
                'data' => json_encode([ // The data should be a JSON string
                    'message' => 'مرحباً! هذا إشعار تجريبي.',
                    'url' => '/profile/notifications',
                    'icon' => asset('images/default_notification_icon.png')
                ]),
                'read_at' => null, // Or now() for read
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};