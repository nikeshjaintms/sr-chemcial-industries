<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotEngineService;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected ChatbotEngineService $engine;

    public function __construct(ChatbotEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function chat(Request $request): JsonResponse
    {
        $query = trim(strip_tags($request->input('message', '')));
        $sessionId = $request->input('session_id') ?: session()->getId();

        if (empty($sessionId)) {
            $sessionId = bin2hex(random_bytes(16));
        }

        $response = $this->engine->processQuery($query, $sessionId);

        // Deduplicate API response products array strictly by database Product ID
        if (isset($response['products']) && is_array($response['products'])) {
            $seenIds = [];
            $uniqueProducts = [];
            foreach ($response['products'] as $p) {
                $id = $p['id'] ?? null;
                if ($id) {
                    if (!isset($seenIds[$id])) {
                        $seenIds[$id] = true;
                        $uniqueProducts[] = $p;
                    }
                } else {
                    $uniqueProducts[] = $p;
                }
            }
            $response['products'] = $uniqueProducts;
        }

        return response()->json($response);
    }

    public function history(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id') ?: session()->getId();

        $history = ChatHistory::where('session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'session_id' => $sessionId,
            'history' => $history
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id') ?: session()->getId();

        ChatHistory::where('session_id', $sessionId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Chat session history cleared.'
        ]);
    }
}
