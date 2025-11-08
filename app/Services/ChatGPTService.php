<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    private string $apiKey;
    private string $model = 'gpt-3.5-turbo';
    private ?GeminiService $geminiService = null;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $this->geminiService = new GeminiService();
    }

    public function chat(string $message, array $context = []): array
    {

        if ($this->geminiService->isConfigured()) {
            $geminiResponse = $this->geminiService->chat($message, $context);
            if ($geminiResponse['success'] ?? false) {
                return $geminiResponse;
            }

            Log::info('Gemini API failed, trying next option', ['error' => $geminiResponse['error'] ?? 'Unknown']);
        }

        if (!empty($this->apiKey)) {
            $openaiResponse = $this->tryOpenAI($message, $context);
            if ($openaiResponse !== null) {
                return $openaiResponse;
            }
        }

        return $this->fallbackResponse($message);
    }

    private function tryOpenAI(string $message, array $context = []): ?array
    {

        try {
            $systemPrompt = $this->getSystemPrompt();

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            if (!empty($context)) {
                $messages[] = ['role' => 'assistant', 'content' => 'Context: ' . json_encode($context)];
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'I apologize, but I could not generate a response.';

                return [
                    'type' => 'ai_response',
                    'title' => 'ResQBot (OpenAI)',
                    'message' => $reply,
                    'source' => 'ChatGPT',
                ];
            }

            Log::error('ChatGPT API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('ChatGPT service error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function getSystemPrompt(): string
    {
        return "You are ResQBot, an AI assistant for ResQHub - a disaster monitoring system for the Philippines.

Your role is to:
- Provide accurate information about earthquakes, floods, typhoons, and fires
- Give safety tips and emergency procedures
- Answer questions about disaster preparedness
- Provide Philippine emergency contact information
- Be concise, clear, and helpful
- Use a professional but friendly tone
- Respond in BOTH English and Tagalog (Filipino language)
- Understand and respond to greetings warmly
- Be culturally sensitive to Filipino context

Important Philippine Emergency Contacts:
- NDRRMC: 911
- PAGASA: (02) 8927-1335
- PHIVOLCS: (02) 8426-1468 to 79
- BFP (Fire): (02) 8426-0219
- Red Cross: 143

When users greet you (hello, hi, kumusta, etc.), respond warmly and introduce yourself.
When users ask in Tagalog, respond in Tagalog.
When users ask in English, respond in English.
Always prioritize safety and direct users to official sources when needed.";
    }

    private function fallbackResponse(string $message): array
    {
        $message = strtolower(trim($message));

        if ($this->containsKeywords($message, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'])) {
            return [
                'type' => 'greeting',
                'title' => 'Hello! 👋',
                'message' => "Hello! I'm **ResQBot**, your disaster monitoring assistant for the Philippines.\n\n" .
                    "I can help you with:\n" .
                    "🔹 Safety tips for earthquakes, floods, typhoons, and fires\n" .
                    "🔹 Emergency contact information\n" .
                    "🔹 Disaster preparedness advice\n" .
                    "🔹 Current disaster information\n\n" .
                    "Ask me anything about disaster safety! You can also ask in **Tagalog**. 🇵🇭",
            ];
        }

        if ($this->containsKeywords($message, ['kumusta', 'kamusta', 'musta', 'magandang umaga', 'magandang hapon', 'magandang gabi'])) {
            return [
                'type' => 'greeting',
                'title' => 'Kumusta! 👋',
                'message' => "Kumusta! Ako si **ResQBot**, ang iyong disaster monitoring assistant para sa Pilipinas.\n\n" .
                    "Makakatulong ako sa:\n" .
                    "🔹 Safety tips para sa lindol, baha, bagyo, at sunog\n" .
                    "🔹 Emergency contact information\n" .
                    "🔹 Disaster preparedness advice\n" .
                    "🔹 Kasalukuyang impormasyon tungkol sa mga sakuna\n\n" .
                    "Magtanong ka lang tungkol sa disaster safety! Pwede rin sa **English**. 🇵🇭",
            ];
        }

        if ($this->containsKeywords($message, ['safety', 'safe', 'what to do', 'protect'])) {
            return [
                'type' => 'safety',
                'title' => 'Safety Tips',
                'message' => "**General Disaster Safety:**\n\n" .
                    "🔹 Stay informed through official channels (PAGASA, NDRRMC)\n" .
                    "🔹 Have an emergency kit ready (water, food, first aid)\n" .
                    "🔹 Know your evacuation routes\n" .
                    "🔹 Keep emergency contacts handy\n" .
                    "🔹 Follow local authority instructions\n\n" .
                    "**During Earthquakes:** DROP, COVER, HOLD ON\n" .
                    "**During Typhoons:** Stay indoors, away from windows\n" .
                    "**During Floods:** Move to higher ground immediately\n" .
                    "**During Fires:** Stay low, exit quickly, don't use elevators",
            ];
        }

        if ($this->containsKeywords($message, ['kaligtasan', 'ligtas', 'ano gagawin', 'paano', 'protektahan'])) {
            return [
                'type' => 'safety',
                'title' => 'Mga Paalala sa Kaligtasan',
                'message' => "**Pangkalahatang Kaligtasan sa Sakuna:**\n\n" .
                    "🔹 Manatiling updated sa opisyal na balita (PAGASA, NDRRMC)\n" .
                    "🔹 Maghanda ng emergency kit (tubig, pagkain, first aid)\n" .
                    "🔹 Alamin ang evacuation routes\n" .
                    "🔹 Itago ang emergency contacts\n" .
                    "🔹 Sundin ang mga tagubilin ng mga awtoridad\n\n" .
                    "**Sa Lindol:** YUMUKO, MAGTAKIP, KUMAPIT\n" .
                    "**Sa Bagyo:** Manatili sa loob, lumayo sa bintana\n" .
                    "**Sa Baha:** Pumunta agad sa mataas na lugar\n" .
                    "**Sa Sunog:** Yumuko, lumabas agad, huwag gumamit ng elevator",
            ];
        }

        if ($this->containsKeywords($message, ['emergency', 'contact', 'hotline', 'call', 'number', 'tulong', 'tawag'])) {
            return [
                'type' => 'emergency',
                'title' => 'Emergency Contacts (Philippines)',
                'message' => "**Emergency Hotlines:**\n\n" .
                    "🚨 **NDRRMC:** 911\n" .
                    "🌀 **PAGASA:** (02) 8927-1335\n" .
                    "🌍 **PHIVOLCS:** (02) 8426-1468 to 79\n" .
                    "🔥 **BFP (Fire):** (02) 8426-0219\n" .
                    "🚑 **Red Cross:** 143\n" .
                    "👮 **Police:** 117\n\n" .
                    "Para sa life-threatening emergencies, tumawag sa **911**",
            ];
        }

        if ($this->containsKeywords($message, ['uwan', 'fung-wong', 'typhoon', 'bagyo'])) {
            return [
                'type' => 'typhoon',
                'title' => 'Typhoon Uwan (Fung-wong)',
                'message' => "**Typhoon Uwan (International: Fung-wong)**\n\n" .
                    "🌀 **Status:** Active and approaching Northern Luzon\n" .
                    "💨 **Wind Speed:** 150 km/h\n" .
                    "📍 **Location:** Northern Luzon area\n" .
                    "➡️ **Direction:** West-Northwest at 25 km/h\n\n" .
                    "**Safety Reminders:**\n" .
                    "⚠️ Stay indoors and away from windows\n" .
                    "⚠️ Prepare emergency supplies\n" .
                    "⚠️ Monitor PAGASA updates\n" .
                    "⚠️ Follow evacuation orders if issued\n\n" .
                    "Check the Weather Map for live tracking!",
            ];
        }

        if ($this->containsKeywords($message, ['help', 'tulong', 'ano', 'what'])) {
            return [
                'type' => 'help',
                'title' => 'How can I help you? / Paano kita matutulungan?',
                'message' => "I'm **ResQBot**, your disaster monitoring assistant.\n" .
                    "Ako si **ResQBot**, ang iyong disaster monitoring assistant.\n\n" .
                    "**I can help with / Makakatulong ako sa:**\n" .
                    "🔹 Safety tips / Mga paalala sa kaligtasan\n" .
                    "🔹 Emergency contacts / Emergency hotlines\n" .
                    "🔹 Disaster preparedness / Paghahanda sa sakuna\n" .
                    "🔹 Current disasters / Kasalukuyang mga sakuna\n\n" .
                    "Ask me in **English** or **Tagalog**! 🇵🇭",
            ];
        }

        return [
            'type' => 'help',
            'title' => 'ResQBot - Disaster Assistant',
            'message' => "I'm **ResQBot**, your disaster monitoring assistant for the Philippines.\n\n" .
                "I can help you with:\n" .
                "🔹 Safety tips for earthquakes, floods, typhoons, and fires\n" .
                "🔹 Emergency contact information\n" .
                "🔹 Disaster preparedness advice\n" .
                "🔹 Current disaster information\n\n" .
                "Try asking:\n" .
                "• \"What should I do during an earthquake?\"\n" .
                "• \"Emergency contacts\"\n" .
                "• \"Tell me about Typhoon Uwan\"\n" .
                "• \"Kumusta\" (in Tagalog)\n\n" .
                "Ask me anything! You can use **English** or **Tagalog**. 🇵🇭",
        ];
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
}
