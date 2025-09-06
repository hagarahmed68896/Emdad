<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class TaqnyatOTP
{

    protected $sender;
    protected $bearerToken;

    public function __construct()
    {
        $this->sender = config('services.taqnyat.sender');
        $this->bearerToken = config('services.taqnyat.bearer_token');
    }


    public function sendOTP($phone, $otp)
    {
        $phone = trim($phone);
        $phone = ltrim($phone, '+');
      // ✅ Add this line to log the phone number and OTP
        Log::info("Sending OTP to phone number: {$phone} with OTP: {$otp}");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
            'Content-Type' => 'application/json',
        ])    ->withoutVerifying() // ✅ Add this line
              ->post('https://api.taqnyat.sa/v1/messages', [
            'recipients' => [$phone],
            'body' =>
                'رمز التحقق الخاص بك: ' . $otp .
                ' لتسجيل الدخول إلى موقع امداد: ' . url('/'),
            'sender' => $this->sender,
            'priority' => 'high',
        ]);

        Log::info("SMS Response: " . $response->body());

        return $response->successful();
    }

}
