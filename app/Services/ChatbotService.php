<?php

namespace App\Services;

use App\Models\Earthquake;
use Carbon\Carbon;

class ChatbotService
{
    public function processMessage(string $message): array
    {
        $message = strtolower(trim($message));

        if ($this->containsKeywords($message, ['safety', 'safe', 'what to do', 'help', 'protect'])) {
            return $this->getSafetyTips();
        }

        if ($this->containsKeywords($message, ['recent', 'latest', 'today', 'now'])) {
            return $this->getRecentEarthquakes();
        }

        if ($this->containsKeywords($message, ['statistics', 'stats', 'count', 'how many'])) {
            return $this->getStatistics();
        }

        if ($this->containsKeywords($message, ['magnitude', 'scale', 'richter'])) {
            return $this->getMagnitudeInfo();
        }

        if ($this->containsKeywords($message, ['emergency', 'contact', 'hotline', 'call'])) {
            return $this->getEmergencyContacts();
        }

        return $this->getDefaultResponse();
    }

    private function containsKeywords(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function getSafetyTips(): array
    {
        return [
            'type' => 'safety',
            'title' => 'Earthquake Safety Tips',
            'message' => "**During an Earthquake:**\n\n" .
                "🔹 **DROP** - Get down on your hands and knees\n" .
                "🔹 **COVER** - Take cover under a sturdy desk or table\n" .
                "🔹 **HOLD ON** - Hold on until shaking stops\n\n" .
                "**After an Earthquake:**\n\n" .
                "✓ Check for injuries and damage\n" .
                "✓ Be prepared for aftershocks\n" .
                "✓ Stay away from damaged buildings\n" .
                "✓ Listen to emergency broadcasts\n" .
                "✓ Use text messages instead of calls",
            'actions' => [
                ['label' => 'Emergency Contacts', 'action' => 'emergency'],
                ['label' => 'Recent Earthquakes', 'action' => 'recent'],
            ]
        ];
    }

    private function getRecentEarthquakes(): array
    {
        $earthquakes = Earthquake::orderBy('occurred_at', 'desc')
            ->take(5)
            ->get();

        if ($earthquakes->isEmpty()) {
            return [
                'type' => 'info',
                'title' => 'Recent Earthquakes',
                'message' => 'No recent earthquake data available. The system will update automatically when new data is received.',
            ];
        }

        $message = "**Recent Earthquakes:**\n\n";
        foreach ($earthquakes as $eq) {
            $timeAgo = $eq->occurred_at->diffForHumans();
            $message .= "🔴 **M{$eq->magnitude}** - {$eq->location}\n";
            $message .= "   📍 Depth: {$eq->depth}km | ⏰ {$timeAgo}\n\n";
        }

        return [
            'type' => 'earthquakes',
            'title' => 'Recent Earthquakes',
            'message' => $message,
            'data' => $earthquakes,
        ];
    }

    private function getStatistics(): array
    {
        $today = Earthquake::whereDate('occurred_at', '>=', Carbon::today())->count();
        $week = Earthquake::whereDate('occurred_at', '>=', Carbon::now()->subDays(7))->count();
        $month = Earthquake::whereDate('occurred_at', '>=', Carbon::now()->subDays(30))->count();

        $strongest = Earthquake::orderBy('magnitude', 'desc')->first();

        $message = "**Earthquake Statistics:**\n\n";
        $message .= "📊 Today: {$today} earthquakes\n";
        $message .= "📊 Last 7 days: {$week} earthquakes\n";
        $message .= "📊 Last 30 days: {$month} earthquakes\n\n";

        if ($strongest) {
            $message .= "**Strongest Recent:**\n";
            $message .= "🔴 M{$strongest->magnitude} - {$strongest->location}\n";
            $message .= "⏰ {$strongest->occurred_at->diffForHumans()}";
        }

        return [
            'type' => 'statistics',
            'title' => 'Statistics',
            'message' => $message,
        ];
    }

    private function getMagnitudeInfo(): array
    {
        return [
            'type' => 'info',
            'title' => 'Earthquake Magnitude Scale',
            'message' => "**Richter Scale:**\n\n" .
                "🟢 **< 3.0** - Micro: Not felt\n" .
                "🟡 **3.0-3.9** - Minor: Often felt, rarely causes damage\n" .
                "🟠 **4.0-4.9** - Light: Noticeable shaking, minor damage\n" .
                "🔴 **5.0-5.9** - Moderate: Can cause damage to buildings\n" .
                "🔴 **6.0-6.9** - Strong: Can be destructive\n" .
                "⚫ **7.0+** - Major/Great: Serious damage over large areas",
        ];
    }

    private function getEmergencyContacts(): array
    {
        return [
            'type' => 'emergency',
            'title' => 'Emergency Contacts',
            'message' => "**Philippines Emergency Hotlines:**\n\n" .
                "🚨 **NDRRMC:** 911\n" .
                "🚨 **PHIVOLCS:** (02) 8426-1468 to 79\n" .
                "🚨 **Red Cross:** 143\n" .
                "🚑 **Emergency:** 911\n" .
                "🚒 **Fire:** (02) 8426-0219\n" .
                "👮 **Police:** 117\n\n" .
                "**International:**\n" .
                "🌍 **USGS:** https://earthquake.usgs.gov",
        ];
    }

    private function getDefaultResponse(): array
    {
        return [
            'type' => 'help',
            'title' => 'How can I help you?',
            'message' => "I can help you with:\n\n" .
                "🔹 **Safety tips** - Learn what to do during earthquakes\n" .
                "🔹 **Recent earthquakes** - View latest seismic activity\n" .
                "🔹 **Statistics** - Get earthquake data and trends\n" .
                "🔹 **Emergency contacts** - Important hotlines\n" .
                "🔹 **Magnitude info** - Understand earthquake scales\n\n" .
                "Just ask me anything!",
            'actions' => [
                ['label' => 'Safety Tips', 'action' => 'safety'],
                ['label' => 'Recent Activity', 'action' => 'recent'],
                ['label' => 'Emergency Contacts', 'action' => 'emergency'],
            ]
        ];
    }
}
