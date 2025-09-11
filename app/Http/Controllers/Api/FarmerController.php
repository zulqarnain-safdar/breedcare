<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\CattleFarm;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BullSemen;
use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmerController extends Controller
{
    public function profile(Request $request)
    {
        $farmer = Farmer::with('user', 'cattleFarms')->where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'farmer' => $farmer
        ]);
    }

    public function updateProfile(Request $request)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'profile_picture' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farmer->update($request->only(['name', 'date_of_birth', 'profile_picture']));
        $request->user()->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'farmer' => $farmer->fresh()
        ]);
    }

    public function farms(Request $request)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $farms = $farmer->cattleFarms;

        return response()->json([
            'success' => true,
            'farms' => $farms
        ]);
    }

    public function createFarm(Request $request)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'picture' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'number_of_cows' => 'required|integer|min:0',
            'number_of_calves' => 'required|integer|min:0',
            'number_of_bulls' => 'required|integer|min:0',
            'number_of_goats' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farm = $farmer->cattleFarms()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Farm created successfully',
            'farm' => $farm
        ], 201);
    }

    public function updateFarm(Request $request, CattleFarm $farm)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer || $farm->farmer_id !== $farmer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'picture' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'number_of_cows' => 'required|integer|min:0',
            'number_of_calves' => 'required|integer|min:0',
            'number_of_bulls' => 'required|integer|min:0',
            'number_of_goats' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farm->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Farm updated successfully',
            'farm' => $farm->fresh()
        ]);
    }

    public function deleteFarm(Request $request, CattleFarm $farm)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer || $farm->farmer_id !== $farmer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found or access denied'
            ], 404);
        }

        $farm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Farm deleted successfully'
        ]);
    }

    public function orders(Request $request)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $orders = Order::with(['cattleFarm', 'orderItems.bullSemen.breed', 'jobs.technician.user'])
            ->where('farmer_id', $farmer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function showOrder(Request $request, Order $order)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer || $order->farmer_id !== $farmer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or access denied'
            ], 404);
        }

        $order->load(['cattleFarm', 'orderItems.bullSemen.breed', 'jobs.technician.user']);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function confirmService(Request $request, $jobId)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $job = Job::with(['order', 'technician.user'])
            ->where('id', $jobId)
            ->whereHas('order', function($query) use ($farmer) {
                $query->where('farmer_id', $farmer->id);
            })
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_completed' => 'required|boolean',
            'cow_in_heat' => 'nullable|boolean',
            'amount_paid' => 'nullable|numeric|min:0',
            'semen_straw_code' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'farmer_notes' => 'nullable|string|max:1000',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('service-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Update job with farmer confirmation
        $job->update([
            'farmer_notes' => $request->farmer_notes,
            'rating' => $request->rating,
            'amount_paid' => $request->amount_paid,
            'semen_straw_code' => $request->semen_straw_code,
            'semen_straw_image' => !empty($photoPaths) ? implode(',', $photoPaths) : null,
        ]);

        // Update job status based on service completion
        if ($request->service_completed) {
            if ($request->cow_in_heat) {
                $job->update([
                    'status' => 'completed',
                    'progress_status' => 'administered',
                    'cow_status' => 'pregnant'
                ]);
            } else {
                $job->update([
                    'status' => 'completed',
                    'progress_status' => 'failed_no_heat',
                    'cow_status' => null
                ]);
            }
        } else {
            $job->update([
                'status' => 'failed',
                'progress_status' => 'failed'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service confirmation submitted successfully',
            'job' => $job->fresh()
        ]);
    }

    public function getJobs(Request $request)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $jobs = Job::with(['order.cattleFarm', 'technician.user'])
            ->whereHas('order', function($query) use ($farmer) {
                $query->where('farmer_id', $farmer->id);
            })
            ->whereIn('cow_status', ['pregnant', 'miscarried', 'delivered'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    public function markMiscarriage(Request $request, $jobId)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $job = Job::with(['order', 'technician.user'])
            ->where('id', $jobId)
            ->whereHas('order', function($query) use ($farmer) {
                $query->where('farmer_id', $farmer->id);
            })
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $job->update([
            'cow_status' => 'miscarried',
            'delivery_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as miscarried successfully',
            'job' => $job->fresh()
        ]);
    }

    public function markDelivery(Request $request, $jobId)
    {
        $farmer = Farmer::where('user_id', $request->user()->id)->first();
        
        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found'
            ], 404);
        }

        $job = Job::with(['order', 'technician.user'])
            ->where('id', $jobId)
            ->whereHas('order', function($query) use ($farmer) {
                $query->where('farmer_id', $farmer->id);
            })
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('calf-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $job->update([
            'cow_status' => 'delivered',
            'delivery_notes' => $request->notes,
            'calf_image' => !empty($photoPaths) ? implode(',', $photoPaths) : $job->calf_image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as delivered successfully',
            'job' => $job->fresh()
        ]);
    }
}