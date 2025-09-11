<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\BullSemen;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function breeds(Request $request)
    {
        $query = Breed::with('bullSemen');
        
        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $breeds = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'breeds' => $breeds
        ]);
    }

    public function semen(Request $request)
    {
        $query = BullSemen::with('breed');
        
        if ($request->has('breed_id')) {
            $query->where('breed_id', $request->breed_id);
        }
        
        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $semen = $query->orderBy('name')->paginate(15);

        return response()->json([
            'success' => true,
            'semen' => $semen
        ]);
    }

    public function showSemen(BullSemen $semen)
    {
        $semen->load('breed');

        return response()->json([
            'success' => true,
            'semen' => $semen
        ]);
    }
}