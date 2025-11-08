<?php

/**
 * OpenAI API Test Script
 *
 * This script tests if your OpenAI API key is working correctly.
 * Run: php test-openai.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=================================\n";
echo "OpenAI API Connection Test\n";
echo "=================================\n\n";

// Get API key from config
$apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));

if (empty($apiKey)) {
    echo "❌ No API key found!\n\n";
    echo "To fix this:\n";
    echo "1. Get an API key from: https://platform.openai.com/api-keys\n";
    echo "2. Add it to your .env file:\n";
    echo "   OPENAI_API_KEY=sk-proj-your-key-here\n";
    echo "3. Run: php artisan config:clear\n";
    echo "4. Run this test again\n\n";
    echo "Note: The chatbot will still work using fallback responses!\n";
    exit(1);
}

echo "✓ API key found: " . substr($apiKey, 0, 20) . "...\n\n";
echo "Testing connection to OpenAI...\n";

try {
    $response = \Illuminate\Support\Facades\Http::timeout(30)
        ->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant. Respond in one short sentence.'
                ],
                [
                    'role' => 'user',
                    'content' => 'Say hello and confirm you are working.'
                ]
            ],
            'max_tokens' => 50,
        ]);

    if ($response->successful()) {
        $data = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? 'No response';

        echo "✅ SUCCESS! OpenAI API is working!\n\n";
        echo "Response from GPT-3.5-turbo:\n";
        echo "\"$reply\"\n\n";
        echo "=================================\n";
        echo "Your chatbot is now AI-powered! 🚀\n";
        echo "=================================\n";
        exit(0);
    } else {
        $status = $response->status();
        $body = $response->json();
        $error = $body['error']['message'] ?? 'Unknown error';

        echo "❌ API Error (HTTP $status)\n";
        echo "Message: $error\n\n";

        if ($status === 401) {
            echo "This means your API key is invalid.\n";
            echo "Please check:\n";
            echo "1. The key is copied correctly\n";
            echo "2. The key is active in OpenAI dashboard\n";
            echo "3. No extra spaces in .env file\n";
        } elseif ($status === 429) {
            echo "This means you've hit the rate limit.\n";
            echo "Please:\n";
            echo "1. Wait a few minutes\n";
            echo "2. Check your OpenAI usage limits\n";
            echo "3. Consider upgrading your plan\n";
        } elseif ($status === 402) {
            echo "This means insufficient quota.\n";
            echo "Please:\n";
            echo "1. Add billing information to OpenAI\n";
            echo "2. Add credits to your account\n";
        }

        echo "\nThe chatbot will use fallback responses instead.\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ Connection Error\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. Your internet connection\n";
    echo "2. OpenAI service status: https://status.openai.com/\n";
    echo "3. Firewall settings\n\n";
    echo "The chatbot will use fallback responses instead.\n";
    exit(1);
}
