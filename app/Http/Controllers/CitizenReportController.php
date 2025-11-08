<?php

namespace App\Http\Controllers;

use App\Models\CitizenReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CitizenReportController extends Controller
{
    public function pending()
    {
        $reports = Cache::remember('citizen_reports_pending', 30, function () {
            return CitizenReport::with(['user:id,name', 'disaster:id,name,type'])
                ->select('id', 'user_id', 'disaster_id', 'report_type', 'type', 'name', 'description', 'latitude', 'longitude', 'location', 'severity', 'status', 'photo', 'created_at')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        });

        return response()->json($reports);
    }

    public function verified()
    {
        $reports = CitizenReport::with(['user:id,name', 'verifiedBy:id,name', 'disaster:id,name,type'])
            ->select('id', 'user_id', 'disaster_id', 'verified_by', 'report_type', 'type', 'name', 'description', 'latitude', 'longitude', 'location', 'severity', 'status', 'photo', 'admin_notes', 'verified_at', 'created_at')
            ->where('status', 'verified')
            ->orderBy('verified_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($report) {
                $report->verified_by_user = $report->verifiedBy;
                return $report;
            });

        return response()->json($reports);
    }

    public function rejected()
    {
        $reports = CitizenReport::with(['user:id,name', 'verifiedBy:id,name', 'disaster:id,name,type'])
            ->select('id', 'user_id', 'disaster_id', 'verified_by', 'report_type', 'type', 'name', 'description', 'latitude', 'longitude', 'location', 'severity', 'status', 'photo', 'admin_notes', 'verified_at', 'created_at')
            ->where('status', 'rejected')
            ->orderBy('verified_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($report) {
                $report->verified_by_user = $report->verifiedBy;
                return $report;
            });

        return response()->json($reports);
    }

    public function create()
    {
        return view('citizen-report');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disaster_id' => 'required|exists:disasters,id',
            'report_type' => 'required|in:felt_tremor,infrastructure_damage,safety_update,casualty,resource_need,other',
            'description' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'report_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/reports'), $filename);
            $photoPath = 'uploads/reports/' . $filename;
        }

        $disaster = \App\Models\Disaster::find($validated['disaster_id']);

        CitizenReport::create([
            'user_id' => Auth::id(),
            'disaster_id' => $validated['disaster_id'],
            'report_type' => $validated['report_type'],
            'type' => $disaster->type,
            'name' => $disaster->name,
            'latitude' => $disaster->latitude,
            'longitude' => $disaster->longitude,
            'location' => $disaster->location,
            'severity' => $disaster->severity,
            'description' => $validated['description'],
            'status' => 'pending',
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Thank you! Your on-ground report has been submitted and is pending verification.');
    }

    public function verify(Request $request, CitizenReport $report)
    {
        $report->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->input('notes'),
        ]);

        Cache::forget('citizen_reports_pending');
        Cache::forget('citizen_reports_verified');

        return response()->json([
            'message' => 'Report verified successfully',
            'report' => $report,
        ]);
    }

    public function reject(Request $request, CitizenReport $report)
    {
        $report->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->input('notes'),
        ]);

        Cache::forget('citizen_reports_pending');
        Cache::forget('citizen_reports_rejected');

        return response()->json([
            'message' => 'Report rejected',
            'report' => $report,
        ]);
    }

    public function destroy(CitizenReport $report)
    {

        if ($report->photo && file_exists(public_path($report->photo))) {
            unlink(public_path($report->photo));
        }

        $report->delete();

        return response()->json([
            'message' => 'Report deleted successfully',
        ]);
    }
}
