<?php
Illuminate\Session\SessionServiceProvider::class;



return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'امداد'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'ar'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'locales' => ['en', 'ar'],

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    //    'providers' => [
    //        Illuminate\View\ViewServiceProvider::class,
    //        Laravel\Socialite\SocialiteServiceProvider::class,
    //    ],
    //   'aliases' => Illuminate\Support\Facades\Facade::defaultAliases()->merge([ // Use defaultAliases() to include Laravel's default aliases
    //     'Session' => Illuminate\Support\Facades\Session::class, // <-- Your Session alias
    //     'Socialite' => Laravel\Socialite\Facades\Socialite::class, // <-- Your Socialite alias
    //     // 'PDF' => Barryvdh\DomPDF\Facade::class, // Example of another custom alias
    // ])->toArray(), // Don't forget toArray() if using merge

    // ... (Your existing configuration above this point) ...

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant them access to the Laravel framework.
    |
    */

    // 'providers' => [
    //     /*
    //      * Laravel Framework Service Providers...
    //      */
    //     Illuminate\Auth\AuthServiceProvider::class,
    //     Illuminate\Broadcasting\BroadcastServiceProvider::class,
    //     Illuminate\Bus\BusServiceProvider::class,
    //     Illuminate\Cache\CacheServiceProvider::class,
    //     Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    //     Illuminate\Cookie\CookieServiceProvider::class,
    //     Illuminate\Database\DatabaseServiceProvider::class,
    //     Illuminate\Encryption\EncryptionServiceProvider::class,
    //     Illuminate\Filesystem\FilesystemServiceProvider::class,
    //     Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    //     Illuminate\Hashing\HashServiceProvider::class,
    //     Illuminate\Mail\MailServiceProvider::class,
    //     Illuminate\Notifications\NotificationServiceProvider::class,
    //     Illuminate\Pagination\PaginationServiceProvider::class,
    //     Illuminate\Pipeline\PipelineServiceProvider::class,
    //     Illuminate\Queue\QueueServiceProvider::class,
    //     Illuminate\Redis\RedisServiceProvider::class,
    //     Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    //     Illuminate\Session\SessionServiceProvider::class, // This line was outside, make sure it's inside 'providers'
    //     Illuminate\Translation\TranslationServiceProvider::class,
    //     Illuminate\Validation\ValidationServiceProvider::class,
    //     Illuminate\View\ViewServiceProvider::class,

    //     /*
    //      * Package Service Providers...
    //      */
    //     Laravel\Socialite\SocialiteServiceProvider::class, // Example, if you use Socialite
    //     Maatwebsite\Excel\ExcelServiceProvider::class, // <-- ADD THIS LINE FOR LARAVEL EXCEL

    //     /*
    //      * Application Service Providers...
    //      */
    //     App\Providers\AppServiceProvider::class,
    //     App\Providers\AuthServiceProvider::class,
    //     // App\Providers\BroadcastServiceProvider::class, // Uncomment if you're using broadcasting
    //     //        App\Providers\EventServiceProvider::class,
    //     // App\Providers\RouteServiceProvider::class,

    // ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    // 'aliases' => Illuminate\Support\Facades\Facade::defaultAliases()->merge([ // This part is correct if uncommented
    //     // 'Example' => App\Facades\Example::class, // Your custom application aliases
    //     'Session' => Illuminate\Support\Facades\Session::class,
    //     'Socialite' => Laravel\Socialite\Facades\Socialite::class, // Example, if you use Socialite
    //     'Excel' => Maatwebsite\Excel\Facades\Excel::class, // <-- ADD THIS LINE FOR LARAVEL EXCEL
    // ])->toArray(), // Don't forget toArray()

// 'aliases' => [
//     // ...
//     'LaravelMpdf' => Meneses\LaravelMpdf\Facades\LaravelMpdf::class,
// ],

   ];
