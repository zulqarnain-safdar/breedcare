<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\Job;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TechnicianController extends Controller
{
    public function profile(Request $request)
    {
        $technician = Technician::with('user')->where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'technician' => $technician
        ]);
    }

    public function updateProfile(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'cnic' => 'required|string|unique:technicians,cnic,' . $technician->id,
            'date_of_birth' => 'required|date',
            'home_address' => 'required|string',
            'clinic_address' => 'nullable|string',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'tehsil' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'degree_year' => 'required|integer',
            'years_of_experience' => 'required|integer|min:0',
            'customers_per_month' => 'required|integer|min:0',
            'areas_of_expertise' => 'required|string',
            'mobile_1' => 'required|string',
            'mobile_2' => 'nullable|string',
            'clinic_latitude' => 'required|numeric',
            'clinic_longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $technician->update($request->all());
        $request->user()->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'technician' => $technician->fresh()
        ]);
    }

    public function jobs(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $status = $request->get('status', 'all');
        $query = Job::with(['order.farmer.user', 'order.cattleFarm', 'order.orderItems.bullSemen.breed'])
            ->where('technician_id', $technician->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    public function showJob(Request $request, Job $job)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician || $job->technician_id !== $technician->id) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        $job->load(['order.farmer.user', 'order.cattleFarm', 'order.orderItems.bullSemen.breed']);

        return response()->json([
            'success' => true,
            'job' => $job
        ]);
    }

    public function acceptJob(Request $request, Job $job)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician || $job->technician_id !== $technician->id) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        if ($job->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be accepted in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'estimated_visit_at' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $job->update([
            'status' => 'accepted',
            'progress_status' => 'accepted',
            'estimated_visit_at' => $request->estimated_visit_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job accepted successfully',
            'job' => $job->fresh()
        ]);
    }

    public function rejectJob(Request $request, Job $job)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician || $job->technician_id !== $technician->id) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        if ($job->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be rejected in current status'
            ], 400);
        }

        $job->update([
            'status' => 'rejected',
            'progress_status' => 'rejected',
            'technician_notes' => $request->get('notes', ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job rejected successfully',
            'job' => $job->fresh()
        ]);
    }

    public function completeJob(Request $request, Job $job)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician || $job->technician_id !== $technician->id) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or access denied'
            ], 404);
        }

        if ($job->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Job must be accepted before completion'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'visit_latitude' => 'required|numeric',
            'visit_longitude' => 'required|numeric',
            'cow_in_heat' => 'required|boolean',
            'amount_paid' => 'required|numeric|min:0',
            'semen_straw_code' => 'nullable|string',
            'cow_status' => 'nullable|string',
            'delivery_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $job->update([
            'status' => $request->cow_in_heat ? 'completed' : 'failed_no_heat',
            'progress_status' => 'completed',
            'actual_visit_at' => now(),
            'visit_latitude' => $request->visit_latitude,
            'visit_longitude' => $request->visit_longitude,
            'amount_paid' => $request->amount_paid,
            'semen_straw_code' => $request->semen_straw_code,
            'cow_status' => $request->cow_status,
            'delivery_notes' => $request->delivery_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job completed successfully',
            'job' => $job->fresh()
        ]);
    }

    public function updateAvailability(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'availability' => 'required|in:online,offline,on_leave',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'available_24_7' => 'nullable|boolean',
            'service_areas' => 'nullable|array',
            'service_areas.*.name' => 'required_with:service_areas|string|max:255',
            'service_areas.*.radius' => 'required_with:service_areas|numeric|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['availability' => $request->availability];
        
        if ($request->has('working_hours_start')) {
            $updateData['working_hours_start'] = $request->working_hours_start;
        }
        
        if ($request->has('working_hours_end')) {
            $updateData['working_hours_end'] = $request->working_hours_end;
        }
        
        if ($request->has('available_24_7')) {
            $updateData['available_24_7'] = $request->available_24_7;
        }
        
        if ($request->has('service_areas')) {
            $updateData['service_areas'] = $request->service_areas;
        }

        $technician->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Availability settings updated successfully',
            'technician' => $technician->fresh()
        ]);
    }

    public function earnings(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $period = $request->get('period', 'month'); // day, week, month, year
        $query = Job::where('technician_id', $technician->id)
            ->where('status', 'completed');

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $totalEarnings = $query->sum('amount_paid');
        $jobCount = $query->count();
        $jobs = $query->with(['order.farmer.user'])->get();

        return response()->json([
            'success' => true,
            'earnings' => [
                'total' => $totalEarnings,
                'job_count' => $jobCount,
                'period' => $period,
                'jobs' => $jobs
            ]
        ]);
    }

    public function inventory(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        // Get assigned stock items with quantities
        $assignedItems = $technician->semenProducts()->withPivot('quantity')->with('breed')->get();
        
        // Calculate statistics
        $totalItems = $assignedItems->count();
        $availableItems = $assignedItems->where('pivot.quantity', '>', 0)->count();
        $lowStockItems = $assignedItems->where('pivot.quantity', '<=', 10)->where('pivot.quantity', '>', 0)->count();
        $outOfStockItems = $assignedItems->where('pivot.quantity', '=', 0)->count();

        // Format items for display
        $items = $assignedItems->map(function ($item) {
            return [
                'id' => $item->id,
                'bull_name' => $item->name,
                'breed' => $item->breed,
                'price' => $item->price,
                'stock_quantity' => $item->pivot->quantity,
                'main_stock' => $item->stock_quantity,
                'description' => $item->description,
                'is_active' => $item->is_active
            ];
        });

        return response()->json([
            'success' => true,
            'total_items' => $totalItems,
            'available_items' => $availableItems,
            'low_stock_items' => $lowStockItems,
            'out_of_stock' => $outOfStockItems,
            'items' => $items
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:cnic_front,cnic_back,degree_front,degree_back',
            'document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('document');
            $documentType = $request->document_type;
            
            // Generate unique filename
            $filename = $technician->id . '_' . $documentType . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file in public/technician-documents directory
            $path = $file->storeAs('technician-documents', $filename, 'public');
            
            // Update the appropriate field in the database
            $fieldName = $documentType . '_image';
            $technician->update([$fieldName => $path]);
            
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'file_path' => $path,
                'file_url' => Storage::url($path)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocument(Request $request)
    {
        $technician = Technician::where('user_id', $request->user()->id)->first();
        
        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:cnic_front,cnic_back,degree_front,degree_back',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $documentType = $request->document_type;
            $fieldName = $documentType . '_image';
            
            // Get current file path
            $currentPath = $technician->$fieldName;
            
            if ($currentPath && Storage::disk('public')->exists($currentPath)) {
                // Delete the file
                Storage::disk('public')->delete($currentPath);
            }
            
            // Clear the field in database
            $technician->update([$fieldName => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }
}