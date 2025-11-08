<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $alerts = Alert::with(['earthquake', 'disaster'])
            ->where(function ($query) use ($request, $sessionId) {
                $query->where('session_id', $sessionId);
                if ($request->user()) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            })
            ->orderBy('sent_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json($alerts);
    }

    public function markAsRead(Request $request, Alert $alert)
    {
        $alert->update(['is_read' => true]);
        return response()->json(['message' => 'Alert marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $sessionId = $request->session()->getId();

        Alert::where(function ($query) use ($request, $sessionId) {
            $query->where('session_id', $sessionId);
            if ($request->user()) {
                $query->orWhere('user_id', $request->user()->id);
            }
        })->update(['is_read' => true]);

        return response()->json(['message' => 'All alerts marked as read']);
    }

    public function unreadCount(Request $request)
    {
        $sessionId = $request->session()->getId();

        $count = Alert::where('is_read', false)
            ->where(function ($query) use ($request, $sessionId) {
                $query->where('session_id', $sessionId);
                if ($request->user()) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
