<?php

namespace App\Http\Controllers;

use App\Models\SafetyGuide;
use Illuminate\Http\Request;

class SafetyGuideController extends Controller
{
    public function index(Request $request)
    {
        $query = SafetyGuide::where('is_active', true)->orderBy('order');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get());
    }

    public function show(SafetyGuide $safetyGuide)
    {
        return response()->json($safetyGuide);
    }
}
