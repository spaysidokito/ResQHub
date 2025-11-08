<?php

/**
 * Google Gemini API Test Script
 *
 * This script tests if your Google Gemini API key is working correctly.
 * Run: php test-gemini.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=================================\n";
echo "Google Gemini API Connection Test\n";
echo "=================================\n\n";

// Get API key from config
$apiKey = config('services.gemini.api_key', env('GOOGLE_GEMINI_API_KEY', ''));

if (empty($apiKey)) {
    echo "❌ No API key found!\n\n";
    echo "To fix this:\n";
    echo "1. Get a FREE API key from: https://makersuite.google.com/app/apikey\n";
    echo "   - Sign in with your Google account\n";
    echo "   - Click 'Create API Key'\n";
    echo "   - No credit card required!\n\n";
    echo "2. Add it to your .env file:\n";
    echo "   GOOGLE_GEMINI_API_KEY=AIzaSy...\n\n";
    echo "3. Run: php artisan config:clear\n";
    echo "4. Run this test again\n\n";
    echo "Note: The chatbot will still work using fallback responses!\n";
    exit(1);
}

echo "✓ API key found: " . substr($apiKey, 0, 20) . "...\n\n";
echo "Testing connection to Google Gemini...\n";

try {
    $geminiService = new \App\Services\GeminiService();
    $result = $geminiService->testConnection();

    if ($result['success']) {
        echo "✅ SUCCESS! Google Gemini API is working!\n\n";
        echo "Response from Gemini:\n";
        echo "\"" . ($result['response'] ?? 'OK') . "\"\n\n";
        echo "=================================\n";
        echo "Your chatbot is now AI-powered! 🚀\n";
        echo "=================================\n\n";
        echo "Benefits:\n";
        echo "✓ FREE - 1,500 requests/day\n";
        echo "✓ Excellent Filipino/Tagalog support\n";
        echo "✓ Fast and reliable\n";
        echo "✓ No credit card required\n\n";
        echo "Try these in your chatbot:\n";
        echo "- \"Hello, how are you?\"\n";
        echo "- \"What should I do during Typhoon Uwan?\"\n";
        echo "- \"Kumusta, ano gagawin sa bagyo?\"\n";
        exit(0);
    } else {
        $error = $result['message'] ?? 'Unknown error';
        echo "❌ API Error\n";
        echo "Message: $error\n\n";

        if (str_contains($error, 'API_KEY_INVALID') || str_contains($error, '400')) {
            echo "This means your API key is invalid.\n";
            echo "Please check:\n";
            echo "1. The key is copied correctly from Google AI Studio\n";
            echo "2. The key starts with 'AIzaSy'\n";
            echo "3. No extra spaces in .env file\n";
            echo "4. Run: php artisan config:clear\n";
        } elseif (str_contains($error, '429')) {
            echo "This means you've hit the rate limit.\n";
            echo "Please:\n";
            echo "1. Wait a few minutes\n";
            echo "2. Check your usage at: https://makersuite.google.com/\n";
        } elseif (str_contains($error, '403')) {
            echo "This means the API key doesn't have permission.\n";
            echo "Please:\n";
            echo "1. Make sure Generative Language API is enabled\n";
            echo "2. Create a new API key if needed\n";
        }

        echo "\nThe chatbot will use fallback responses instead.\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ Connection Error\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. Your internet connection\n";
    echo "2. Google AI service status\n";
    echo "3. Firewall settings\n\n";
    echo "The chatbot will use fallback responses instead.\n";
    exit(1);
}
