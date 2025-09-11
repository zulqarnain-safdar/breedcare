<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Technician;
use App\Models\Order;
use App\Models\Job;
use App\Models\BullSemen;
use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function farmers(Request $request)
    {
        $query = Farmer::with('user', 'cattleFarms');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $farmers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'farmers' => $farmers
        ]);
    }

    public function updateFarmerStatus(Request $request, Farmer $farmer)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farmer->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Farmer status updated successfully',
            'farmer' => $farmer->fresh()
        ]);
    }

    public function technicians(Request $request)
    {
        $query = Technician::with('user');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('availability')) {
            $query->where('availability', $request->availability);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile_1', 'like', "%{$search}%")
                  ->orWhere('unique_code', 'like', "%{$search}%");
            });
        }

        $technicians = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'technicians' => $technicians
        ]);
    }

    public function updateTechnicianStatus(Request $request, Technician $technician)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:incomplete,pending,active,inactive,blacklist',
            'availability' => 'nullable|in:online,offline,on_leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['status' => $request->status];
        if ($request->has('availability')) {
            $updateData['availability'] = $request->availability;
        }

        $technician->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Technician status updated successfully',
            'technician' => $technician->fresh()
        ]);
    }

    public function approveTechnician(Request $request, Technician $technician)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_comments' => 'nullable|string',
            'admin_signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'status' => $request->status === 'approved' ? 'active' : 'inactive',
            'admin_comments' => $request->admin_comments,
            'admin_signature' => $request->admin_signature,
        ];

        $technician->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Technician approval updated successfully',
            'technician' => $technician->fresh()
        ]);
    }

    public function orders(Request $request)
    {
        $query = Order::with(['farmer.user', 'cattleFarm', 'orderItems.bullSemen.breed', 'jobs.technician.user']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }
        
        if ($request->has('technician_id')) {
            $query->whereHas('jobs', function($q) use ($request) {
                $q->where('technician_id', $request->technician_id);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function jobs(Request $request)
    {
        $query = Job::with(['order.farmer.user', 'order.cattleFarm', 'technician.user', 'order.orderItems.bullSemen.breed']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('progress_status')) {
            $query->where('progress_status', $request->progress_status);
        }
        
        if ($request->has('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'day':
                    $query = Order::whereDate('created_at', today());
                    break;
                case 'week':
                    $query = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query = Order::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query = Order::whereYear('created_at', now()->year);
                    break;
                default:
                    $query = Order::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
            }
        }

        $totalOrders = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $completedOrders = $query->where('status', 'completed')->count();
        $pendingOrders = $query->where('status', 'pending')->count();

        $totalFarmers = Farmer::count();
        $activeFarmers = Farmer::where('status', 'active')->count();
        $totalTechnicians = Technician::count();
        $activeTechnicians = Technician::where('status', 'active')->count();
        $onlineTechnicians = Technician::where('availability', 'online')->count();

        $topTechnicians = Job::with('technician.user')
            ->selectRaw('technician_id, COUNT(*) as job_count, SUM(amount_paid) as total_earnings')
            ->where('status', 'completed')
            ->groupBy('technician_id')
            ->orderBy('total_earnings', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = Order::with(['farmer.user', 'cattleFarm'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'analytics' => [
                'orders' => [
                    'total' => $totalOrders,
                    'completed' => $completedOrders,
                    'pending' => $pendingOrders,
                    'revenue' => $totalRevenue,
                ],
                'users' => [
                    'total_farmers' => $totalFarmers,
                    'active_farmers' => $activeFarmers,
                    'total_technicians' => $totalTechnicians,
                    'active_technicians' => $activeTechnicians,
                    'online_technicians' => $onlineTechnicians,
                ],
                'top_technicians' => $topTechnicians,
                'recent_orders' => $recentOrders,
                'period' => $period,
            ]
        ]);
    }

    public function breeds(Request $request)
    {
        $breeds = Breed::with('bullSemen')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'breeds' => $breeds
        ]);
    }

    public function semenProducts(Request $request)
    {
        $query = BullSemen::with('breed');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    public function earnings(Request $request)
    {
        $period = $request->get('filter', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'month':
                    $query = Order::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query = Order::whereYear('created_at', now()->year);
                    break;
                default:
                    $query = Order::query();
            }
        }

        $totalRevenue = $query->sum('total_amount');
        $totalOrders = $query->count();

        // Calculate platform earnings (10% of total revenue)
        $platformEarnings = $totalRevenue * 0.10;
        $technicianEarnings = $totalRevenue * 0.90;

        // Get earnings by technician
        $earningsByTechnician = Job::with('technician.user')
            ->selectRaw('technician_id, COUNT(*) as orders_count, SUM(amount_paid) as total_earnings, SUM(amount_paid) * 0.9 as commission')
            ->where('status', 'completed')
            ->whereHas('order', function($q) use ($query) {
                $q->whereIn('id', $query->pluck('id'));
            })
            ->groupBy('technician_id')
            ->orderBy('total_earnings', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->technician_id,
                    'name' => $item->technician->user->name ?? 'Unknown',
                    'orders_count' => $item->orders_count,
                    'total_earnings' => $item->total_earnings,
                    'commission' => $item->commission
                ];
            });

        return response()->json([
            'success' => true,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'platform_earnings' => $platformEarnings,
            'technician_earnings' => $technicianEarnings,
            'by_technician' => $earningsByTechnician,
        ]);
    }

    public function performance(Request $request)
    {
        $period = $request->get('filter', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $query = Job::whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'month':
                    $query = Job::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query = Job::whereYear('created_at', now()->year);
                    break;
                default:
                    $query = Job::query();
            }
        }

        $totalJobs = $query->count();
        $completedJobs = $query->where('status', 'completed')->count();
        $successRate = $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;

        // Calculate average response time (time from job creation to acceptance)
        $avgResponseTime = Job::whereNotNull('estimated_visit_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, estimated_visit_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // Calculate customer satisfaction (average rating)
        $customerSatisfaction = Job::whereNotNull('rating')
            ->avg('rating') ?? 0;

        // Get technician performance - use the same date filtering as the main query
        $technicianPerformance = $query->with('technician.user')
            ->selectRaw('
                technician_id,
                COUNT(*) as total_jobs,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_jobs,
                AVG(rating) as avg_rating,
                AVG(TIMESTAMPDIFF(HOUR, created_at, estimated_visit_at)) as avg_response_time
            ')
            ->groupBy('technician_id')
            ->orderBy('avg_rating', 'desc')
            ->get()
            ->map(function($item) {
                $successRate = $item->total_jobs > 0 ? round(($item->completed_jobs / $item->total_jobs) * 100, 1) : 0;
                return [
                    'id' => $item->technician_id,
                    'name' => $item->technician->user->name ?? 'Unknown',
                    'jobs_completed' => $item->completed_jobs,
                    'success_rate' => $successRate,
                    'avg_rating' => round($item->avg_rating ?? 0, 1),
                    'avg_response_time' => round($item->avg_response_time ?? 0, 1)
                ];
            });

        return response()->json([
            'success' => true,
            'success_rate' => $successRate,
            'avg_response_time' => round($avgResponseTime, 1),
            'customer_satisfaction' => round($customerSatisfaction, 1),
            'total_jobs' => $totalJobs,
            'completed_jobs' => $completedJobs,
            'technicians' => $technicianPerformance,
        ]);
    }

    public function stock(Request $request)
    {
        // Get stock information for semen products
        $totalItems = BullSemen::sum('stock_quantity');
        $lowStockItems = BullSemen::where('stock_quantity', '<=', 10)->count();
        $outOfStockItems = BullSemen::where('stock_quantity', '=', 0)->count();
        
        // Calculate total value
        $totalValue = BullSemen::selectRaw('SUM(price * stock_quantity) as total')
            ->value('total') ?? 0;

        // Get stock by breed
        $stockByBreed = BullSemen::with('breed')
            ->selectRaw('breed_id, SUM(stock_quantity) as total_stock, AVG(price) as avg_price')
            ->groupBy('breed_id')
            ->orderBy('total_stock', 'desc')
            ->get();

        // Get stock by technician using many-to-many relationship with quantities
        $stockByTechnician = Technician::with(['user', 'semenProducts' => function($query) {
            $query->withPivot('quantity');
        }])
            ->whereHas('semenProducts')
            ->get()
            ->map(function($technician) {
                $totalQuantity = $technician->semenProducts->sum('pivot.quantity');
                $lowStockCount = $technician->semenProducts->where('pivot.quantity', '<=', 10)->where('pivot.quantity', '>', 0)->count();
                // Count items where technician's assigned quantity is 0 OR main inventory is 0
                $outOfStockCount = $technician->semenProducts->filter(function($item) {
                    return $item->pivot->quantity == 0 || $item->stock_quantity == 0;
                })->count();
                
                return [
                    'id' => $technician->id,
                    'name' => $technician->user?->name ?? 'Unknown Technician',
                    'total_items' => $technician->semenProducts->count(),
                    'total_quantity' => $totalQuantity,
                    'low_stock_items' => $lowStockCount,
                    'out_of_stock_items' => $outOfStockCount,
                    'stock_items' => $technician->semenProducts->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'breed' => $item->breed->name ?? 'Unknown',
                            'quantity' => $item->pivot->quantity,
                            'price' => $item->price,
                            'main_stock' => $item->stock_quantity
                        ];
                    })
                ];
            });

        // Get low stock items
        $lowStockProducts = BullSemen::with('breed')
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'total_items' => $totalItems,
            'low_stock' => $lowStockItems,
            'out_of_stock' => $outOfStockItems,
            'total_value' => $totalValue,
            'by_breed' => $stockByBreed,
            'by_technician' => $stockByTechnician,
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    public function storeSemenProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'breed_id' => 'required|exists:breeds,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $semenProduct = BullSemen::create([
                'name' => $request->name,
                'breed_id' => $request->breed_id,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semen product created successfully',
                'data' => $semenProduct->load('breed', 'technicians.user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create semen product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroySemenProduct(BullSemen $semenProduct)
    {
        try {
            // Detach all technicians first
            $semenProduct->technicians()->detach();
            
            // Delete the semen product
            $semenProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semen product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete semen product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTechniciansForStock()
    {
        $technicians = Technician::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function($technician) {
                return [
                    'id' => $technician->id,
                    'name' => $technician->user?->name ?? 'Unknown Technician',
                    'unique_code' => $technician->unique_code
                ];
            });

        return response()->json([
            'success' => true,
            'technicians' => $technicians
        ]);
    }

    public function assignStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:technicians,id',
            'stock_assignments' => 'required|array',
            'stock_assignments.*.stock_item_id' => 'required|exists:bull_semen,id',
            'stock_assignments.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $technician = Technician::findOrFail($request->technician_id);
            $assignedCount = 0;

            // Process each stock assignment
            foreach ($request->stock_assignments as $assignment) {
                $stockItem = BullSemen::findOrFail($assignment['stock_item_id']);
                $quantity = $assignment['quantity'];

                // Check if there's enough stock available
                if ($stockItem->stock_quantity < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$stockItem->name}. Available: {$stockItem->stock_quantity}, Requested: {$quantity}"
                    ], 400);
                }

                // Check if already assigned to this technician
                $existingAssignment = $technician->semenProducts()->where('bull_semen_id', $stockItem->id)->first();
                
                if ($existingAssignment) {
                    // Update existing assignment with new quantity
                    $technician->semenProducts()->updateExistingPivot($stockItem->id, [
                        'quantity' => $existingAssignment->pivot->quantity + $quantity
                    ]);
                } else {
                    // Create new assignment
                    $technician->semenProducts()->attach($stockItem->id, [
                        'quantity' => $quantity
                    ]);
                }

                // Update the main stock quantity
                $stockItem->decrement('stock_quantity', $quantity);
                $assignedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock items assigned successfully',
                'assigned_count' => $assignedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign stock items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'technician_id' => 'required|exists:technicians,id',
            'estimated_visit_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);
            $technician = Technician::findOrFail($request->technician_id);

            // Check if technician is active and available
            if ($technician->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign job to inactive technician'
                ], 400);
            }

            // Check if job already exists for this order
            $existingJob = Job::where('order_id', $request->order_id)->first();
            if ($existingJob) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job already exists for this order'
                ], 400);
            }

            // Create the job
            $job = Job::create([
                'order_id' => $request->order_id,
                'technician_id' => $request->technician_id,
                'status' => 'assigned',
                'progress_status' => 'pending',
                'estimated_visit_at' => $request->estimated_visit_at,
                'technician_notes' => $request->notes
            ]);

            // Update order status to 'assigned'
            $order->update(['status' => 'assigned']);

            return response()->json([
                'success' => true,
                'message' => 'Job assigned successfully',
                'job' => $job->load(['order.farmer.user', 'technician.user'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateJobStatus(Request $request, Job $job)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:assigned,accepted,rejected,completed,cancelled',
            'progress_status' => 'nullable|in:pending,in_progress,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'status' => $request->status
            ];

            if ($request->has('progress_status')) {
                $updateData['progress_status'] = $request->progress_status;
            }

            if ($request->has('notes')) {
                $updateData['technician_notes'] = $request->notes;
            }

            $job->update($updateData);

            // Update order status based on job status
            if ($request->status === 'completed') {
                $job->order->update(['status' => 'completed']);
            } elseif ($request->status === 'cancelled') {
                $job->order->update(['status' => 'cancelled']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job status updated successfully',
                'job' => $job->load(['order.farmer.user', 'technician.user'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}