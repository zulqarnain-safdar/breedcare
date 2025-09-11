<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate a 6-digit OTP
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to mobile number
     * For now, we'll just log it. In production, integrate with SMS service
     */
    public function sendOtp(string $mobile, string $otp): bool
    {
        // TODO: Integrate with actual SMS service (Twilio, etc.)
        \Log::info("OTP for {$mobile}: {$otp}");
        
        // For development, we'll return true
        return true;
    }

    /**
     * Generate and send OTP to user
     */
    public function generateAndSendOtp(User $user): bool
    {
        $otp = $this->generateOtp();
        $expiresAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        return $this->sendOtp($user->mobile, $otp);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        if (!$user->otp || !$user->otp_expires_at) {
            return false;
        }

        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            return false;
        }

        if ($user->otp !== $otp) {
            return false;
        }

        // OTP is valid, mark user as verified and clear OTP
        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Check if user is verified
     */
    public function isUserVerified(User $user): bool
    {
        return $user->is_verified;
    }
}
