<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatGPTService $chatGPTService)
    {
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $context = [
            'active_disasters' => \App\Models\Disaster::where('status', 'active')
                ->where('country', 'Philippines')
                ->count(),
            'recent_earthquakes' => \App\Models\Earthquake::where('occurred_at', '>=', now()->subDay())
                ->count(),
        ];

        $response = $this->chatGPTService->chat($request->message, $context);

        return response()->json([
            'response' => $response,
            'timestamp' => now(),
        ]);
    }
}

