<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Farmer routes
    Route::prefix('farmer')->group(function () {
        Route::get('/profile', [FarmerController::class, 'profile']);
        Route::put('/profile', [FarmerController::class, 'updateProfile']);
        Route::get('/farms', [FarmerController::class, 'farms']);
        Route::post('/farms', [FarmerController::class, 'createFarm']);
        Route::put('/farms/{farm}', [FarmerController::class, 'updateFarm']);
        Route::delete('/farms/{farm}', [FarmerController::class, 'deleteFarm']);
        Route::get('/orders', [FarmerController::class, 'orders']);
        Route::get('/orders/{order}', [FarmerController::class, 'showOrder']);
        Route::post('/jobs/{jobId}/confirm', [FarmerController::class, 'confirmService']);
        Route::get('/jobs', [FarmerController::class, 'getJobs']);
        Route::put('/jobs/{jobId}/miscarriage', [FarmerController::class, 'markMiscarriage']);
        Route::put('/jobs/{jobId}/delivery', [FarmerController::class, 'markDelivery']);
    });
    
    // Technician routes
    Route::prefix('technician')->group(function () {
        Route::get('/profile', [TechnicianController::class, 'profile']);
        Route::put('/profile', [TechnicianController::class, 'updateProfile']);
        Route::get('/jobs', [TechnicianController::class, 'jobs']);
        Route::get('/jobs/{job}', [TechnicianController::class, 'showJob']);
        Route::put('/jobs/{job}/accept', [TechnicianController::class, 'acceptJob']);
        Route::put('/jobs/{job}/reject', [TechnicianController::class, 'rejectJob']);
        Route::put('/jobs/{job}/complete', [TechnicianController::class, 'completeJob']);
        Route::put('/availability', [TechnicianController::class, 'updateAvailability']);
        Route::get('/earnings', [TechnicianController::class, 'earnings']);
        Route::get('/inventory', [TechnicianController::class, 'inventory']);
        Route::post('/upload-document', [TechnicianController::class, 'uploadDocument']);
        Route::delete('/delete-document', [TechnicianController::class, 'deleteDocument']);
    });
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/farmers', [AdminController::class, 'farmers']);
        Route::put('/farmers/{farmer}/status', [AdminController::class, 'updateFarmerStatus']);
        Route::get('/technicians', [AdminController::class, 'technicians']);
        Route::put('/technicians/{technician}/status', [AdminController::class, 'updateTechnicianStatus']);
        Route::put('/technicians/{technician}/approve', [AdminController::class, 'approveTechnician']);
        Route::get('/orders', [AdminController::class, 'orders']);
        Route::get('/jobs', [AdminController::class, 'jobs']);
        Route::post('/jobs/assign', [AdminController::class, 'assignJob']);
        Route::put('/jobs/{job}/status', [AdminController::class, 'updateJobStatus']);
        Route::get('/analytics', [AdminController::class, 'analytics']);
        Route::get('/breeds', [AdminController::class, 'breeds']);
        Route::get('/semen-products', [AdminController::class, 'semenProducts']);
        Route::post('/semen-products', [AdminController::class, 'storeSemenProduct']);
        Route::delete('/semen-products/{semenProduct}', [AdminController::class, 'destroySemenProduct']);
        Route::get('/technicians-for-stock', [AdminController::class, 'getTechniciansForStock']);
        Route::post('/assign-stock', [AdminController::class, 'assignStock']);
        Route::get('/earnings', [AdminController::class, 'earnings']);
        Route::get('/performance', [AdminController::class, 'performance']);
        Route::get('/stock', [AdminController::class, 'stock']);
    });
    
    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });
    
    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('/breeds', [ProductController::class, 'breeds']);
        Route::get('/semen', [ProductController::class, 'semen']);
        Route::get('/semen/{semen}', [ProductController::class, 'showSemen']);
    });
    
    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });
    
    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/{payment}', [PaymentController::class, 'show']);
        Route::post('/', [PaymentController::class, 'create']);
        Route::put('/{payment}/status', [PaymentController::class, 'updateStatus']);
        Route::get('/analytics', [PaymentController::class, 'analytics']);
    });
    
    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::get('/{review}', [ReviewController::class, 'show']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('/{review}', [ReviewController::class, 'update']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
        Route::get('/technician/{technician}/stats', [ReviewController::class, 'technicianStats']);
    });
    
    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/conversations', [MessageController::class, 'conversations']);
        Route::post('/', [MessageController::class, 'store']);
        Route::put('/{message}/read', [MessageController::class, 'markAsRead']);
        Route::put('/mark-conversation-read', [MessageController::class, 'markConversationAsRead']);
        Route::get('/unread-count', [MessageController::class, 'unreadCount']);
        Route::delete('/{message}', [MessageController::class, 'destroy']);
    });
});
