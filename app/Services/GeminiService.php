<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model = 'gemini-2.5-flash';
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GOOGLE_GEMINI_API_KEY', ''));
    }

    public function chat(string $message, array $context = []): array
    {
        // If no API key, return error to use fallback
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'No API key configured',
            ];
        }

        try {
            $systemPrompt = $this->getSystemPrompt();

            // Combine system prompt with user message
            $fullPrompt = $systemPrompt . "\n\nUser: " . $message . "\n\nAssistant:";

            // Add context if provided
            if (!empty($context)) {
                $fullPrompt = $systemPrompt . "\n\nContext: " . json_encode($context) . "\n\nUser: " . $message . "\n\nAssistant:";
            }

            $response = Http::timeout(30)
                ->post($this->apiUrl . $this->model . ':generateContent?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $fullPrompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                        'topP' => 0.8,
                        'topK' => 40,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_ONLY_HIGH'
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Extract the response text
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'I apologize, but I could not generate a response.';

                return [
                    'success' => true,
                    'type' => 'ai_response',
                    'title' => 'ResQBot (Gemini)',
                    'message' => trim($reply),
                    'source' => 'Google Gemini',
                ];
            }

            // Handle API errors
            $status = $response->status();
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';

            Log::error('Gemini API error', [
                'status' => $status,
                'error' => $errorMessage,
                'body' => $errorBody
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('Gemini service error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getSystemPrompt(): string
    {
        return "You are ResQBot, an AI assistant for ResQHub - a disaster monitoring system for the Philippines.

Your role is to:
- Provide accurate information about earthquakes, floods, typhoons, and fires
- Give safety tips and emergency procedures specific to the Philippines
- Answer questions about disaster preparedness
- Provide Philippine emergency contact information
- Be concise, clear, and helpful
- Use a professional but friendly and compassionate tone
- Respond in BOTH English and Tagalog (Filipino language)
- Understand and respond to greetings warmly
- Be culturally sensitive to Filipino context

Important Philippine Emergency Contacts:
- NDRRMC (National Disaster Risk Reduction and Management Council): 911
- PAGASA (Weather Bureau): (02) 8927-1335
- PHIVOLCS (Earthquake/Volcano): (02) 8426-1468 to 79
- BFP (Bureau of Fire Protection): (02) 8426-0219
- Philippine Red Cross: 143
- PNP (Police): 117

Current Active Disasters:
- Typhoon Uwan (International name: Fung-wong) - Approaching Northern Luzon with 150 km/h winds

Language Guidelines:
- When users greet you in English (hello, hi, good morning), respond warmly in English
- When users greet you in Tagalog (kumusta, magandang umaga), respond warmly in Tagalog
- When users ask questions in English, respond in English
- When users ask questions in Tagalog, respond in Tagalog
- You can understand Taglish (mixed English-Tagalog)

Always prioritize safety and direct users to official sources when needed.
Keep responses concise but informative.";
    }

    /**
     * Check if the API key is configured and valid
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Test the API connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'No API key configured',
            ];
        }

        try {
            $response = $this->chat('Hello, please respond with "OK" if you are working.');

            if ($response['success'] ?? false) {
                return [
                    'success' => true,
                    'message' => 'Google Gemini API is working!',
                    'response' => $response['message'] ?? '',
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
