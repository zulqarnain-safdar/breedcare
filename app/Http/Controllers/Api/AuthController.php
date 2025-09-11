<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Technician;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:farmer,technician',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Create role-specific profile
        if ($request->role === 'farmer') {
            Farmer::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'mobile' => $request->mobile,
            ]);
        } elseif ($request->role === 'technician') {
            Technician::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'mobile_1' => $request->mobile,
                'status' => 'incomplete',
            ]);
        }

        // Generate and send OTP
        $this->otpService->generateAndSendOtp($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify your mobile number with the OTP sent.',
            'user' => $user,
            'requires_verification' => true,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($this->otpService->verifyOtp($user, $request->otp)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Mobile number verified successfully',
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP'
        ], 400);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($this->otpService->generateAndSendOtp($user)) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP'
        ], 500);
    }
}